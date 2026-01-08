<?php

namespace App\Http\Controllers\Inertia\User;

use App\Models\User;
use App\Support\Inertia\Resources\User\UserResource;
use InertiaResource\Http\Controllers\BaseResourceController;
use Illuminate\Database\Eloquent\Builder;

class UserController extends BaseResourceController
{
    protected function getResourceClass(): string
    {
        return UserResource::class;
    }

    protected function getModel(): string
    {
        return User::class;
    }

    protected function getIndexRoute(): string
    {
        return 'vue.users.index';
    }

    // Optionally override getQuery() to eager load relationships
    // protected function getQuery(): Builder
    // {
    //     return parent::getQuery()->with(['relationship']);
    // }
}

