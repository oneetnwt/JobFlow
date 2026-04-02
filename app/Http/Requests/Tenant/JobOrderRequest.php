<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class JobOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['required', 'string', 'in:low,medium,high,urgent'],
            'deadline_at' => ['nullable', 'date', 'after:today'],
            'status'      => ['sometimes', 'required', 'string', 'in:draft,open,assigned,in_progress,completed,cancelled'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ];
    }
}
