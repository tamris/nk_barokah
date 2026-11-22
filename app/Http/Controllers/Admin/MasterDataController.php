<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MasterDataController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.bookings.index', ['tab' => 'services']);
    }

    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'base_price'  => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_active'] = true;

        Package::create($validated);

        return back()->with('success', 'Paket layanan baru berhasil ditambahkan.');
    }

    public function updatePackage(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'base_price'  => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $package->update($validated);

        return back()->with('success', "Paket {$package->name} berhasil diperbarui.");
    }

    public function togglePackage($id)
    {
        $package = Package::findOrFail($id);
        $package->update(['is_active' => !$package->is_active]);

        $status = $package->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Paket {$package->name} berhasil {$status}.");
    }

    public function storeAddon(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'unit'       => ['required', 'string', 'max:50'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['is_active'] = true;

        Addon::create($validated);

        return back()->with('success', 'Fasilitas Add-on baru berhasil ditambahkan.');
    }

    public function updateAddon(Request $request, $id)
    {
        $addon = Addon::findOrFail($id);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'unit'       => ['required', 'string', 'max:50'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $addon->update($validated);

        return back()->with('success', "Add-on {$addon->name} berhasil diperbarui.");
    }

    public function toggleAddon($id)
    {
        $addon = Addon::findOrFail($id);
        $addon->update(['is_active' => !$addon->is_active]);

        $status = $addon->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Add-on {$addon->name} berhasil {$status}.");
    }
}

