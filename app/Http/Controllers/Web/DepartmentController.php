<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Manager;
use App\Traits\HasMultiGuardAuth;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    use HasMultiGuardAuth;

    /**
     * Create a new department. DMs cannot create departments.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'manager_id' => 'nullable|exists:managers,id',
        ]);

        $user = $this->getCurrentUser();

        if ($user instanceof Manager && $user->isDM()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - Managers cannot create departments'], 403);
        }

        Department::create([
            'name'       => $request->name,
            'company_id' => $user->company_id,
            'manager_id' => $request->manager_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Department created successfully!']);
    }

    /**
     * Update an existing department.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'         => 'required|exists:departments,id',
            'name'       => 'required|string|max:255',
            'manager_id' => 'nullable|exists:managers,id',
        ]);

        $user = $this->getCurrentUser();

        $dept = Department::where('id', $request->id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $dept->update([
            'name'       => $request->name,
            'manager_id' => $request->manager_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Department updated successfully!']);
    }
}
