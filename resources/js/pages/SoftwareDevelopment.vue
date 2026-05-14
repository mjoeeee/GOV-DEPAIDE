<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';
import { appPath } from '@/lib/basePath';

defineOptions({ layout: DepAideLayout });

const form = useForm({
    projName: '',
    briefDesc: '',
    primeObj: '',
    features: '',
    spec: '',
    addInfo: '',
    projDeadline: new Date().toISOString().slice(0, 16),
    attachment: null as File | null,
});

const previewUrl = ref<string | null>(null);

function handleAttachment(e: Event) {
    const input = e.target as HTMLInputElement;

    if (input.files && input.files[0]) {
        form.attachment = input.files[0];
        const reader = new FileReader();

        reader.onload = (event) => {
            previewUrl.value = event.target?.result as string;
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function resetForm() {
    form.reset();
    previewUrl.value = null;
}

function submit() {
    form.post(appPath('/software-request'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Software development request submitted.',
                timer: 2000,
                showConfirmButton: false,
            });
            resetForm();
        },
    });
}
</script>

<template>
    <Head title="Software Development" />

    <div class="page-header">
        <h1>Software Development Request Form</h1>
    </div>

    <div class="form-container software-form">
        <form @submit.prevent="submit" enctype="multipart/form-data">
            <div class="form-card">
                <div class="form-card-header">
                    <h2>Project Details</h2>
                    <hr />
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="projectName">Project Name <span class="required">*</span></label>
                        <input id="projectName" v-model="form.projName" type="text" class="form-control" placeholder="Enter project name" required />
                    </div>

                    <div class="input-group">
                        <label for="briefDescription">Brief Description <span class="required">*</span></label>
                        <textarea id="briefDescription" v-model="form.briefDesc" rows="4" class="form-control" placeholder="Enter brief description" required></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="primaryObjectives">Primary Objectives <span class="required">*</span></label>
                        <textarea id="primaryObjectives" v-model="form.primeObj" rows="4" class="form-control" placeholder="Enter primary objectives" required></textarea>
                    </div>

                    <div class="input-group">
                        <label for="keyFeatures">Key Features / Functionalities</label>
                        <textarea id="keyFeatures" v-model="form.features" rows="4" class="form-control" placeholder="Enter key features / functionalities"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="designSpecifications">Design Specifications <span class="required">*</span></label>
                        <textarea id="designSpecifications" v-model="form.spec" rows="4" class="form-control" placeholder="Enter design specifications" required></textarea>
                    </div>

                    <div class="input-group">
                        <label for="additionalInfo">Additional Information, if any</label>
                        <textarea id="additionalInfo" v-model="form.addInfo" rows="4" class="form-control" placeholder="Enter additional information"></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-group">
                        <label for="projectDate">Project Deadline <span class="required">*</span></label>
                        <input id="projectDate" v-model="form.projDeadline" type="datetime-local" class="form-control" required />
                    </div>

                    <div class="input-group">
                        <label for="inspoFiles">Attach Inspo Files</label>
                        <div class="preview-container">
                            <div id="imagePreviewBox" class="preview-box">
                                <img v-if="previewUrl" :src="previewUrl" alt="Screenshot Preview" />
                                <i v-else class="fas fa-image preview-icon"></i>
                            </div>
                        </div>
                        <input id="inspoFiles" type="file" class="form-control" @change="handleAttachment" />
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-reset" @click="resetForm">Reset Form</button>
                    <button type="submit" class="btn btn-apply" :disabled="form.processing">Submit Form</button>
                </div>
            </div>
        </form>
    </div>
</template>
