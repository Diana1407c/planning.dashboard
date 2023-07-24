<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineerHistory extends Model
{
    use HasFactory;

    protected $fillable = ['engineer_id', 'historyable_type', 'historyable_id', 'value', 'label'];

    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }
}
