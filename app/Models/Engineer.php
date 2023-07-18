<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'id', 'first_name', 'last_name', 'email', 'username', 'team_id', 'user_id'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function teamLeadPlannings(): HasMany
    {
        return $this->HasMany(TLPlanning::class);
    }

    public function fullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function teamworkTime(): HasMany
    {
        return $this->HasMany(TeamworkTime::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
