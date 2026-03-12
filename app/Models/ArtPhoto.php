<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArtPhoto extends Model
{
    use SoftDeletes;

    protected $table = 'art_photos';

    protected $dates = [];
}
