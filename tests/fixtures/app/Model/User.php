<?php

declare(strict_types=1);
/**
 * This file is part of gokure/hyperf-cors.
 *
 * @link     https://github.com/gokure/hyperf-cors
 * @document https://github.com/gokure/hyperf-cors/blob/main/README.md
 * @contact  gokure@gmail.com
 * @license  https://github.com/hyperf/hyperf-cors/blob/main/LICENSE
 */

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
