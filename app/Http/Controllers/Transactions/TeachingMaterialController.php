<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\TeachingMaterialType;
use App\Repositories\Contracts\TeachingMaterialRepositoryInterface;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TeachingMaterialController extends Controller
{
    protected $repo;

    public function __construct(TeachingMaterialRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    // =========================================================
    //  TEACHER PANEL
    // =========================================================

    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'teacher') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $teacher    = Teacher::where('user_id', $user->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $types     = $this->repo->getAllTypes();
        $materials = $this->repo->getByTeacherAndYear($teacher->id, $activeYear->id);
        $subjects  = Subject::orderBy('name')->get();

        // Group by type_id for card rendering
        $materialGroups = $materials->groupBy('teaching_material_type_id');

        // Additional grouping: by grade_level (for tab filtering in JS)
        $gradeGroups = $materials->groupBy('grade_level');

        return view('transactions.teaching_material.index', compact(
            'types', 'materialGroups', 'gradeGroups', 'activeYear', 'subjects'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'teacher') {
            abort(403, 'Unauthorized');
        }

        $teacher    = Teacher::where('user_id', $user->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $request->validate([
            'teaching_material_type_id' => 'required|exists:teaching_material_types,id',
            'grade_level'               => 'required|string|in:7,8,9',
            'subject_id'                => 'nullable|exists:subjects,id',
            'file_type'                 => 'required|in:file,link',
            'description'               => 'required|string|max:255',
            'file_path'                 => [
                'required_if:file_type,file',
                'nullable',
                'file',
                'mimes:pdf',
                'max:2048',
            ],
            'link_url'                  => [
                'required_if:file_type,link',
                'nullable',
                'url',
                'regex:/drive\.google\.com/',
            ],
        ], [
            'file_path.mimes' => 'File harus berupa PDF.',
            'file_path.max'   => 'Ukuran file maksimal 2MB.',
            'link_url.regex'  => 'Link harus berasal dari Google Drive.',
        ]);

        $data = [
            'teacher_id'                => $teacher->id,
            'academic_year_id'          => $activeYear->id,
            'teaching_material_type_id' => $request->teaching_material_type_id,
            'grade_level'               => $request->grade_level,
            'subject_id'                => $request->subject_id,
            'file_type'                 => $request->file_type,
            'description'               => $request->description,
            'status'                    => 'pending',
        ];

        if ($request->file_type === 'file' && $request->hasFile('file_path')) {
            $data['file_path'] = $request->file('file_path')->store('teaching_materials', 'public');
        } elseif ($request->file_type === 'link') {
            $data['link_url'] = $request->link_url;
        }

        $this->repo->store($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Perangkat Ajar berhasil dikirim, menunggu review admin.'], 200);
        }

        return redirect()->route('transactions.teaching-materials.index')
            ->with('success', 'Perangkat Ajar berhasil dikirim.');
    }

    /**
     * Teacher revises a rejected module.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'teacher') {
            abort(403, 'Unauthorized');
        }

        $teacher  = Teacher::where('user_id', $user->id)->firstOrFail();
        $material = $this->repo->find($id);

        // Ownership & status guard
        if ($material->teacher_id !== $teacher->id) {
            abort(403, 'Bukan milik Anda.');
        }
        if ($material->status !== 'rejected') {
            return response()->json(['message' => 'Hanya modul yang ditolak yang dapat direvisi.'], 422);
        }

        $request->validate([
            'file_type'   => 'required|in:file,link',
            'description' => 'required|string|max:255',
            'grade_level' => 'required|string|in:7,8,9',
            'subject_id'  => 'nullable|exists:subjects,id',
            'file_path'   => [
                'required_if:file_type,file',
                'nullable',
                'file',
                'mimes:pdf',
                'max:2048',
            ],
            'link_url'    => [
                'required_if:file_type,link',
                'nullable',
                'url',
                'regex:/drive\.google\.com/',
            ],
        ], [
            'file_path.mimes' => 'File harus berupa PDF.',
            'file_path.max'   => 'Ukuran file maksimal 2MB.',
            'link_url.regex'  => 'Link harus berasal dari Google Drive.',
        ]);

        $data = [
            'file_type'   => $request->file_type,
            'description' => $request->description,
            'grade_level' => $request->grade_level,
            'subject_id'  => $request->subject_id,
            'status'      => 'pending',        // Reset to pending after revision
            'rejection_note' => null,          // Clear the rejection note
        ];

        if ($request->file_type === 'file' && $request->hasFile('file_path')) {
            // Delete old file if exists
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            $data['file_path'] = $request->file('file_path')->store('teaching_materials', 'public');
            $data['link_url']  = null;
        } elseif ($request->file_type === 'link') {
            $data['link_url']  = $request->link_url;
            $data['file_path'] = null;
        }

        $this->repo->update($id, $data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Revisi berhasil dikirim, menunggu review admin.'], 200);
        }

        return redirect()->route('transactions.teaching-materials.index')
            ->with('success', 'Revisi berhasil dikirim.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role !== 'teacher') {
            abort(403, 'Unauthorized');
        }

        $teacher  = Teacher::where('user_id', $user->id)->firstOrFail();
        $material = $this->repo->find($id);

        if ($material->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Bukan milik Anda.'], 403);
        }

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $this->repo->delete($id);

        return response()->json(['message' => 'Perangkat Ajar berhasil dihapus.'], 200);
    }

    // =========================================================
    //  ADMIN PANEL
    // =========================================================

    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $years    = AcademicYear::orderBy('id', 'desc')->get();
        $teachers = Teacher::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('transactions.teaching_material.admin_index', compact('years', 'teachers', 'subjects'));
    }

    /**
     * AJAX: Per-teacher summary for the "Per Guru" tab.
     */
    public function adminData(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $yearId    = $request->input('year_id');
        $teacherId = $request->input('teacher_id');

        $teachersQuery = Teacher::with(['teachingMaterials' => function ($q) use ($yearId) {
            $q->where('academic_year_id', $yearId);
        }]);

        if ($teacherId) {
            $teachersQuery->where('id', $teacherId);
        }

        $teachers = $teachersQuery->get()->map(function ($teacher) {
            $materials = $teacher->teachingMaterials;

            return [
                'id'       => $teacher->id,
                'name'     => $teacher->name,
                'nip'      => $teacher->nip,
                'approved' => $materials->where('status', 'approved')->count(),
                'pending'  => $materials->where('status', 'pending')->count(),
                'rejected' => $materials->where('status', 'rejected')->count(),
                'total'    => $materials->count(),
            ];
        });

        return response()->json(['teachers' => $teachers]);
    }

    /**
     * AJAX: Flat module list for the "Per Modul" tab.
     */
    public function adminModules(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $yearId     = $request->input('year_id');
        $teacherId  = $request->input('teacher_id');
        $gradeLevel = $request->input('grade_level');
        $subjectId  = $request->input('subject_id');
        $status     = $request->input('status');

        $modules = $this->repo->getModulesForAdmin($yearId, $teacherId, $gradeLevel, $subjectId, $status);

        return response()->json(['modules' => $modules]);
    }

    /**
     * AJAX: Legacy detail endpoint (kept for compatibility).
     */
    public function adminDetail(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $teacherId = $request->input('teacher_id');
        $yearId    = $request->input('year_id');

        $types     = $this->repo->getAllTypes();
        $materials = $this->repo->getByTeacherAndYear($teacherId, $yearId);

        return response()->json([
            'types'     => $types,
            'materials' => $materials->load('subject'),
        ]);
    }

    /**
     * Admin approves a module.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $material = $this->repo->updateStatus($id, 'approved');

        return response()->json([
            'message'  => 'Modul berhasil disetujui.',
            'material' => $material,
        ]);
    }

    /**
     * Admin rejects a module with a note.
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'rejection_note' => 'required|string|max:500',
        ], [
            'rejection_note.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $material = $this->repo->updateStatus($id, 'rejected', $request->rejection_note);

        return response()->json([
            'message'  => 'Modul berhasil ditolak.',
            'material' => $material,
        ]);
    }
}
