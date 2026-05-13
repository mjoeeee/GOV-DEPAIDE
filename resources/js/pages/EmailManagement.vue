<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{ already_requested: boolean }>();
const page = usePage();
const auth = (page.props as any).auth;

const emailType = ref<'deped_email' | 'email_concern'>('deped_email');
const previewUrl = ref<string | null>(null);

const suffix = computed(() => auth.user.extname || '');
const displaySuffix = computed(() => suffix.value || 'N/A');

const generatedEmail = computed(() => {
    const fn = (auth.user.firstname || '').toLowerCase();
    const ln = (auth.user.lastname || '').toLowerCase().replace(/\s+/g, '');
    const sfx = suffix.value.replace(/[^a-zA-Z]/g, '').toLowerCase();
    return `${fn}${sfx}.${ln}@deped.gov.ph`;
});

const depedEmailForm = useForm({
    emailType: 'deped_email',
    officeId: '',
    emailFormat: '',
});

const emailConcernForm = useForm({
    emailType: 'email_concern',
    reason: '',
    attachment: null as File | null,
});

function restrictNumericDash(e: Event) {
    const input = e.target as HTMLInputElement;
    depedEmailForm.officeId = input.value.replace(/[^0-9-]/g, '');
}

function previewImage(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (ev) => { previewUrl.value = ev.target?.result as string; };
        reader.readAsDataURL(input.files[0]);
        emailConcernForm.attachment = input.files[0];
    }
}

function submitDepedEmail() {
    depedEmailForm.emailFormat = generatedEmail.value;
    depedEmailForm.post('/email-management', {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Email Management request submitted.', timer: 2000, showConfirmButton: false });
        },
    });
}

function submitEmailConcern() {
    emailConcernForm.post('/email-management', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Email Management request submitted.', timer: 2000, showConfirmButton: false });
            emailConcernForm.reset();
            previewUrl.value = null;
        },
    });
}
</script>

<template>
    <Head title="Email Management" />

    <div class="page-header">
        <h1>EMAIL MANAGEMENT</h1>
    </div>

    <div class="form-container">
        <div v-if="already_requested" class="login-error mb-3">
            <i class="fas fa-exclamation-triangle"></i> You have already submitted an Email Management request. Check your status page.
        </div>

        <div class="tabs-container" style="margin-bottom: 24px;">
            <button
                @click="emailType = 'deped_email'"
                :class="['tab-button', { active: emailType === 'deped_email' }]"
                :disabled="already_requested"
            >
                <i class="fas fa-envelope"></i> DepEd Email Request
            </button>
            <button
                @click="emailType = 'email_concern'"
                :class="['tab-button', { active: emailType === 'email_concern' }]"
                :disabled="already_requested"
            >
                <i class="fas fa-flag"></i> Email Concern
            </button>
        </div>

        <div class="request-type-badge" style="margin-bottom:16px;padding:10px 14px;border-radius:999px;background:#eef7f5;color:#0f5132;font-weight:700;display:inline-block;">
            {{ emailType === 'deped_email' ? 'DepEd Email Request' : 'Email Concern' }}
        </div>

        <!-- DepEd Email Request Form -->
        <form v-if="emailType === 'deped_email'" @submit.prevent="submitDepedEmail">
            <div class="form-group">
                <label>Office/School ID <span style="color:red">*</span></label>
                <input v-model="depedEmailForm.officeId" type="text" class="form-control" required @input="restrictNumericDash" />
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
                <button type="submit" class="btn btn-apply" :disabled="depedEmailForm.processing || already_requested">Apply Request</button>
            </div>
        </form>

        <!-- Email Concern Form -->
        <form v-if="emailType === 'email_concern'" @submit.prevent="submitEmailConcern" enctype="multipart/form-data">
            <div class="form-group">
                <label>DepEd Email</label>
                <input :value="auth.user.email" type="email" class="form-control" readonly />
            </div>

            <div class="form-group">
                <label>Reason <span style="color:red">*</span></label>
                <textarea v-model="emailConcernForm.reason" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Screenshot</label>
                <input type="file" accept="image/*" class="form-control" @change="previewImage" />
                <div class="file-preview mt-2">
                    <img v-if="previewUrl" :src="previewUrl" alt="Preview" />
                    <i v-else class="fa-solid fa-image placeholder-icon"></i>
                </div>
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-apply" :disabled="emailConcernForm.processing || already_requested">Apply Request</button>
            </div>
        </form>
    </div>
</template>

<style scoped>
.tabs-container {
    display: flex;
    gap: 12px;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 12px;
}

.tab-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: none;
    background: transparent;
    color: #666;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    border-bottom: 3px solid transparent;
    margin-bottom: -15px;
    position: relative;
    bottom: -2px;
}

.tab-button:hover:not(:disabled) {
    color: #1a5f7a;
    background-color: rgba(26, 95, 122, 0.05);
}

.tab-button.active {
    color: #1a5f7a;
    border-bottom-color: #1a5f7a;
}

.tab-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.file-preview {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 120px;
    border: 2px dashed #ccc;
    border-radius: 4px;
    background-color: #f9f9f9;
    overflow: hidden;
}

.file-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.placeholder-icon {
    font-size: 32px;
    color: #ccc;
}
</style>
