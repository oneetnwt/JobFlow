<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'industry'     => ['nullable', 'string', 'max:255'],
            'size'         => ['nullable', 'string', 'max:255'],
            'website'      => ['nullable', 'string', 'max:255'],
            'terms'        => ['accepted'],
            'subdomain'    => ['required', 'string', 'max:60', 'alpha_dash'],   
            'domain'       => ['required', 'string', 'unique:domains,domain'],  
            'admin_name'   => ['required', 'string', 'max:255'],
            'admin_email'  => ['required', 'string', 'email', 'max:255'],       
            'password'     => ['required', 'confirmed', Password::defaults()],  
            'plan_id'      => ['required', 'exists:plans,id'],
            'billing_cycle'=> ['required', 'string', 'in:monthly,annual'],      
        ];
    }

    protected function prepareForValidation(): void
    {
        $centralDomain = config('tenancy.central_domains')[0] ?? 'localhost';   
        $subdomain = strtolower($this->domain ?? $this->subdomain);

        $this->merge([
            'subdomain' => $subdomain,
            'domain'    => $subdomain . '.' . $centralDomain,
        ]);
    }
}
