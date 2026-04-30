<script setup lang="ts">
import { onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{
    serviceRequest: any;
    maintenance: any;
    divisionOffices: string[];
}>();

const form = useForm({
    date: props.maintenance.date_current || '',
    time: props.maintenance.time_current || '',
    name: props.maintenance.req_name || '',
    designation: props.maintenance.req_designation || '',
    divisionOffice: props.maintenance.req_DO || '',
    propertyDescription: props.maintenance.DOPE || '',
    brand: props.maintenance.brand || '',
    propertyNumber: props.maintenance.prop_no || '',
    serialNumber: props.maintenance.serial_no || '',
    lastRepairDate: props.maintenance.last_repair_date || '',
    defects: props.maintenance.defects || '',
});

function submit() {
    form.post(`/status/view/ict-maintenance/${props.maintenance.request_id}`);
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
    <Head title="View ICT Maintenance" />

    <div class="form-container">
        <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">ICT Maintenance Request Form</h2>
        <form @submit.prevent="submit">
            <table class="form-table">
                <tr>
                    <th>Control Number</th>
                    <td><input :value="maintenance.request_id" type="text" class="form-control" readonly /></td>
                </tr>
                <tr><th>Date</th><td><input v-model="form.date" type="date" class="form-control" required /></td></tr>
                <tr><th>Time</th><td><input v-model="form.time" type="time" class="form-control" required /></td></tr>
                <tr><th>Name</th><td><input v-model="form.name" type="text" class="form-control" required /></td></tr>
                <tr><th>Designation</th><td><input v-model="form.designation" type="text" class="form-control" required /></td></tr>
                <tr>
                    <th>Division Office</th>
                    <td>
                        <select v-model="form.divisionOffice" class="form-control" required>
                            <option value="">Select Office</option>
                            <option v-for="o in divisionOffices" :key="o" :value="o">{{ o }}</option>
                        </select>
                    </td>
                </tr>
                <tr><th>Description of Property/Equipment</th><td><input v-model="form.propertyDescription" type="text" class="form-control" /></td></tr>
                <tr><th>Brand</th><td><input v-model="form.brand" type="text" class="form-control" /></td></tr>
                <tr><th>Property No.</th><td><input v-model="form.propertyNumber" type="text" class="form-control" /></td></tr>
                <tr><th>Serial/Engine No.</th><td><input v-model="form.serialNumber" type="text" class="form-control" /></td></tr>
                <tr><th>Date of Last Repair</th><td><input v-model="form.lastRepairDate" type="date" class="form-control" /></td></tr>
                <tr><th>Defects/Complaints</th><td><textarea v-model="form.defects" class="form-control" rows="4"></textarea></td></tr>
            </table>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    <i class="fas fa-save"></i> Update Details
                </button>
                <a href="/status" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</template>
