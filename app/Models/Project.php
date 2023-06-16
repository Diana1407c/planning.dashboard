<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

/**
 * @property integer $id
 * @property string $name
 * @property string $state
 * @property StdClass $teamLeadPlannings
 * @property StdClass $projectManagerPlannings
 */

class Project extends Model
{
    const STATE_ACTIVE = 'active';
    const STATE_MAINTENANCE = 'maintenance';
    const STATE_OPERATIONAL = 'operational';

    use CrudTrait;
    use HasFactory;

    protected $fillable = ['id', 'name', 'state'];

    public function teamLeadPlannings(): HasMany
    {
        return $this->hasMany(TLPlanning::class);
    }

    public function projectManagerPlannings(): HasMany
    {
        return $this->hasMany(PMPlanning::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(PMPlanningPrices::class);
    }
}
