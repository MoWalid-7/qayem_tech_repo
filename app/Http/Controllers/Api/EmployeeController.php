<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::where('company_id', $request->user()->company_id)
            ->with('department')
            ->get();
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:employees,email',
            'department_id' => 'nullable|exists:departments,id',
            'position'      => 'nullable|string|max:255',
            'salary'        => 'nullable|numeric|min:0',
        ]);

        $validated['company_id'] = $request->user()->company_id;

        $employee = Employee::create($validated);

        return response()->json($employee->load('department'), 201);
    }

    public function show(Request $request, Employee $employee)
    {
        $this->authorizeCompany($request, $employee->company_id);
        return response()->json($employee->load('department', 'evaluations'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeCompany($request, $employee->company_id);

        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|email|unique:employees,email,' . $employee->id,
            'department_id' => 'nullable|exists:departments,id',
            'position'      => 'nullable|string|max:255',
            'salary'        => 'nullable|numeric|min:0',
        ]);

        $employee->update($validated);
        return response()->json($employee->load('department'));
    }

    public function destroy(Request $request, Employee $employee)
    {
        $this->authorizeCompany($request, $employee->company_id);
        $employee->delete();
        return response()->json(['message' => 'Employee deleted.']);
    }

    private function authorizeCompany(Request $request, int $companyId)
    {
        abort_if($request->user()->company_id !== $companyId, 403, 'Unauthorized');
    }
}
