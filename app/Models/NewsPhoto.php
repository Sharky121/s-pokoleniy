<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsPhoto extends Model
{
    use SoftDeletes;

    protected $table = 'news_photos';

    protected $dates = [];
}
