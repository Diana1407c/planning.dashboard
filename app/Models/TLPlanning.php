<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TLPlanning extends Model
{
    use HasFactory;

    protected $table = 'team_lead_planning';
    protected $fillable = [
        'project_id', 'engineer_id', 'year', 'week', 'hours'
    ];

    public function project(): belongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function engineer(): belongsTo
    {
        return $this->belongsTo(Engineer::class);
    }
}
