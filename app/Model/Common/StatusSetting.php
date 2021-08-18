<?php

namespace App\Model\Common;

use Illuminate\Database\Eloquent\Model;

class StatusSetting extends Model
{
    protected $table = 'status_settings';
    public $timestamps = false;
    protected $fillable = ['expiry_mail', 'activity_log_delete', 'license_status', 'github_status', 'mailchimp_status', 'twitter_status', 'msg91_status', 'emailverification_status', 'recaptcha_status', 'update_status', 'zoho_status', 'rzp_status', 'mailchimp_product_status', 'mailchimp_ispaid_status', 'terms', 'pipedrive_status', 'domain_check'];
}
