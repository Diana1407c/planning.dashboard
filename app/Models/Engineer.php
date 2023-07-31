<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $username
 * @property integer $team_id
 * @property integer $user_id
 * @property Team $team
 * @property User $user
 */
class Engineer extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'id', 'first_name', 'last_name', 'email', 'username', 'performance', 'team_id', 'user_id'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function plannedHours(): MorphMany
    {
        return $this->morphMany(PlannedHour::class, 'planable');
    }

    public function fullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function teamworkTime(): HasMany
    {
        return $this->HasMany(TeamworkTime::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class)
            ->withPivot('from')
            ->orderBy('from', 'desc');
    }

    public function displayPerformance(): string
    {
        if ($performance = $this->performancePercent()) {
            return $performance . '%';
        }

        return '-';
    }

    public function currentLevel()
    {
        return $this->levels()->first();
    }

    public function performancePercent(): int
    {
        if ($this->performance) {
            return $this->performance;
        }

        $currentLevel = $this->currentLevel();

        return $currentLevel ? $currentLevel->performance : 0;
    }


}
