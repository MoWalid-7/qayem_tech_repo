<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class HrUser extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\HrUserFactory> */
    use HasFactory, HasApiTokens;
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function bulk_uploads()
    {
        return $this->hasMany(BulkUpload::class);
    }
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }
}
