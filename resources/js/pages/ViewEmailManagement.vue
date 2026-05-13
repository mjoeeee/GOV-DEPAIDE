<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{ serviceRequest: any; emailManagement: any }>();

const isDepedEmail = computed(() => props.emailManagement.type === 'deped_email');
const isEmailConcern = computed(() => props.emailManagement.type === 'email_concern');

const depedEmailForm = useForm({
    schoolId: props.emailManagement.school_id || '',
    officeId: props.emailManagement.office_id || '',
    firstname: props.emailManagement.firstname || '',
    lastname: props.emailManagement.lastname || '',
    suffix: props.emailManagement.suffix || '',
    position: props.emailManagement.position || '',
    emailFormat: props.emailManagement.email_format || '',
});

const emailConcernForm = useForm({
    reason: props.emailManagement.reason || '',
    attachment: null as File | null,
});

const previewUrl = ref<string | null>(null);
const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

const requestTypeLabel = computed(() => props.emailManagement.request_type_label || (isDepedEmail.value ? 'DepEd Email Request' : 'Email Concern'));

const existingIsImage = computed(() => {
    if (!props.emailManagement.attachment) return false;
    const ext = props.emailManagement.attachment.split('.').pop()?.toLowerCase() || '';
    return imageExtensions.includes(ext);
});

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
    depedEmailForm.post(`/status/view/email-management/${props.emailManagement.request_id}`);
}

function submitEmailConcern() {
    emailConcernForm.post(`/status/view/email-management/${props.emailManagement.request_id}`, { forceFormData: true });
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('update') === 'success') {
        Swal.fire({ icon: 'success', title: 'Updated Successfully!', showConfirmButton: false, timer: 2000 });
    }
});
</script>

<template>
    <Head title="View Email Management" />

    <div class="form-container">
        <div class="request-type-badge" style="margin-bottom:12px;padding:8px 12px;background:#eef5ff;border:1px solid #c8ddff;border-radius:8px;color:#0d47a1;font-weight:600;display:inline-block;">
            {{ requestTypeLabel }}
        </div>

        <!-- DepEd Email Request Form -->
        <div v-if="isDepedEmail">
            <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">DepEd Email Request Details</h2>

            <form @submit.prevent="submitDepedEmail">
                <div class="form-group"><label>School ID</label><input v-model="depedEmailForm.schoolId" type="text" class="form-control" /></div>
                <div class="form-group"><label>Office ID</label><input v-model="depedEmailForm.officeId" type="text" class="form-control" /></div>
                <div class="form-row">
                    <div class="form-group"><label>Firstname</label><input v-model="depedEmailForm.firstname" type="text" class="form-control" /></div>
                    <div class="form-group"><label>Lastname</label><input v-model="depedEmailForm.lastname" type="text" class="form-control" /></div>
                    <div class="form-group" style="max-width:120px;"><label>Suffix</label><input v-model="depedEmailForm.suffix" type="text" class="form-control" /></div>
                </div>
                <div class="form-group"><label>Position</label><input v-model="depedEmailForm.position" type="text" class="form-control" /></div>
                <div class="form-group"><label>DepEd Email</label><input v-model="depedEmailForm.emailFormat" type="email" class="form-control" /></div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary" :disabled="depedEmailForm.processing"><i class="fas fa-save"></i> Update Details</button>
                    <a href="/status" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>

        <!-- Email Concern Form -->
        <div v-if="isEmailConcern">
            <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">Email Management - Concern Form</h2>

            <form @submit.prevent="submitEmailConcern" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Email</label>
                    <input :value="emailManagement.email" type="email" class="form-control" readonly />
                </div>

                <div class="form-group">
                    <label>Reason</label>
                    <textarea v-model="emailConcernForm.reason" class="form-control" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label>Attachment</label>
                    <div v-if="emailManagement.attachment" class="attachment-preview mb-2">
                        <img v-if="existingIsImage" :src="emailManagement.attachment_url" alt="Attachment" />
                        <div v-else class="doc-icon">
                            <i class="fas fa-file-alt"></i>
                            <a :href="emailManagement.attachment_url" target="_blank">Download Document</a>
                        </div>
                    </div>
                    <input type="file" accept="image/*" class="form-control" @change="previewImage" />
                    <div v-if="previewUrl" class="file-preview mt-2">
                        <img :src="previewUrl" alt="New Preview" />
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary" :disabled="emailConcernForm.processing"><i class="fas fa-save"></i> Update Details</button>
                    <a href="/status" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</template>
