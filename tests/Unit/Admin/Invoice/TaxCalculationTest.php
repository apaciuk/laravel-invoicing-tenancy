<?php

namespace Tests\Unit\Admin\Invoice;

use App\Http\Controllers\Order\InvoiceController;
use App\Model\License\LicenseType;
use App\Model\Payment\TaxClass;
use App\Model\Payment\TaxOption;
use App\Model\Payment\TaxProductRelation;
use App\Model\Product\Product;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\DBTestCase;

class TaxCalculationTest extends DBTestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->classObject = new InvoiceController();
    }

    /** @group tax */
    public function test_calculateTax_whenNoTaxIsAppliedOnProduct()
    {
        $user = factory(User::class)->create();
        $product = factory(Product::class)->create();
        $cont = new \App\Http\Controllers\Order\InvoiceController();
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenIntraStateGstAppliedOnProductWhenGstIsDisabled_taxValueIsNull()
    {
        $user = factory(User::class)->create();
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Intra State GST', 'tax-name'=>'CGST+SGST', 'active'=>1]);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenIntraStateGstAppliedOnProduct_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'IN-KA', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Intra State GST', 'tax-name'=>'CGST+SGST', 'active'=>1]);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'CGST+SGST');
        $this->assertEquals($tax['value'], '18%');
    }

    /** @group tax */
    public function test_calculateTax_whenInterStateGstAppliedButUserStateEqualsOriginState_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'IN-KA', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Inter State GST', 'tax-name'=>'IGST', 'active'=>1]);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenInterStateGstApplied_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'IN-DL', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Inter State GST', 'tax-name'=>'IGST', 'active'=>1]);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'IGST');
        $this->assertEquals($tax['value'], '18%');
    }

    /** @group tax */
    public function test_calculateTax_whenInterStateGstAppliedWhenStatusIsInactive_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'IN-DL', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Inter State GST', 'tax-name'=>'IGST', 'active'=>0]);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenUnionTerritoryGstAppliedWhenUserStateIsNotUT_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'IN-DL', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Union Territory GST', 'tax-name'=>'CGST+UTGST', 'active'=>1]);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenUnionTerritoryGstApplied_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'IN-AN', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Union Territory GST', 'tax-name'=>'CGST+UTGST', 'active'=>1]);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'CGST+UTGST');
        $this->assertEquals($tax['value'], '18%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxAppliedWhenUserStateIsIndian_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'IN-DL', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxApplied_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'VAT');
        $this->assertEquals($tax['value'], '20%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxAppliedwhenTaxIsInactive_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>0, 'rate'=>'20']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxAppliedwhenWhenUserIsFromOtherState_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>0, 'rate'=>'20', 'country'=>'AF', 'state'=>'AF-BDG']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxAppliedwhenWhenUserIsFromOtherState_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20', 'country'=>'AU', 'state'=>'AU-NT']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'VAT');
        $this->assertEquals($tax['value'], '20%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxAppliedWhenUserIsFromSameCountryOtherState_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20', 'country'=>'AU', 'state'=>'AU-NSW']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxAppliedForAllStatesofUsersCountry_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20', 'country'=>'AU', 'state'=>'']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'VAT');
        $this->assertEquals($tax['value'], '20%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxAppliedForAllStatesWhenTaxInactive_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>0, 'rate'=>'20', 'country'=>'AU', 'state'=>'']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenTaxIsCreatedButNotLinkedToAProduct_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20', 'country'=>'AU', 'state'=>'']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }

    /** @group tax */
    public function test_calculateTax_whenTaxIsAppliedToAllCountriesAllStates_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'AU-NT', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20', 'country'=>'', 'state'=>'']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'VAT');
        $this->assertEquals($tax['value'], '20%');
    }

    /** @group tax */
    public function test_calculateTax_whenTaxIsAppliedToAllCountriesAllStateUserStateIsNull_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'', 'country'=> 'AU']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20', 'country'=>'', 'state'=>'']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxOption::where('id', 1)->update(['tax_enable'=> 1]);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'VAT');
        $this->assertEquals($tax['value'], '20%');
    }

    /** @group tax */
    public function test_calculateTax_whenTaxIsAppliedToAllCountriesAllStateWhenGstDisabled_taxValueAndNameIsReturned()
    {
        $user = factory(User::class)->create(['state'=>'IN-KA', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20', 'country'=>'', 'state'=>'']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'VAT');
        $this->assertEquals($tax['value'], '20%');
    }

    /** @group tax */
    public function test_calculateTax_whenOtherTaxAppliedUserIsIndianGstDisabled_taxValueIsNull()
    {
        $user = factory(User::class)->create(['state'=>'IN-KA', 'country'=> 'IN']);
        $this->withoutMiddleware();
        $this->call('POST', 'taxes/class', ['name'=>'Others', 'tax-name'=>'VAT', 'active'=>1, 'rate'=>'20', 'country'=>'AU', 'state'=>'']);
        $this->call('POST', 'license-type', ['name'=>'Download Perpetual']);
        $taxClass = TaxClass::first();
        $licenseType = LicenseType::first();
        $product = factory(Product::class)->create(['type'=>$licenseType->id, 'product_sku'=>'test']);
        TaxProductRelation::create(['product_id'=>$product->id, 'tax_class_id'=>$taxClass->id]);
        $tax = $this->classObject->calculateTax($product->id, $user->state, $user->country, true);
        $this->assertEquals($tax['name'], 'null');
        $this->assertEquals($tax['value'], '0%');
    }
}
