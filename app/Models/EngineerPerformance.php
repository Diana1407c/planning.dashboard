<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineerPerformance extends Model
{
    use HasFactory;

    protected $fillable = ['engineer_id', 'level_id', 'performance', 'from_date', 'is_current'];

    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function levelName(): string
    {
        return $this->level->name;
    }

    public function performancePercent()
    {
        if ($this->performance) {
            return $this->performance;
        }

        return $this->level->performance;
    }
}
