<?php

namespace App\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class SchoolScope implements Scope
{
    public function apply(Builder $query, Model $model): void
    {
        if ($this->isBypassed()) {
            return;
        }

        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->school_id) {
            return;
        }

        if ($user->isAdmin()) {
            return;
        }

        $query->where($model->getTable().'.school_id', $user->school_id);
    }

    protected function isBypassed(): bool
    {
        return (bool) app('school.scope.bypass');
    }

    public static function bypass(): void
    {
        app()->instance('school.scope.bypass', true);
    }

    public static function restore(): void
    {
        app()->instance('school.scope.bypass', false);
    }
}
