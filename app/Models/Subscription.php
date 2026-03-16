<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Cashier\Subscription as CashierSubscription;

class Subscription extends CashierSubscription
{
    /** @use HasFactory<\Database\Factories\SubscriptionFactory> */
    use HasFactory;
    protected $guarded = [];

    /**
     * Get the model related to the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
    public function hr_user()
    {
        return $this->belongsTo(HrUser::class);
    }
}
