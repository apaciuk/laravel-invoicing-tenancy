<?php

namespace App\Model\Order;

use App\BaseModel;

class InvoiceItem extends BaseModel
{
    protected $table = 'invoice_items';
    protected $fillable = ['invoice_id', 'product_name',
        'regular_price', 'quantity', 'discount', 'tax_name',
        'tax_percentage', 'tax_code', 'discount_mode', 'subtotal', 'domain', 'plan_id', 'agents', ];

    public function setDomainAttribute($value)
    {
        $this->attributes['domain'] = $this->get_domain($value);
    }

    public function get_domain($url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        if (! $domain) {
            $domain = $pieces['path'];
        }

        return strtolower($domain);
    }

    public function order()
    {
        return $this->hasOne('App\Model\Order\Order');
    }
}
