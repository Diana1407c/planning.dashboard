<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
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

    const DAY_HOURS = 8;

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

    public function hours(): int
    {
        if ($this->isRecoverable()) {
            return $this->day_hours;
        }

        $date = $this->carbonDate();

        if ($date->isWeekend()) {
            return 0;
        }

        if ($this->isShortDay()) {
            return $this->day_hours - self::DAY_HOURS;
        }

        return -1 * self::DAY_HOURS;
    }

    public function isFreeDay(): bool
    {
        return in_array($this->type, [
            self::HOLIDAY_TYPE,
            self::DAY_OFF_TYPE,
        ]);
    }

    public function isShortDay(): bool
    {
        return $this->type == self::SHORT_TYPE;
    }

    public function isRecoverable(): bool
    {
        return $this->type == self::RECOVERABLE_TYPE;
    }

    public function carbonDate(): Carbon
    {
        $date = Carbon::parse($this->date);

        if ($this->every_year) {
            $date->setYear(Carbon::now()->year);
        }

        return $date;
    }
}
