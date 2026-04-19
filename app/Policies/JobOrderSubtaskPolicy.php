<?php

namespace App\Policies;

use App\Models\JobOrderSubtask;
use App\Models\User;

class JobOrderSubtaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('subtasks.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobOrderSubtask $jobOrderSubtask): bool
    {
        return $user->hasPermission('subtasks.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('subtasks.create');
    }

    /**
     * Determine whether the user can update the model (edit subtask title/desc).
     */
    public function update(User $user, JobOrderSubtask $jobOrderSubtask): bool
    {
        return $user->hasPermission('subtasks.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobOrderSubtask $jobOrderSubtask): bool
    {
        return $user->hasPermission('subtasks.delete');
    }

    /**
     * Determine whether the user can reorder models.
     */
    public function reorder(User $user): bool
    {
        return $user->hasPermission('subtasks.reorder');
    }

    /**
     * Determine whether the user can check/uncheck subtask items.
     */
    public function check(User $user): bool
    {
        return $user->hasPermission('subtasks.check');
    }
}
