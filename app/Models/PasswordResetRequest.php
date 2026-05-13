<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class PasswordResetRequest extends Model
{
    protected $table = 'tbl_passreset_depaide';

    protected $fillable = [
        'reason',
        'attachment',
        'request_id',
        'user_id',
        'email',
    ];

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_passreset_depaide')) {
            return 'tbl_passreset_depaide';
        }

        return 'password_reset_requests';
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
        $array['type'] = 'email_concern';

        return $array;
    }
}
