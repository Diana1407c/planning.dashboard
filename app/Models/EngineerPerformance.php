<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineerPerformance extends Model
{
    use HasFactory;

    protected $fillable = ['engineer_id', 'performance', 'from_date'];

    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }
}
