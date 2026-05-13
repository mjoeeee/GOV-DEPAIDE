<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: DepAideLayout });

const page = usePage();
const auth = (page.props as any).auth;

const form = useForm({
    title: '',
    event_location: '',
    event_date: new Date().toISOString().split('T')[0],
    start_time: '',
    end_time: '',
    description: '',
    details: '',
    photo_link: '',
});

function submit() {
    form.post('/documentation', {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Documentation request submitted.', timer: 2000, showConfirmButton: false });
            form.reset();
        },
    });
}
</script>

<template>
    <Head title="Documentation" />

    <div class="page-header">
        <h1>Documentation Request Form</h1>
    </div>

    <div class="form-container">
        <form @submit.prevent="submit">
            <div class="form-group">
                <label>Title <span class="required">*</span></label>
                <input v-model="form.title" type="text" class="form-control" required />
            </div>

            <div class="form-group">
                <label>Event Location <span class="required">*</span></label>
                <input v-model="form.event_location" type="text" class="form-control" required />
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Event Date <span class="required">*</span></label>
                    <input v-model="form.event_date" type="date" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Start Time</label>
                    <input v-model="form.start_time" type="time" class="form-control" />
                </div>
                <div class="form-group">
                    <label>End Time</label>
                    <input v-model="form.end_time" type="time" class="form-control" />
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea v-model="form.description" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label>Details</label>
                <textarea v-model="form.details" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>Photo Link</label>
                <input v-model="form.photo_link" type="url" class="form-control" placeholder="https://drive.google.com/..." />
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-apply" :disabled="form.processing">Submit Request</button>
            </div>
        </form>
    </div>
</template>
