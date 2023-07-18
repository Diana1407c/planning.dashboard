<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $performance
 * @property integer $engineer_id
 * @property Engineer $level_id
 */
class Level extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['name','performance'];

    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }
}
