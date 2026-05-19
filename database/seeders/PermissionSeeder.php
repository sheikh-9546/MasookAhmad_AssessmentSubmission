<?php

namespace Database\Seeders;

use App\Concerns\Contenable;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    use Contenable;

    private array $actions = ['create', 'update', 'view', 'show', 'delete'];

    private array $alterActions = [];

    private array $models = [];

    private array $extraEntityActions = [

    ];

    private array $filterSubjects = [
        'Permission',
        'PermissionRole',
        'Role',
        'RoleUser',
    ];

    /**
     * @throws BindingResolutionException
     */
    private function upsertAbility($subject, $actions): Collection
    {
        return collect($actions)
            ->each(function ($action) use ($subject) {
                $ability = [
                    'name'         => "Can {$action} ".Str::lower($subject),
                    'slug'         => Str::slug($action),
                    'content_type' => $subject,
                ];
                app()->make(Permission::class)->upsert($ability, $ability);
            });
    }

    protected function loadModels(): static
    {
        $this->models = $this->getModels()
            ->map(fn ($subject) => Str::replace('\\App\\Models\\', '', $subject))
            ->reject(fn ($name): bool => in_array($name, $this->filterSubjects))
            ->toArray();

        return $this;
    }

    protected function actions(): static
    {
        collect($this->models)
            ->each(function ($subject) {
                $this->alterActions[$subject] = array_key_exists($subject, $this->extraEntityActions)
                    ? array_merge($this->actions, $this->extraEntityActions[$subject])
                    : $this->actions;
            });

        return $this;
    }

    public function permissionSet(): static
    {
        collect($this->alterActions)
            ->map(fn ($actions, $subject) => $this->upsertAbility($subject, $actions));

        return $this;
    }

    public function run()
    {
        $this->loadModels()->actions()->permissionSet();

    }
}
