<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import DepAideLayout from '@/layouts/DepAideLayout.vue';
import Swal from 'sweetalert2';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import { appPath } from '@/lib/basePath';

defineOptions({ layout: DepAideLayout });

const page = usePage();
const userName = computed(() => {
    const authUser = (page.props as any).auth?.user;

    if (!authUser) {
        return 'Admin';
    }

    return authUser.fullname || authUser.name || `${authUser.firstname ?? ''} ${authUser.lastname ?? ''}`.trim() || 'Admin';
});

const formatDate = (date: Date): string => date.toLocaleString('en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    second: '2-digit',
    hour12: true,
});

const currentDate = ref(formatDate(new Date()));
let timer: number | undefined;
const calendarVisible = ref(true);
const calendarEvents = ref<any[]>([]);
const selectedStatuses = ref<Record<string, boolean>>({
    Pending: true,
    'In Progress': true,
    Completed: true,
    Rejected: true,
});

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

const statusColors: Record<string, string> = {
    Pending: '#f9aa0b',
    'In Progress': '#0a72cf',
    Completed: '#358308',
    Rejected: '#ec160b',
};

const typeMap: Record<string, string> = {
    deped_email_request: 'DepEd Email Request',
    password_reset: 'Email Concern',
    email_management: 'Email Management',
    id_card_printing: 'ID Card Printing',
    ict_maintenance_inspection: 'ICT Maintenance & Inspection',
    documentation: 'Documentation',
    audio_visual_editing: 'Audio Visual Editing',
    software_development: 'Software Development',
};

const filteredCalendarEvents = computed(() => {
    return calendarEvents.value.filter((event) => selectedStatuses.value[event.extendedProps.stat]);
});

watch(filteredCalendarEvents, (events) => {
    calendarOptions.value.events = events;
}, { immediate: true });

onMounted(() => {
    timer = window.setInterval(() => {
        currentDate.value = formatDate(new Date());
    }, 1000);

    fetchCalendarEvents();
});

onUnmounted(() => {
    if (timer !== undefined) {
        window.clearInterval(timer);
    }
});

async function fetchCalendarEvents() {
    try {
        const res = await fetch(appPath('/api/calendar-events'));
        const data = await res.json();

        calendarEvents.value = data.map((event: any) => ({
            title: typeMap[event.request_type_table] || event.request_type_table,
            start: event.start,
            backgroundColor: statusColors[event.stat] || '#6c757d',
            borderColor: statusColors[event.stat] || '#6c757d',
            textColor: '#ffffff',
            extendedProps: {
                request_id: event.request_id,
                stat: event.stat,
                remarks: event.remarks ?? '',
            },
        }));
    } catch (error) {
        // ignore fetch errors
    }
}

function toggleCalendar() {
    calendarVisible.value = !calendarVisible.value;
}

function toggleStatus(status: string) {
    selectedStatuses.value[status] = !selectedStatuses.value[status];
}

function handleEventClick(info: any) {
    const event = info.event;
    const ep = event.extendedProps;
    const dt = event.start ? new Date(event.start) : null;
    const formattedDate = dt ? `${dt.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })} • ${dt.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}` : 'N/A';

    const statusColor = statusColors[ep.stat] || '#6c757d';

    Swal.fire({
        title: `<i class="fas fa-calendar-alt"></i> ${event.title}`,
        html: `
            <div style="text-align:left;font-size:14px;line-height:1.8;">
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
            window.location.href = appPath(`/status?request_id=${ep.request_id}`);
        }
    });
}

const props = defineProps<{
    stats: { total: number; pending: number; inProgress: number; completed: number; rejected: number };
    recentRequests: Array<{ request_id: number; user_name: string; mapped_type: string; stat: string; created_at: string }>;
    typeCards: Array<{ key: string; label: string; count: number; pending: number; inProgress: number; url: string }>;
    monthlyTotals: Array<{ month: number; total: number }>;
}>();

const chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

const monthlyData = computed(() => {
    const data = Array.from({ length: 12 }, () => 0);

    for (const row of props.monthlyTotals) {
        if (row.month >= 1 && row.month <= 12) {
            data[row.month - 1] = row.total;
        }
    }

    return data;
});

const chartMax = computed(() => Math.max(...monthlyData.value, 1));
const chartSteps = 4;
const chartPaddingLeft = 48;
const chartPaddingTop = 24;
const chartWidth = 520;
const chartHeight = 160;

const chartPoints = computed(() => monthlyData.value
    .map((value, index) => {
        const x = chartPaddingLeft + index * 44;
        const y = chartPaddingTop + chartHeight - (value / chartMax.value) * chartHeight;
        return `${x},${y}`;
    })
    .join(' ')
);

const chartAreaPath = computed(() => {
    const points = monthlyData.value.map((value, index) => {
        const x = chartPaddingLeft + index * 44;
        const y = chartPaddingTop + chartHeight - (value / chartMax.value) * chartHeight;
        return `${x},${y}`;
    });

    if (!points.length) {
        return '';
    }

    return `M${points[0]} L${points.slice(1).join(' ')} L${chartPaddingLeft + (points.length - 1) * 44},${chartPaddingTop + chartHeight} L${chartPaddingLeft},${chartPaddingTop + chartHeight} Z`;
});

const yAxisTicks = computed(() => Array.from({ length: chartSteps + 1 }, (_, index) => {
    const percent = index / chartSteps;
    const value = Math.round(chartMax.value - percent * chartMax.value);
    const y = chartPaddingTop + percent * chartHeight;
    return { value, y };
}));

const hoveredPoint = ref<number | null>(null);
const tooltipMonth = computed(() => hoveredPoint.value !== null ? chartMonths[hoveredPoint.value] : '');
const tooltipTotal = computed(() => hoveredPoint.value !== null ? monthlyData.value[hoveredPoint.value] : 0);
const tooltipX = computed(() => hoveredPoint.value !== null ? chartPaddingLeft + hoveredPoint.value * 44 : 0);
const tooltipY = computed(() => hoveredPoint.value !== null ? chartPaddingTop + chartHeight - (monthlyData.value[hoveredPoint.value] / chartMax.value) * chartHeight : 0);
const tooltipBoxX = computed(() => {
    if (hoveredPoint.value === null) {
        return 0;
    }

    const x = tooltipX.value - 42;
    return Math.max(chartPaddingLeft + 8, Math.min(x, 600 - 92));
});
const tooltipBoxY = computed(() => {
    if (hoveredPoint.value === null) {
        return 0;
    }

    const top = tooltipY.value - 52;
    if (top < chartPaddingTop + 6) {
        return tooltipY.value + 12;
    }

    return Math.min(top, chartPaddingTop + chartHeight - 50);
});

const statCards = [
    {
        label: 'Pending',
        value: props.stats.pending,
        subtitle: `Total Requests: ${props.stats.total}`,
        icon: 'fas fa-clock',
        color: '#f9aa0b',
    },
    { label: 'In Progress', value: props.stats.inProgress, icon: 'fas fa-spinner', color: '#0a72cf' },
    { label: 'Completed', value: props.stats.completed, icon: 'fas fa-check-circle', color: '#358308' },
    { label: 'Rejected', value: props.stats.rejected, icon: 'fas fa-times-circle', color: '#ec160b' },
];

const statusClass: Record<string, string> = {
    'Pending': 'status-text-pending', 'In Progress': 'status-text-in-progress',
    'Completed': 'status-text-completed', 'Rejected': 'status-text-rejected',
};

const typeCardIcons: Record<string, string> = {
    deped_email_request: 'fas fa-envelope',
    password_reset: 'fas fa-key',
    id_card_printing: 'fas fa-id-card',
    ict_maintenance_inspection: 'fas fa-tools',
    documentation: 'fas fa-book',
    audio_visual_editing: 'fas fa-video',
    software_development: 'fas fa-code',
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="page-header"><h1>Admin Dashboard</h1></div>

    <div class="admin-dashboard-banner">
        <div class="admin-dashboard-banner-content">
            <div>
                <div class="banner-title">Welcome {{ userName }}</div>
                <div class="banner-subtitle">{{ currentDate }}</div>
            </div>
            <div class="banner-right">
                <div class="banner-total-requests">
                    <span class="total-requests-label">Total Requests:</span>
                    <span class="total-requests-value">{{ props.stats.total }}</span>
                </div>
                <div class="banner-statuses">
                    <div class="status-icon-item">
                        <i class="fas fa-clock status-icon pending"></i>
                        <div class="status-info">
                            <div class="status-label">Pending</div>
                            <div class="status-value">{{ props.stats.pending }}</div>
                        </div>
                    </div>
                    <div class="status-icon-item">
                        <i class="fas fa-spinner status-icon in-progress"></i>
                        <div class="status-info">
                            <div class="status-label">In Progress</div>
                            <div class="status-value">{{ props.stats.inProgress }}</div>
                        </div>
                    </div>
                    <div class="status-icon-item">
                        <i class="fas fa-check-circle status-icon completed"></i>
                        <div class="status-info">
                            <div class="status-label">Completed</div>
                            <div class="status-value">{{ props.stats.completed }}</div>
                        </div>
                    </div>
                    <div class="status-icon-item">
                        <i class="fas fa-times-circle status-icon rejected"></i>
                        <div class="status-info">
                            <div class="status-label">Rejected</div>
                            <div class="status-value">{{ props.stats.rejected }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="type-cards">
        <Link v-for="card in props.typeCards" :key="card.key" :href="card.url" class="type-card">
            <div class="type-card-icon"><i :class="typeCardIcons[card.key] || 'fas fa-list'" /></div>
            <div>
                <div class="type-card-title">{{ card.label }}</div>
                <div class="type-card-stats">
                    <div class="stat-item">
                        <i class="fas fa-clock" style="color: #f9aa0b;"></i>
                        <span>{{ card.pending }}</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-spinner" style="color: #0a72cf;"></i>
                        <span>{{ card.inProgress }}</span>
                    </div>
                </div>
            </div>
            <div class="type-card-link">
                View {{ card.label }}
                <i class="fas fa-arrow-right"></i>
            </div>
        </Link>
    </div>

    <div class="dashboard-chart-card">
        <div class="dashboard-chart-header">
            <div>
                <div class="dashboard-chart-title">Monthly Request Trend</div>
                <div class="dashboard-chart-subtitle">Requests by month this year</div>
            </div>
            <div class="dashboard-chart-status-pill">Total Requests: {{ props.stats.total }}</div>
        </div>
        <div class="dashboard-chart-graph">
            <svg viewBox="0 0 600 240" preserveAspectRatio="none">
                <g>
                    <rect x="0" y="0" width="100%" height="100%" fill="transparent" />
                    <g class="chart-grid">
                        <line v-for="tick in yAxisTicks" :key="tick.y" :x1="chartPaddingLeft" :y1="tick.y" x2="600" :y2="tick.y" stroke="rgba(0,0,0,0.08)" stroke-width="1" />
                    </g>
                    <g class="chart-y-axis-labels">
                        <text v-for="tick in yAxisTicks" :key="tick.y" :x="chartPaddingLeft - 12" :y="tick.y + 4" text-anchor="end" fill="#6c757d" font-size="11">{{ tick.value }}</text>
                    </g>
                    <line :x1="chartPaddingLeft" :y1="chartPaddingTop + chartHeight" x2="600" :y2="chartPaddingTop + chartHeight" stroke="#dfe3e8" stroke-width="1.5" />
                </g>
                <path :d="chartAreaPath" fill="url(#chartGradient)" opacity="0.9" />
                <polyline :points="chartPoints" fill="none" stroke="url(#chartLineGradient)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                <defs>
                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#d7e8ff" stop-opacity="0.65" />
                        <stop offset="100%" stop-color="#ffffff" stop-opacity="0" />
                    </linearGradient>
                    <linearGradient id="chartLineGradient" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#3b82f6" />
                        <stop offset="100%" stop-color="#0b69d9" />
                    </linearGradient>
                </defs>
                <g v-if="hoveredPoint !== null">
                    <circle :cx="tooltipX" :cy="tooltipY" r="8" fill="rgba(0, 0, 0, 0.1)" />
                    <line
                        :x1="tooltipX"
                        :y1="chartPaddingTop"
                        :x2="tooltipX"
                        :y2="chartPaddingTop + chartHeight"
                        class="chart-hover-line"
                    />
                </g>
                <g v-for="(value, index) in monthlyData" :key="index">
                    <circle
                        :cx="chartPaddingLeft + index * 44"
                        :cy="chartPaddingTop + chartHeight - (value / chartMax) * chartHeight"
                        r="4"
                        fill="#ffffff"
                        stroke="#0a72cf"
                        stroke-width="2"
                        @mouseover="hoveredPoint = index"
                        @mouseleave="hoveredPoint = null"
                        class="chart-point"
                    />
                </g>

                <g v-if="hoveredPoint !== null">
                    <rect :x="tooltipBoxX" :y="tooltipBoxY" width="84" height="44" rx="12" fill="rgba(0, 0, 0, 0.78)" />
                    <text :x="tooltipBoxX + 42" :y="tooltipBoxY + 20" text-anchor="middle" fill="#ffffff" font-size="12" font-weight="700">{{ tooltipTotal }} requests</text>
                    <text :x="tooltipBoxX + 42" :y="tooltipBoxY + 34" text-anchor="middle" fill="#cbd5e1" font-size="10">{{ tooltipMonth }}</text>
                </g>
            </svg>
        </div>
        <div class="dashboard-chart-labels">
            <span v-for="month in chartMonths" :key="month">{{ month }}</span>
        </div>
    </div>

    <div class="calendar-section">
        <div class="calendar-header">
            <button class="btn btn-primary btn-sm" @click="toggleCalendar" style="position: absolute; left: 0;">
                <i :class="calendarVisible ? 'fas fa-compress' : 'fas fa-expand'"></i>
                {{ calendarVisible ? 'Minimize' : 'Enlarge' }}
            </button>
            <h2>Request Status Calendar</h2>
        </div>
        <div class="calendar-filters">
            <button
                v-for="status in Object.keys(statusColors)"
                :key="status"
                type="button"
                class="calendar-filter-button"
                :class="{ active: selectedStatuses[status] }"
                :style="{ borderColor: statusColors[status], color: selectedStatuses[status] ? '#ffffff' : statusColors[status], backgroundColor: selectedStatuses[status] ? statusColors[status] : 'transparent' }"
                @click="toggleStatus(status)">
                {{ status }}
            </button>
        </div>
        <div v-if="calendarVisible">
            <p class="calendar-note">Click on the event to see details.</p>
            <FullCalendar :options="calendarOptions" />
        </div>
    </div>

    <div class="data-table-wrapper">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:12px;">Recent Requests</h3>
        <table class="data-table">
            <thead><tr><th>ID</th><th>User</th><th>Type</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                <tr v-for="req in props.recentRequests" :key="req.request_id">
                    <td>{{ req.request_id }}</td>
                    <td>{{ req.user_name }}</td>
                    <td>{{ req.mapped_type }}</td>
                    <td><span :class="statusClass[req.stat]">{{ req.stat }}</span></td>
                    <td>{{ req.created_at }}</td>
                </tr>
            </tbody>
        </table>
        <div class="mt-3"><Link :href="appPath('/admin/requests')" class="btn btn-primary btn-sm">View All Requests</Link></div>
    </div>
</template>
