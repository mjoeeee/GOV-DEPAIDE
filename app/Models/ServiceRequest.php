<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;

class ServiceRequest extends Model
{
    protected $table = 'requests';

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'user_id',
        'userId',
        'request_type_table',
        'request_type_id',
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
        'software_development' => 'Software Development',
        'ict_maintenance_inspection' => 'ICT Maintenance & Inspection',
        'documentation' => 'Documentation',
        'audio_visual_editing' => 'Audio Visual Editing',
        'email_management' => 'Email Management',
        'deped_email_request' => 'DepEd Email Request',
        'password_reset' => 'Email Concern',
        'id_card_printing' => 'ID Card Printing',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->resolveUserColumn(), 'userId');
    }

    public function getMappedTypeAttribute(): string
    {
        return self::TYPE_MAP[$this->request_type_table] ?? $this->request_type_table;
    }

    public function documentation(): HasOne
    {
        if (Schema::hasTable('tbl_document_depaide')) {
            return $this->hasOne(Documentation::class, 'id', 'request_type_id');
        }

        return $this->hasOne(Documentation::class, 'request_id', 'request_id');
    }

    public function audioVisualEditing(): HasOne
    {
        if (Schema::hasTable('tbl_audiovisual_depaide')) {
            return $this->hasOne(AudioVisualEditing::class, 'id', 'request_type_id');
        }

        return $this->hasOne(AudioVisualEditing::class, 'request_id', 'request_id');
    }

    public function idCardPrinting(): HasOne
    {
        if (Schema::hasTable('tbl_printingid_depaide')) {
            return $this->hasOne(IdCardPrinting::class, 'id', 'request_type_id');
        }

        return $this->hasOne(IdCardPrinting::class, 'request_id', 'request_id');
    }

    public function softwareDevelopment(): HasOne
    {
        if (Schema::hasTable('tbl_softdev_depaide')) {
            return $this->hasOne(SoftwareDevelopment::class, 'id', 'request_type_id');
        }

        return $this->hasOne(SoftwareDevelopment::class, 'request_id', 'request_id');
    }

    public function ictMaintenance(): HasOne
    {
        if (Schema::hasTable('tbl_ictmrf_depaide')) {
            return $this->hasOne(IctMaintenance::class, 'id', 'request_type_id');
        }

        return $this->hasOne(IctMaintenance::class, 'request_id', 'request_id');
    }

    public function ictEquipmentInspection(): HasOne
    {
        if (Schema::hasTable('tbl_inspection_depaide')) {
            return $this->hasOne(IctEquipmentInspection::class, 'id', 'request_type_id');
        }

        return $this->hasOne(IctEquipmentInspection::class, 'request_id', 'request_id');
    }

    public function ictMaintenanceInspection(): HasOne
    {
        return $this->hasOne(IctMaintenanceInspection::class, 'request_id', 'request_id');
    }

    public function depedEmailRequest(): HasOne
    {
        if (Schema::hasTable('tbl_depedemail_depaide')) {
            return $this->hasOne(DepedEmailRequest::class, 'id', 'request_type_id');
        }

        return $this->hasOne(DepedEmailRequest::class, 'request_id', 'request_id');
    }

    public function passwordResetRequest(): HasOne
    {
        if (Schema::hasTable('tbl_passreset_depaide')) {
            return $this->hasOne(PasswordResetRequest::class, 'id', 'request_type_id');
        }

        return $this->hasOne(PasswordResetRequest::class, 'request_id', 'request_id');
    }

    public function getEmailManagementData(): array
    {
        if (in_array($this->request_type_table, ['deped_email_request', 'email_management'])) {
            $email = $this->depedEmailRequest;
            if ($email) {
                return [
                    'type' => 'deped_email',
                    ...$email->toArray(),
                ];
            }
        }

        if (in_array($this->request_type_table, ['password_reset', 'email_management'])) {
            $email = $this->passwordResetRequest;
            if ($email) {
                return [
                    'type' => 'email_concern',
                    ...$email->toArray(),
                ];
            }
        }

        return [];
    }

    public function getEventTitleAttribute(): string
    {
        return match ($this->request_type_table) {
            'documentation' => $this->documentation?->title,
            'audio_visual_editing' => $this->audioVisualEditing?->title,
            'id_card_printing' => $this->idCardPrinting?->email ?? $this->idCardPrinting?->dep_id ?? $this->idCardPrinting?->role,
            'software_development' => $this->softwareDevelopment?->proj_name,
            'ict_maintenance' => $this->ictMaintenance?->req_name,
            'ict_equipment_inspection' => $this->ictEquipmentInspection?->item,
            'ict_maintenance_inspection' => $this->ictMaintenanceInspection?->type === 'maintenance' ? $this->ictMaintenanceInspection?->req_name : $this->ictMaintenanceInspection?->item,
            'deped_email_request' => $this->depedEmailRequest?->email_format,
            'password_reset' => $this->passwordResetRequest?->reason,
            default => null,
        } ?? $this->mapped_type;
    }

    public function getLocationEventAttribute(): string
    {
        return match ($this->request_type_table) {
            'documentation' => $this->documentation?->event_location ?? $this->documentation?->description,
            'audio_visual_editing' => $this->audioVisualEditing?->delivery_method ?? $this->audioVisualEditing?->project_type,
            'id_card_printing' => $this->idCardPrinting?->job_title ?? $this->idCardPrinting?->role,
            'software_development' => $this->softwareDevelopment?->brief_desc,
            'ict_maintenance' => $this->ictMaintenance?->req_DO,
            'ict_equipment_inspection' => $this->ictEquipmentInspection?->property_no,
            'ict_maintenance_inspection' => $this->ictMaintenanceInspection?->type === 'maintenance' ? $this->ictMaintenanceInspection?->req_DO : $this->ictMaintenanceInspection?->property_no,
            'deped_email_request' => $this->depedEmailRequest?->school_id ?? $this->depedEmailRequest?->office_id,
            'password_reset' => $this->passwordResetRequest?->attachment ?? '',
            default => '',
        } ?? '';
    }

    public function getEventDateTimeAttribute(): string
    {
        return match ($this->request_type_table) {
            'documentation' => $this->formatDocumentationDateTime(),
            'software_development' => $this->formatDateTimeValue($this->softwareDevelopment?->proj_deadline),
            'ict_maintenance' => $this->formatDateTimeValue($this->ictMaintenance?->date_current, $this->ictMaintenance?->time_current),
            'ict_equipment_inspection' => $this->formatDateTimeValue($this->ictEquipmentInspection?->acquisition_date),
            'ict_maintenance_inspection' => $this->ictMaintenanceInspection?->type === 'maintenance' ? $this->formatDateTimeValue($this->ictMaintenanceInspection?->date_current, $this->ictMaintenanceInspection?->time_current) : $this->formatDateTimeValue($this->ictMaintenanceInspection?->acquisition_date),
            default => $this->created_at?->format('m/d/Y • g:i A'),
        } ?? $this->created_at?->format('m/d/Y • g:i A') ?? '';
    }

    public function getEventDetailsAttribute(): string
    {
        return match ($this->request_type_table) {
            'documentation' => $this->documentation?->details,
            'audio_visual_editing' => $this->audioVisualEditing?->proj_desc ?? $this->audioVisualEditing?->details,
            'id_card_printing' => $this->idCardPrinting?->email ?? $this->idCardPrinting?->details,
            'software_development' => $this->softwareDevelopment?->add_info ?? $this->softwareDevelopment?->features,
            'ict_maintenance' => $this->ictMaintenance?->defects,
            'ict_equipment_inspection' => $this->ictEquipmentInspection?->complaints,
            'ict_maintenance_inspection' => $this->ictMaintenanceInspection?->type === 'maintenance' ? $this->ictMaintenanceInspection?->defects : $this->ictMaintenanceInspection?->complaints,
            'deped_email_request' => $this->depedEmailRequest?->email_format,
            'password_reset' => $this->passwordResetRequest?->reason,
            default => $this->remarks,
        } ?? ($this->remarks ?? '');
    }

    private function formatDocumentationDateTime(): string
    {
        $documentation = $this->documentation;

        if (! $documentation) {
            return $this->created_at?->format('m/d/Y • g:i A') ?? '';
        }

        $date = $documentation->event_date;
        $time = $documentation->start_time;
        $endTime = $documentation->end_time;

        if (! $date) {
            return $this->created_at?->format('m/d/Y • g:i A') ?? '';
        }

        return trim($date.($time ? ' • '.$time : '').($endTime ? ' - '.$endTime : ''));
    }

    private function formatDateTimeValue(?string $date, ?string $time = null): string
    {
        if (! $date) {
            return $this->created_at?->format('m/d/Y • g:i A') ?? '';
        }

        $formatted = Carbon::parse($date)->format('m/d/Y');

        if ($time) {
            return $formatted.' • '.$time;
        }

        return $formatted;
    }

    public function getTable(): string
    {
        if (Schema::hasTable('tbl_request_depaide')) {
            return 'tbl_request_depaide';
        }

        return 'requests';
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where($this->resolveUserColumn(), $userId);
    }

    public function setUserIdAttribute(int $value): void
    {
        $this->attributes[$this->resolveUserColumn()] = $value;
    }

    private function resolveUserColumn(): string
    {
        if (Schema::hasColumn($this->getTable(), 'userId')) {
            return 'userId';
        }

        return 'user_id';
    }
}
