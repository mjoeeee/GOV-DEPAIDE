<script setup lang="ts">
import { onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{ serviceRequest: any; audioVisual: any }>();

const form = useForm({
    title: props.audioVisual.title || '',
    projectType: props.audioVisual.project_type || '',
    deliveryMethod: props.audioVisual.delivery_method || '',
    projectDeadline: props.audioVisual.project_deadline || '',
    projDesc: props.audioVisual.proj_desc || '',
    musicPreference: props.audioVisual.music_preference || '',
    deliverables: props.audioVisual.deliverables || '',
    styleTone: props.audioVisual.style_tone || '',
});

function submit() {
    form.post(`/status/view/audio-visual-editing/${props.audioVisual.request_id}`);
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('update') === 'success') {
        Swal.fire({ icon: 'success', title: 'Updated Successfully!', showConfirmButton: false, timer: 2000 });
    }
});
</script>

<template>
    <Head title="View Audio Visual Editing" />

    <div class="form-container">
        <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">Audio Visual Editing Request Details</h2>

        <form @submit.prevent="submit">
            <div class="form-group"><label>Title</label><input v-model="form.title" type="text" class="form-control" required /></div>
            <div class="form-group"><label>Project Type</label><input v-model="form.projectType" type="text" class="form-control" /></div>
            <div class="form-group"><label>Delivery Method</label><input v-model="form.deliveryMethod" type="text" class="form-control" /></div>
            <div class="form-group"><label>Project Deadline</label><input v-model="form.projectDeadline" type="date" class="form-control" /></div>
            <div class="form-group"><label>Project Description</label><textarea v-model="form.projDesc" class="form-control" rows="4"></textarea></div>
            <div class="form-group"><label>Music Preference</label><input v-model="form.musicPreference" type="text" class="form-control" /></div>
            <div class="form-group"><label>Deliverables</label><textarea v-model="form.deliverables" class="form-control" rows="3"></textarea></div>
            <div class="form-group"><label>Style / Tone</label><input v-model="form.styleTone" type="text" class="form-control" /></div>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary" :disabled="form.processing"><i class="fas fa-save"></i> Update Details</button>
                <a href="/status" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</template>
