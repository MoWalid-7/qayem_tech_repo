<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HrUser;
use App\Models\Manager;
use App\Traits\HasMultiGuardAuth;
use Illuminate\Http\Request;

class HrController extends Controller
{
    use HasMultiGuardAuth;

    /**
     * Only General Managers are allowed to manage HR accounts.
     */
    private function authorizeGM(): Manager|\App\Models\HrUser|null
    {
        $user = $this->getCurrentUser();

        if (!$user || $user->role !== 'general_manager') {
            abort(403, 'Unauthorized');
        }

        return $user;
    }

    /**
     * Create a new HR account.
     */
    public function store(Request $request)
    {
        $user = $this->authorizeGM();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:hr_users,email',
            'password' => 'required|min:6',
        ]);

        HrUser::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => bcrypt($request->password),
            'company_id' => $user->company_id,
            'role'       => 'hr',
        ]);

        return response()->json(['success' => true, 'message' => 'HR account created successfully!']);
    }

    /**
     * Update an existing HR account.
     */
    public function update(Request $request)
    {
        $user = $this->authorizeGM();

        $request->validate([
            'id'       => 'required|exists:hr_users,id',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:hr_users,email,' . $request->id,
            'password' => 'nullable|min:6',
        ]);

        $hr = HrUser::where('id', $request->id)
            ->where('company_id', $user->company_id)
            ->firstOrFail();

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $hr->update($data);

        return response()->json(['success' => true, 'message' => 'HR account updated successfully!']);
    }

    /**
     * Delete an HR account.
     */
    public function destroy(Request $request)
    {
        $user = $this->authorizeGM();

        $request->validate([
            'id' => 'required|exists:hr_users,id',
        ]);

        HrUser::where('id', $request->id)
            ->where('company_id', $user->company_id)
            ->firstOrFail()
            ->delete();

        return response()->json(['success' => true, 'message' => 'HR account deleted successfully!']);
    }
}
