<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\InputAspirations;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_name' => 'required|string|max:100|unique:locations,location_name',
            'location_type' => 'required|in:kelas,toilet,lab,perpustakaan,kantin,lapangan,koridor,lainnya',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Location::create($validated);

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Location $location, Request $request)
    {
        $validated = $request->validate([
            'location_name' => 'required|string|max:100|unique:locations,location_name,' . $location->id_location . ',id_location',
            'location_type' => 'required|in:kelas,toilet,lab,perpustakaan,kantin,lapangan,koridor,lainnya',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $location->update($validated);

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil diperbarui');
    }

    public function destroy(Location $location)
    {
        if (InputAspirations::where('id_location', $location->id_location)->exists()) {
            return redirect()->route('admin.locations.index')->with('error', 'Lokasi tidak bisa dihapus karena sudah digunakan pada data aspirasi');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil dihapus');
    }
}
