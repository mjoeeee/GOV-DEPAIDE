<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $table = 'requests';

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'user_id',
        'request_type_table',
        'stat',
        'remarks',
        'rated',
    ];

    protected function casts(): array
    {
        return [
            'rated' => 'boolean',
        ];
    }

    public const TYPE_MAP = [
        'ict_maintenance' => 'ICT Maintenance',
        'software_development' => 'Software Development',
        'ict_equipment_inspection' => 'ICT Equipment Inspection',
        'documentation' => 'Documentation',
        'audio_visual_editing' => 'Audio Visual Editing',
        'deped_email_request' => 'DepEd Email Request',
        'password_reset' => 'Email Concern',
        'id_card_printing' => 'ID Card Printing',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMappedTypeAttribute(): string
    {
        return self::TYPE_MAP[$this->request_type_table] ?? $this->request_type_table;
    }
}
