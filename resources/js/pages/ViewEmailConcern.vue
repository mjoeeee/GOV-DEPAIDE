<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import { appPath } from '@/lib/basePath';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{ serviceRequest: any; concern: any }>();

const form = useForm({
    reason: props.concern.reason || '',
    attachment: null as File | null,
});

const previewUrl = ref<string | null>(null);
const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

const existingIsImage = computed(() => {
    if (!props.concern.attachment) return false;
    const ext = props.concern.attachment.split('.').pop()?.toLowerCase() || '';
    return imageExtensions.includes(ext);
});

function previewImage(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (ev) => { previewUrl.value = ev.target?.result as string; };
        reader.readAsDataURL(input.files[0]);
        form.attachment = input.files[0];
    }
}

function submit() {
    form.post(appPath(`/status/view/email-concern/${props.concern.request_id}`), { forceFormData: true });
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('update') === 'success') {
        Swal.fire({ icon: 'success', title: 'Updated Successfully!', showConfirmButton: false, timer: 2000 });
    }
});
</script>

<template>
    <Head title="View Email Concern" />

    <div class="form-container">
        <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">Email Concern Form</h2>

        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="form-group">
                <label>Email</label>
                <input :value="concern.email" type="email" class="form-control" readonly />
            </div>

            <div class="form-group">
                <label>Reason</label>
                <textarea v-model="form.reason" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Attachment</label>
                <div v-if="concern.attachment" class="attachment-preview mb-2">
                    <img v-if="existingIsImage" :src="concern.attachment_url" alt="Attachment" />
                    <div v-else class="doc-icon">
                        <i class="fas fa-file-alt"></i>
                        <a :href="concern.attachment_url" target="_blank">Download Document</a>
                    </div>
                </div>
                <input type="file" accept="image/*" class="form-control" @change="previewImage" />
                <div v-if="previewUrl" class="file-preview mt-2">
                    <img :src="previewUrl" alt="New Preview" />
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary" :disabled="form.processing"><i class="fas fa-save"></i> Update Details</button>
                <a :href="appPath('/status')" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</template>
