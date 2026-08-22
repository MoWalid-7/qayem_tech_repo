<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EvaloWebController extends Controller
{
    /**
     * Home / Landing page.
     */
    public function index()
    {
        return view('evalo.index');
    }

    /**
     * About page.
     */
    public function about()
    {
        return view('evalo.about');
    }

    /**
     * Contact page.
     */
    public function contact()
    {
        return view('evalo.contact');
    }

    /**
     * Subscription / Pricing page.
     */
    public function subscription()
    {
        $plans = \App\Models\Plan::all();
        return view('evalo.subscription', compact('plans'));
    }

    /**
     * Process subscription sign-up and Stripe payment.
     */
    public function processSubscription(Request $request)
    {
        $request->validate([
            'company_name'      => 'required|string|max:255',
            'admin_name'        => 'required|string|max:255',
            'email'             => 'required|email|max:255',
            'address'           => 'required|string',
            'phone'             => 'required|string|max:20',
            'plan_id'           => 'required|exists:plans,id',
            'payment_method_id' => 'required|string',
        ]);

        $plan = \App\Models\Plan::findOrFail($request->plan_id);

        try {
            Log::info('Subscription request received', ['email' => $request->email]);
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Create Company
            $company = \App\Models\Company::create([
                'name'    => $request->company_name,
                'email'   => $request->email,
                'phone'   => $request->phone,
                'address' => $request->address,
            ]);

            // Create General Manager with a temporary password
            $tempPassword = strtolower(str_replace(' ', '', $request->admin_name)) . '123';

            \App\Models\Manager::create([
                'name'       => $request->admin_name,
                'email'      => $request->email,
                'password'   => bcrypt($tempPassword),
                'company_id' => $company->id,
                'role'       => 'general_manager',
            ]);

            // Process Stripe subscription via Cashier
            if (!$plan->stripe_price_id) {
                throw new \Exception("The selected plan has no Stripe Price ID configured.");
            }

            $company->createOrGetStripeCustomer();
            $company->addPaymentMethod($request->payment_method_id);
            $subscription = $company->newSubscription('default', $plan->stripe_price_id)
                ->create($request->payment_method_id);

            if ($subscription) {
                $subscription->update(['plan_id' => $plan->id]);
            }

            \Illuminate\Support\Facades\DB::commit();

            // Send credentials via email instead of exposing in response
            try {
                Mail::to($request->email)->send(new \App\Mail\CredentialsMail(
                    $request->admin_name,
                    $request->email,
                    $tempPassword
                ));
            } catch (\Exception $mailEx) {
                // Non-fatal: log but don't fail the subscription
                Log::warning('Failed to send credentials email: ' . $mailEx->getMessage());
            }

            return response()->json([
                'success'     => true,
                'message'     => 'Subscription successful! Your account credentials have been sent to your email.',
                'credentials' => [
                    'email'    => $request->email,
                    'password' => $tempPassword,
                ],
                'redirect'    => route('login'),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error('Subscription error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Subscription Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process contact form submission.
     */
    public function processContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $contactEmail = config('mail.contact_email', 'mo7mdw3lid@gmail.com');

            Mail::to($contactEmail)->send(new \App\Mail\ContactMail(
                $request->name,
                $request->email,
                $request->subject,
                $request->message
            ));

            return response()->json([
                'success' => true,
                'message' => __('Your message has been sent successfully! We will get back to you soon.'),
            ]);
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Sorry, an error occurred.'),
            ], 500);
        }
    }
}
