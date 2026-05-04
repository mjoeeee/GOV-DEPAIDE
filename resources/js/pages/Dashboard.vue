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

    <div class="dashboard-wrapper">
        <div class="dashboard-title">Dashboard</div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <!-- 1. DepEd Email Creation -->
            <div class="card">
                <div class="card-header-icon">
                    <div class="card-icon"><i class="fas fa-envelope"></i></div>
                    <div class="card-title">DepEd Email Creation</div>
                </div>
                <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 8px;">
                    <span v-if="deped_email_already_requested" class="status-text-completed">Already Requested</span>
                    <span v-else>Not yet requested</span>
                </div>
                <div class="card-actions">
                    <Link href="/deped-email" class="btn btn-outline-primary btn-sm">
                        Request
                    </Link>
                    <Link href="/status?type=DepEd Email Request" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-list"></i> view status
                    </Link>
                </div>
            </div>

            <!-- 2. DepEd Email Concern -->
            <div class="card">
                <div class="card-header-icon">
                    <div class="card-icon"><i class="fas fa-key"></i></div>
                    <div class="card-title">DepEd Email Concern</div>
                </div>
                <div class="card-actions">
                    <Link href="/email-concern" class="btn btn-outline-primary btn-sm">
                        Request
                    </Link>
                    <Link :href="`/status?type=Email Concern`" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-list"></i> Total: {{ email_concern_count }}
                    </Link>
                </div>
            </div>

            <!-- 3. DCP Maintenance -->
            <div class="card">
                <div class="card-header-icon">
                    <div class="card-icon"><i class="fas fa-tools"></i></div>
                    <div class="card-title">DCP Maintenance</div>
                </div>
                <div class="card-actions">
                    <Link href="/ict-maintenance" class="btn btn-outline-primary btn-sm">
                        Request
                    </Link>
                    <Link :href="`/status?type=ICT Maintenance`" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-list"></i> Total: {{ ict_maintenance_count }}
                    </Link>
                </div>
            </div>

            <!-- 4. ICT Assistance -->
            <div class="card">
                <div class="card-header-icon">
                    <div class="card-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="card-title">ICT Assistance</div>
                </div>
                <div class="card-actions">
                    <Link href="#" class="btn btn-outline-primary btn-sm">
                        Request
                    </Link>
                    <Link href="#" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-list"></i> Total: 0
                    </Link>
                </div>
            </div>

            <!-- 5. DCP Equipment Inspection -->
            <div class="card">
                <div class="card-header-icon">
                    <div class="card-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div class="card-title">DCP Equipment Inspection</div>
                </div>
                <div class="card-actions">
                    <Link href="/inspection-form" class="btn btn-outline-primary btn-sm">
                        Request
                    </Link>
                    <Link :href="`/status?type=ICT Equipment Inspection`" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-list"></i> Total: {{ inspection_count }}
                    </Link>
                </div>
            </div>

            <!-- 6. Digital Production -->
            <div class="card">
                <div class="card-header-icon">
                    <div class="card-icon"><i class="fas fa-video"></i></div>
                    <div class="card-title">Digital Production</div>
                </div>
                <div class="card-actions">
                    <Link href="#" class="btn btn-outline-primary btn-sm">
                        Request
                    </Link>
                    <Link href="#" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-list"></i> Total: 0
                    </Link>
                </div>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="calendar-section">
            <div class="calendar-header">
                <button class="btn btn-primary btn-sm" @click="toggleCalendar" style="position: absolute; left: 0;">
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
    </div>
</template>
