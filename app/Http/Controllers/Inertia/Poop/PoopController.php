<?php

namespace App\Http\Controllers\Inertia\Poop;

use App\Models\Poop;
use App\Support\Inertia\Resources\Poop\PoopResource;
use InertiaResource\Http\Controllers\BaseResourceController;
use Illuminate\Database\Eloquent\Builder;

class PoopController extends BaseResourceController
{
    protected function getResourceClass(): string
    {
        return PoopResource::class;
    }

    protected function getModel(): string
    {
        return Poop::class;
    }

    protected function getIndexRoute(): string
    {
        return 'vue.poops.index';
    }

    // Optionally override getQuery() to eager load relationships
    // protected function getQuery(): Builder
    // {
    //     return parent::getQuery()->with(['relationship']);
    // }
}

