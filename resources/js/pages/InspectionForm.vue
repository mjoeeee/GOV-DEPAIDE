<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';

defineOptions({ layout: DepAideLayout });

const form = useForm({
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
    form.post('/inspection-form', {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Success!', text: 'Inspection request submitted.', timer: 2000, showConfirmButton: false });
            form.reset();
        },
    });
}
</script>

<template>
    <Head title="ICT Equipment Inspection" />

    <div class="page-header">
        <h1>ICT Equipment Inspection Form</h1>
    </div>

    <div class="form-container">
        <form @submit.prevent="submit">
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

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-apply" :disabled="form.processing">Submit Form</button>
            </div>
        </form>
    </div>
</template>
