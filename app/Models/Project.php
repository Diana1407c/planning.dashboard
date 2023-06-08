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
 * @property string $status
 * @property StdClass $teamLeadPlannings
 * @property StdClass $projectManagerPlannings
 */

class Project extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['name', 'status'];

    public function teamLeadPlannings(): HasMany
    {
        return $this->hasMany(TLPlanning::class);
    }

    public function projectManagerPlannings(): HasMany
    {
        return $this->hasMany(PMPlanning::class);
    }
}
