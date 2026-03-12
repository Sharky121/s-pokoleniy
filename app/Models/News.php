<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    protected $table = 'news';

    protected $dates = ['date'];

    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_active', 1);
    }

    public function photos()
    {
        return $this->hasMany(NewsPhoto::class, 'news_id', 'id');
    }
}
