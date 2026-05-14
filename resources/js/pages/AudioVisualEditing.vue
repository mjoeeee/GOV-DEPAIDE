<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';
import { appPath } from '@/lib/basePath';

defineOptions({ layout: DepAideLayout });

const form = useForm({
    title: '',
    projectType: '',
    otherProjectType: '',
    musicPreference: '',
    deliverables: '',
    otherDeliverables: '',
    styleTone: '',
    otherStyleTone: '',
    deliveryMethod: '',
    otherDeliveryMethod: '',
    projectDeadline: new Date().toISOString().slice(0, 16),
    projDesc: '',
});

const showOtherProjectType = ref(false);
const showOtherDeliverables = ref(false);
const showOtherStyleTone = ref(false);
const showOtherDeliveryMethod = ref(false);
const hideOptionalFields = ref(false);

watch(
    () => form.projectType,
    (value) => {
        showOtherProjectType.value = value === 'Other';
        hideOptionalFields.value = value === 'Tarpaulin';

        if (hideOptionalFields.value) {
            form.musicPreference = '';
            form.deliverables = '';
            form.styleTone = '';
            form.otherDeliverables = '';
            form.otherStyleTone = '';
        }
    },
);

watch(
    () => form.deliverables,
    (value) => {
        showOtherDeliverables.value = value === 'Other';
    },
);

watch(
    () => form.styleTone,
    (value) => {
        showOtherStyleTone.value = value === 'Other';
    },
);

watch(
    () => form.deliveryMethod,
    (value) => {
        showOtherDeliveryMethod.value = value === 'Other';
    },
);

const projectTypeOptions = [
    'Corporate Video',
    'Event Video',
    'Promotional Video',
    'Tutorial/Training Video',
    'Documentary',
    'Tarpaulin',
    'Other',
];

const musicPreferenceOptions = [
    'Background Music',
    'No Music',
    'Music Provided by Client',
];

const deliverablesOptions = [
    'Edited Video',
    'Audio-Only',
    'Image-Only',
    'Raw Footage/Audio',
    'Clips',
    'Other',
];

const styleToneOptions = [
    'Cinematic',
    'Documentary-Style',
    'Corporate/Professional',
    'Casual/Informal',
    'High-Energy',
    'Artistic/Experimental',
    'Other',
];

const deliveryMethodOptions = [
    'Google Drive',
    'Dropbox',
    'WeTransfer',
    'FTP Upload',
    'Other',
];

function submit() {
    if (showOtherProjectType.value) {
        form.projectType = form.otherProjectType;
    }
    if (showOtherDeliverables.value) {
        form.deliverables = form.otherDeliverables;
    }
    if (showOtherStyleTone.value) {
        form.styleTone = form.otherStyleTone;
    }
    if (showOtherDeliveryMethod.value) {
        form.deliveryMethod = form.otherDeliveryMethod;
    }

    form.post(appPath('/audio-visual'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Digital production request submitted.', timer: 2000, showConfirmButton: false });
            form.reset();
            showOtherProjectType.value = false;
            showOtherDeliverables.value = false;
            showOtherStyleTone.value = false;
            showOtherDeliveryMethod.value = false;
            hideOptionalFields.value = false;
        },
    });
}
</script>

<template>
    <Head title="Audio Visual Editing" />

    <div class="page-header">
        <h1>Digital Production Request Form</h1>
    </div>

    <div class="form-container">
        <form @submit.prevent="submit">
            <div class="form-group">
                <label>Project Title/Name <span class="required">*</span></label>
                <input v-model="form.title" type="text" class="form-control" required />
            </div>

            <div class="form-group">
                <label>Project Description <span class="required">*</span></label>
                <textarea v-model="form.projDesc" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Type of Project <span class="required">*</span></label>
                    <select v-model="form.projectType" class="form-control" required>
                        <option value="">Select Project</option>
                        <option v-for="option in projectTypeOptions" :key="option" :value="option">{{ option }}</option>
                    </select>
                </div>
                <div class="form-group" v-if="showOtherProjectType">
                    <label>Specify Other Project</label>
                    <input v-model="form.otherProjectType" type="text" class="form-control" placeholder="Enter project type" required />
                </div>
            </div>

            <div v-if="!hideOptionalFields">
                <div class="form-row">
                    <div class="form-group">
                        <label>Music Preferences</label>
                        <select v-model="form.musicPreference" class="form-control">
                            <option value="">Select Music Preference</option>
                            <option v-for="option in musicPreferenceOptions" :key="option" :value="option">{{ option }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Expected Deliverables</label>
                        <select v-model="form.deliverables" class="form-control">
                            <option value="">Select Deliverables</option>
                            <option v-for="option in deliverablesOptions" :key="option" :value="option">{{ option }}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" v-if="showOtherDeliverables">
                    <label>Specify Other Deliverable</label>
                    <input v-model="form.otherDeliverables" type="text" class="form-control" placeholder="Enter deliverable" required />
                </div>

                <div class="form-group">
                    <label>Project Style & Tone</label>
                    <select v-model="form.styleTone" class="form-control">
                        <option value="">Select Style</option>
                        <option v-for="option in styleToneOptions" :key="option" :value="option">{{ option }}</option>
                    </select>
                </div>

                <div class="form-group" v-if="showOtherStyleTone">
                    <label>Specify Other Style & Tone</label>
                    <input v-model="form.otherStyleTone" type="text" class="form-control" placeholder="Enter style" required />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Preferred Delivery Method <span class="required">*</span></label>
                    <select v-model="form.deliveryMethod" class="form-control" required>
                        <option value="">Select Delivery</option>
                        <option v-for="option in deliveryMethodOptions" :key="option" :value="option">{{ option }}</option>
                    </select>
                </div>
                <div class="form-group" v-if="showOtherDeliveryMethod">
                    <label>Specify Other Delivery Method</label>
                    <input v-model="form.otherDeliveryMethod" type="text" class="form-control" placeholder="Enter method" required />
                </div>
            </div>

            <div class="form-group">
                <label>Project Deadline <span class="required">*</span></label>
                <input v-model="form.projectDeadline" type="datetime-local" class="form-control" required />
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-apply" :disabled="form.processing">Submit Request</button>
            </div>
        </form>
    </div>
</template>
