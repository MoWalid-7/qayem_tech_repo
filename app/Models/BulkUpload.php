<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkUpload extends Model
{
    /** @use HasFactory<\Database\Factories\BulkUploadFactory> */
    use HasFactory;
    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class);
    }
    public function hr_user(){
        return $this->belongsTo(HrUser::class);
    }
}
