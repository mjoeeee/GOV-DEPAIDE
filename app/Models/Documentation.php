<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

class Documentation extends Model
{
    protected $table = 'tbl_document_depaide';

    protected $fillable = [
        'request_id',
        'title',
        'description',
        'details',
        'event_location',
        'event_date',
        'start_time',
        'end_time',
        'photo_link',
    ];

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_document_depaide')) {
            return 'tbl_document_depaide';
        }

        return 'documentation';
    }

    public function serviceRequest(): HasOne|BelongsTo
    {
        if (Schema::hasTable('tbl_document_depaide')) {
            return $this->hasOne(ServiceRequest::class, 'request_type_id', 'id');
        }

        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }
}
