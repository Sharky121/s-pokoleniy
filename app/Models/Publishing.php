<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publishing extends Model
{
    use SoftDeletes;

    protected $table = 'publishings';

    protected $dates = ['date'];

    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_active', 1);
    }

    public function photos()
    {
        return $this->hasMany(PublishingPhoto::class, 'publishing_id', 'id');
    }
}
