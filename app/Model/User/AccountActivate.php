<?php

namespace App\Model\User;

use Illuminate\Database\Eloquent\Model;

class AccountActivate extends Model
{
    protected $table = 'account_activates';
    protected $fillable = ['email', 'token'];
}
