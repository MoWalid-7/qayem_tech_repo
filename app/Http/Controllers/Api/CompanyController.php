<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function show(Request $request)
    {
        $company = $request->user()->company()->with(['managers', 'departments', 'employees', 'subscriptions.plan'])->first();

        return response()->json($company);
    }

    public function update(Request $request)
    {
        $company = $request->user()->company;

        $validated = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'email'   => 'sometimes|email|max:255',
            'phone'   => 'sometimes|string|max:50',
            'address' => 'sometimes|string',
        ]);

        $company->update($validated);

        return response()->json($company);
    }
}
