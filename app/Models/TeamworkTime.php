<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamworkTime extends Model
{
    protected $table = 'teamwork_time';
    protected $fillable = [
        'id', 'engineer_id', 'hours', 'date', 'billable', 'project_id'
    ];

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(Engineer::class);
    }
}
