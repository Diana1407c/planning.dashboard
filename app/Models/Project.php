<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $state
 * @property boolean $no_performance
 */

class Project extends Model
{
    const STATE_ACTIVE = 'active';
    const STATE_MAINTENANCE = 'maintenance';
    const STATE_OPERATIONAL = 'operational';

    use CrudTrait;
    use HasFactory;

    protected $fillable = ['id', 'name', 'state', 'no_performance'];

    public function isNoPerformance(): bool
    {
        return $this->no_performance;
    }

    public static function states(): array
    {
        return [
            self::STATE_ACTIVE,
            self::STATE_MAINTENANCE,
            self::STATE_OPERATIONAL,
        ];
    }
}
