<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use \App\Traits\HasMultiGuardAuth;

    /**
     * Handle Employee Check In
     */
    public function checkIn(Request $request)
    {
        $user = $this->getCurrentUser();
        
        $today = now()->toDateString();
        $attendance = \App\Models\Attendance::where('user_type', get_class($user))
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked in today.'
            ], 400);
        }

        \App\Models\Attendance::create([
            'user_type' => get_class($user),
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => now(),
            'status' => 'present' // can be enhanced to check against schedule
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully!',
            'check_in_time' => now()->format('H:i:s')
        ]);
    }

    /**
     * Handle Employee Check Out
     */
    public function checkOut(Request $request)
    {
        $user = $this->getCurrentUser();
        
        $today = now()->toDateString();
        $attendance = \App\Models\Attendance::where('user_type', get_class($user))
            ->where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'You must check in first.'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked out today.'
            ], 400);
        }

        $attendance->update([
            'check_out' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully!',
            'check_out_time' => now()->format('H:i:s')
        ]);
    }
}
