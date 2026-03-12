<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $table = 'quotes';

    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_active', 1);
    }
}
