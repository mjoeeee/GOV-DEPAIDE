<script setup lang="ts">
import { onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{ serviceRequest: any; idCard: any }>();

const form = useForm({
    email: props.idCard.email || '',
    depId: props.idCard.dep_id || '',
    role: props.idCard.role || '',
    jobTitle: props.idCard.job_title || '',
    hrId: props.idCard.hr_id || '',
    bday: props.idCard.bday || '',
    empId: props.idCard.emp_id || '',
    prcNo: props.idCard.prc_no || '',
    emrgncyName: props.idCard.emrgncy_name || '',
    emrgncyNo: props.idCard.emrgncy_no || '',
    emrgncyEmail: props.idCard.emrgncy_email || '',
    fname: props.idCard.fname || '',
    lname: props.idCard.lname || '',
    mname: props.idCard.mname || '',
    extName: props.idCard.ext_name || '',
});

function submit() {
    form.post(`/status/view/id-card-printing/${props.idCard.request_id}`);
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('update') === 'success') {
        Swal.fire({ icon: 'success', title: 'Updated Successfully!', showConfirmButton: false, timer: 2000 });
    }
});
</script>

<template>
    <Head title="View ID Card Printing" />

    <div class="form-container">
        <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">ID Card Printing Request Details</h2>

        <form @submit.prevent="submit">
            <div class="form-row">
                <div class="form-group"><label>Email</label><input v-model="form.email" type="email" class="form-control" /></div>
                <div class="form-group"><label>Department/Office</label><input v-model="form.depId" type="text" class="form-control" /></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Role</label><input v-model="form.role" type="text" class="form-control" /></div>
                <div class="form-group"><label>Job Title</label><input v-model="form.jobTitle" type="text" class="form-control" /></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>HR ID</label><input v-model="form.hrId" type="text" class="form-control" /></div>
                <div class="form-group"><label>Employee ID</label><input v-model="form.empId" type="text" class="form-control" /></div>
                <div class="form-group"><label>Birthday</label><input v-model="form.bday" type="date" class="form-control" /></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>PRC No.</label><input v-model="form.prcNo" type="text" class="form-control" /></div>
                <div class="form-group"><label>Emergency Contact Name</label><input v-model="form.emrgncyName" type="text" class="form-control" /></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Emergency Contact Number</label><input v-model="form.emrgncyNo" type="text" class="form-control" /></div>
                <div class="form-group"><label>Emergency Contact Email</label><input v-model="form.emrgncyEmail" type="email" class="form-control" /></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>First Name</label><input v-model="form.fname" type="text" class="form-control" /></div>
                <div class="form-group"><label>Last Name</label><input v-model="form.lname" type="text" class="form-control" /></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Middle Name</label><input v-model="form.mname" type="text" class="form-control" /></div>
                <div class="form-group"><label>Extension Name</label><input v-model="form.extName" type="text" class="form-control" /></div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary" :disabled="form.processing"><i class="fas fa-save"></i> Update Details</button>
                <a href="/status" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</template>
