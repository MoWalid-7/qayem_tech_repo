<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $subscriptions = Subscription::where('company_id', $request->user()->company_id)
            ->with('plan')
            ->get();
        return response()->json($subscriptions);
    }

    public function show(Request $request, Subscription $subscription)
    {
        abort_if($subscription->company_id !== $request->user()->company_id, 403, 'Unauthorized');
        return response()->json($subscription->load('plan'));
    }
}
