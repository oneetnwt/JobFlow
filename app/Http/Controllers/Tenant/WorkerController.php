<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\WorkerRequest;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class WorkerController extends Controller
{
    /**
     * Display a listing of workers.
     */
    public function index(): View
    {
        $workers = User::workers()
            ->with('profile')
            ->latest()
            ->paginate(15);

        return view('tenant.workers.index', compact('workers'));
    }

    /**
     * Show the form for creating a new worker.
     */
    public function create(): View
    {
        $roles = Role::all();

        return view('tenant.workers.create', compact('roles'));
    }

    /**
     * Store a newly created worker in storage.
     */
    public function store(WorkerRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign the requested role
            $user->assignRole($request->role ?? 'worker');

            $user->profile()->create($request->only([
                'employee_id',
                'department',
                'skills',
                'employment_type',
                'phone_number',
                'joined_at',
                'hourly_rate',
            ]));

            Employee::create([
                'employee_code' => $request->employee_id ?: ('EMP-' . $user->id),
                'full_name' => $request->name,
                'position' => $request->department,
                'employment_type' => $request->employment_type,
                'daily_rate' => $request->daily_rate,
                'hourly_rate' => $request->hourly_rate,
                'status' => 'active',
            ]);

            // Send verification email to the new worker
            $user->sendEmailVerificationNotification();
        });

        return redirect()->route('tenant.workers.index')
            ->with('success', 'Worker created successfully.');
    }

    /**
     * Display the specified worker.
     */
    public function show(User $worker): View
    {
        $worker->load('profile');

        return view('tenant.workers.show', compact('worker'));
    }

    /**
     * Show the form for editing the specified worker.
     */
    public function edit(User $worker): View
    {
        $worker->load('profile');
        $roles = Role::all();

        return view('tenant.workers.edit', compact('worker', 'roles'));
    }

    /**
     * Update the specified worker in storage.
     */
    public function update(WorkerRequest $request, User $worker): RedirectResponse
    {
        DB::transaction(function () use ($request, $worker) {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $worker->update($userData);

            // Re-assign role
            if ($request->filled('role')) {
                $worker->roles()->detach();
                $worker->assignRole($request->role);
            }

            $worker->profile()->updateOrCreate(
                ['user_id' => $worker->id],
                $request->only([
                    'employee_id',
                    'department',
                    'skills',
                    'employment_type',
                    'phone_number',
                    'joined_at',
                    'hourly_rate',
                ])
            );

            $employeeCode = $request->employee_id ?: ('EMP-' . $worker->id);
            Employee::updateOrCreate(
                ['employee_code' => $employeeCode],
                [
                    'full_name' => $request->name,
                    'position' => $request->department,
                    'employment_type' => $request->employment_type,
                    'daily_rate' => $request->daily_rate,
                    'hourly_rate' => $request->hourly_rate,
                    'status' => 'active',
                ]
            );
        });

        return redirect()->route('tenant.workers.index')
            ->with('success', 'Worker updated successfully.');
    }

    /**
     * Remove the specified worker from storage.
     */
    public function destroy(User $worker): RedirectResponse
    {
        $worker->delete();

        return redirect()->route('tenant.workers.index')
            ->with('success', 'Worker deleted successfully.');
    }
}
