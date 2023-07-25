<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $date
 * @property string $name
 * @property string $type
 * @property int $day_hours
 * @property boolean $every_year
 */
class Holiday extends Model
{
    use CrudTrait;
    use HasFactory;

    const HOLIDAY_TYPE = 'holiday';
    const DAY_OFF_TYPE = 'day_off';
    const SHORT_TYPE = 'short';
    const RECOVERABLE_TYPE = 'recoverable';

    protected $fillable = [
        'name',
        'date',
        'type',
        'day_hours',
        'every_year',
    ];

    public static function types(): array
    {
        return [
            self::HOLIDAY_TYPE,
            self::DAY_OFF_TYPE,
            self::SHORT_TYPE,
            self::RECOVERABLE_TYPE,
        ];
    }
}
