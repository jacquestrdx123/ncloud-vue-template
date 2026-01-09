<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use InertiaResource\Inertia\InertiaResource;
use JacquesTredoux\VueSidebarMenu\Models\MenuGroup;
use JacquesTredoux\VueSidebarMenu\Models\MenuItem;
use ReflectionClass;

class MenuResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating menu groups and items from InertiaResource classes...');

        $resourcesPath = app_path('Support/Inertia/Resources');
        $resources = $this->scanResources($resourcesPath);

        if (empty($resources)) {
            $this->command->warn('No InertiaResource classes found.');

            return;
        }

        $this->command->info('Found '.count($resources).' InertiaResource class(es).');

        // Group resources by their first folder level
        $groupedResources = $this->groupResourcesByFolder($resources);

        $groupSortOrder = 0;
        $itemSortOrder = 0;

        foreach ($groupedResources as $groupName => $groupResources) {
            $groupSortOrder += 10;

            // Create or get MenuGroup
            $menuGroup = MenuGroup::firstOrCreate(
                ['key' => strtolower($groupName)],
                [
                    'label' => $this->formatLabel($groupName),
                    'icon' => null,
                    'sort_order' => $groupSortOrder,
                    'is_active' => true,
                ]
            );

            $this->command->info("Created/Updated MenuGroup: {$menuGroup->label}");

            // Create MenuItems for each resource in this group
            foreach ($groupResources as $resourceClass) {
                $itemSortOrder += 10;

                try {
                    $reflection = new ReflectionClass($resourceClass);
                    $title = $reflection->getStaticPropertyValue('title');
                    $slug = $reflection->getStaticPropertyValue('slug');

                    if (! $title || ! $slug) {
                        $this->command->warn("Skipping {$resourceClass}: missing title or slug");

                        continue;
                    }

                    // Generate route name: {slug}.index
                    $routeName = "{$slug}.index";

                    // Create MenuItem
                    MenuItem::firstOrCreate(
                        [
                            'menu_group_id' => $menuGroup->id,
                            'key' => $slug,
                        ],
                        [
                            'label' => $title,
                            'icon' => null,
                            'route' => $routeName,
                            'url' => null,
                            'permission_name' => null,
                            'sort_order' => $itemSortOrder,
                            'is_active' => true,
                            'parent_id' => null,
                        ]
                    );

                    $this->command->line("  ✓ Created MenuItem: {$title} (route: {$routeName})");
                } catch (\Exception $e) {
                    $this->command->error("Error processing {$resourceClass}: {$e->getMessage()}");

                    continue;
                }
            }
        }

        $this->command->info('Menu groups and items created successfully!');
    }

    /**
     * Scan for all InertiaResource classes in the given path.
     */
    protected function scanResources(string $path): array
    {
        $resources = [];
        $files = File::allFiles($path);

        foreach ($files as $file) {
            $filePath = $file->getPathname();
            $content = File::get($filePath);

            // Skip if it's not a Resource class
            if (! preg_match('/extends\s+InertiaResource/', $content)) {
                continue;
            }

            // Extract namespace and class name
            if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch) &&
                preg_match('/class\s+(\w+)/', $content, $classMatch)) {
                $namespace = $namespaceMatch[1];
                $className = $classMatch[1];
                $fullClass = "{$namespace}\\{$className}";

                // Verify it's an InertiaResource class
                if (class_exists($fullClass) && is_subclass_of($fullClass, InertiaResource::class)) {
                    // Skip MenuItem and MenuGroup resources to avoid circular references
                    if (str_contains($fullClass, 'MenuItem') || str_contains($fullClass, 'MenuGroup')) {
                        continue;
                    }

                    $resources[] = $fullClass;
                }
            }
        }

        sort($resources);

        return $resources;
    }

    /**
     * Group resources by their first folder level.
     */
    protected function groupResourcesByFolder(array $resources): array
    {
        $grouped = [];

        foreach ($resources as $resourceClass) {
            // Extract folder structure from namespace
            // App\Support\Inertia\Resources\Customer\Customer\CustomerResource
            // -> Customer
            $parts = explode('\\', $resourceClass);

            // Find the index of 'Resources' in the namespace
            $resourcesIndex = array_search('Resources', $parts);

            if ($resourcesIndex === false || ! isset($parts[$resourcesIndex + 1])) {
                // Fallback: use the class name
                $groupName = class_basename($resourceClass);
                $groupName = str_replace('Resource', '', $groupName);
            } else {
                // Get the first folder after Resources
                $groupName = $parts[$resourcesIndex + 1];
            }

            if (! isset($grouped[$groupName])) {
                $grouped[$groupName] = [];
            }

            $grouped[$groupName][] = $resourceClass;
        }

        return $grouped;
    }

    /**
     * Format a label from a folder/class name.
     */
    protected function formatLabel(string $name): string
    {
        // Convert PascalCase or snake_case to Title Case
        $name = preg_replace('/([A-Z])/', ' $1', $name);
        $name = str_replace('_', ' ', $name);
        $name = trim($name);

        return ucwords($name);
    }
}
