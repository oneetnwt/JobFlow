<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->worker?->id; // for update

        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password'        => [$this->isMethod('POST') ? 'required' : 'nullable', 'string', 'min:8'],
            'role'            => ['required', 'string', 'in:worker,manager'],
            
            // Profile fields
            'employee_id'     => ['nullable', 'string', Rule::unique('worker_profiles')->ignore($this->worker?->profile?->id)],
            'department'      => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'string', 'in:full-time,part-time,contract,seasonal'],
            'phone_number'    => ['nullable', 'string', 'max:20'],
            'hourly_rate'     => ['required', 'numeric', 'min:0'],
            'skills'          => ['nullable', 'string'],
            'joined_at'       => ['nullable', 'date'],
        ];
    }
}
