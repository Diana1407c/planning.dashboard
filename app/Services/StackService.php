<?php

namespace App\Services;

use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StackService
{
    public static function filter(Request $request): Collection|array
    {
        $query = Stack::query();

        if($stack_ids = $request->get('stack_ids')){
            $query->whereIn('id', $stack_ids);
        }

        return $query->get();
    }
}
