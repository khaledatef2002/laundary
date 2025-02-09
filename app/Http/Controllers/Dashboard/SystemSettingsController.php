<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\AutoEncoder;

class SystemSettingsController extends Controller implements HasMiddleware
{
    public static function Middleware()
    {
        return [
            new Middleware('can:system_settings_show', only: ['edit']),
            new Middleware('can:system_settings_edit', only: ['update']),
        ];
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemSetting $system_setting)
    {
        return view('dashboard.system_settings.edit', compact('system_setting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemSetting $system_setting)
    {
        $rules = [
            'title' => ['required', 'min:2', 'max:255'],
        ];
        $request->file('logo') ? ($rules['logo'] = ['image', 'mimes:jpeg,png,jpg|max:10240']) : '';

        $data = $request->validate($rules);

        if($request->file('logo'))
        {
            if($system_setting->logo && Storage::disk('public')->exists($system_setting->logo))
            {
                Storage::disk('public')->delete($system_setting->logo);
            }

            $image = $request->file('logo');

            $imagePath = 'logo/' . uniqid() . '.' . $image->getClientOriginalExtension();

            $manager = new ImageManager(new GdDriver());
            $optimizedImage = $manager->read($image)
                ->scale(height:250)
                ->encode(new AutoEncoder(quality: 75));

            Storage::disk('public')->put($imagePath, (string) $optimizedImage);

            $data['logo'] = 'storage/' . $imagePath;
        }

        $system_setting->update($data);
    }
}
