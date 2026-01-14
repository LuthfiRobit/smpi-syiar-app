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

    public function index()
    {
        $user = Auth::user();

        // Ensure user is a teacher
        if ($user->role !== 'teacher') {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        $types = $this->repo->getAllTypes();
        $materials = $this->repo->getByTeacherAndYear($teacher->id, $activeYear->id);

        // Group materials by type for easier display in view
        $materialGroups = $materials->groupBy('teaching_material_type_id');

        $subjects = Subject::all();

        return view('transactions.teaching_material.index', compact('types', 'materialGroups', 'activeYear', 'subjects'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'teacher') {
            abort(403, 'Unauthorized');
        }
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->firstOrFail();

        // VALIDATION LOGIC
        $request->validate([
            'teaching_material_type_id' => 'required|exists:teaching_material_types,id',
            'grade_level' => 'required|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'file_type' => 'required|in:file,link',
            'description' => 'required|string', // Per user request to have description
            'file_path' => [
                'required_if:file_type,file',
                'file',
                'mimes:pdf',
                'max:2048', // 2MB
            ],
            'link_url' => [
                'required_if:file_type,link',
                'nullable',
                'url',
                'regex:/drive\.google\.com/', // Must contain drive.google.com
            ],
        ], [
            'file_path.mimes' => 'File harus berupa PDF.',
            'file_path.max' => 'Ukuran file maksimal 2MB.',
            'link_url.regex' => 'Link harus berasal dari Google Drive dan bersifat publik.',
        ]);

        $data = [
            'teacher_id' => $teacher->id,
            'academic_year_id' => $activeYear->id,
            'teaching_material_type_id' => $request->teaching_material_type_id,
            'grade_level' => $request->grade_level,
            'subject_id' => $request->subject_id,
            'file_type' => $request->file_type,
            'description' => $request->description,
            'status' => 'pending',
        ];

        // Handle File Upload
        if ($request->file_type === 'file' && $request->hasFile('file_path')) {
            $path = $request->file('file_path')->store('teaching_materials', 'public');
            $data['file_path'] = $path;
        }
        // Handle Link
        else if ($request->file_type === 'link') {
            $data['link_url'] = $request->link_url;
        }

        $this->repo->store($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Perangkat Ajar berhasil dikirim.'], 200);
        }

        return redirect()->route('transactions.teaching-materials.index')->with('success', 'Perangkat Ajar berhasil dikirim.');
    }

    public function destroy($id)
    {
        // Add destroy method later if needed
    }

    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('transactions.teaching_material.admin_index');
    }

    public function adminData(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $yearId = $request->input('year_id');
        $teacherId = $request->input('teacher_id');

        $types = $this->repo->getAllTypes();

        // Get teachers with materials count
        $teachersQuery = Teacher::query();

        if ($teacherId) {
            $teachersQuery->where('id', $teacherId);
        }

        $teachers = $teachersQuery->get()->map(function ($teacher) use ($yearId, $types) {
            $materialsCount = $this->repo->getByTeacherAndYear($teacher->id, $yearId)->count();

            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'nip' => $teacher->nip,
                'materials_count' => $materialsCount,
            ];
        });

        return response()->json([
            'teachers' => $teachers,
            'types' => $types
        ]);
    }

    public function adminDetail(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $teacherId = $request->input('teacher_id');
        $yearId = $request->input('year_id');

        $types = $this->repo->getAllTypes();
        $materials = $this->repo->getByTeacherAndYear($teacherId, $yearId);

        return response()->json([
            'types' => $types,
            'materials' => $materials->load('subject')
        ]);
    }
}
