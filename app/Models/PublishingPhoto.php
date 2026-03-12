<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublishingPhoto extends Model
{
    use SoftDeletes;

    protected $table = 'publishings_photos';

    protected $dates = [];
}
