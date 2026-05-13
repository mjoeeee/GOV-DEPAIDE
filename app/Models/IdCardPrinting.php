<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class IdCardPrinting extends Model
{
    protected $table = 'tbl_printingid_depaide';

    protected $fillable = [
        'request_id',
        'email',
        'dep_id',
        'role',
        'job_title',
        'hr_id',
        'bday',
        'emp_id',
        'prc_no',
        'emrgncy_no',
        'emrgncy_name',
        'emrgncy_email',
        'prfx_name',
        'fname',
        'lname',
        'mname',
        'ext_name',
        'tin_no',
        'gsis_no',
        'pagibig_no',
        'philhealth_no',
        'blood_type',
        'image',
        'sign',
    ];

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_printingid_depaide')) {
            return 'tbl_printingid_depaide';
        }

        return 'id_card_printing';
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
