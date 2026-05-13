<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class IctMaintenance extends Model
{
    protected $table = 'tbl_ictmrf_depaide';

    protected $fillable = [
        'date_current',
        'time_current',
        'req_name',
        'req_designation',
        'req_DO',
        'DOPE',
        'brand',
        'prop_no',
        'serial_no',
        'date_last_repair',
        'defects',
        'date_inspected',
        'IPI',
        'DTS',
        'recomend',
    ];

    protected function casts(): array
    {
        return [
            'date_current' => 'date',
            'date_last_repair' => 'date',
        ];
    }

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_ictmrf_depaide')) {
            return 'tbl_ictmrf_depaide';
        }

        return 'ict_maintenance';
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
