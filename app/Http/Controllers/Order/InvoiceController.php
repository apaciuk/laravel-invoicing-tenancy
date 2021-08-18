<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Front\CartController;
use App\Model\Common\Setting;
use App\Model\Common\Template;
use App\Model\Order\Invoice;
use App\Model\Order\InvoiceItem;
use App\Model\Order\Order;
use App\Model\Order\Payment;
use App\Model\Payment\Currency;
use App\Model\Payment\Plan;
use App\Model\Payment\Promotion;
use App\Model\Payment\Tax;
use App\Model\Payment\TaxByState;
use App\Model\Payment\TaxOption;
use App\Model\Product\Price;
use App\Model\Product\Product;
use App\Traits\CoupCodeAndInvoiceSearch;
use App\Traits\PaymentsAndInvoices;
use App\Traits\TaxCalculation;
use App\User;
use Illuminate\Http\Request;

class InvoiceController extends TaxRatesAndCodeExpiryController
{
    use  CoupCodeAndInvoiceSearch;
    use  PaymentsAndInvoices;
    use TaxCalculation;

    public $invoice;
    public $invoiceItem;
    public $user;
    public $template;
    public $setting;
    public $payment;
    public $product;
    public $price;
    public $promotion;
    public $currency;
    public $tax;
    public $tax_option;
    public $order;
    public $cartController;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin', ['except' => ['pdf']]);

        $invoice = new Invoice();
        $this->invoice = $invoice;

        $invoiceItem = new InvoiceItem();
        $this->invoiceItem = $invoiceItem;

        $user = new User();
        $this->user = $user;

        $template = new Template();
        $this->template = $template;

        $seting = new Setting();
        $this->setting = $seting;

        $payment = new Payment();
        $this->payment = $payment;

        $product = new Product();
        $this->product = $product;

        $price = new Price();
        $this->price = $price;

        $promotion = new Promotion();
        $this->promotion = $promotion;

        $currency = new Currency();
        $this->currency = $currency;

        $tax = new Tax();
        $this->tax = $tax;

        $tax_option = new TaxOption();
        $this->tax_option = $tax_option;

        $order = new Order();
        $this->order = $order;

        $tax_by_state = new TaxByState();
        $this->tax_by_state = new $tax_by_state();

        $cartController = new CartController();
        $this->cartController = $cartController;
    }

    public function index(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'from'     => 'nullable',
            'till'     => 'nullable|after:from',

        ]);
        if ($validator->fails()) {
            $request->from = '';
            $request->till = '';

            return redirect('invoices')->with('fails', 'Start date should be before end date');
        }
        try {
            $currencies = Currency::where('status', 1)->pluck('code')->toArray();
            $name = $request->input('name');
            $invoice_no = $request->input('invoice_no');
            $status = $request->input('status');

            $currency_id = $request->input('currency_id');
            $from = $request->input('from');
            $till = $request->input('till');

            return view('themes.default1.invoice.index', compact('request','name','invoice_no','status','currencies','currency_id','from',

                'till'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function getInvoices(Request $request)
    {
        $name = $request->input('name');
        $invoice_no = $request->input('invoice_no');
        $status = $request->input('status');
        $currency = $request->input('currency_id');
        $from = $request->input('from');
        $till = $request->input('till');
        $query = $this->advanceSearch($name, $invoice_no, $currency, $status, $from, $till);

        return \DataTables::of($query->take(100))
         ->setTotalRecords($query->count())

         ->addColumn('checkbox', function ($model) {
             return "<input type='checkbox' class='invoice_checkbox' 
                            value=".$model->id.' name=select[] id=check>';
         })
                        ->addColumn('user_id', function ($model) {
                            $user = $this->user->where('id', $model->user_id)->first() ?: User::onlyTrashed()->find($model->user_id);

                            $id = $user->id;
                            $first = $user->first_name;
                            $last = $user->last_name;

                            return '<a href='.url('clients/'.$id).'>'.ucfirst($first).' '.ucfirst($last).'</a>';
                        })
                         ->addColumn('number', function ($model) {
                             return ucfirst($model->number);
                         })

                        ->addColumn('date', function ($model) {
                            return getDateHtml($model->created_at);
                        })
                         ->addColumn('grand_total', function ($model) {
                             return currencyFormat($model->grand_total, $code = $model->currency);
                         })
                          ->addColumn('status', function ($model) {
                              return getStatusLabel($model->status);
                          })

                        ->addColumn('action', function ($model) {
                            $action = '';

                            $check = $this->checkExecution($model->id);
                            if ($check == false) {
                                $action = '<form method="post" action='.url('order/execute?invoiceid='.$model->id).'>'.'<input type="hidden" name="_token" value='.\Session::token().'>'.'
                                    <button type="submit" class="btn btn-sm secondary btn-xs"'.tooltip('Execute&nbsp;Order').'<i class="fa fa-tasks" style="color:white;"></i></button></form>';
                            }

                            return '<a href='.url('invoices/show?invoiceid='.$model->id)
                            ." class='btn btn-sm btn-secondary btn-xs'".tooltip('View')."<i class='fa fa-eye' 
                            style='color:white;'> </i></a>"
                                    ."   $action";
                        })
                         ->filterColumn('user_id', function ($query, $keyword) {
                             $sql = 'first_name like ?';
                             $query->whereRaw($sql, ["%{$keyword}%"]);
                         })

                          ->filterColumn('status', function ($query, $keyword) {
                              $sql = 'status like ?';
                              $query->whereRaw($sql, ["%{$keyword}%"]);
                          })

                        ->filterColumn('number', function ($query, $keyword) {
                            $sql = 'number like ?';
                            $query->whereRaw($sql, ["%{$keyword}%"]);
                        })
                         ->filterColumn('grand_total', function ($query, $keyword) {
                             $sql = 'grand_total like ?';
                             $query->whereRaw($sql, ["%{$keyword}%"]);
                         })
                          ->filterColumn('date', function ($query, $keyword) {
                              $sql = 'date like ?';
                              $query->whereRaw($sql, ["%{$keyword}%"]);
                          })

                         ->rawColumns(['checkbox', 'user_id', 'number', 'date', 'grand_total', 'status', 'action'])
                        ->make(true);
    }

    /**
     * Shoe Invoice when view Invoice is selected from dropdown in Admin Panel.
     *
     * @param Request $request Get InvoiceId as Request
     */
    public function show(Request $request)
    {
        try {
            $invoice = Invoice::leftJoin('order_invoice_relations', 'invoices.id', '=', 'order_invoice_relations.invoice_id')
                ->select('invoices.id', 'invoices.user_id', 'invoices.date', 'invoices.currency', 'invoices.number', 'invoices.discount', 'invoices.grand_total', 'order_invoice_relations.order_id')
                ->where('invoices.id', '=', $request->input('invoiceid'))
                ->first();
            if (User::onlyTrashed()->find($invoice->user_id)) {
                throw new \Exception('This user is suspended from the system. Restore the user to view invoice details.');
            }
            $invoiceItems = $invoice->invoiceItem()->get();
            $user = $this->user->find($invoice->user_id);
            $order = Order::getOrderLink($invoice->order_id, 'orders');

            return view('themes.default1.invoice.show', compact('invoiceItems', 'invoice', 'user', 'order'));
        } catch (\Exception $ex) {
            app('log')->warning($ex->getMessage());

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * not in use case.
     *
     * @param Request $request
     *
     * @return type
     */
    public function generateById(Request $request)
    {
        try {
            $clientid = $request->input('clientid');
            $user = new User();
            if ($clientid) {
                $user = $user->where('id', $clientid)->first();
                if (! $user) {
                    return redirect()->back()->with('fails', 'Invalid user');
                }
            } else {
                $user = '';
            }
            $products = $this->product->pluck('name', 'id')->toArray();
            $currency = $this->currency->pluck('name', 'code')->toArray();

            return view('themes.default1.invoice.generate', compact('user', 'products', 'currency'));
        } catch (\Exception $ex) {
            app('log')->info($ex->getMessage());

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Generate invoice from client panel.
     *
     * @throws \Exception
     */
    public function generateInvoice()
    {
        try {
            $tax_rule = new \App\Model\Payment\TaxOption();
            $rule = $tax_rule->findOrFail(1);
            $rounding = $rule->rounding;
            $user_id = \Auth::user()->id;

            $grand_total = \Cart::getTotal();
            $number = rand(11111111, 99999999);
            $date = \Carbon\Carbon::now();
            if ($rounding) {
                $grand_total = round($grand_total);
            }
            $currency = \Session::has('cart_currency') ? \Session::get('cart_currency') : getCurrencyForClient(\Auth::user()->country);
            $invoice = $this->invoice->create(['user_id' => $user_id, 'number' => $number, 'date'=> $date, 'grand_total' => $grand_total, 'status' => 'pending',
                'currency' => $currency, ]);
            foreach (\Cart::getContent() as $cart) {
                $this->createInvoiceItems($invoice->id, $cart);
            }
            if (emailSendingStatus()) {
                $this->sendMail($user_id, $invoice->id);
            }

            return $invoice;
        } catch (\Exception $ex) {
            app('log')->error($ex->getMessage());

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function createInvoiceItems($invoiceid, $cart)
    {
        try {
            $planid = 0;
            $product_name = $cart->name;
            $regular_price = $cart->price;
            $quantity = $cart->quantity;
            $agents = $cart->attributes->agents;
            $domain = $this->domain($cart->id);
            if (checkPlanSession()) {
                $planid = \Session::get('plan');
            }
            if ($planid == 0) {
                //When Product is added from Faveo Website
                $planid = Plan::where('product', $cart->id)->pluck('id')->first();
            }
            $subtotal = $cart->getPriceSum();
            $tax_name = $cart->conditions->getName();
            $tax_percentage = $cart->conditions->getValue();
            $invoiceItem = $this->invoiceItem->create([
                'invoice_id'     => $invoiceid,
                'product_name'   => $product_name,
                'regular_price'  => $regular_price,
                'quantity'       => $quantity,
                'tax_name'       => $tax_name,
                'tax_percentage' => $tax_percentage,
                'subtotal'       => $subtotal,
                'domain'         => $domain,
                'plan_id'        => $planid,
                'agents'         => $agents,
            ]);

            return $invoiceItem;
        } catch (\Exception $ex) {
            throw new \Exception('Can not create Invoice Items');
        }
    }

    /**
     * Generate invoice from admin panel.
     *
     * @throws \Exception
     */
    public function invoiceGenerateByForm(Request $request, $user_id = '')
    {
        $this->validate($request, [
            'date'      => 'required|date',
            'domain'    => 'sometimes|nullable|regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i',
            'plan'      => 'required_if:subscription,true',
            'price'     => 'required',
            'product'   => 'required',
        ], [
            'plan.required_if' => 'Select a Plan',
        ]);

        try {
            $agents = $request->input('agents');
            $status = 'pending';
            $qty = $request->input('quantity');
            if ($user_id == '') {
                $user_id = $request->input('user');
            }
            $productid = $request->input('product');

            $plan = $request->input('plan');
            $agents = $this->getAgents($agents, $productid, $plan);
            $qty = $this->getQuantity($qty, $productid, $plan);

            $code = $request->input('code');
            $total = $request->input('price');
            $description = $request->input('description');
            if ($request->has('domain')) {
                $domain = $request->input('domain');
                $this->setDomain($productid, $domain);
            }
            $planObj = Plan::where('id', $plan)->first();
            $userCurrency = userCurrencyAndPrice($user_id, $planObj);
            $currency = $userCurrency['currency'];
            $number = rand(11111111, 99999999);
            $date = \Carbon\Carbon::parse($request->input('date'));
            $product = Product::find($productid);

            $cost = $this->cartController->cost($productid, $plan, $user_id);

            $couponTotal = $this->getGrandTotal($code, $total, $cost, $productid, $currency, $user_id);
            $grandTotalAfterCoupon = $qty * $couponTotal['total'];
            if (! $grandTotalAfterCoupon) {
                $status = 'success';
            }
            $user = User::where('id', $user_id)->select('state', 'country')->first();
            $tax = $this->calculateTax($product->id, $user->state, $user->country, true);
            $grand_total = rounding($this->calculateTotal($tax['value'], $grandTotalAfterCoupon));
            $invoice = Invoice::create(['user_id' => $user_id, 'number' => $number, 'date' => $date,
                'coupon_code'  => $couponTotal['code'], 'discount'=>$couponTotal['value'], 'discount_mode'  => $couponTotal['mode'], 'grand_total'  => $grand_total,  'currency'  => $currency, 'status' => $status, 'description' => $description, ]);

            $items = $this->createInvoiceItemsByAdmin($invoice->id, $productid,
              $total, $currency, $qty, $agents, $plan, $user_id, $tax['name'], $tax['value'], $grandTotalAfterCoupon);
            $result = $this->getMessage($items, $user_id);

            return successResponse($result);
        } catch (\Exception $ex) {
            app('log')->info($ex->getMessage());

            return errorResponse([$ex->getMessage()]);
        }
    }

    public function createInvoiceItemsByAdmin($invoiceid, $productid, $price,
        $currency, $qty, $agents, $planid = '', $userid = '', $tax_name = '', $tax_rate = '', $grandTotalAfterCoupon)
    {
        try {
            $product = $this->product->findOrFail($productid);
            $plan = Plan::where('product', $productid)->first();
            $subtotal = $qty * intval($grandTotalAfterCoupon);

            $domain = $this->domain($productid);
            $items = $this->invoiceItem->create([
                'invoice_id'     => $invoiceid,
                'product_name'   => $product->name,
                'regular_price'  => $price,
                'quantity'       => $qty,
                'subtotal'       => rounding($subtotal),
                'tax_name'       => $tax_name,
                'tax_percentage' => $tax_rate,
                'domain'         => $domain,
                'plan_id'        => $planid,
                'agents'         => $agents,
            ]);

            return $items;
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    public function setDomain($productid, $domain)
    {
        try {
            if (\Session::has('domain'.$productid)) {
                \Session::forget('domain'.$productid);
            }
            \Session::put('domain'.$productid, $domain);
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function sendMail($userid, $invoiceid)
    {
        try {
            $invoice = $this->invoice->find($invoiceid);
            $number = $invoice->number;
            $total = $invoice->grand_total;

            return $this->sendInvoiceMail($userid, $number, $total, $invoiceid);
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function pdf(Request $request)
    {
        try {
            $id = $request->input('invoiceid');
            if (! $id) {
                return redirect()->back()->with('fails', \Lang::get('message.no-invoice-id'));
            }
            $invoice = $this->invoice->where('id', $id)->first();
            if (! $invoice) {
                return redirect()->back()->with('fails', \Lang::get('message.invalid-invoice-id'));
            }
            $invoiceItems = $this->invoiceItem->where('invoice_id', $id)->get();
            if ($invoiceItems->count() == 0) {
                return redirect()->back()->with('fails', \Lang::get('message.invalid-invoice-id'));
            }
            $user = $this->user->find($invoice->user_id);
            if (! $user) {
                return redirect()->back()->with('fails', 'No User');
            }
            $order = $this->order->getOrderLink($invoice->orderRelation()->value('order_id'), 'my-order');
            // $order = Order::getOrderLink($invoice->order_id);
            $currency = $invoice->currency;
            $gst = TaxOption::select('tax_enable', 'Gst_No')->first();
            $symbol = $invoice->currency;
            // ini_set('max_execution_time', '0');
            $pdf = \PDF::loadView('themes.default1.invoice.newpdf', compact('invoiceItems', 'invoice', 'user', 'currency', 'symbol', 'gst', 'order'));

            return $pdf->download($user->first_name.'-invoice.pdf');
        } catch (\Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }
}
