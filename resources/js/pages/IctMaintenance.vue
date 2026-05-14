<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';
import { appPath } from '@/lib/basePath';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{ divisionOffices: string[] }>();
const page = usePage();
const auth = (page.props as any).auth;

const form = useForm({
    date: new Date().toISOString().split('T')[0],
    time: new Date().toTimeString().slice(0, 5),
    name: auth.user.fullname || '',
    designation: auth.user.job_title || '',
    divisionOffice: '',
    propertyDescription: '',
    brand: '',
    propertyNumber: '',
    serialNumber: '',
    lastRepairDate: '',
    defects: '',
});

function submit() {
    form.post(appPath('/ict-maintenance'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'ICT Maintenance request submitted.', timer: 2000, showConfirmButton: false });
            form.reset();
        },
    });
}
</script>

<template>
    <Head title="ICT Maintenance" />

    <div class="page-header">
        <h1>ICT Maintenance Request Form</h1>
    </div>

    <div class="form-container">
        <form @submit.prevent="submit">
            <h3 class="mb-3" style="font-size:15px;font-weight:600;">Section I</h3>
            <table class="form-table">
                <tr>
                    <th>Date</th>
                    <td><input v-model="form.date" type="date" class="form-control" required /></td>
                </tr>
                <tr>
                    <th>Time</th>
                    <td><input v-model="form.time" type="time" class="form-control" required /></td>
                </tr>
                <tr>
                    <th>Requested by — Name</th>
                    <td><input v-model="form.name" type="text" class="form-control" required /></td>
                </tr>
                <tr>
                    <th>Designation</th>
                    <td><input v-model="form.designation" type="text" class="form-control" required /></td>
                </tr>
                <tr>
                    <th>Division Office</th>
                    <td>
                        <select v-model="form.divisionOffice" class="form-control" required>
                            <option value="">Select Office</option>
                            <option v-for="office in divisionOffices" :key="office" :value="office">{{ office }}</option>
                        </select>
                    </td>
                </tr>
            </table>

            <h3 class="mb-3 mt-4" style="font-size:15px;font-weight:600;">Section II</h3>
            <table class="form-table">
                <tr>
                    <th>Description of Property/Equipment</th>
                    <td><input v-model="form.propertyDescription" type="text" class="form-control" /></td>
                </tr>
                <tr>
                    <th>Brand</th>
                    <td><input v-model="form.brand" type="text" class="form-control" /></td>
                </tr>
                <tr>
                    <th>Property No.</th>
                    <td><input v-model="form.propertyNumber" type="text" class="form-control" /></td>
                </tr>
                <tr>
                    <th>Serial/Engine No.</th>
                    <td><input v-model="form.serialNumber" type="text" class="form-control" /></td>
                </tr>
                <tr>
                    <th>Date of Last Repair</th>
                    <td><input v-model="form.lastRepairDate" type="date" class="form-control" /></td>
                </tr>
                <tr>
                    <th>Defects/Complaints</th>
                    <td><textarea v-model="form.defects" class="form-control" rows="4"></textarea></td>
                </tr>
            </table>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-apply" :disabled="form.processing">Submit Form</button>
            </div>
        </form>
    </div>
</template>
