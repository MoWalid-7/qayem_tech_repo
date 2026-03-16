<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function index(Request $request)
    {
        $managers = Manager::where('company_id', $request->user()->company_id)->get();
        return response()->json($managers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:managers,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:general_manager,department_manager',
        ]);

        $validated['password']   = Hash::make($validated['password']);
        $validated['company_id'] = $request->user()->company_id;

        $manager = Manager::create($validated);

        return response()->json($manager, 201);
    }

    public function show(Request $request, Manager $manager)
    {
        $this->authorizeCompany($request, $manager->company_id);
        return response()->json($manager);
    }

    public function update(Request $request, Manager $manager)
    {
        $this->authorizeCompany($request, $manager->company_id);

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:managers,email,' . $manager->id,
            'password' => 'sometimes|string|min:6',
            'role'     => 'sometimes|in:general_manager,department_manager',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $manager->update($validated);
        return response()->json($manager);
    }

    public function destroy(Request $request, Manager $manager)
    {
        $this->authorizeCompany($request, $manager->company_id);
        $manager->delete();
        return response()->json(['message' => 'Manager deleted.']);
    }

    private function authorizeCompany(Request $request, int $companyId)
    {
        abort_if($request->user()->company_id !== $companyId, 403, 'Unauthorized');
    }
}
