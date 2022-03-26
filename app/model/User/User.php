<?php
namespace App\Model\User;

use Core\BaseModel;

class User extends BaseModel
{
    protected $table = 'users';
    protected $fillable = [
        'username',
        'name',
        'email',
        'email_alternate',
        'status',
        'created_at'
    ];
}