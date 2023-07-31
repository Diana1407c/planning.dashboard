<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineerLevel extends Model
{
    use HasFactory;

    protected $table = 'engineer_level';

    protected $fillable = ['engineer_id', 'level_id', 'from_date'];
}
