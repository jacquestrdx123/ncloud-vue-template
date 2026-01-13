<?php

namespace App\Console\Commands;

use App\Models\MenuGroup;
use App\Models\MenuItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateMenuItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:create-item';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new menu item interactively';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating a new menu item...');
        $this->newLine();

        // Step 1: Select or create menu group
        $menuGroup = $this->selectMenuGroup();

        // Step 2: Get key
        $key = $this->getKey($menuGroup);

        // Step 3: Get label
        $label = text('Menu item label', required: true);

        // Step 4: Get icon (optional)
        $icon = text('Icon (optional, e.g., "heroicon-o-home")', default: '');

        // Step 5: Choose between route or URL
        $useRoute = confirm('Use a route? (No = use URL instead)', default: true);

        $route = null;
        $url = null;

        if ($useRoute) {
            $route = $this->selectRoute();
        } else {
            $url = text('URL', required: true);
        }

        // Step 6: Get permission (optional)
        $permissionName = $this->selectPermission();

        // Step 7: Get parent menu item (optional)
        $parentId = $this->selectParentMenuItem($menuGroup);

        // Step 8: Get sort order
        $sortOrder = (int) text('Sort order', default: '0');

        // Step 9: Is active
        $isActive = confirm('Is active?', default: true);

        // Step 10: Confirm creation
        $this->newLine();
        $this->info('Menu item details:');
        $this->line("  Group: {$menuGroup->label}");
        $this->line("  Key: {$key}");
        $this->line("  Label: {$label}");
        if ($icon) {
            $this->line("  Icon: {$icon}");
        }
        if ($route) {
            $this->line("  Route: {$route}");
        }
        if ($url) {
            $this->line("  URL: {$url}");
        }
        if ($permissionName) {
            $this->line("  Permission: {$permissionName}");
        }
        if ($parentId) {
            $parent = MenuItem::find($parentId);
            $this->line("  Parent: {$parent->label}");
        }
        $this->line("  Sort Order: {$sortOrder}");
        $this->line('  Active: '.($isActive ? 'Yes' : 'No'));

        $this->newLine();

        if (! confirm('Create this menu item?', default: true)) {
            $this->info('Cancelled.');

            return Command::SUCCESS;
        }

        // Create the menu item
        MenuItem::create([
            'menu_group_id' => $menuGroup->id,
            'key' => $key,
            'label' => $label,
            'icon' => $icon ?: null,
            'route' => $route,
            'url' => $url,
            'permission_name' => $permissionName,
            'parent_id' => $parentId,
            'sort_order' => $sortOrder,
            'is_active' => $isActive,
        ]);

        $this->newLine();
        $this->info('Menu item created successfully!');

        return Command::SUCCESS;
    }

    /**
     * Select or create a menu group.
     */
    protected function selectMenuGroup(): MenuGroup
    {
        $groups = MenuGroup::orderBy('sort_order')->get();

        if ($groups->isEmpty()) {
            $this->warn('No menu groups found. Creating a new one...');
            $key = text('Menu group key', required: true);
            $label = text('Menu group label', required: true);
            $icon = text('Icon (optional)', default: '');
            $sortOrder = (int) text('Sort order', default: '0');

            return MenuGroup::create([
                'key' => $key,
                'label' => $label,
                'icon' => $icon ?: null,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        }

        $options = [];
        foreach ($groups as $index => $group) {
            $options[$index] = "{$group->label} ({$group->key})";
        }
        $createNewKey = 'new';
        $options[$createNewKey] = 'Create new menu group';

        $selectedKey = select(
            'Select menu group',
            $options
        );

        if ($selectedKey === $createNewKey) {
            $key = text('Menu group key', required: true);
            $label = text('Menu group label', required: true);
            $icon = text('Icon (optional)', default: '');
            $sortOrder = (int) text('Sort order', default: '0');

            return MenuGroup::create([
                'key' => $key,
                'label' => $label,
                'icon' => $icon ?: null,
                'sort_order' => $sortOrder,
                'is_active' => true,
            ]);
        }

        return $groups[$selectedKey];
    }

    /**
     * Get a unique key for the menu item.
     */
    protected function getKey(MenuGroup $menuGroup): string
    {
        while (true) {
            $key = text('Menu item key (unique within group)', required: true);

            $exists = MenuItem::where('menu_group_id', $menuGroup->id)
                ->where('key', $key)
                ->exists();

            if (! $exists) {
                return $key;
            }

            $this->error("Key '{$key}' already exists in this menu group. Please choose another.");
        }
    }

    /**
     * Select a route from the route list.
     */
    protected function selectRoute(): ?string
    {
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return $route->getName() !== null;
        })->map(function ($route) {
            $methods = implode('|', $route->methods());
            $uri = $route->uri();

            return [
                'name' => $route->getName(),
                'label' => "{$route->getName()} ({$methods} {$uri})",
            ];
        })->sortBy('name')->values();

        if ($routes->isEmpty()) {
            $this->warn('No named routes found.');

            return null;
        }

        $options = $routes->pluck('label', 'name')->toArray();

        $selected = search(
            'Search and select a route',
            function ($value) use ($options) {
                if (empty($value)) {
                    return $options;
                }

                $filtered = [];
                foreach ($options as $routeName => $label) {
                    if (stripos($routeName, $value) !== false || stripos($label, $value) !== false) {
                        $filtered[$routeName] = $label;
                    }
                }

                return $filtered;
            },
            scroll: 15
        );

        return $selected;
    }

    /**
     * Select a permission (optional).
     */
    protected function selectPermission(): ?string
    {
        if (! confirm('Add a permission requirement?', default: false)) {
            return null;
        }

        $permissions = Permission::orderBy('name')->pluck('name')->toArray();

        if (empty($permissions)) {
            $this->warn('No permissions found. You can create permissions using Spatie Permission.');

            return text('Enter permission name manually (optional)', default: '');
        }

        $options = array_combine($permissions, $permissions);
        $options[''] = 'None';
        $options['custom'] = 'Enter custom permission name';

        $selected = search(
            'Search and select a permission',
            function ($value) use ($options) {
                if (empty($value)) {
                    return $options;
                }

                $filtered = [];
                foreach ($options as $key => $label) {
                    if (stripos($key, $value) !== false || stripos($label, $value) !== false) {
                        $filtered[$key] = $label;
                    }
                }

                return $filtered;
            },
            scroll: 15
        );

        if ($selected === '' || $selected === null) {
            return null;
        }

        if ($selected === 'custom') {
            return text('Enter permission name', required: true);
        }

        return $selected;
    }

    /**
     * Select a parent menu item (optional).
     */
    protected function selectParentMenuItem(MenuGroup $menuGroup): ?int
    {
        if (! confirm('Set a parent menu item?', default: false)) {
            return null;
        }

        $items = MenuItem::where('menu_group_id', $menuGroup->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        if ($items->isEmpty()) {
            $this->warn('No parent menu items available in this group.');

            return null;
        }

        $options = [];
        foreach ($items as $index => $item) {
            $options[$index] = "{$item->label} ({$item->key})";
        }

        $selectedIndex = select('Select parent menu item', $options);

        return $items[$selectedIndex]->id;
    }
}
