<?php

namespace App\Model\Payment;

use App\BaseModel;
use Spatie\Activitylog\Traits\LogsActivity;

class Promotion extends BaseModel
{
    use LogsActivity;
    protected $table = 'promotions';
    protected $fillable = ['code', 'type', 'uses', 'value', 'start', 'expiry'];
    protected static $logName = 'Promotion';
    protected static $logAttributes = ['code', 'type', 'uses', 'value', 'start', 'expiry'];
    protected static $logOnlyDirty = true;

    // protected $dates = ['start','expiry'];
    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            return 'Promotion Code  <strong> '.$this->code.' </strong> was created';
        }

        if ($eventName == 'updated') {
            return 'Promotion Code <strong> '.$this->code.'</strong> was updated';
        }

        if ($eventName == 'deleted') {
            return 'Promotion Code <strong> '.$this->code.' </strong> was deleted';
        }

        return '';
    }

    public function relation()
    {
        return $this->hasMany('App\Model\Payment\PromoProductRelation', 'promotion_id');
    }

    public function delete()
    {
        $this->relation()->delete();

        return parent::delete();
    }
}
