<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{
    ict_maintenance_count: number;
    inspection_count: number;
    email_concern_count: number;
    deped_email_count: number;
    deped_email_already_requested: boolean;
}>();

const page = usePage();
const auth = (page.props as any).auth;
const calendarVisible = ref(true);
const calendarEvents = ref<any[]>([]);

const statusColors: Record<string, string> = {
    'Pending': '#f9aa0b',
    'In Progress': '#0a72cf',
    'Completed': '#358308',
    'Rejected': '#ec160b',
};

const typeMap: Record<string, string> = {
    'ict_maintenance': 'ICT Maintenance',
    'software_development': 'Software Development',
    'ict_equipment_inspection': 'ICT Equipment Inspection',
    'documentation': 'Documentation',
    'audio_visual_editing': 'Audio Visual Editing',
    'deped_email_request': 'DepEd Email Request',
    'password_reset': 'Email Concern',
    'id_card_printing': 'ID Card Printing',
};

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    },
    events: [] as any[],
    eventClick: handleEventClick,
});

function toggleCalendar() {
    calendarVisible.value = !calendarVisible.value;
}

function handleEventClick(info: any) {
    const event = info.event;
    const ep = event.extendedProps;
    const dt = new Date(event.start);
    const formattedDate = dt.toLocaleDateString('en-US', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
    }) + ' • ' + dt.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

    const statusColor = statusColors[ep.stat] || '#6c757d';

    Swal.fire({
        title: `<i class="fas fa-calendar-alt"></i> ${event.title}`,
        html: `
            <div style="text-align:left;font-size:14px;line-height:2;">
                <strong>Request ID:</strong> ${ep.request_id}<br>
                <strong>Status:</strong> <span style="color:${statusColor};font-weight:700;">${ep.stat}</span><br>
                <strong>Remarks:</strong> ${ep.remarks || 'N/A'}<br>
                <strong>Date Requested:</strong> ${formattedDate}
            </div>
        `,
        confirmButtonText: '<i class="fas fa-eye"></i> View',
        confirmButtonColor: '#007bff',
        showCloseButton: true,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `/status?request_id=${ep.request_id}`;
        }
    });
}

onMounted(async () => {
    try {
        const res = await fetch(`/api/calendar-events`);
        const data = await res.json();
        calendarOptions.value.events = data.map((e: any) => ({
            title: typeMap[e.request_type_table] || e.request_type_table,
            start: e.start,
            backgroundColor: statusColors[e.stat] || '#6c757d',
            borderColor: statusColors[e.stat] || '#6c757d',
            textColor: '#ffffff',
            extendedProps: {
                request_id: e.request_id,
                stat: e.stat,
                remarks: e.remarks,
            },
        }));
    } catch (e) { /* */ }
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="page-header">
        <h1>Dashboard</h1>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card">
            <div class="card-icon"><i class="fas fa-tools"></i></div>
            <div class="card-title">ICT Maintenance</div>
            <div class="card-actions">
                <Link href="/ict-maintenance" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Request
                </Link>
                <Link :href="`/status?type=ICT Maintenance`" class="custom-btn">
                    <i class="fas fa-list-check"></i> Total: {{ ict_maintenance_count }}
                </Link>
            </div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-clipboard-check"></i></div>
            <div class="card-title">ICT Equipment Inspection</div>
            <div class="card-actions">
                <Link href="/inspection-form" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Request
                </Link>
                <Link :href="`/status?type=ICT Equipment Inspection`" class="custom-btn">
                    <i class="fas fa-list-check"></i> Total: {{ inspection_count }}
                </Link>
            </div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-key"></i></div>
            <div class="card-title">Email Concern</div>
            <div class="card-actions">
                <Link href="/email-concern" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Request
                </Link>
                <Link :href="`/status?type=Email Concern`" class="custom-btn">
                    <i class="fas fa-list-check"></i> Total: {{ email_concern_count }}
                </Link>
            </div>
        </div>

        <div class="card">
            <div class="card-icon"><i class="fas fa-envelope"></i></div>
            <div class="card-title">DepEd Email Request</div>
            <div class="card-actions">
                <span v-if="deped_email_already_requested" class="badge badge-completed">Already Requested</span>
                <span v-else class="badge badge-pending">Not yet requested</span>
                <Link href="/status?type=DepEd Email" class="custom-btn ml-2">
                    <i class="fas fa-eye"></i> View Status
                </Link>
            </div>
        </div>

        <!-- Hidden Module Cards
        <div class="card">
            <div class="card-icon"><i class="fas fa-code"></i></div>
            <div class="card-title">Software Development</div>
        </div>
        <div class="card">
            <div class="card-icon"><i class="fas fa-book"></i></div>
            <div class="card-title">Documentation</div>
        </div>
        <div class="card">
            <div class="card-icon"><i class="fas fa-video"></i></div>
            <div class="card-title">Audio Visual Editing</div>
        </div>
        <div class="card">
            <div class="card-icon"><i class="fas fa-id-card"></i></div>
            <div class="card-title">ID Card Printing</div>
        </div>
        -->
    </div>

    <!-- Calendar Section -->
    <div class="calendar-section">
        <div class="calendar-header">
            <button class="btn btn-primary btn-sm" @click="toggleCalendar">
                <i :class="calendarVisible ? 'fas fa-compress' : 'fas fa-expand'"></i>
                {{ calendarVisible ? 'Minimize' : 'Enlarge' }}
            </button>
            <h2>Request Status Calendar</h2>
        </div>
        <div v-if="calendarVisible">
            <p class="calendar-note">Click on the event to see details.</p>
            <FullCalendar :options="calendarOptions" />
        </div>
    </div>
</template>
