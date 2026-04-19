<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class TenantRBACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define raw permission structures (slug => [name, group, description])
        $permissionsData = [
            // User Management
            'users.view' => ['View Users', 'Users', 'Can view the list of users'],
            'users.create' => ['Create Users', 'Users', 'Can create new users'],
            'users.edit' => ['Edit Users', 'Users', 'Can edit user details'],
            'users.delete' => ['Delete Users', 'Users', 'Can delete users'],

            // Roles/RBAC
            'roles.view' => ['View Roles', 'Roles', 'Can view role details'],
            'roles.manage' => ['Manage Roles', 'Roles', 'Can create, edit, and assign roles'],

            // Job Orders & Tasks
            'jobs.view' => ['View Jobs', 'Job Orders', 'Can view job orders'],
            'jobs.create' => ['Create Jobs', 'Job Orders', 'Can create job orders'],
            'jobs.manage' => ['Manage Jobs', 'Job Orders', 'Can fully manage job statuses and dispatching'],

            // Subtask Checklists
            'subtasks.create' => ['Create Subtasks', 'Subtasks', 'Can add subtask items to a job order'],
            'subtasks.edit' => ['Edit Subtasks', 'Subtasks', 'Can edit subtask items'],
            'subtasks.delete' => ['Delete Subtasks', 'Subtasks', 'Can delete subtask items'],
            'subtasks.reorder' => ['Reorder Subtasks', 'Subtasks', 'Can reorder subtask items'],
            'subtasks.templates' => ['Manage Subtask Templates', 'Subtasks', 'Can create, edit, or apply subtask templates'],
            'subtasks.check' => ['Check Subtasks', 'Subtasks', 'Can check/uncheck subtask items (worker action)'],
            'subtasks.view' => ['View Subtasks', 'Subtasks', 'Can view the checklist'],

            // Billing
            'billing.view' => ['View Billing', 'Billing', 'Can view subscription and billing details'],
            'billing.manage' => ['Manage Billing', 'Billing', 'Can subscribe, upgrade, or cancel plans'],

            // Payroll
            'payroll.view' => ['View Payroll', 'Payroll', 'Can view processed payroll periods'],
            'payroll.manage' => ['Manage Payroll', 'Payroll', 'Can generate and finalize payroll'],
        ];

        $permissions = [];

        foreach ($permissionsData as $slug => $info) {
            $permissions[$slug] = Permission::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $info[0],
                    'group' => $info[1],
                    'description' => $info[2],
                ]
            );
        }

        // 2. Define Roles

        $admin = Role::updateOrCreate([
            'slug' => 'admin',
        ], [
            'name' => 'Admin',
            'description' => 'Tenant Administrator. Has broad administrative rights but cannot manage RBAC natively unless granted.',
            'is_default' => false,
        ]);

        $manager = Role::updateOrCreate([
            'slug' => 'manager',
        ], [
            'name' => 'Manager',
            'description' => 'Can manage jobs, view basic stats, and manage worker assignments.',
            'is_default' => false,
        ]);

        $worker = Role::updateOrCreate([
            'slug' => 'worker',
        ], [
            'name' => 'Worker',
            'description' => 'Default system role. Can only view assigned tasks and personal data.',
            'is_default' => true,
        ]);

        // 3. Sync role permissions
        // Admin gets all permissions by default.
        $admin->permissions()->sync(
            collect($permissions)->pluck('id')->toArray()
        );

        $manager->permissions()->sync([
            $permissions['users.view']->id,
            $permissions['jobs.view']->id,
            $permissions['jobs.create']->id,
            $permissions['jobs.manage']->id,
            $permissions['payroll.view']->id,
            $permissions['subtasks.create']->id,
            $permissions['subtasks.edit']->id,
            $permissions['subtasks.delete']->id,
            $permissions['subtasks.reorder']->id,
            $permissions['subtasks.templates']->id,
            $permissions['subtasks.check']->id,
            $permissions['subtasks.view']->id,
        ]);

        $worker->permissions()->sync([
            // Workers have very restricted access natively
            $permissions['subtasks.check']->id,
            $permissions['subtasks.view']->id,
        ]);
    }
}
