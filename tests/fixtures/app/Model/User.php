<?php

namespace App\Model;

use Hyperf\Database\Model\Model;

class User extends Model
{
    protected array $guarded = [];

    protected array $appends = [
        'full_name',
    ];

    protected array $hidden = [
        'secret',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
