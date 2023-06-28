<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property string $name
 */
class Stack extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function technologies(): HasMany
    {
        return $this->hasMany(Technology::class);
    }
}
