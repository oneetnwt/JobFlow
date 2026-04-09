<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
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
            'subdomain' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:tenants,subdomain'],
            'domain' => ['required', 'string', 'unique:domains,domain'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            // Legacy fields are optional to avoid breaking existing links or old clients.
            'plan_id' => ['nullable', 'exists:plans,id'],
            'billing_cycle' => ['nullable', 'string', 'in:monthly,annual'],
            'industry' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'terms' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $rawCentralDomain = (string) (config('tenancy.central_domains')[0] ?? 'localhost');
        $centralDomain = preg_replace('/:\\d+$/', '', $rawCentralDomain) ?: 'localhost';
        $subdomainInput = strtolower((string) ($this->subdomain ?? $this->domain ?? ''));

        // Allow users to paste either "acme" or "acme.localhost".
        $subdomain = Str::of($subdomainInput)
            ->when(
                Str::endsWith($subdomainInput, '.'.$centralDomain),
                fn ($value) => $value->beforeLast('.'.$centralDomain)
            )
            ->trim('.')
            ->toString();

        $this->merge([
            'subdomain' => $subdomain,
            'domain' => $subdomain.'.'.$centralDomain,
        ]);
    }
}
