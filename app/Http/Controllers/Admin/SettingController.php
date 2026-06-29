<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Whitelist key yang boleh diupdate — mencegah arbitrary key injection
        $allowedKeys = Setting::pluck('key')->toArray();

        $data = collect($request->except(['_token', '_method']))
            ->only($allowedKeys) // Hanya izinkan key yang sudah ada di database
            ->toArray();

        foreach ($data as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->back()->with('success', 'Konfigurasi berhasil diperbarui.');
    }
}
