<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Model\Common\StatusSetting;
use App\Model\Order\Order;
use Illuminate\Http\Request;

class ExtendedOrderController extends Controller
{
    /**
     * Create orders.
     *
     * @param Request $request
     *
     * @return type
     */
    public function orderExecute(Request $request)
    {
        try {
            $invoiceid = $request->input('invoiceid');
            $execute = $this->executeOrder($invoiceid);
            if ($execute == 'success') {
                return redirect()->back()->with('success', \Lang::get('message.saved-successfully'));
            } else {
                return redirect()->back()->with('fails', \Lang::get('message.not-saved-successfully'));
            }
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * generate serial key and add no of agents in the last 4 digits og the 16 string/digit serial key .
     *
     * @param int $productid
     * @param int $agents    No Of Agents
     *
     * @throws \Exception
     *
     * @return string The Final Serial Key after adding no of agents in the last 4 digits
     */
    public function generateSerialKey(int $productid, $agents)
    {
        try {
            $len = strlen($agents);
            switch ($len) {//Get Last Four digits based on No.Of Agents

                case '1':
                   $lastFour = '000'.$agents;
                    break;
                   case '2':

                    $lastFour = '00'.$agents;
                     break;
                      case '3':
                    $lastFour = '0'.$agents;
                     break;
                      case '4':
                    $lastFour = $agents;

                     break;
                default:
                    $lastFour = '0000';
                    break;
            }
            $str = strtoupper(str_random(12));
            $licCode = $str.$lastFour;

            return $licCode;
        } catch (\Exception $ex) {
            app('log')->error($ex->getMessage());

            throw new \Exception($ex->getMessage());
        }
    }

    public function generateNumber()
    {
        try {
            return rand('10000000', '99999999');
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function changeDomain(Request $request)
    {
        $domain = '';
        $arrayOfDomains = [];
        $allDomains = $request->input('domain');
        $seperateDomains = explode(',', $allDomains); //Bifurcate the domains here
        $allowedDomains = $this->getAllowedDomains($seperateDomains);
        $id = $request->input('id');
        $order = Order::findorFail($id);
        $licenseCode = $order->serial_key;
        $order->domain = implode(',', $allowedDomains);
        $order->save();
        $licenseStatus = StatusSetting::pluck('license_status')->first();
        if ($licenseStatus == 1) {
            $licenseExpiry = $order->subscription->ends_at;
            $updatesExpiry = $order->subscription->update_ends_at;
            $supportExpiry = $order->subscription->support_ends_at;
            $cont = new \App\Http\Controllers\License\LicenseController();
            $updateLicensedDomain = $cont->updateLicensedDomain($licenseCode, $order->domain, $order->product, $licenseExpiry, $updatesExpiry, $supportExpiry, $order->number);
            //Now make Installation status as inactive
            $updateInstallStatus = $cont->updateInstalledDomain($licenseCode, $order->product);
        }

        return ['message' => 'success', 'update'=>'Licensed Domain Updated'];
    }

    public function reissueLicense(Request $request)
    {
        $order = Order::findorFail($request->input('id'));
        if (\Auth::user()->role != 'admin' && $order->client != \Auth::user()->id) {
            return errorResponse('Cannot reissue license. Invalid modification of data');
        }
        $order->domain = '';
        $licenseCode = $order->serial_key;
        $order->save();
        $licenseStatus = StatusSetting::pluck('license_status')->first();
        if ($licenseStatus == 1) {
            $licenseExpiry = $order->subscription->ends_at;
            $updatesExpiry = $order->subscription->update_ends_at;
            $supportExpiry = $order->subscription->support_ends_at;
            $cont = new \App\Http\Controllers\License\LicenseController();
            $updateLicensedDomain = $cont->updateLicensedDomain($licenseCode, $order->domain, $order->product, $licenseExpiry, $updatesExpiry, $supportExpiry, $order->number);
            //Now make Installation status as inactive
            $updateInstallStatus = $cont->updateInstalledDomain($licenseCode, $order->product);
        }

        return ['message' => 'success', 'update'=>'License Reissued'];
    }

    public function getAllowedDomains($seperateDomains)
    {
        $needle = 'www';
        foreach ($seperateDomains as $domain) {
            $allowedDomains[] = $domain;
        }

        return  $allowedDomains;
    }
}
