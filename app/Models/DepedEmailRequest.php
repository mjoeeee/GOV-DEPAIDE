<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepedEmailRequest extends Model
{
    protected $table = 'deped_email_request';

    protected $fillable = [
        'request_id',
        'user_id',
        'office_id',
        'firstname',
        'lastname',
        'suffix',
        'position',
        'email_format',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
