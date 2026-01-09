<?php

namespace App\Support\Inertia\Resources\User;

use InertiaResource\Inertia\InertiaResource;
use InertiaResource\Columns\TextColumn;
use InertiaResource\FormFields\TextField;

class UserResource extends InertiaResource
{
    protected static ?string $model = \App\Models\User::class;

    protected static ?string $title = 'User';

    protected static ?string $slug = 'users';

    protected static string $indexPage = 'Resources/User/Index';

    protected static string $createPage = 'Resources/User/Create';

    protected static string $editPage = 'Resources/User/Edit';

    protected static string $showPage = 'Resources/User/Show';

    public static function table(): array
    {
        return [
            'columns' => [
                                TextColumn::make('id', 'ID'),
                TextColumn::make('name', 'Name'),
                TextColumn::make('email', 'EMAIL'),
                // Add your columns here
            ],
            'filters' => [
                // Add your filters here
            ],
            'actions' => [
                // Add your actions here
            ],
            'bulkActions' => [
                // Add your bulk actions here
            ],
        ];
    }

    public static function form(): array
    {
        return [
            // Add your form fields here
            // TextField::make('name', 'Name')->required(),
        ];
    }
}

