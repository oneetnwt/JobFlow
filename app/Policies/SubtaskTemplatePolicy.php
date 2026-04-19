<?php

namespace App\Policies;

use App\Models\SubtaskTemplate;
use App\Models\User;

class SubtaskTemplatePolicy
{
    /**
     * Determine whether the user can view any template models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('subtasks.templates');
    }

    /**
     * Determine whether the user can view the specific template model.
     */
    public function view(User $user, SubtaskTemplate $subtaskTemplate): bool
    {
        return $user->hasPermission('subtasks.templates');
    }

    /**
     * Determine whether the user can create template models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('subtasks.templates');
    }

    /**
     * Determine whether the user can update the template model.
     */
    public function update(User $user, SubtaskTemplate $subtaskTemplate): bool
    {
        return $user->hasPermission('subtasks.templates');
    }

    /**
     * Determine whether the user can delete the template model.
     */
    public function delete(User $user, SubtaskTemplate $subtaskTemplate): bool
    {
        return $user->hasPermission('subtasks.templates');
    }
}
