<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';
import { appPath } from '@/lib/basePath';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{
    requests: Array<{
        request_id: number;
        user_name: string;
        request_type_table: string;
        event_title: string;
        location_event: string;
        event_date_time: string;
        details: string | null;
        stat: string;
        remarks: string | null;
        maintenance?: {
            req_name: string | null;
            req_designation: string | null;
            req_DO: string | null;
            date_current: string | null;
            time_current: string | null;
            brand: string | null;
            prop_no: string | null;
            serial_no: string | null;
            defects: string | null;
        } | null;
        typeData?: Record<string, string | null> | null;
    }>;
    typeMap: Record<string, string>;
    currentType: string | null;
}>();

const searchQuery = ref('');
const pageSize = ref(10);
const currentPage = ref(1);
const currentType = computed(() => props.currentType);

const pageTitle = computed(() => {
    if (!currentType.value) {
        return 'All Requests';
    }

    return `${props.typeMap[currentType.value] ?? currentType.value} Requests`;
});

const isIctMaintenance = computed(() => currentType.value === 'ict_maintenance');

const isDocumentation = computed(() => currentType.value === 'documentation');
const isAudioVisualEditing = computed(() => currentType.value === 'audio_visual_editing');
const isIdCardPrinting = computed(() => currentType.value === 'id_card_printing');
const isSoftwareDevelopment = computed(() => currentType.value === 'software_development');
const isIctEquipmentInspection = computed(() => currentType.value === 'ict_equipment_inspection');
const isIctMaintenanceInspection = computed(() => currentType.value === 'ict_maintenance_inspection');
const isDepedEmailRequest = computed(() => currentType.value === 'deped_email_request');
const isPasswordReset = computed(() => currentType.value === 'password_reset');
const isEmailManagement = computed(() => currentType.value === 'email_management');
const emailManagementTypes = ['email_management', 'deped_email_request', 'password_reset'];

function isEmailManagementType(type: string): boolean {
    return emailManagementTypes.includes(type);
}

const statusClass: Record<string, string> = {
    'Pending': 'status-text-pending', 'In Progress': 'status-text-in-progress',
    'Completed': 'status-text-completed', 'Rejected': 'status-text-rejected',
};

const expandedCells = ref<Record<string, boolean>>({});
const textPreviewLimit = 100;

function toggleCell(requestId: number, field: string): void {
    const key = `${requestId}-${field}`;
    expandedCells.value[key] = !expandedCells.value[key];
}

function isCellExpanded(requestId: number, field: string): boolean {
    return !!expandedCells.value[`${requestId}-${field}`];
}

function shortText(value: string | null): string {
    if (!value) {
        return '';
    }

    return value.length > textPreviewLimit ? `${value.slice(0, textPreviewLimit).trim()}` : value;
}

const viewRoutes: Record<string, string> = {
    'ict_maintenance': '/admin/view/ict-maintenance',
    'ict_equipment_inspection': '/admin/view/ict-inspection',
    'email_management': '/admin/view/email-management',
    'email_concern': '/admin/view/email-concern',
    'deped_email_request': '/admin/view/deped-email-request',
    'password_reset': '/admin/view/password-reset',
    'software_development': '/admin/view/software-request',
    'documentation': '/admin/view/documentation',
    'audio_visual_editing': '/admin/view/audio-visual-editing',
    'id_card_printing': '/admin/view/id-card-printing',
};

function getViewUrl(type: string, id: number): string | null {
    const base = viewRoutes[type];
    return base ? appPath(`${base}/${id}`) : null;
}

const filteredRequests = computed(() => {
    let data = [...props.requests];
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(r => {
            const typeDataText = Object.values(r.typeData ?? {}).filter(Boolean).join(' ').toLowerCase();
            return r.user_name.toLowerCase().includes(q) || r.event_title.toLowerCase().includes(q) ||
                r.location_event.toLowerCase().includes(q) || String(r.request_id).includes(q) ||
                r.stat.toLowerCase().includes(q) || r.event_date_time.toLowerCase().includes(q) ||
                (r.details ?? '').toLowerCase().includes(q) || (r.remarks ?? '').toLowerCase().includes(q) ||
                typeDataText.includes(q);
        });
    }
    return data;
});

const totalPages = computed(() => Math.ceil(filteredRequests.value.length / pageSize.value));
const paginatedRequests = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    return filteredRequests.value.slice(start, start + pageSize.value);
});

const columnCount = computed(() => {
    if (!currentType.value) {
        return 8;
    }

    if (isIctMaintenance.value) {
        return 8;
    }

    if (isDocumentation.value || isAudioVisualEditing.value || isIdCardPrinting.value || isIctEquipmentInspection.value || isDepedEmailRequest.value) {
        return isDocumentation.value ? 10 : 9;
    }

    if (isSoftwareDevelopment.value) {
        return 8;
    }

    if (isIctMaintenanceInspection.value) {
        return 10;
    }

    if (isEmailManagement.value) {
        return 11;
    }

    if (isPasswordReset.value) {
        return 7;
    }

    return 8;
});

async function updateStatus(requestId: number, currentStat: string, currentRemarks: string | null) {
    const { value: formValues } = await Swal.fire({
        title: 'Update Request Status',
        html: `
            <div style="text-align:left;font-size:14px;">
                <label style="display:block;margin-bottom:4px;font-weight:600;">Status</label>
                <select id="swal-status" class="swal2-input" style="width:100%;">
                    <option value="Pending" ${currentStat === 'Pending' ? 'selected' : ''}>Pending</option>
                    <option value="In Progress" ${currentStat === 'In Progress' ? 'selected' : ''}>In Progress</option>
                    <option value="Completed" ${currentStat === 'Completed' ? 'selected' : ''}>Completed</option>
                    <option value="Rejected" ${currentStat === 'Rejected' ? 'selected' : ''}>Rejected</option>
                </select>
                <label style="display:block;margin:12px 0 4px;font-weight:600;">Remarks</label>
                <textarea id="swal-remarks" class="swal2-textarea" style="width:100%;">${currentRemarks || ''}</textarea>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Update',
        confirmButtonColor: '#007bff',
        preConfirm: () => ({
            stat: (document.getElementById('swal-status') as HTMLSelectElement).value,
            remarks: (document.getElementById('swal-remarks') as HTMLTextAreaElement).value,
        }),
    });

    if (formValues) {
        router.patch(appPath(`/admin/requests/${requestId}`), formValues, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({ icon: 'success', title: 'Updated!', timer: 1500, showConfirmButton: false });
            },
        });
    }
}

async function deleteRequest(requestId: number) {
    const result = await Swal.fire({
        title: 'Delete this request?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancel',
    });

    if (result.isConfirmed) {
        router.delete(appPath(`/admin/requests/${requestId}`), {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500, showConfirmButton: false });
            },
        });
    }
}
</script>

<template>
    <Head :title="`Admin - ${pageTitle}`" />

    <div class="page-header"><h1>{{ pageTitle }}</h1></div>

    <div class="data-table-wrapper">
        <div class="data-table-controls">
            <div class="d-flex align-items-center gap-2">
                <label>Show</label>
                <select v-model="pageSize" class="page-size-select" @change="currentPage = 1">
                    <option :value="10">10</option><option :value="25">25</option><option :value="50">50</option>
                </select>
                <label>entries</label>
            </div>
            <input v-model="searchQuery" type="text" class="data-table-search" placeholder="Search..." @input="currentPage = 1" />
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th v-if="!isIctMaintenance && !isIctMaintenanceInspection">ID</th>
                    <th v-if="!isIctMaintenance && !isIctMaintenanceInspection">User</th>

                    <template v-if="isDocumentation">
                        <th>Title</th>
                        <th>Location</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Details</th>
                        <th>Photos</th>
                    </template>

                    <template v-else-if="isAudioVisualEditing">
                        <th>Title</th>
                        <th>Project Type</th>
                        <th>Delivery Method</th>
                        <th>Deadline</th>
                        <th>Description</th>
                    </template>

                    <template v-else-if="isIdCardPrinting">
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Job Title</th>
                        <th>Name</th>
                    </template>

                    <template v-else-if="isSoftwareDevelopment">
                        <th>Project Name</th>
                        <th>Deadline</th>
                        <th>Brief Description</th>
                        <th>Features</th>
                    </template>

                    <template v-else-if="isIctMaintenance">
                        <th>Requester</th>
                        <th>Position</th>
                        <th>Office / Division</th>
                        <th>Date & Time</th>
                        <th>Defects</th>
                    </template>

                    <template v-else-if="isIctEquipmentInspection">
                        <th>Item</th>
                        <th>Property No</th>
                        <th>Acquisition Date</th>
                        <th>Complaints</th>
                        <th>Last Repair Scope</th>
                    </template>

                    <template v-else-if="isIctMaintenanceInspection">
                        <th>Control No./ID</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Division Office</th>
                        <th>Item/Description</th>
                        <th>Property Number</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </template>

                    <template v-else-if="isEmailManagement">
                        <th>Request Type</th>
                        <th>Email Format</th>
                        <th>School ID</th>
                        <th>Position</th>
                        <th>Reason</th>
                        <th>Attachment</th>
                        <th>Remarks</th>
                    </template>

                    <template v-else-if="isDepedEmailRequest">
                        <th>Email Format</th>
                        <th>School ID</th>
                    </template>

                    <template v-else-if="isPasswordReset">
                        <th>Email</th>
                        <th>Reason</th>
                        <th>Attachment</th>
                    </template>

                    <template v-else>
                        <th>Event Title</th>
                        <th>Location Event</th>
                        <th>Event Date/Time</th>
                        <th>Details</th>
                    </template>

                    <th v-if="!isIctMaintenanceInspection">Status</th>
                    <th v-if="isIctMaintenance">Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="req in paginatedRequests" :key="req.request_id">
                    <td v-if="!isIctMaintenance && !isIctMaintenanceInspection">{{ req.request_id }}</td>
                    <td v-if="!isIctMaintenance && !isIctMaintenanceInspection">{{ req.user_name }}</td>

                    <template v-if="isDocumentation">
                        <td>{{ req.typeData?.title || 'N/A' }}</td>
                        <td>{{ req.typeData?.event_location || 'N/A' }}</td>
                        <td>{{ req.typeData?.event_date || 'N/A' }}</td>
                        <td>{{ req.typeData?.start_time || 'N/A' }}</td>
                        <td>{{ req.typeData?.end_time || 'N/A' }}</td>
                        <td>{{ req.typeData?.details || req.typeData?.description || 'N/A' }}</td>
                        <td>
                            <a v-if="req.typeData?.photo_link" :href="req.typeData.photo_link" target="_blank" rel="noopener noreferrer">
                                View Link
                            </a>
                            <span v-else>N/A</span>
                        </td>
                    </template>

                    <template v-else-if="isAudioVisualEditing">
                        <td>{{ req.typeData?.title || 'N/A' }}</td>
                        <td>{{ req.typeData?.project_type || 'N/A' }}</td>
                        <td>{{ req.typeData?.delivery_method || 'N/A' }}</td>
                        <td>{{ req.typeData?.project_deadline || 'N/A' }}</td>
                        <td>{{ req.typeData?.proj_desc || 'N/A' }}</td>
                    </template>

                    <template v-else-if="isIdCardPrinting">
                        <td>{{ req.typeData?.email || 'N/A' }}</td>
                        <td>{{ req.typeData?.dep_id || 'N/A' }}</td>
                        <td>{{ req.typeData?.role || 'N/A' }}</td>
                        <td>{{ req.typeData?.job_title || 'N/A' }}</td>
                        <td>{{ req.typeData?.fullname || 'N/A' }}</td>
                    </template>

                    <template v-else-if="isSoftwareDevelopment">
                        <td>{{ req.typeData?.proj_name || 'N/A' }}</td>
                        <td>{{ req.typeData?.proj_deadline || 'N/A' }}</td>
                        <td>{{ req.typeData?.brief_desc || 'N/A' }}</td>
                        <td>{{ req.typeData?.features || 'N/A' }}</td>
                    </template>

                    <template v-else-if="isIctMaintenance">
                        <td>{{ req.typeData?.req_name || 'N/A' }}</td>
                        <td>{{ req.typeData?.req_designation || 'N/A' }}</td>
                        <td>{{ req.typeData?.req_DO || 'N/A' }}</td>
                        <td>{{ (req.typeData?.date_current || '') + (req.typeData?.time_current ? ' • ' + req.typeData?.time_current : '') || 'N/A' }}</td>
                        <td>{{ req.typeData?.defects || 'N/A' }}</td>
                    </template>

                    <template v-else-if="isIctEquipmentInspection">
                        <td>{{ req.typeData?.item || 'N/A' }}</td>
                        <td>{{ req.typeData?.property_no || 'N/A' }}</td>
                        <td>{{ req.typeData?.acquisition_date || 'N/A' }}</td>
                        <td>{{ req.typeData?.complaints || 'N/A' }}</td>
                        <td>{{ req.typeData?.scope_last_repair || 'N/A' }}</td>
                    </template>

                    <template v-else-if="isIctMaintenanceInspection">
                        <td>{{ req.request_id }}</td>
                        <td>{{ req.request_type_table === 'ict_maintenance' ? 'Maintenance' : req.request_type_table === 'ict_equipment_inspection' ? 'Inspection' : req.typeData?.type === 'maintenance' ? 'Maintenance' : 'Inspection' }}</td>
                        <td>{{ req.typeData?.req_name || 'N/A' }}</td>
                        <td>{{ req.typeData?.req_designation || 'N/A' }}</td>
                        <td>{{ req.typeData?.req_DO || 'N/A' }}</td>
                        <td>{{ req.typeData?.defects || req.typeData?.item || req.typeData?.complaints || 'N/A' }}</td>
                        <td>{{ req.typeData?.prop_no || req.typeData?.property_no || 'N/A' }}</td>
                        <td><span :class="statusClass[req.stat]">{{ req.stat }}</span></td>
                        <td>{{ req.remarks || 'N/A' }}</td>
                    </template>

                    <template v-else-if="isEmailManagement">
                        <td>{{ req.typeData?.type === 'deped_email' ? 'DepEd Email Request' : 'Email Concern' }}</td>
                        <td>{{ req.typeData?.email_format || 'N/A' }}</td>
                        <td>{{ req.typeData?.school_id || 'N/A' }}</td>
                        <td>{{ req.typeData?.position || 'N/A' }}</td>
                        <td>
                            <span class="short-text-reason" v-show="!isCellExpanded(req.request_id, 'reason')">
                                {{ shortText(req.typeData?.reason) || 'N/A' }}
                            </span>
                            <span class="full-text-reason" v-show="isCellExpanded(req.request_id, 'reason')">
                                {{ req.typeData?.reason || 'N/A' }}
                            </span>
                            <a
                                v-if="req.typeData?.reason && req.typeData.reason.length > textPreviewLimit"
                                href="#"
                                class="reason-toggle"
                                @click.prevent="toggleCell(req.request_id, 'reason')"
                            >
                                {{ isCellExpanded(req.request_id, 'reason') ? '↖' : '...' }}
                            </a>
                        </td>
                        <td>
                            <div v-if="req.typeData?.attachment_url" class="attachment-cell">
                                <a :href="req.typeData.attachment_url" target="_blank" rel="noopener noreferrer">
                                    <img :src="req.typeData.attachment_url" alt="Attachment" class="attachment-preview" />
                                </a>
                            </div>
                            <div v-else>{{ req.typeData?.attachment || 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="short-text" v-show="!isCellExpanded(req.request_id, 'remarks')">
                                {{ shortText(req.remarks) || 'N/A' }}
                            </span>
                            <span class="full-text" v-show="isCellExpanded(req.request_id, 'remarks')">
                                {{ req.remarks || 'N/A' }}
                            </span>
                            <a
                                v-if="req.remarks && req.remarks.length > textPreviewLimit"
                                href="#"
                                class="reason-toggle"
                                @click.prevent="toggleCell(req.request_id, 'remarks')"
                            >
                                {{ isCellExpanded(req.request_id, 'remarks') ? '↖' : '...' }}
                            </a>
                        </td>
                    </template>

                    <template v-else-if="isDepedEmailRequest">
                        <td>{{ req.typeData?.email_format || 'N/A' }}</td>
                        <td>{{ req.typeData?.school_id || 'N/A' }}</td>
                    </template>

                    <template v-else-if="isPasswordReset">
                        <td>{{ req.typeData?.email || 'N/A' }}</td>
                        <td>{{ req.typeData?.reason || 'N/A' }}</td>
                        <td>
                            <div v-if="req.typeData?.attachment_url" class="attachment-cell">
                                <a :href="req.typeData.attachment_url" target="_blank" rel="noopener noreferrer">
                                    <img :src="req.typeData.attachment_url" alt="Attachment" class="attachment-preview" />
                                </a>
                            </div>
                            <div v-else>{{ req.typeData?.attachment || 'N/A' }}</div>
                        </td>
                    </template>

                    <template v-else>
                        <td>{{ req.event_title }}</td>
                        <td>{{ req.location_event || 'N/A' }}</td>
                        <td>{{ req.event_date_time }}</td>
                        <td>{{ req.details || 'N/A' }}</td>
                    </template>

                    <td v-if="!isIctMaintenanceInspection"><span :class="statusClass[req.stat]">{{ req.stat }}</span></td>
                    <td v-if="isIctMaintenance">{{ req.remarks || 'N/A' }}</td>
                    <td class="action-cell">
                        <a
                            v-if="getViewUrl(req.request_type_table, req.request_id) && !isEmailManagementType(req.request_type_table)"
                            :href="getViewUrl(req.request_type_table, req.request_id)!"
                            class="btn btn-secondary btn-icon btn-sm"
                            title="View details"
                        >
                            <i class="fas fa-eye"></i>
                        </a>
                        <button
                            class="btn btn-primary btn-sm btn-icon-only"
                            @click="updateStatus(req.request_id, req.stat, req.remarks)"
                            aria-label="Update"
                        >
                            <i class="fas fa-edit"></i>
                            <span class="button-text">Update</span>
                        </button>
                        <button
                            class="btn btn-danger btn-sm btn-icon-only"
                            @click="deleteRequest(req.request_id)"
                            aria-label="Delete"
                        >
                            <i class="fas fa-trash"></i>
                            <span class="button-text">Delete</span>
                        </button>
                    </td>
                </tr>
                <tr v-if="paginatedRequests.length === 0">
                    <td :colspan="columnCount" class="text-center text-muted" style="padding:24px;">No requests found.</td>
                </tr>
            </tbody>
        </table>

        <div class="data-table-controls mt-3">
            <div class="text-muted" style="font-size:12px;">
                Showing {{ ((currentPage - 1) * pageSize) + 1 }} to {{ Math.min(currentPage * pageSize, filteredRequests.length) }} of {{ filteredRequests.length }}
            </div>
            <div class="data-table-pagination">
                <button :disabled="currentPage <= 1" @click="currentPage--">Prev</button>
                <button v-for="p in totalPages" :key="p" :class="{ active: p === currentPage }" @click="currentPage = p">{{ p }}</button>
                <button :disabled="currentPage >= totalPages" @click="currentPage++">Next</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.action-cell {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: nowrap;
}

.data-table th:last-child,
.data-table td:last-child {
    text-align: center;
    white-space: nowrap;
}

button.btn.btn-icon-only,
    .btn.btn-icon-only {
        position: relative;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        width: 38px !important;
        min-width: 38px !important;
        height: 38px !important;
        padding: 0 !important;
        white-space: nowrap;
        overflow: visible !important;
        z-index: 1;
    }

    button.btn.btn-icon-only .button-text,
    .btn.btn-icon-only .button-text {
        position: absolute;
        left: calc(100% + 0.4rem);
        top: 50%;
        transform: translateY(-50%);
        display: inline-block;
        padding: 0.3rem 0.5rem;
        border-radius: 0.35rem;
        background: rgba(32, 33, 36, 0.95);
        color: #fff;
        font-size: 0.75rem;
        line-height: 1;
        opacity: 0;
        visibility: hidden;
        white-space: nowrap;
        transition: opacity 120ms ease, visibility 120ms ease;
        pointer-events: none;
        z-index: 2;
        box-shadow: 0 8px 20px rgba(0,0,0,.12);
    }

    .btn.btn-icon-only:hover .button-text,
    .btn.btn-icon-only:focus-visible .button-text,
    button.btn.btn-icon-only:hover .button-text,
    button.btn.btn-icon-only:focus-visible .button-text {
        opacity: 1;
        visibility: visible;
    }

.attachment-cell {
    display: flex;
    align-items: center;
    justify-content: center;
}

.attachment-preview {
    max-width: 140px;
    max-height: 100px;
    object-fit: contain;
    border-radius: 0.35rem;
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.short-text,
.short-text-reason,
.full-text,
.full-text-reason {
    display: inline;
    word-break: break-word;
}

.reason-toggle {
    text-decoration: none;
    font-size: 25px;
    cursor: pointer;
    margin-left: 0.25rem;
}

.text-toggle {
    margin-top: 0.25rem;
    padding: 0;
    color: #1a73e8;
    font-size: 0.85rem;
}
</style>
