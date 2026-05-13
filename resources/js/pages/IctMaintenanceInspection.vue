<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{ divisionOffices: string[] }>();
const page = usePage();
const auth = (page.props as any).auth;

const form = useForm({
    type: 'maintenance',
    // Maintenance fields
    date: new Date().toISOString().split('T')[0],
    time: new Date().toTimeString().slice(0, 5),
    name: auth.user.fullname || '',
    designation: auth.user.job_title || '',
    divisionOffice: '',
    propertyDescription: '',
    brand: '',
    propertyNumber: '',
    serialNumber: '',
    lastRepairDate: '',
    defects: '',
    // Inspection fields
    item: '',
    propertyNo: '',
    receiptNo: '',
    acquisitionCost: '',
    acquisitionDate: '',
    complaints: '',
    scopeLastRepair: '',
});

function restrictNumericDash(e: Event, field: 'propertyNo' | 'receiptNo') {
    const input = e.target as HTMLInputElement;
    form[field] = input.value.replace(/[^0-9-]/g, '');
}

function formatPesoInput(e: Event) {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/[^0-9.]/g, '');
    if (value === '') { form.acquisitionCost = ''; return; }
    const parts = value.split('.');
    if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
    const num = parseFloat(value);
    if (!isNaN(num)) {
        form.acquisitionCost = '₱ ' + num.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}

function clearCost() { form.acquisitionCost = ''; }

function submit() {
    form.post('/ict-maintenance-inspection', {
        preserveScroll: true,
        onSuccess: () => {
            const message = form.type === 'maintenance' 
                ? 'ICT Maintenance request submitted.' 
                : 'ICT Equipment Inspection request submitted.';
            Swal.fire({ icon: 'success', title: 'Success!', text: message, timer: 2000, showConfirmButton: false });
            form.reset();
            form.type = 'maintenance';
        },
    });
}
</script>

<template>
    <Head title="ICT Maintenance & Inspection" />

    <div class="page-header">
        <h1>ICT Maintenance & Inspection Request Form</h1>
    </div>

    <div class="form-container">
        <form @submit.prevent="submit">
            <!-- Request Type Selection -->
            <div class="form-row mb-4">
                <div class="form-group">
                    <label style="font-weight: 600; margin-bottom: 1rem; display: block;">Select Request Type</label>
                    <div style="display: flex; gap: 2rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input v-model="form.type" type="radio" value="maintenance" />
                            <span>ICT Maintenance</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input v-model="form.type" type="radio" value="inspection" />
                            <span>ICT Equipment Inspection</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ICT Maintenance Section -->
            <div v-if="form.type === 'maintenance'">
                <h3 class="mb-3" style="font-size:15px;font-weight:600;">Section I</h3>
                <table class="form-table">
                    <tr>
                        <th>Date</th>
                        <td><input v-model="form.date" type="date" class="form-control" required /></td>
                    </tr>
                    <tr>
                        <th>Time</th>
                        <td><input v-model="form.time" type="time" class="form-control" required /></td>
                    </tr>
                    <tr>
                        <th>Requested by — Name</th>
                        <td><input v-model="form.name" type="text" class="form-control" required /></td>
                    </tr>
                    <tr>
                        <th>Designation</th>
                        <td><input v-model="form.designation" type="text" class="form-control" required /></td>
                    </tr>
                    <tr>
                        <th>Division Office</th>
                        <td>
                            <select v-model="form.divisionOffice" class="form-control" required>
                                <option value="">Select Office</option>
                                <option v-for="office in divisionOffices" :key="office" :value="office">{{ office }}</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <h3 class="mb-3 mt-4" style="font-size:15px;font-weight:600;">Section II</h3>
                <table class="form-table">
                    <tr>
                        <th>Description of Property/Equipment</th>
                        <td><input v-model="form.propertyDescription" type="text" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th>Brand</th>
                        <td><input v-model="form.brand" type="text" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th>Property No.</th>
                        <td><input v-model="form.propertyNumber" type="text" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th>Serial/Engine No.</th>
                        <td><input v-model="form.serialNumber" type="text" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th>Date of Last Repair</th>
                        <td><input v-model="form.lastRepairDate" type="date" class="form-control" /></td>
                    </tr>
                    <tr>
                        <th>Defects/Complaints</th>
                        <td><textarea v-model="form.defects" class="form-control" rows="4"></textarea></td>
                    </tr>
                </table>
            </div>

            <!-- ICT Equipment Inspection Section -->
            <div v-if="form.type === 'inspection'">
                <table class="form-table">
                    <thead>
                        <tr>
                            <th>Item/Description</th>
                            <th>Property Number</th>
                            <th>Property Acknowledgement Receipt No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input v-model="form.item" type="text" class="form-control" required /></td>
                            <td><input v-model="form.propertyNo" type="text" class="form-control" @input="restrictNumericDash($event, 'propertyNo')" /></td>
                            <td><input v-model="form.receiptNo" type="text" class="form-control" @input="restrictNumericDash($event, 'receiptNo')" /></td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-row mt-3">
                    <div class="form-group">
                        <label>Acquisition Cost</label>
                        <div class="peso-input-wrapper">
                            <span class="peso-sign">₱</span>
                            <input v-model="form.acquisitionCost" type="text" class="form-control" @input="formatPesoInput" placeholder="0.00" />
                            <button v-if="form.acquisitionCost" type="button" class="clear-btn" @click="clearCost">&times;</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Acquisition Date</label>
                        <input v-model="form.acquisitionDate" type="date" class="form-control" />
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="form-group">
                        <label>Complaints <span style="color:red">*</span></label>
                        <textarea v-model="form.complaints" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Nature and Scope of Last Repair, if any</label>
                        <textarea v-model="form.scopeLastRepair" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-apply" :disabled="form.processing">Submit Form</button>
            </div>
        </form>
    </div>
</template>
