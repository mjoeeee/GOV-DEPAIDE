<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';
import { appPath, assetPath } from '@/lib/basePath';

defineOptions({ layout: DepAideLayout });

const page = usePage();
const auth = (page.props as any).auth;
const user = auth?.user ?? {};

const form = useForm({
    email: user.email || '',
    depId: user.department || '',
    role: user.role || '',
    jobTitle: '',
    prfxName: '',
    hrId: user.hrId || '',
    bday: '',
    empId: user.employeeId || '',
    prcNo: '',
    tinNo: '',
    gsisNo: '',
    pagibigNo: '',
    philhealthNo: '',
    bloodType: '',
    emrgncyName: '',
    emrgncyNo: '',
    emrgncyEmail: '',
    fname: user.firstname || '',
    mname: user.mname || '',
    lname: user.lastname || '',
    extName: user.extname || '',
    image: null,
    sign: null,
});

const photoPreview = ref<string>('');
const signPreview = ref<string>('');

function handleFileChange(event: Event, field: 'image' | 'sign') {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) {
        form[field] = null;
        if (field === 'image') {
            photoPreview.value = '';
        } else {
            signPreview.value = '';
        }
        return;
    }

    form[field] = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        if (field === 'image') {
            photoPreview.value = String(e.target?.result ?? '');
        } else {
            signPreview.value = String(e.target?.result ?? '');
        }
    };
    reader.readAsDataURL(file);
}

function submit() {
    form.post(appPath('/id-card-printing'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'ID Card Printing request submitted.', timer: 2000, showConfirmButton: false });
            form.reset();
            photoPreview.value = '';
            signPreview.value = '';
        },
    });
}
</script>

<template>
    <Head title="ID Card Printing" />

    <div class="page-header">
        <h1>ID Card Printing Request Form</h1>
    </div>

    <div class="form-container id-card-printing-form">
        <div class="profile-section">
            <div class="profile-card">
                <h2>Profile</h2>
                <div class="photo-upload">
                    <label for="image">
                        <strong>ID Picture</strong>
                    </label>
                    <div class="preview-image">
                        <img :src="photoPreview || assetPath('/images/2x2-default.png')" alt="ID Picture Preview" />
                    </div>
                    <input id="image" type="file" accept=".jpg,.jpeg" @change="(event) => handleFileChange(event, 'image')" />
                    <p class="hint">JPG/JPEG, 2x2 inches, white background.</p>
                </div>

                <div class="photo-upload">
                    <label for="sign">
                        <strong>Signature</strong>
                    </label>
                    <div class="preview-image">
                        <img :src="signPreview || assetPath('/images/2x2-signature.png')" alt="Signature Preview" />
                    </div>
                    <input id="sign" type="file" accept=".png" @change="(event) => handleFileChange(event, 'sign')" />
                    <p class="hint">PNG, 2x2 inches, white background.</p>
                </div>
            </div>
        </div>

        <form class="request-form" @submit.prevent="submit">
            <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input v-model="form.email" type="email" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Department/Office <span class="required">*</span></label>
                    <input v-model="form.depId" type="text" class="form-control" required />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Role <span class="required">*</span></label>
                    <input v-model="form.role" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Job Title <span class="required">*</span></label>
                    <input v-model="form.jobTitle" type="text" class="form-control" required />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Prefix Name</label>
                    <select v-model="form.prfxName" class="form-control">
                        <option value="">Select Prefix</option>
                        <option value="Mr.">Mr.</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Mrs.">Mrs.</option>
                        <option value="Dr.">Dr.</option>
                        <option value="Engr.">Engr.</option>
                        <option value="Prof.">Prof.</option>
                        <option value="Atty.">Atty.</option>
                        <option value="Hon.">Hon.</option>
                        <option value="Rev.">Rev.</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Extension Name</label>
                    <select v-model="form.extName" class="form-control">
                        <option value="">Select Extension</option>
                        <option value="Jr.">Jr.</option>
                        <option value="Sr.">Sr.</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                        <option value="VII">VII</option>
                        <option value="VIII">VIII</option>
                        <option value="IX">IX</option>
                        <option value="X">X</option>
                        <option value="Esq.">Esq.</option>
                        <option value="MD">MD</option>
                        <option value="PhD">PhD</option>
                        <option value="DDS">DDS</option>
                        <option value="DMD">DMD</option>
                        <option value="JD">JD</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>HR ID</label>
                    <input v-model="form.hrId" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Employee ID</label>
                    <input v-model="form.empId" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Birthdate</label>
                    <input v-model="form.bday" type="date" class="form-control" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>PRC No.</label>
                    <input v-model="form.prcNo" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>TIN No.</label>
                    <input v-model="form.tinNo" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>GSIS No.</label>
                    <input v-model="form.gsisNo" type="text" class="form-control" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Pag-IBIG No.</label>
                    <input v-model="form.pagibigNo" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>PhilHealth No.</label>
                    <input v-model="form.philhealthNo" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Blood Type</label>
                    <select v-model="form.bloodType" class="form-control">
                        <option value="">Select Blood Type</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            </div>

            <hr />

            <div class="form-row">
                <div class="form-group">
                    <label>First Name <span class="required">*</span></label>
                    <input v-model="form.fname" type="text" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Middle Name</label>
                    <input v-model="form.mname" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Last Name <span class="required">*</span></label>
                    <input v-model="form.lname" type="text" class="form-control" required />
                </div>
            </div>

            <h3>Emergency Contact</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Contact Number</label>
                    <input v-model="form.emrgncyNo" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <input v-model="form.emrgncyName" type="text" class="form-control" />
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input v-model="form.emrgncyEmail" type="email" class="form-control" />
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" @click="form.reset()">Clear Fields</button>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">Submit Request</button>
            </div>
        </form>
    </div>
</template>
