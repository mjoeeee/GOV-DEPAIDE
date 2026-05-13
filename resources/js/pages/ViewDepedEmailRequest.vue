<script setup lang="ts">
import { onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{ serviceRequest: any; depedEmail: any }>();

const form = useForm({
    schoolId: props.depedEmail.school_id || '',
    officeId: props.depedEmail.office_id || '',
    firstname: props.depedEmail.firstname || '',
    lastname: props.depedEmail.lastname || '',
    suffix: props.depedEmail.suffix || '',
    position: props.depedEmail.position || '',
    emailFormat: props.depedEmail.email_format || '',
});

function submit() {
    form.post(`/status/view/deped-email-request/${props.depedEmail.request_id}`);
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('update') === 'success') {
        Swal.fire({ icon: 'success', title: 'Updated Successfully!', showConfirmButton: false, timer: 2000 });
    }
});
</script>

<template>
    <Head title="View DepEd Email Request" />

    <div class="form-container">
        <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">DepEd Email Request Details</h2>

        <form @submit.prevent="submit">
            <div class="form-group"><label>School ID</label><input v-model="form.schoolId" type="text" class="form-control" /></div>
            <div class="form-group"><label>Office ID</label><input v-model="form.officeId" type="text" class="form-control" /></div>
            <div class="form-row">
                <div class="form-group"><label>Firstname</label><input v-model="form.firstname" type="text" class="form-control" /></div>
                <div class="form-group"><label>Lastname</label><input v-model="form.lastname" type="text" class="form-control" /></div>
                <div class="form-group" style="max-width:120px;"><label>Suffix</label><input v-model="form.suffix" type="text" class="form-control" /></div>
            </div>
            <div class="form-group"><label>Position</label><input v-model="form.position" type="text" class="form-control" /></div>
            <div class="form-group"><label>DepEd Email</label><input v-model="form.emailFormat" type="email" class="form-control" /></div>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary" :disabled="form.processing"><i class="fas fa-save"></i> Update Details</button>
                <a href="/status" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</template>
