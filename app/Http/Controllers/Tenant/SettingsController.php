<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $tenant = tenant();

        return view('tenant.settings.edit', compact('tenant'));
    }

    public function update(Request $request)
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $tenant = tenant();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'admin_name' => 'nullable|string|max:255',
            'admin_email' => 'nullable|email|max:255',
            'brand_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|max:2048', // 2MB max
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists from public directory
            if ($tenant->logo_url && file_exists(public_path($tenant->logo_url))) {
                @unlink(public_path($tenant->logo_url));
            }

            // Store logo physically in central public folder
            $file = $request->file('logo');
            $filename = uniqid('logo_').'.'.$file->getClientOriginalExtension();
            $destinationPath = public_path('tenant-logos/'.$tenant->id);
            $file->move($destinationPath, $filename);

            $tenant->logo_url = '/tenant-logos/'.$tenant->id.'/'.$filename;
        }

        $tenant->company_name = $validated['company_name'];
        if (isset($validated['admin_name'])) {
            $tenant->admin_name = $validated['admin_name'];
        }
        if (isset($validated['admin_email'])) {
            $tenant->admin_email = $validated['admin_email'];
        }
        if (isset($validated['brand_color'])) {
            $tenant->brand_color = $validated['brand_color'];
        }

        $tenant->save();

        return redirect()->route('tenant.settings.edit')->with('success', 'Tenant customization updated successfully.');
    }
}
