<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property integer $id
 * @property string $name
 * @property integer $stack_id
 */
class Technology extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['name', 'stack_id'];

    public function stack(): belongsTo
    {
        return $this->belongsTo(Stack::class);
    }

    public function teams():BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }
}
