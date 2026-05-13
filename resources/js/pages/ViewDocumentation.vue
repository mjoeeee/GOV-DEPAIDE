<script setup lang="ts">
import { onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import MinimalLayout from '@/layouts/MinimalLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: MinimalLayout });

const props = defineProps<{ serviceRequest: any; documentation: any }>();

const form = useForm({
    title: props.documentation.title || '',
    eventLocation: props.documentation.event_location || '',
    eventDate: props.documentation.event_date || '',
    startTime: props.documentation.start_time || '',
    endTime: props.documentation.end_time || '',
    description: props.documentation.description || '',
    details: props.documentation.details || '',
    photoLink: props.documentation.photo_link || '',
});

function submit() {
    form.post(`/status/view/documentation/${props.documentation.request_id}`);
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('update') === 'success') {
        Swal.fire({ icon: 'success', title: 'Updated Successfully!', showConfirmButton: false, timer: 2000 });
    }
});
</script>

<template>
    <Head title="View Documentation" />

    <div class="form-container">
        <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">Documentation Request Details</h2>

        <form @submit.prevent="submit">
            <div class="form-group"><label>Title</label><input v-model="form.title" type="text" class="form-control" required /></div>
            <div class="form-group"><label>Event Location</label><input v-model="form.eventLocation" type="text" class="form-control" /></div>
            <div class="form-row">
                <div class="form-group"><label>Event Date</label><input v-model="form.eventDate" type="date" class="form-control" /></div>
                <div class="form-group"><label>Start Time</label><input v-model="form.startTime" type="time" class="form-control" /></div>
                <div class="form-group"><label>End Time</label><input v-model="form.endTime" type="time" class="form-control" /></div>
            </div>
            <div class="form-group"><label>Description</label><textarea v-model="form.description" class="form-control" rows="3"></textarea></div>
            <div class="form-group"><label>Details</label><textarea v-model="form.details" class="form-control" rows="4"></textarea></div>
            <div class="form-group"><label>Photo Link</label><input v-model="form.photoLink" type="url" class="form-control" /></div>

            <div class="d-flex justify-content-between mt-3">
                <button type="submit" class="btn btn-primary" :disabled="form.processing"><i class="fas fa-save"></i> Update Details</button>
                <a href="/status" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</template>
