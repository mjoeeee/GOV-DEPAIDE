<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { appPath, assetPath, stripBasePath } from '@/lib/basePath';

const page = usePage();
const auth = computed(() => (page.props as any).auth);
const user = computed(() => auth.value?.user);
const adminRequestTypeCounts = computed(() => (page.props as any).adminRequestTypeCounts ?? {});
const currentUrl = computed(() => stripBasePath((page as any).url));
const normalizedRole = computed(() =>
    String(user.value?.role ?? '')
        .trim()
        .toLowerCase(),
);
const isAdminUser = computed(() => ['admin', 'system admin'].includes(normalizedRole.value));
const dashboardLink = computed(() => appPath(isAdminUser.value ? '/admin/dashboard' : '/dashboard'));

const requestTypeSidebarItems = computed(() => [
    {
        key: 'ict_maintenance_inspection',
        label: 'ICT Maintenance & Inspection',
        icon: 'fas fa-tools',
        href: appPath('/admin/requests?request_type_table=ict_maintenance_inspection'),
    },
    {
        key: 'software_development',
        label: 'Software Development',
        icon: 'fas fa-code',
        href: appPath('/admin/requests?request_type_table=software_development'),
    },
    {
        key: 'documentation',
        label: 'Documentation',
        icon: 'fas fa-book',
        href: appPath('/admin/requests?request_type_table=documentation'),
    },
    {
        key: 'audio_visual_editing',
        label: 'Audio Visual Editing',
        icon: 'fas fa-video',
        href: appPath('/admin/requests?request_type_table=audio_visual_editing'),
    },
    {
        key: 'email_management',
        label: 'Email Management',
        icon: 'fas fa-envelope',
        href: appPath('/admin/requests?request_type_table=email_management'),
    },
    {
        key: 'id_card_printing',
        label: 'ID Card Printing',
        icon: 'fas fa-id-card',
        href: appPath('/admin/requests?request_type_table=id_card_printing'),
    },
]);

const sidebarOpen = ref(false);
const dropdownOpen = ref(false);

function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value;
}

function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value;
}

function isActive(path: string): boolean {
    return currentUrl.value.startsWith(path);
}

function handleLogout() {
    Swal.fire({
        title: 'Are you sure you want to logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(appPath('/logout'));
        }
    });
}

onMounted(async () => {
    try {
        const response = await fetch(appPath('/api/check-unrated'));
        const data = await response.json();
        if (data.has_unrated) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'You have unrated completed requests. Please rate them.',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });
        }
    } catch (e) {
        // silently fail
    }
});

const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="layout-wrapper" :class="{ 'sidebar-open': sidebarOpen }">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="navbar-brand">
                <Link :href="dashboardLink" class="navbar-brand-link">
                    <img :src="assetPath('/images/deped-ozamiz-2.png')" alt="DepEd Logo" class="navbar-logo" />
                </Link>
                <Link :href="dashboardLink" class="navbar-brand-link">
                    <img :src="assetPath('/images/depaide-logo.png')" alt="DepAIDE Logo" class="navbar-secondary-logo" />
                </Link>
            </div>
            <button class="burger-menu" @click="toggleSidebar" aria-label="Toggle sidebar">
                <div></div>
                <div></div>
                <div></div>
            </button>
        </nav>

        <!-- Sidebar -->
        <aside class="sidebar" :class="{ active: sidebarOpen }" id="sidebar">
            <div class="sidebar-profile">
                <div class="profile-pill">
                    <i class="fas fa-user-circle"></i>
                    <div class="user-name">{{ user?.fullname }}</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <Link :href="dashboardLink" :class="{ active: isActive('/dashboard') || isActive('/admin/dashboard') }">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </Link>

                <!-- Request Forms Dropdown -->
                <button class="nav-link dropdown-toggle" :class="{ open: dropdownOpen }" @click="toggleDropdown">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt"></i>
                        <span>Request Forms</span>
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </button>

                <div class="dropdown-content" :class="{ open: dropdownOpen }">
                    <Link :href="appPath('/email-management')" :class="{ active: isActive('/email-management') }">
                        <i class="fas fa-envelope"></i> Email Management
                    </Link>
                    <Link :href="appPath('/ict-maintenance-inspection')" :class="{ active: isActive('/ict-maintenance') || isActive('/ict-maintenance-inspection') || isActive('/inspection-form') }">
                        <i class="fas fa-tools"></i> ICT Maintenance & Inspection
                    </Link>
                    <Link :href="appPath('/documentation')" :class="{ active: isActive('/documentation') }">
                        <i class="fas fa-file-alt"></i> Documentation
                    </Link>
                    <Link :href="appPath('/audio-visual')" :class="{ active: isActive('/audio-visual') }">
                        <i class="fas fa-video"></i> Audio Visual Editing
                    </Link>
                    <Link :href="appPath('/software-request')" :class="{ active: isActive('/software-request') }">
                        <i class="fas fa-code"></i> Software Development
                    </Link>
                    <Link :href="appPath('/id-card-printing')" :class="{ active: isActive('/id-card-printing') }">
                        <i class="fas fa-id-card"></i> ID Card Printing
                    </Link>
                </div>

                <div v-if="isAdminUser" class="sidebar-section">
                    <div class="sidebar-section-title">Pending Requests</div>
                    <div class="sidebar-badge-list">
                        <Link
                            v-for="item in requestTypeSidebarItems"
                            :key="item.key"
                            :href="item.href"
                            class="sidebar-badge-link"
                        >
                            <span class="sidebar-badge-icon"><i :class="item.icon"></i></span>
                            <span class="sidebar-badge-label">{{ item.label }}</span>
                            <span class="sidebar-badge-count">{{ adminRequestTypeCounts[item.key] ?? 0 }}</span>
                        </Link>
                    </div>
                </div>

                <Link :href="appPath('/status')" :class="{ active: isActive('/status') }">
                    <i class="fas fa-info-circle"></i>
                    <span>Status</span>
                </Link>

                <button class="nav-link" @click="handleLogout">
                    <i class="fas fa-power-off"></i>
                    <span>Logout</span>
                </button>
            </nav>

            <div class="app-footer">
                &copy; {{ currentYear }} DepAIDE. All Rights Reserved. | DepEdOzamiz ICT Services
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <slot />
        </main>
    </div>
</template>
