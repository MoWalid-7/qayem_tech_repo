<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Employee;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $evaluations = Evaluation::whereHas('employee', function ($q) use ($request) {
            $q->where('company_id', $request->user()->company_id);
        })->with('employee')->get();

        return response()->json($evaluations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'score'       => 'nullable|numeric|min:0|max:100',
            'notes'       => 'nullable|string',
        ]);

        // Ensure employee belongs to same company
        $employee = Employee::findOrFail($validated['employee_id']);
        abort_if($employee->company_id !== $request->user()->company_id, 403, 'Unauthorized');

        $validated['company_id'] = $request->user()->company_id;

        $evaluation = Evaluation::create($validated);

        return response()->json($evaluation->load('employee'), 201);
    }

    public function show(Request $request, Evaluation $evaluation)
    {
        abort_if($evaluation->employee->company_id !== $request->user()->company_id, 403, 'Unauthorized');
        return response()->json($evaluation->load('employee'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        abort_if($evaluation->employee->company_id !== $request->user()->company_id, 403, 'Unauthorized');

        $validated = $request->validate([
            'score' => 'sometimes|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $evaluation->update($validated);
        return response()->json($evaluation->load('employee'));
    }

    public function destroy(Request $request, Evaluation $evaluation)
    {
        abort_if($evaluation->employee->company_id !== $request->user()->company_id, 403, 'Unauthorized');
        $evaluation->delete();
        return response()->json(['message' => 'Evaluation deleted.']);
    }
}
