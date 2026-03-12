<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageGallery extends Model
{
    use SoftDeletes;

    protected $table = 'pages_galleries';

    protected $dates = [];

    public function photos()
    {
        return $this->hasMany(PageGalleryPhoto::class, 'page_gallery_id', 'id');
    }
}
