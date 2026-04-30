<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import DepAideLayout from '@/layouts/DepAideLayout.vue';

defineOptions({ layout: DepAideLayout });

const props = defineProps<{
    stats: { total: number; pending: number; inProgress: number; completed: number; rejected: number };
    recentRequests: Array<{ request_id: number; user_name: string; mapped_type: string; stat: string; created_at: string }>;
}>();

const statCards = [
    { label: 'Total Requests', value: props.stats.total, icon: 'fas fa-clipboard-list', color: '#007bff' },
    { label: 'Pending', value: props.stats.pending, icon: 'fas fa-clock', color: '#f9aa0b' },
    { label: 'In Progress', value: props.stats.inProgress, icon: 'fas fa-spinner', color: '#0a72cf' },
    { label: 'Completed', value: props.stats.completed, icon: 'fas fa-check-circle', color: '#358308' },
    { label: 'Rejected', value: props.stats.rejected, icon: 'fas fa-times-circle', color: '#ec160b' },
];

const statusClass: Record<string, string> = {
    'Pending': 'status-text-pending', 'In Progress': 'status-text-in-progress',
    'Completed': 'status-text-completed', 'Rejected': 'status-text-rejected',
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="page-header"><h1>Admin Dashboard</h1></div>

    <div class="summary-cards">
        <div v-for="card in statCards" :key="card.label" class="card">
            <div class="card-icon"><i :class="card.icon" :style="{ color: card.color }"></i></div>
            <div class="card-title">{{ card.label }}</div>
            <div style="font-size:28px;font-weight:700;color:var(--text-dark);">{{ card.value }}</div>
        </div>
    </div>

    <div class="data-table-wrapper">
        <h3 style="font-size:16px;font-weight:600;margin-bottom:12px;">Recent Requests</h3>
        <table class="data-table">
            <thead><tr><th>ID</th><th>User</th><th>Type</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                <tr v-for="req in recentRequests" :key="req.request_id">
                    <td>{{ req.request_id }}</td>
                    <td>{{ req.user_name }}</td>
                    <td>{{ req.mapped_type }}</td>
                    <td><span :class="statusClass[req.stat]">{{ req.stat }}</span></td>
                    <td>{{ req.created_at }}</td>
                </tr>
            </tbody>
        </table>
        <div class="mt-3"><Link href="/admin/requests" class="btn btn-primary btn-sm">View All Requests</Link></div>
    </div>
</template>
