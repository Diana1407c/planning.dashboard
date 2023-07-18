<?php

namespace App\Http\Requests\Planning;

interface PlanningFilterInterface
{
    public function filter(): array;
}
