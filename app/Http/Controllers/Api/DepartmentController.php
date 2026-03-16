<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::where('company_id', $request->user()->company_id)->with('manager')->get();
        return response()->json($departments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'manager_id' => 'nullable|exists:managers,id',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        $department = Department::create($validated);

        return response()->json($department->load('manager'), 201);
    }

    public function show(Request $request, Department $department)
    {
        $this->authorizeCompany($request, $department->company_id);
        return response()->json($department->load('manager', 'employees'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorizeCompany($request, $department->company_id);

        $validated = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'manager_id' => 'nullable|exists:managers,id',
        ]);

        $department->update($validated);
        return response()->json($department->load('manager'));
    }

    public function destroy(Request $request, Department $department)
    {
        $this->authorizeCompany($request, $department->company_id);
        $department->delete();
        return response()->json(['message' => 'Department deleted.']);
    }

    private function authorizeCompany(Request $request, int $companyId)
    {
        abort_if($request->user()->company_id !== $companyId, 403, 'Unauthorized');
    }
}
