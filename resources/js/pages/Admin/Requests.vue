<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{
    requests: Array<{
        request_id: number; user_name: string; request_type_table: string; mapped_type: string;
        stat: string; remarks: string | null; created_at: string; updated_at: string;
    }>;
    typeMap: Record<string, string>;
}>();

const searchQuery = ref('');
const pageSize = ref(10);
const currentPage = ref(1);

const statusClass: Record<string, string> = {
    'Pending': 'status-text-pending', 'In Progress': 'status-text-in-progress',
    'Completed': 'status-text-completed', 'Rejected': 'status-text-rejected',
};

const filteredRequests = computed(() => {
    let data = [...props.requests];
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        data = data.filter(r =>
            r.user_name.toLowerCase().includes(q) || r.mapped_type.toLowerCase().includes(q) ||
            String(r.request_id).includes(q) || r.stat.toLowerCase().includes(q)
        );
    }
    return data;
});

const totalPages = computed(() => Math.ceil(filteredRequests.value.length / pageSize.value));
const paginatedRequests = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    return filteredRequests.value.slice(start, start + pageSize.value);
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
        router.patch(`/admin/requests/${requestId}`, formValues, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({ icon: 'success', title: 'Updated!', timer: 1500, showConfirmButton: false });
            },
        });
    }
}
</script>

<template>
    <Head title="Admin - All Requests" />

    <div class="page-header"><h1>All Requests</h1></div>

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
            <thead><tr><th>ID</th><th>User</th><th>Type</th><th>Date</th><th>Status</th><th>Remarks</th><th>Action</th></tr></thead>
            <tbody>
                <tr v-for="req in paginatedRequests" :key="req.request_id">
                    <td>{{ req.request_id }}</td>
                    <td>{{ req.user_name }}</td>
                    <td>{{ req.mapped_type }}</td>
                    <td>{{ req.created_at }}</td>
                    <td><span :class="statusClass[req.stat]">{{ req.stat }}</span></td>
                    <td>{{ req.remarks || 'N/A' }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" @click="updateStatus(req.request_id, req.stat, req.remarks)">
                            <i class="fas fa-edit"></i> Update
                        </button>
                    </td>
                </tr>
                <tr v-if="paginatedRequests.length === 0">
                    <td colspan="7" class="text-center text-muted" style="padding:24px;">No requests found.</td>
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
