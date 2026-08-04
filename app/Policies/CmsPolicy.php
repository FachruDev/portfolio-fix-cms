<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CmsPolicy
{
    /**
     * @param  array<int, mixed>  $arguments
     */
    public function before(User $user, string $ability, mixed ...$arguments): ?bool
    {
        $model = $arguments[0] ?? null;

        if ($model === null) {
            return false;
        }

        return $user->can($this->permissionName($ability, $model));
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Model $model): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Model $model): bool
    {
        return false;
    }

    public function delete(User $user, Model $model): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Model $model): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    private function permissionName(string $ability, Model|string $model): string
    {
        return $this->permissionAffix($ability).' '.class_basename($model);
    }

    private function permissionAffix(string $ability): string
    {
        return match ($ability) {
            'viewAny' => 'view-any',
            'deleteAny' => 'delete-any',
            'forceDelete' => 'force-delete',
            'forceDeleteAny' => 'force-delete-any',
            'restoreAny' => 'restore-any',
            default => str($ability)->kebab()->toString(),
        };
    }
}
