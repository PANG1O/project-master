<?php
namespace App\Models;

class UserModel extends Base {
    protected static string $table = 'users';
    protected static array $fields = [
        'id',
        'salutation',
        'title',
        'first_name',
        'last_name',
        'email',
        'department',
        'password',
        'admin',
        'active',
        'deleted',
        'created',
        'updated'
    ];
}