<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:30
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    protected $table = 'users';

    protected $fillable = [
        'username', 'email', 'password', 'full_name', 'profile_image'
    ];

    protected $hidden = ['password', 'remember_token', 'api_token'];
}
