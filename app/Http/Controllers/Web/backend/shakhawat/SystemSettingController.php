<?php

namespace App\Http\Controllers\Web\Backend\Shakhawat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemSettingController extends Controller
{
    /**
     * Show system settings edit form.
     */
    public function edit()
    {
        $settings = SystemSetting::getCached(); // load cached settings
        return view('backend.layouts.system_setting.edit', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request)
    {
        try {
            // Validate inputs
            $validated = $request->validate([
                'system_logo'    => 'nullable|image|mimes:jpeg,jpg,png,svg|max:2048',
                'system_favicon' => 'nullable|image|mimes:png,ico,jpeg,jpg|max:1024',
                'system_title'   => 'nullable|string|max:150',
                'system_short_title' => 'nullable|string|max:100',
                'company_name'   => 'nullable|string|max:100',
                'company_address' => 'nullable|string|max:255',
                'tagline'        => 'nullable|string|max:255',
                'phone'          => 'nullable|string|max:15',
                'email'          => 'nullable|email|max:50',
                'timezone'       => 'nullable|string|max:50',
                'language'       => 'nullable|string|max:50',
                'copyright_text' => 'nullable|string|max:1000',
            ]);

            $settings = SystemSetting::getCached();
            $data = collect($validated)->except(['system_logo', 'system_favicon'])->toArray();

            // Handle file uploads
            $fileFields = [
                'system_logo'    => 'logo',
                'system_favicon' => 'favicon',
            ];

            foreach ($fileFields as $field => $subfolder) {
                if ($request->hasFile($field)) {
                    $folderPath = public_path("uploads/systems/{$subfolder}");
                    if (!file_exists($folderPath)) mkdir($folderPath, 0755, true);

                    // Delete old file if exists
                    if ($settings->$field && file_exists(public_path($settings->$field))) {
                        unlink(public_path($settings->$field));
                    }

                    $file = $request->file($field);
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move($folderPath, $filename);

                    $data[$field] = "uploads/systems/{$subfolder}/{$filename}";
                }
            }

            // Update or create settings
            if ($settings->exists) {
                $settings->update($data);
            } else {
                SystemSetting::create($data);
            }

            // Clear cache
            Cache::forget('system_settings');

            return back()->with('success', 'Settings updated successfully!');
        } catch (\Exception $e) {
            Log::error('Settings update failed: ' . $e->getMessage());
            return back()->with('error', 'Update failed. Please try again.');
        }
    }
}
