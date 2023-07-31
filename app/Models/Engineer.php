<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'id',
        'first_name',
        'last_name',
        'email',
        'username',
        'team_id',
        'user_id',
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

    public function performance(): HasOne
    {
        return $this->hasOne(EngineerPerformance::class)->where('is_current', true);
    }

    public function performances(): HasMany
    {
        return $this->hasMany(EngineerPerformance::class);
    }

    public function displayPerformance(): string
    {
        if ($performance = $this->performancePercent()) {
            return $performance . '%';
        }

        return '-';
    }

    public function performancePercent(): int
    {
        return $this->performance ? $this->performance->performancePercent() : 0;
    }

    public function levelName(): ?string
    {
        return $this->performance ? $this->performance->levelName() : null;
    }


}
