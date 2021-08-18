<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Model\Common\StatusSetting;
use App\Model\Order\Invoice;
use App\Model\Payment\TaxProductRelation;
use App\Model\Product\Product;
use App\Model\Product\ProductUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExtendedBaseProductController extends Controller
{
    public function getUpload($id)
    {
        $new_upload = ProductUpload::where('product_id', '=', $id)
        ->select('id', 'product_id', 'title', 'description', 'version', 'file')
        ->get();

        return \DataTables::of($new_upload)
        ->addColumn('checkbox', function ($model) {
            return "<input type='checkbox' class='upload_checkbox' value=".$model->id.' name=select[] id=checks>';
        })

        ->addColumn('product_id', function ($model) {
            return ucfirst($this->product->where('id', $model->product_id)->first()->name);
        })

        ->addColumn('title', function ($model) {
            return ucfirst($model->title);
        })
        ->addColumn('description', function ($model) {
            return ucfirst($model->description);
        })
        ->addColumn('version', function ($model) {
            return $model->version;
        })

        ->addColumn('file', function ($model) {
            return $model->file;
        })
        ->addColumn('action', function ($model) {
            return '<p><a href='.url('edit-upload/'.$model->id).
                                " class='btn btn-sm btn-secondary'".tooltip('Edit')."<i class='fa fa-edit'
                                 style='color:white;'> </i></a></p>";
        })
        ->rawcolumns(['checkbox', 'product_id', 'title', 'description', 'version', 'file', 'action'])
        ->make(true);
    }

    /**
     * Go to edit Product Upload Page.
     *
     * @date   2019-03-07T13:15:58+0530
     *
     * @param int $id Product Upload id
     */
    public function editProductUpload($id)
    {
        $model = ProductUpload::where('id', $id)->first();
        $selectedProduct = $model->product->name;

        return view('themes.default1.product.product.edit-upload-option', compact('model', 'selectedProduct'));
    }

    //Update the File Info
    public function uploadUpdate($id, Request $request)
    {
        $this->validate($request, [
            'title'        => 'required',
            'version'      => 'required',
            'dependencies'  =>'required',
        ]);
        try {
            $file_upload = ProductUpload::find($id);
            $file_upload->where('id', $id)->update(['title'=>$request->input('title'), 'description'=>$request->input('description'), 'version'=> $request->input('version'), 'dependencies'=>json_encode($request->input('dependencies')), 'is_private'=>$request->input('is_private'), 'is_restricted'=>$request->input('is_restricted')]);
            $autoUpdateStatus = StatusSetting::pluck('update_settings')->first();
            if ($autoUpdateStatus == 1) { //If License Setting Status is on,Add Product to the AutoUpdate Script
                $productSku = $file_upload->product->product_sku;
                $updateClassObj = new \App\Http\Controllers\AutoUpdate\AutoUpdateController();
                $addProductToAutoUpdate = $updateClassObj->editVersion($request->input('version'), $productSku);
            }

            return redirect()->back()->with('success', 'Product Updated Successfully');
        } catch (\Exception $e) {
            app('log')->error($e->getMessage());
            $message = [$e->getMessage()];
            $response = ['success'=>'false', 'message'=>$message];

            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    public function saveTax($taxes, $product_id)
    {
        TaxProductRelation::where('product_id', $product_id)->delete();
        if ($taxes) {
            foreach ($taxes as $tax) {
                $newTax = new TaxProductRelation();
                $newTax->product_id = $product_id;
                $newTax->tax_class_id = $tax;
                $newTax->save();
            }
        }
    }

    /**
     * Whether the Product Requires the domain to be entered.
     *
     * @param int $productid
     */
    public function getProductField(int $productid)
    {
        try {
            $field = '';
            $product = Product::find($productid);
            if ($product->require_domain == 1) {
                $field .= '<div>
                        <label>'./* @scrutinizer ignore-type */
                         \Lang::get('message.domain')."</label>
                        <input type='text' name='domain' class='form-control' 
                        id='domain' placeholder='domain.com or sub.domain.com'>
                </div>";
            }

            return $field;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function adminDownload($id, $invoice = '', $api = false)
    {
        try {
            if ($this->downloadValidation(true, $id, $invoice, $api)) {
                $release = $this->downloadProductAdmin($id);
                $name = Product::where('id', $id)->value('name');
                if (is_array($release) && array_key_exists('type', $release)) {
                    header('Location: '.$release['release']);
                    exit;
                } else {
                    header('Content-type: Zip');
                    header('Content-Description: File Transfer');
                    header('Content-Disposition: attachment; filename = '.$name.'.zip');
                    header('Content-Length: '.filesize($release));
                    readfile($release);
                    // ob_end_clean();
                }
            } else {
                throw new \Exception(\Lang::get('message.no_permission_for_action'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('fails', $e->getMessage());
        }
    }

    /**
     * Checks whether order exists or not for a product and invoice.
     *
     * @date   2020-04-13T14:53:04+0530
     *
     * @param  int $id                  Product id
     * @param  int $invoice             Invoice Number
     * @param  bool $allowDownload
     * @return bool
     */
    private function downloadValidation(bool $allowDownload, $id, $invoice, $api)
    {
        if ($api == false) {
            if (\Auth::user()->role == 'user') {
                $invoice = Invoice::where('number', $invoice)->first(); //If invoice number sent as parameter exists
                $this->checkSubscriptionExpiry($invoice);
                $allowDownload = $invoice ? $invoice->order()->value('product') == $id : false; //If the order for the product sent in the parameter exists
            }
        }

        return $allowDownload;
    }

    public function checkSubscriptionExpiry($invoice)
    {
        $checkSubscription = false;
        if ($invoice) {
            if ($invoice->user_id != \Auth::user()->id) {
                throw new \Exception('Invalid modification of data. This user does not have permission for this action.');
            }
            $checkSubscription = $invoice->order()->first() ? $invoice->order()->first()->subscription : false;
        }
        if ($checkSubscription) {
            if (strtotime($checkSubscription->update_ends_at) > 1) {
                if ($checkSubscription->update_ends_at < (new Carbon())->toDateTimeString()) {
                    throw new \Exception('Please renew your subscription to download');
                }
            }
        } else {
            throw new \Exception('No order exists for this invoice.');
        }
    }

    /**
     * Save Values Related to Cart(eg: whether show Agents or Quantity in Cart etc).
     *
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     *
     * @date   2019-01-07T14:34:54+0530
     *
     * @param Illuminate\Http\Request $input               All the Product Detais Sent from  the form
     * @param bool                    $can_modify_agent    Whether Agents can be modified by customer
     * @param bool                    $can_modify_quantity Whether Product Quantity can be modified by Customers
     *
     * @return
     */
    public function saveCartValues($input, bool $can_modify_agent, bool $can_modify_quantity)
    {
        $this->product->show_agent = $input['show_agent'] == 1; //if Show Agents Selected
        $this->product->can_modify_agent = $can_modify_agent;
        $this->product->can_modify_quantity = $can_modify_quantity;
    }

    /**
     * Save Values Related to Cart while Updating Produc(eg: whether show Agents or Quantityof Product in Cart etc).
     *
     * @author Ashutosh Pathak <ashutosh.pathak@ladybirdweb.com>
     *
     * @date   2019-01-07T20:40:20+0530
     *
     *@param  Illuminate\Http\Request     $input      All the Product Detais Sent from  the form
     * @param Illuminate\Http\Request; $request
     * @param array                    $product instance of the Product
     *
     * @return Save The Details
     */
    public function saveCartDetailsWhileUpdating($input, $request, $product)
    {
        $product->show_agent = $input['show_agent'] == 1 ? 1 : 0; //if Show Agents Selected
        if ($product->show_agent == 1) {
            $product->can_modify_quantity = 0;
            if ($request->has('can_modify_agent')) {
                $product->can_modify_agent = 1;
            } else {
                $product->can_modify_agent = 0;
                $product->can_modify_quantity = 0;
            }
        } else {
            $product->can_modify_agent = 0;
            if ($request->has('can_modify_quantity')) {
                $product->can_modify_quantity = 1;
            } else {
                $product->can_modify_agent = 0;
                $product->can_modify_quantity = 0;
            }
        }

        $product->save();
    }
}
