<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = Setting::create([
                'nama_usaha' => 'Nama Usaha',
                'nama_pemilik' => 'Nama Pemilik',
                'nama_aplikasi' => 'Nama Aplikasi',
                'alamat' => 'Alamat',
                'telepon' => 'Telepon',
                'email' => 'email@example.com',
                'deskripsi' => 'Deskripsi',
                'permission_front_menu' => '1'
            ]);
        }

        return view('setting.setting', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();
        $data = $request->validate([
            'nama_usaha' => 'required',
            'nama_pemilik' => 'required',
            'nama_aplikasi' => 'required',
            'deskripsi' => 'nullable',
            'alamat' => 'required',
            'telepon' => 'required',
            'email' => 'required|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'midtrans_merchant_id' => 'nullable',
            'midtrans_client_key' => 'nullable',
            'midtrans_server_key' => 'nullable',
            'midtrans_environment' => 'nullable|boolean'
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Storage::delete($setting->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Storage::delete($setting->favicon);
            }
            $data['favicon'] = $request->file('favicon')->store('favicons');
        }

        $data['permission_front_menu'] = $request->permission_front_menu;

        $setting->update($data);
        return redirect()->back()->with('success', 'Pengaturan berhasil diupdate');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
