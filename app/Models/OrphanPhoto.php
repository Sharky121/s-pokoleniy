<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrphanPhoto extends Model
{
    use SoftDeletes;

    protected $table = 'orphans_photos';

    protected $dates = [];
}
