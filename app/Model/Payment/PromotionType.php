<?php

namespace App\Model\Payment;

use App\BaseModel;

class PromotionType extends BaseModel
{
    protected $table = 'promotion_types';
    protected $fillable = ['name'];
}
