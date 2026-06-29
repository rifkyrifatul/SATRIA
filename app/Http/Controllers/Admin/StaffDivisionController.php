<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffDivision;
use Illuminate\Http\Request;

class StaffDivisionController extends Controller
{
    public function index()
    {
        $divisions = StaffDivision::latest()->get();
        return view('admin.staff_divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('admin.staff_divisions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:staff_divisions,name',
        ]);

        StaffDivision::create($validated);

        return redirect()->route('super_admin.staff_divisions.index')
            ->with('success', 'Divisi Staff berhasil ditambahkan.');
    }

    public function edit(StaffDivision $staffDivision)
    {
        return view('admin.staff_divisions.edit', compact('staffDivision'));
    }

    public function update(Request $request, StaffDivision $staffDivision)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:staff_divisions,name,' . $staffDivision->id,
        ]);

        $staffDivision->update($validated);

        return redirect()->route('super_admin.staff_divisions.index')
            ->with('success', 'Divisi Staff berhasil diperbarui.');
    }

    public function destroy(StaffDivision $staffDivision)
    {
        if ($staffDivision->users()->count() > 0) {
            return redirect()->route('super_admin.staff_divisions.index')
                ->with('error', 'Tidak dapat menghapus divisi yang masih memiliki staff terkait.');
        }

        $staffDivision->delete();

        return redirect()->route('super_admin.staff_divisions.index')
            ->with('success', 'Divisi Staff berhasil dihapus.');
    }
}
