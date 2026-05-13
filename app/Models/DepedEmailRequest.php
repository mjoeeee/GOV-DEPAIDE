<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class DepedEmailRequest extends Model
{
    protected $table = 'tbl_depedemail_depaide';

    protected $fillable = [
        'school_id',
        'email_format',
        'request_id',
        'user_id',
        'office_id',
        'firstname',
        'lastname',
        'suffix',
        'position',
    ];

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_depedemail_depaide')) {
            return 'tbl_depedemail_depaide';
        }

        return 'deped_email_request';
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id', 'request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['type'] = 'deped_email';

        return $array;
    }
}
