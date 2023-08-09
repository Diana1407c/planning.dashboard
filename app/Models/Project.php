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
    const STATE_CLOSED = 'closed';

    const TYPE_BILLABLE = 'billable';
    const TYPE_NON_BILLABLE = 'non_billable';
    const TYPE_HOLIDAY = 'holiday';

    use CrudTrait;
    use HasFactory;

    protected $fillable = ['id', 'name', 'state', 'no_performance', 'type'];

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

    public static function indexedStates(): array
    {
        return [
            [
                'name' => ucwords(self::STATE_ACTIVE),
                'id' => self::STATE_ACTIVE
            ],
            [
                'name' => ucwords(self::STATE_MAINTENANCE),
                'id' => self::STATE_MAINTENANCE
            ],
            [
                'name' => ucwords(self::STATE_OPERATIONAL),
                'id' => self::STATE_OPERATIONAL
            ],
            [
                'name' => ucwords(self::STATE_CLOSED),
                'id' => self::STATE_CLOSED
            ]
        ];
    }
}
