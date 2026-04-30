<script setup lang="ts">
import { onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{ serviceRequest: any; inspection: any }>();

const form = useForm({
    item: props.inspection.item || '',
    propertyNo: props.inspection.property_no || '',
    receiptNo: props.inspection.receipt_no || '',
    acquisitionCost: props.inspection.acquisition_cost || '',
    acquisitionDate: props.inspection.acquisition_date || '',
    complaints: props.inspection.complaints || '',
    scopeLastRepair: props.inspection.scope_last_repair || '',
});

function submit() {
    form.post(`/status/view/ict-inspection/${props.inspection.request_id}`);
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('update') === 'success') {
        Swal.fire({ icon: 'success', title: 'Updated Successfully!', showConfirmButton: false, timer: 2000 });
    }
    if (params.get('error') === 'invalid_request') {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Invalid request.' });
    }
});
</script>

<template>
    <Head title="View ICT Inspection" />

    <div class="form-container">
        <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;border-right:2px dashed var(--border-color);display:inline-block;padding-right:16px;">REQUEST FOR INSPECTION</h2>

        <form @submit.prevent="submit">
            <table class="form-table">
                <thead><tr><th>Item/Description</th><th>Property No.</th><th>Receipt No.</th></tr></thead>
                <tbody><tr>
                    <td><input v-model="form.item" type="text" class="form-control" required /></td>
                    <td><input v-model="form.propertyNo" type="text" class="form-control" /></td>
                    <td><input v-model="form.receiptNo" type="text" class="form-control" /></td>
                </tr></tbody>
            </table>

            <div class="form-row mt-3">
                <div class="form-group"><label>Acquisition Cost</label><input v-model="form.acquisitionCost" type="text" class="form-control" /></div>
                <div class="form-group"><label>Acquisition Date</label><input v-model="form.acquisitionDate" type="date" class="form-control" /></div>
            </div>

            <div class="form-group mt-3"><label>Complaints</label><textarea v-model="form.complaints" class="form-control" rows="6" required></textarea></div>
            <div class="form-group mt-3"><label>Nature and Scope of Last Repair</label><textarea v-model="form.scopeLastRepair" class="form-control" rows="6"></textarea></div>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary" :disabled="form.processing"><i class="fas fa-save"></i> Update Details</button>
                <a href="/status" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</template>
