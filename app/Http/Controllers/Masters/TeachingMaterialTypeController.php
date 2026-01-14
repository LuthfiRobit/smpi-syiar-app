<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\TeachingMaterialType;
use Illuminate\Http\Request;

class TeachingMaterialTypeController extends Controller
{
    public function index(Request $request)
    {
        $types = TeachingMaterialType::orderBy('name')->get();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($types);
        }

        return view('masters.teaching_material_type.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teaching_material_types,name',
            'description' => 'nullable|string',
        ]);

        TeachingMaterialType::create($request->all());

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Jenis Perangkat Ajar berhasil ditambahkan.']);
        }

        return redirect()->route('masters.teaching-material-types.index')->with('success', 'Jenis Perangkat Ajar berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teaching_material_types,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $type = TeachingMaterialType::findOrFail($id);
        $type->update($request->all());

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Jenis Perangkat Ajar berhasil diperbarui.']);
        }

        return redirect()->route('masters.teaching-material-types.index')->with('success', 'Jenis Perangkat Ajar berhasil diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $type = TeachingMaterialType::findOrFail($id);

        // Check if type is being used
        if ($type->teachingMaterials()->exists()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Tidak dapat menghapus, jenis ini masih digunakan.'], 422);
            }
            return redirect()->back()->with('error', 'Tidak dapat menghapus, jenis ini masih digunakan.');
        }

        $type->delete();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Jenis Perangkat Ajar berhasil dihapus.']);
        }

        return redirect()->route('masters.teaching-material-types.index')->with('success', 'Jenis Perangkat Ajar berhasil dihapus.');
    }
}
