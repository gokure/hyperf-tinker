<?php

namespace App\Model;

use Hyperf\Database\Model\Model;

class User extends Model
{
    protected $attributes = [
        'first_name' => '',
        'last_name' => '',
        'secret' => '',
    ];

    protected $guarded = [];

    protected $appends = [
        'full_name',
    ];

    protected $hidden = [
        'secret',
    ];

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->last_name;
    }
}
