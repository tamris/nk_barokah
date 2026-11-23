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
        if ($request->has('base_price')) {
            $cleanPrice = preg_replace('/[^\d]/', '', (string) $request->input('base_price'));
            $request->merge(['base_price' => $cleanPrice !== '' ? (float) $cleanPrice : 0]);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'base_price'  => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['is_active'] = true;

        Package::create($validated);

        return redirect()->route('admin.bookings.index', ['tab' => 'services', 'subtab' => 'packages'])
            ->with('success', 'New wedding package created successfully.');
    }

    public function updatePackage(Request $request, $id)
    {
        $package = Package::findOrFail($id);

        if ($request->has('base_price')) {
            $cleanPrice = preg_replace('/[^\d]/', '', (string) $request->input('base_price'));
            $request->merge(['base_price' => $cleanPrice !== '' ? (float) $cleanPrice : 0]);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'base_price'  => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $package->update($validated);

        return redirect()->route('admin.bookings.index', ['tab' => 'services', 'subtab' => 'packages'])
            ->with('success', "Package \"{$package->name}\" updated successfully.");
    }

    public function togglePackage($id)
    {
        $package = Package::findOrFail($id);
        $package->update(['is_active' => !$package->is_active]);

        $status = $package->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.bookings.index', ['tab' => 'services', 'subtab' => 'packages'])
            ->with('success', "Package \"{$package->name}\" {$status} successfully.");
    }

    public function storeAddon(Request $request)
    {
        if ($request->has('unit_price')) {
            $cleanPrice = preg_replace('/[^\d]/', '', (string) $request->input('unit_price'));
            $request->merge(['unit_price' => $cleanPrice !== '' ? (float) $cleanPrice : 0]);
        }

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'unit'       => ['required', 'string', 'max:50'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['is_active'] = true;

        Addon::create($validated);

        return redirect()->route('admin.bookings.index', ['tab' => 'services', 'subtab' => 'addons'])
            ->with('success', 'New add-on item created successfully.');
    }

    public function updateAddon(Request $request, $id)
    {
        $addon = Addon::findOrFail($id);

        if ($request->has('unit_price')) {
            $cleanPrice = preg_replace('/[^\d]/', '', (string) $request->input('unit_price'));
            $request->merge(['unit_price' => $cleanPrice !== '' ? (float) $cleanPrice : 0]);
        }

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'unit'       => ['required', 'string', 'max:50'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $addon->update($validated);

        return redirect()->route('admin.bookings.index', ['tab' => 'services', 'subtab' => 'addons'])
            ->with('success', "Add-on \"{$addon->name}\" updated successfully.");
    }

    public function toggleAddon($id)
    {
        $addon = Addon::findOrFail($id);
        $addon->update(['is_active' => !$addon->is_active]);

        $status = $addon->is_active ? 'activated' : 'hidden';
        return redirect()->route('admin.bookings.index', ['tab' => 'services', 'subtab' => 'addons'])
            ->with('success', "Add-on \"{$addon->name}\" is now {$status}.");
    }
}


