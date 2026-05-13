<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $relations = ['user'];

        if (Schema::hasTable('documentation') || Schema::hasTable('tbl_document_depaide')) {
            $relations[] = 'documentation';
        }

        if (Schema::hasTable('audio_visual_editing')) {
            $relations[] = 'audioVisualEditing';
        }

        if (Schema::hasTable('id_card_printing')) {
            $relations[] = 'idCardPrinting';
        }

        if (Schema::hasTable('software_development')) {
            $relations[] = 'softwareDevelopment';
        }

        if (Schema::hasTable('ict_maintenance')) {
            $relations[] = 'ictMaintenance';
        }

        if (Schema::hasTable('ict_equipment_inspection')) {
            $relations[] = 'ictEquipmentInspection';
        }

        if (Schema::hasTable('ict_maintenance_inspection')) {
            $relations[] = 'ictMaintenanceInspection';
        }

        if (Schema::hasTable('deped_email_request') || Schema::hasTable('tbl_depedemail_depaide')) {
            $relations[] = 'depedEmailRequest';
        }

        if (Schema::hasTable('password_reset_requests') || Schema::hasTable('tbl_passreset_depaide')) {
            $relations[] = 'passwordResetRequest';
        }

        $query = ServiceRequest::with($relations)->orderBy('updated_at', 'desc');
        $currentType = null;

        if ($request->filled('request_type_table')) {
            $currentType = $request->query('request_type_table');
            // Support new unified email_management type and old separate types for backward compatibility
            if ($currentType === 'email_management') {
                $query->whereIn('request_type_table', ['email_management', 'deped_email_request', 'password_reset']);
            } elseif ($currentType === 'ict_maintenance_inspection') {
                $query->whereIn('request_type_table', ['ict_maintenance_inspection', 'ict_maintenance', 'ict_equipment_inspection']);
            } else {
                $query->where('request_type_table', $currentType);
            }
        } elseif ($request->filled('type')) {
            $requestedType = $request->query('type');
            $matchedKey = array_search($requestedType, ServiceRequest::TYPE_MAP, true);

            if ($matchedKey !== false) {
                $currentType = $matchedKey;
                if ($matchedKey === 'email_management') {
                    $query->whereIn('request_type_table', ['email_management', 'deped_email_request', 'password_reset']);
                } elseif ($matchedKey === 'ict_maintenance_inspection') {
                    $query->whereIn('request_type_table', ['ict_maintenance_inspection', 'ict_maintenance', 'ict_equipment_inspection']);
                } else {
                    $query->where('request_type_table', $matchedKey);
                }
            }
        }

        if ($request->filled('status')) {
            $query->where('stat', $request->query('status'));
        }

        $requests = $query->get()
            ->map(fn ($req) => [
                'request_id' => $req->request_id,
                'user_name' => $req->user?->fullname ?? 'Unknown User',
                'request_type_table' => $req->request_type_table,
                'event_title' => $req->event_title,
                'location_event' => $req->event_location,
                'event_date_time' => $req->event_date_time,
                'details' => $req->event_details,
                'stat' => $req->stat,
                'remarks' => $req->remarks,
                'typeData' => match ($req->request_type_table) {
                    'documentation' => [
                        'title' => $req->documentation?->title,
                        'event_location' => $req->documentation?->event_location,
                        'event_date' => $req->documentation?->event_date,
                        'start_time' => $req->documentation?->start_time,
                        'end_time' => $req->documentation?->end_time,
                        'description' => $req->documentation?->description,
                        'details' => $req->documentation?->details,
                        'photo_link' => $req->documentation?->photo_link,
                    ],
                    'audio_visual_editing' => [
                        'title' => $req->audioVisualEditing?->title,
                        'project_type' => $req->audioVisualEditing?->project_type,
                        'delivery_method' => $req->audioVisualEditing?->delivery_method,
                        'project_deadline' => $req->audioVisualEditing?->project_deadline,
                        'proj_desc' => $req->audioVisualEditing?->proj_desc,
                        'deliverables' => $req->audioVisualEditing?->deliverables,
                    ],
                    'id_card_printing' => [
                        'email' => $req->idCardPrinting?->email,
                        'dep_id' => $req->idCardPrinting?->dep_id,
                        'role' => $req->idCardPrinting?->role,
                        'job_title' => $req->idCardPrinting?->job_title,
                        'fullname' => trim(($req->idCardPrinting?->fname ?? '').' '.($req->idCardPrinting?->mname ?? '').' '.($req->idCardPrinting?->lname ?? '')),
                    ],
                    'software_development' => [
                        'proj_name' => $req->softwareDevelopment?->proj_name,
                        'brief_desc' => $req->softwareDevelopment?->brief_desc,
                        'proj_deadline' => $req->softwareDevelopment?->proj_deadline?->format('Y-m-d H:i:s') ?? null,
                        'features' => $req->softwareDevelopment?->features,
                        'add_info' => $req->softwareDevelopment?->add_info,
                    ],
                    'ict_maintenance' => [
                        'req_name' => $req->ictMaintenance?->req_name,
                        'req_designation' => $req->ictMaintenance?->req_designation,
                        'req_DO' => $req->ictMaintenance?->req_DO,
                        'date_current' => $req->ictMaintenance?->date_current?->format('Y-m-d') ?? null,
                        'time_current' => $req->ictMaintenance?->time_current,
                        'brand' => $req->ictMaintenance?->brand,
                        'prop_no' => $req->ictMaintenance?->prop_no,
                        'serial_no' => $req->ictMaintenance?->serial_no,
                        'defects' => $req->ictMaintenance?->defects,
                    ],
                    'ict_equipment_inspection' => [
                        'item' => $req->ictEquipmentInspection?->item,
                        'property_no' => $req->ictEquipmentInspection?->property_no,
                        'acquisition_date' => $req->ictEquipmentInspection?->acquisition_date?->format('Y-m-d') ?? null,
                        'complaints' => $req->ictEquipmentInspection?->complaints,
                        'scope_last_repair' => $req->ictEquipmentInspection?->scope_last_repair,
                    ],
                    'ict_maintenance_inspection' => $req->ictMaintenanceInspection ? ($req->ictMaintenanceInspection->type === 'maintenance' ? [
                        'type' => 'maintenance',
                        'req_name' => $req->ictMaintenanceInspection?->req_name,
                        'req_designation' => $req->ictMaintenanceInspection?->req_designation,
                        'req_DO' => $req->ictMaintenanceInspection?->req_DO,
                        'date_current' => $req->ictMaintenanceInspection?->date_current?->format('Y-m-d') ?? null,
                        'time_current' => $req->ictMaintenanceInspection?->time_current,
                        'brand' => $req->ictMaintenanceInspection?->brand,
                        'prop_no' => $req->ictMaintenanceInspection?->prop_no,
                        'serial_no' => $req->ictMaintenanceInspection?->serial_no,
                        'defects' => $req->ictMaintenanceInspection?->defects,
                    ] : [
                        'type' => 'inspection',
                        'item' => $req->ictMaintenanceInspection?->item,
                        'property_no' => $req->ictMaintenanceInspection?->property_no,
                        'acquisition_date' => $req->ictMaintenanceInspection?->acquisition_date?->format('Y-m-d') ?? null,
                        'complaints' => $req->ictMaintenanceInspection?->complaints,
                        'scope_last_repair' => $req->ictMaintenanceInspection?->scope_last_repair,
                    ]) : null,
                    'email_management' => [
                        'email_format' => $req->depedEmailRequest?->email_format ?? $req->passwordResetRequest?->reason,
                        'school_id' => $req->depedEmailRequest?->school_id,
                        'office_id' => $req->depedEmailRequest?->office_id ?? $req->passwordResetRequest?->email,
                        'firstname' => $req->depedEmailRequest?->firstname,
                        'lastname' => $req->depedEmailRequest?->lastname,
                        'position' => $req->depedEmailRequest?->position,
                        'reason' => $req->passwordResetRequest?->reason,
                        'attachment' => $req->passwordResetRequest?->attachment,
                        'attachment_url' => $req->passwordResetRequest?->attachment ? Storage::url($req->passwordResetRequest->attachment) : null,
                        'email' => $req->passwordResetRequest?->email,
                        'type' => $req->request_type_table === 'deped_email_request' || $req->depedEmailRequest ? 'deped_email' : 'email_concern',
                    ],
                    'deped_email_request' => [
                        'email_format' => $req->depedEmailRequest?->email_format,
                        'school_id' => $req->depedEmailRequest?->school_id,
                        'office_id' => $req->depedEmailRequest?->office_id,
                        'firstname' => $req->depedEmailRequest?->firstname,
                        'lastname' => $req->depedEmailRequest?->lastname,
                        'position' => $req->depedEmailRequest?->position,
                        'type' => 'deped_email',
                    ],
                    'password_reset' => [
                        'reason' => $req->passwordResetRequest?->reason,
                        'attachment' => $req->passwordResetRequest?->attachment,
                        'attachment_url' => $req->passwordResetRequest?->attachment ? Storage::url($req->passwordResetRequest->attachment) : null,
                        'email' => $req->passwordResetRequest?->email,
                        'type' => 'email_concern',
                    ],
                    default => null,
                },
            ]);

        return Inertia::render('Admin/Requests', [
            'requests' => $requests,
            'typeMap' => ServiceRequest::TYPE_MAP,
            'currentType' => $currentType,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'stat' => 'required|in:Pending,In Progress,Completed,Rejected',
            'remarks' => 'nullable|string',
        ]);

        $serviceRequest = ServiceRequest::where('request_id', $id)->firstOrFail();
        $serviceRequest->update($validated);

        return redirect()->back()->with('success', 'Request updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $serviceRequest = ServiceRequest::where('request_id', $id)->firstOrFail();
        $serviceRequest->delete();

        return redirect()->back()->with('success', 'Request deleted successfully.');
    }
}
