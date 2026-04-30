<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: DepAideLayout });

const page = usePage();
const auth = (page.props as any).auth;
const previewUrl = ref<string | null>(null);

const form = useForm({
    reason: '',
    attachment: null as File | null,
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
    form.post('/email-concern', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Email concern submitted.', timer: 2000, showConfirmButton: false });
            form.reset();
            previewUrl.value = null;
        },
    });
}
</script>

<template>
    <Head title="Email Concern" />

    <div class="page-header">
        <h1>Email Concern</h1>
    </div>

    <div class="form-container">
        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="form-group">
                <label>DepEd Email</label>
                <input :value="auth.user.email" type="email" class="form-control" readonly />
            </div>

            <div class="form-group">
                <label>Reason <span style="color:red">*</span></label>
                <textarea v-model="form.reason" class="form-control" rows="4" required></textarea>
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
                <button type="submit" class="btn btn-apply" :disabled="form.processing">Apply Request</button>
            </div>
        </form>
    </div>
</template>
