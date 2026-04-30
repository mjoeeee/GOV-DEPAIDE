<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{ already_requested: boolean }>();
const page = usePage();
const auth = (page.props as any).auth;

const suffix = computed(() => auth.user.extname || '');
const displaySuffix = computed(() => suffix.value || 'N/A');

const generatedEmail = computed(() => {
    const fn = (auth.user.firstname || '').toLowerCase();
    const ln = (auth.user.lastname || '').toLowerCase().replace(/\s+/g, '');
    const sfx = suffix.value.replace(/[^a-zA-Z]/g, '').toLowerCase();
    return `${fn}${sfx}.${ln}@deped.gov.ph`;
});

const form = useForm({
    officeId: '',
    emailFormat: '',
});

function restrictNumericDash(e: Event) {
    const input = e.target as HTMLInputElement;
    form.officeId = input.value.replace(/[^0-9-]/g, '');
}

function submit() {
    form.emailFormat = generatedEmail.value;
    form.post('/deped-email', {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'DepEd Email request submitted.', timer: 2000, showConfirmButton: false });
        },
    });
}
</script>

<template>
    <Head title="DepEd Email Request" />

    <div class="page-header">
        <h1>REQUEST FOR DEPED EMAIL</h1>
    </div>

    <div class="form-container">
        <div v-if="already_requested" class="login-error mb-3">
            <i class="fas fa-exclamation-triangle"></i> You have already submitted a DepEd Email request. Check your status page.
        </div>

        <form @submit.prevent="submit">
            <div class="form-group">
                <label>Office/School ID <span style="color:red">*</span></label>
                <input v-model="form.officeId" type="text" class="form-control" required @input="restrictNumericDash" />
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Firstname</label>
                    <input :value="auth.user.firstname" type="text" class="form-control" readonly />
                </div>
                <div class="form-group">
                    <label>Lastname</label>
                    <input :value="auth.user.lastname" type="text" class="form-control" readonly />
                </div>
                <div class="form-group" style="max-width:120px;">
                    <label>Suffix</label>
                    <input :value="displaySuffix" type="text" class="form-control" readonly />
                </div>
            </div>

            <div class="form-group">
                <label>Position</label>
                <input :value="auth.user.job_title" type="text" class="form-control" readonly />
            </div>

            <div class="form-group">
                <label>DepEd Email</label>
                <input :value="generatedEmail" type="text" class="form-control" readonly />
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-apply" :disabled="form.processing || already_requested">Apply Request</button>
            </div>
        </form>
    </div>
</template>
