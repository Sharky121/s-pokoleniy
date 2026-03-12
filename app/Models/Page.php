<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $table = 'pages';

    protected $dates = [];

    public function scopeActive(Builder $builder)
    {
        return $builder->where('is_active', 1);
    }

    public function compileUrl()
    {
        if ($this->parent_page_id) {
            $parent = resolve('pages')->firstWhere('id', $this->parent_page_id);
            return e(str_replace('{parentUrl}', $parent->compileUrl(), $this->url));
        }
        return e($this->url);
    }

    public function parent()
    {
        return $this->belongsTo(Page::class, 'parent_page_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Page::class, 'parent_page_id', 'id');
    }

    public function galleries()
    {
        return $this->hasMany(PageGallery::class, 'page_id', 'id');
    }
}