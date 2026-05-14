<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';
import QRCode from 'qrcode';
import { appPath } from '@/lib/basePath';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{
    requests: Array<{
        request_id: number;
        request_type_table: string;
        mapped_type: string;
        stat: string;
        remarks: string | null;
        rated: boolean;
        created_at: string;
        updated_at: string;
    }>;
    typeMap: Record<string, string>;
}>();

const searchQuery = ref('');
const pageSize = ref(10);
const currentPage = ref(1);
const expandedRemarks = ref<Set<number>>(new Set());

const RATING_URL = 'https://forms.office.com/pages/responsepage.aspx?id=gKvjQCQgo0W_dnoHYaJNKWgIw6RkFhNJi7IW9oBkiLxUOTVNWkg5NkxFWlpDNzlWMjY2WDZGVjNXNi4u&route=shorturl';

const statusClass: Record<string, string> = {
    'Pending': 'status-text-pending',
    'In Progress': 'status-text-in-progress',
    'Completed': 'status-text-completed',
    'Rejected': 'status-text-rejected',
};

const viewRoutes: Record<string, string> = {
    'ict_maintenance': '/status/view/ict-maintenance',
    'ict_equipment_inspection': '/status/view/ict-inspection',
    'email_management': '/status/view/email-management',
    'email_concern': '/status/view/email-concern',
    'password_reset': '/status/view/email-management',
    'deped_email_request': '/status/view/email-management',
    'software_development': '/status/view/software-request',
    'documentation': '/status/view/documentation',
    'audio_visual_editing': '/status/view/audio-visual-editing',
    'id_card_printing': '/status/view/id-card-printing',
};

const filteredRequests = computed(() => {
    let data = [...props.requests];
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(r =>
            r.mapped_type.toLowerCase().includes(q) ||
            String(r.request_id).includes(q) ||
            r.stat.toLowerCase().includes(q) ||
            (r.remarks || '').toLowerCase().includes(q) ||
            r.created_at.toLowerCase().includes(q)
        );
    }
    return data;
});

const totalPages = computed(() => Math.ceil(filteredRequests.value.length / pageSize.value));
const paginatedRequests = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    return filteredRequests.value.slice(start, start + pageSize.value);
});

function toggleRemarks(id: number) {
    if (expandedRemarks.value.has(id)) {
        expandedRemarks.value.delete(id);
    } else {
        expandedRemarks.value.add(id);
    }
}

function truncateRemarks(text: string | null, id: number): string {
    if (!text) return 'N/A';
    if (expandedRemarks.value.has(id)) return text;
    if (text.length <= 10) return text;
    return text.substring(0, 10) + '...';
}

function getViewUrl(type: string, id: number): string | null {
    const base = viewRoutes[type];
    return base ? appPath(`${base}/${id}`) : null;
}

async function showRatingPopup(requestType: string, requestId: number, rated: boolean) {
    let qrDataUrl = '';
    try {
        qrDataUrl = await QRCode.toDataURL(RATING_URL, { width: 200 });
    } catch (e) { /* */ }

    const ratedIcon = rated
        ? '<i class="fas fa-star" style="color:#358308"></i> Rated'
        : '<i class="fas fa-star" style="color:#ec160b"></i> Not Rated';

    Swal.fire({
        title: rated ? 'Thank you for providing your rating!' : 'Kindly provide your rating.',
        html: `
            <div style="display:flex;justify-content:space-around;margin:16px 0;font-size:14px;">
                <div><strong>Request Type:</strong><br>${requestType}</div>
                <div><strong>Request ID:</strong><br>${requestId}</div>
                <div><strong>Rated:</strong><br>${ratedIcon}</div>
            </div>
            ${qrDataUrl ? `<img src="${qrDataUrl}" alt="QR Code" style="margin:12px auto;display:block;" />` : ''}
        `,
        showConfirmButton: !rated,
        confirmButtonText: '<i class="fas fa-external-link-alt"></i> Open Rating Link',
        confirmButtonColor: '#dc3545',
        showCloseButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            window.open(RATING_URL, '_blank');
            updateRatedColumn(requestId);
        }
    });
}

async function updateRatedColumn(requestId: number) {
    try {
        await fetch(appPath(`/api/update-rated/${requestId}`), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });
        window.location.reload();
    } catch (e) { /* */ }
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const type = params.get('type');
    const reqId = params.get('request_id');
    if (type) searchQuery.value = type;
    if (reqId) searchQuery.value = reqId;
});
</script>

<template>
    <Head title="Status" />

    <div class="page-header">
        <h1>Employee Service Request</h1>
    </div>

    <div class="data-table-wrapper">
        <div class="data-table-controls">
            <div class="d-flex align-items-center gap-2">
                <label>Show</label>
                <select v-model="pageSize" class="page-size-select" @change="currentPage = 1">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                </select>
                <label>entries</label>
            </div>
            <input v-model="searchQuery" type="text" class="data-table-search" placeholder="Search..." @input="currentPage = 1" />
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>ID</th>
                    <th>Type of Request</th>
                    <th>Date of Request</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="req in paginatedRequests" :key="req.request_id">
                    <td class="action-cell">
                        <div class="action-icons">
                            <a
                                v-if="getViewUrl(req.request_type_table, req.request_id)"
                                :href="getViewUrl(req.request_type_table, req.request_id)!"
                                class="btn btn-secondary btn-icon btn-sm"
                                title="View details"
                            >
                                <i class="fas fa-eye"></i>
                            </a>
                            <button
                                v-if="req.stat === 'Completed'"
                                :class="req.rated ? 'star-rated' : 'star-unrated'"
                                @click="showRatingPopup(req.mapped_type, req.request_id, req.rated)"
                                title="Rate"
                            >
                                <i class="fas fa-star"></i>
                            </button>
                        </div>
                    </td>
                    <td>{{ req.request_id }}</td>
                    <td>{{ req.mapped_type }}</td>
                    <td>{{ req.created_at }}</td>
                    <td><span :class="statusClass[req.stat] || ''">{{ req.stat }}</span></td>
                    <td>
                        <span>{{ truncateRemarks(req.remarks, req.request_id) }}</span>
                        <a
                            v-if="req.remarks && req.remarks.length > 10"
                            href="#"
                            style="font-size:11px;margin-left:4px;"
                            @click.prevent="toggleRemarks(req.request_id)"
                        >
                            {{ expandedRemarks.has(req.request_id) ? '↖' : '...' }}
                        </a>
                    </td>
                </tr>
                <tr v-if="paginatedRequests.length === 0">
                    <td colspan="6" class="text-center text-muted" style="padding:24px;">No requests found.</td>
                </tr>
            </tbody>
        </table>

        <div class="data-table-controls mt-3">
            <div class="text-muted" style="font-size:12px;">
                Showing {{ ((currentPage - 1) * pageSize) + 1 }} to {{ Math.min(currentPage * pageSize, filteredRequests.length) }} of {{ filteredRequests.length }} entries
            </div>
            <div class="data-table-pagination">
                <button :disabled="currentPage <= 1" @click="currentPage--">Prev</button>
                <button
                    v-for="p in totalPages"
                    :key="p"
                    :class="{ active: p === currentPage }"
                    @click="currentPage = p"
                >{{ p }}</button>
                <button :disabled="currentPage >= totalPages" @click="currentPage++">Next</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.action-icons,
.action-cell {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.data-table th:last-child,
.data-table td:last-child {
    text-align: center;
    white-space: nowrap;
}
</style>
