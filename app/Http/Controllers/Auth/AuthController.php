<?php

namespace App\Http\Controllers\Auth;

use App\ApiKey;
use App\Http\Controllers\Controller;
use App\Http\Controllers\License\LicenseController;
use App\Model\Common\StatusSetting;
use App\Model\User\AccountActivate;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;

class AuthController extends BaseAuthController
{
    /*
      |--------------------------------------------------------------------------
      | Registration & Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users, as well as the
      | authentication of existing users. By default, this controller uses
      | a simple trait to add these behaviors. Why don't you explore it?
      |
     */

    // use AuthenticatesAndRegistersUsers;

    /* to redirect after login */

    //protected $redirectTo = 'home';

    /* Direct After Logout */
    protected $redirectAfterLogout = 'home';
    protected $loginPath = 'login';

    //protected $loginPath = 'login';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $license = new LicenseController();
        $this->licensing = $license;
    }

    public function activate($token, AccountActivate $activate, Request $request, User $user)
    {
        try {
            $activate = $activate->where('token', $token)->first();
            $url = 'login';
            if ($activate) {
                $email = $activate->email;
            } else {
                throw new NotFoundHttpException('Token mismatch. Account cannot be activated.');
            }
            $user = $user->where('email', $email)->first();
            if ($user) {
                if ($user->active == 0) {
                    $user->active = 1;
                    $user->save();
                    $status = StatusSetting::select('mailchimp_status', 'pipedrive_status', 'zoho_status')->first();
                    $this->addUserToPipedrive($user, $status->pipedrive_status); //Add user to pipedrive
                $this->addUserToZoho($user, $status->zoho_status); //Add user to zoho
                $this->addUserToMailchimp($user, $status->mailchimp_status); // Add user to mailchimp
                if (\Session::has('session-url')) {
                    $url = \Session::get('session-url');

                    return redirect($url);
                }

                    return redirect($url)->with('success', 'Email verification successful.
                    Please login to access your account !!');
                } else {
                    return redirect($url)->with('warning', 'This email is already verified');
                }
            } else {
                throw new NotFoundHttpException('User with this email not found.');
            }
        } catch (\Exception $ex) {
            if ($ex->getCode() == 400) {
                return redirect($url)->with('success', 'Email verification successful,
                 Please login to access your account');
            }

            return redirect($url)->with('fails', $ex->getMessage());
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    public function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function requestOtp(Request $request)
    {
        $this->validate($request, [
            'code'   => 'required|numeric',
            'mobile' => 'required|numeric',
        ]);

        $number = ltrim($request->oldnumber, '0');
        $newNumber = ltrim($request->newnumber, '0');
        User::where('mobile', $number)->update(['mobile'=>$newNumber]);

        try {
            $code = $request->input('code');
            $mobile = ltrim($request->input('mobile'), '0');
            $number = '(+'.$code.') '.$mobile;
            $result = $this->sendOtp($mobile, $code);
            $response = ['type' => 'success', 'message' => 'OTP has been sent to '.$number.'.Please Verify to Login'];

            return response()->json($response);
        } catch (\Exception $ex) {
            $result = [$ex->getMessage()];

            return response()->json(compact('result'), 500);
        }
    }

    public function retryOTP(Request $request)
    {
        $this->validate($request, [
            'code'   => 'required|numeric',
            'mobile' => 'required|numeric',
        ]);

        try {
            $code = $request->input('code');
            $mobile = ltrim($request->input('mobile'), '0');
            $otp = $request->input('otp');
            $number = '(+'.$code.') '.$mobile;
            $result = $this->sendForReOtp($mobile, $code, $request->input('type'));
            // dd($result);
            switch ($request->input('type')) {
                case 'text':
                   $array = json_decode($result, true);
                   $response = ['type' => 'success',
                       'message'           => 'OTP has been resent to '.$number.'.Please Enter the OTP to login!!', ];

                    break;

                    case 'voice':
                    $array = json_decode($result, true);
                    $response = ['type' => 'success',
                        'message'            => 'Voice call has been sent to '.$number.'.Please Enter the OTP received on the call to login!!', ];
                    break;

                default:
                    $array = json_decode($result, true);
                    $response = ['type' => 'success',
                        'message'            => 'Voice call has been sent to '.$number.'.Please Enter the OTP received on the call to login!!', ];
                    break;

            }

            return response()->json($response);
        } catch (\Exception $ex) {
            $result = [$ex->getMessage()];

            return response()->json(compact('result'), 500);
        }
    }

    public function verifyOtp($mobile, $code, $otp)
    {
        $client = new \GuzzleHttp\Client();
        $key = ApiKey::where('id', 1)->value('msg91_auth_key');
        $number = $code.$mobile;
        $response = $client->request('GET', 'https://api.msg91.com/api/v5/otp/verify', [
            'query' => ['authkey' => $key, 'mobile' => $number, 'otp' => $otp],
        ]);

        return $response->getBody()->getContents();
    }

    public function postOtp(Request $request)
    {
        $this->validate($request, [
            'otp' => 'required|numeric',
        ]);

        try {
            $code = $request->input('code');
            $mobile = ltrim($request->input('mobile'), '0');
            $otp = $request->input('otp');
            $userid = $request->input('id');
            $verify = $this->verifyOtp($mobile, $code, $otp);
            $array = json_decode($verify, true);
            if ($array['type'] == 'error') {
                throw new \Exception('OTP Not Verified!');
            }

            $user = User::find($userid);
            if ($user) {
                $user->mobile = $mobile;
                $user->mobile_code = $code;
                $user->mobile_verified = 1;
                $user->save();
            }
            $check = $this->checkVerify($user);
            $response = ['type' => 'success', 'proceed' => $check,
                'user_id'           => $userid, 'message' =>'Mobile verified..', ];

            return response()->json($response);
            // return redirect('/login');
        } catch (\Exception $ex) {
            $result = [$ex->getMessage()];
            if ($ex->getMessage() == 'OTP Not Verified!') {
                $errors = ['OTP Not Verified!'];
            }

            return response()->json(compact('result'), 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
        ]);
        try {
            $email = $request->input('email');
            $userid = $request->input('id');
            $user = User::find($userid);
            $check = $this->checkVerify($user);
            $method = 'GET';
            //$this->sendActivation($email, $request->method());
            $this->sendActivation($email, $method);
            $response = ['type' => 'success', 'proceed' => $check,
                'email'             => $email, 'message' => 'Activation link has been sent to '.$email, ];

            return response()->json($response);
        } catch (\Exception $ex) {
            $result = [$ex->getMessage()];

            return response()->json(compact('result'), 500);
        }
    }

    public function checkVerify($user)
    {
        $check = false;
        if ($user->active == '1' && $user->mobile_verified == '1') {
            \Auth::login($user);
            $check = true;
        }

        return $check;
    }

    public function getState(Request $request, $state)
    {
        try {
            $id = $state;
            $states = \App\Model\Common\State::where('country_code_char2', $id)
            ->orderBy('state_subdivision_name', 'asc')->get();

            if (count($states) > 0) {
                echo '<option value="">Choose</option>';
                foreach ($states as $stateList) {
                    echo '<option value='.$stateList->state_subdivision_code.'>'
                .$stateList->state_subdivision_name.'</option>';
                }
            } else {
                echo "<option value=''>No States Available</option>";
            }
        } catch (\Exception $ex) {
            echo "<option value=''>Problem while loading</option>";

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function salesManagerMail($user, $bcc = [])
    {
        $manager = $user->manager()

                ->where('position', 'manager')
                ->select('first_name', 'last_name', 'email', 'mobile_code', 'mobile', 'skype')
                ->first();
        if ($user && $user->role == 'user' && $manager) {
            $settings = new \App\Model\Common\Setting();
            $setting = $settings->first();
            $from = $setting->email;
            $to = $user->email;
            $templates = new \App\Model\Common\Template();
            $template = $templates
                    ->join('template_types', 'templates.type', '=', 'template_types.id')
                    ->where('template_types.name', '=', 'sales_manager_email')
                    ->select('templates.data', 'templates.name')
                    ->first();
            $template_data = $template->data;
            $template_name = $template->name;
            $template_controller = new \App\Http\Controllers\Common\TemplateController();
            $replace = [
                'name'               => $user->first_name.' '.$user->last_name,
                'manager_first_name' => $manager->first_name,
                'manager_last_name'  => $manager->last_name,
                'manager_email'      => $manager->email,
                'manager_code'       => $manager->mobile_code,
                'manager_mobile'     => $manager->mobile,
                'manager_skype'      => $manager->skype,
            ];
            $mail = new \App\Http\Controllers\Common\PhpMailController();
            $mail->sendEmail($from, $to, $template_data, $template_name, $replace, 'sales_manager_email', $bcc);
        }
    }

    public function accountManagerMail($user, $bcc = [])
    {
        $manager = $user->accountManager()

                ->where('position', 'account_manager')
                ->select('first_name', 'last_name', 'email', 'mobile_code', 'mobile', 'skype')
                ->first();
        if ($user && $user->role == 'user' && $manager) {
            $settings = new \App\Model\Common\Setting();
            $setting = $settings->first();
            $from = $setting->email;
            $to = $user->email;
            $templates = new \App\Model\Common\Template();
            $template = $templates
                    ->join('template_types', 'templates.type', '=', 'template_types.id')
                    ->where('template_types.name', '=', 'account_manager_email')
                    ->select('templates.data', 'templates.name')
                    ->first();
            $template_data = $template->data;
            $template_name = $template->name;
            $template_controller = new \App\Http\Controllers\Common\TemplateController();
            $replace = [
                'name'               => $user->first_name.' '.$user->last_name,
                'manager_first_name' => $manager->first_name,
                'manager_last_name'  => $manager->last_name,
                'manager_email'      => $manager->email,
                'manager_code'       => $manager->mobile_code,
                'manager_mobile'     => $manager->mobile,
                'manager_skype'      => $manager->skype,
            ];
            $mail = new \App\Http\Controllers\Common\PhpMailController();
            $mail->sendEmail($from, $to, $template_data, $template_name, $replace, 'account_manager_email', $bcc);
        }
    }
}
