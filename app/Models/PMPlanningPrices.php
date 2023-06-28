<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PMPlanningPrices extends Model
{
    use HasFactory;

    protected $table = 'pm_planning_prices';
    protected $fillable = [
        'project_id', 'year', 'week', 'cost'
    ];

    public function project(): belongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
