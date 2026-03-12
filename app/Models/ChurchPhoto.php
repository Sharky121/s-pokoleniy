<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChurchPhoto extends Model
{
    use SoftDeletes;

    protected $table = 'churches_photos';

    protected $dates = [];
}
