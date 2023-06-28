<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

/**
 * @property integer $id
 * @property string $name
 * @property integer $team_lead_id
 * @property integer $technology_id
 * @property StdClass $members
 * @property Engineer $teamLead
 * @property Technology $technology
 */
class Team extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['name', 'technology_id', 'team_lead_id'];

    public function members(): HasMany
    {
        return $this->hasMany(Engineer::class);
    }

    public function teamLead(): belongsTo
    {
        return $this->belongsTo(Engineer::class, 'team_lead_id', 'id');
    }

    public function technology(): belongsTo
    {
        return $this->belongsTo(Technology::class);
    }

    public function membersCount(): int
    {
        return $this->members()->count();
    }
}
