<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PMPlanning extends Model
{
    use HasFactory;

    protected $table = 'project_manager_planning';
    protected $fillable = [
        'project_id', 'stack_id', 'year', 'week', 'hours'
    ];

    public function project(): belongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function technology(): belongsTo
    {
        return $this->belongsTo(Technology::class);
    }
}
