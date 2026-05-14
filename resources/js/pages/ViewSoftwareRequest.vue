<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import { appPath } from '@/lib/basePath';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{ serviceRequest: any; software: any }>();

const form = useForm({
    projName: props.software.proj_name || '',
    briefDesc: props.software.brief_desc || '',
    primeObj: props.software.prime_obj || '',
    features: props.software.features || '',
    spec: props.software.spec || '',
    attachment: null as File | null,
    projDeadline: props.software.proj_deadline || '',
    addInfo: props.software.add_info || '',
});

const previewUrl = ref<string | null>(null);
const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

const existingIsImage = computed(() => {
    if (!props.software.attachment) return false;
    const ext = props.software.attachment.split('.').pop()?.toLowerCase() || '';
    return imageExtensions.includes(ext);
});

function previewFile(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        form.attachment = file;
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (ev) => { previewUrl.value = ev.target?.result as string; };
            reader.readAsDataURL(file);
        } else {
            previewUrl.value = null;
        }
    }
}

function submit() {
    form.post(appPath(`/status/view/software-request/${props.software.request_id}`), { forceFormData: true });
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('update') === 'success') {
        Swal.fire({ icon: 'success', title: 'Updated Successfully!', showConfirmButton: false, timer: 2000 });
    }
});
</script>

<template>
    <Head title="View Software Request" />

    <div class="form-container">
        <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">Software Development Request Form</h2>

        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="form-group"><label>Project Name</label><input v-model="form.projName" type="text" class="form-control" required /></div>
            <div class="form-group"><label>Brief Description</label><textarea v-model="form.briefDesc" class="form-control" rows="4"></textarea></div>
            <div class="form-group"><label>Primary Objectives</label><textarea v-model="form.primeObj" class="form-control" rows="4"></textarea></div>
            <div class="form-group"><label>Key Features/Functionalities</label><textarea v-model="form.features" class="form-control" rows="4"></textarea></div>
            <div class="form-group"><label>Design Specifications</label><textarea v-model="form.spec" class="form-control" rows="4"></textarea></div>

            <div class="form-group">
                <label>Attach Inspo Files</label>
                <div v-if="software.attachment" class="attachment-preview mb-2">
                    <img v-if="existingIsImage" :src="software.attachment_url" alt="Attachment" />
                    <div v-else class="doc-icon"><i class="fas fa-file-alt"></i><a :href="software.attachment_url" target="_blank">Download</a></div>
                </div>
                <input type="file" class="form-control" @change="previewFile" />
                <div v-if="previewUrl" class="file-preview mt-2"><img :src="previewUrl" alt="Preview" /></div>
            </div>

            <div class="form-group"><label>Project Deadline</label><input v-model="form.projDeadline" type="datetime-local" class="form-control" /></div>
            <div class="form-group"><label>Additional Information</label><textarea v-model="form.addInfo" class="form-control" rows="4"></textarea></div>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary" :disabled="form.processing"><i class="fas fa-save"></i> Update Details</button>
                <a :href="appPath('/status')" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</template>
