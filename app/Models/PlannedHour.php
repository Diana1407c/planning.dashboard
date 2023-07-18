<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PlannedHour extends Model
{
    use HasFactory;

    const WEEK_PERIOD_TYPE = 'week';
    const MONTH_PERIOD_TYPE = 'month';

    const TECHNOLOGY_TYPE = 'technology';
    const ENGINEER_TYPE = 'engineer';

    protected $fillable = [
        'project_id',
        'planable_type',
        'planable_id',
        'year',
        'period_number',
        'period_type',
        'hours',
    ];

    public function project(): belongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function planable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeWeekly($query)
    {
        return $query->where('period_type', self::WEEK_PERIOD_TYPE);
    }

    public function scopeMonthly($query)
    {
        return $query->where('period_type', self::MONTH_PERIOD_TYPE);
    }

    public function scopeTechnologyType($query)
    {
        return $query->where('planable_type', self::TECHNOLOGY_TYPE);
    }

    public function scopeEngineerType($query)
    {
        return $query->where('planable_type', self::ENGINEER_TYPE);
    }
}
