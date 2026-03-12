<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageGalleryPhoto extends Model
{
    use SoftDeletes;

    protected $table = 'pages_galleries_photos';

    protected $dates = [];
}
