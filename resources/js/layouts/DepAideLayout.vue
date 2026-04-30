<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const page = usePage();
const auth = computed(() => (page.props as any).auth);
const user = computed(() => auth.value?.user);
const currentUrl = computed(() => (page as any).url);

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
            router.post('/logout');
        }
    });
}

onMounted(async () => {
    try {
        const response = await fetch('/api/check-unrated');
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
            <Link href="/dashboard">
                <img src="/images/logo.png" alt="DepEd Logo" class="navbar-logo" />
            </Link>
            <button class="burger-menu" @click="toggleSidebar" aria-label="Toggle sidebar">
                <div></div>
                <div></div>
                <div></div>
            </button>
        </nav>

        <!-- Sidebar -->
        <aside class="sidebar" :class="{ active: sidebarOpen }" id="sidebar">
            <div class="sidebar-profile">
                <i class="fas fa-user-circle"></i>
                <div class="user-name">{{ user?.fullname }}</div>
            </div>

            <nav class="sidebar-nav">
                <Link href="/dashboard" :class="{ active: isActive('/dashboard') }">
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
                    <Link href="/deped-email" :class="{ active: isActive('/deped-email') }">
                        <i class="fas fa-envelope"></i> DepEd Email
                    </Link>
                    <Link href="/email-concern" :class="{ active: isActive('/email-concern') }">
                        <i class="fas fa-key"></i> Email Concern
                    </Link>
                    <Link href="/ict-maintenance" :class="{ active: isActive('/ict-maintenance') }">
                        <i class="fas fa-desktop"></i> ICT Maintenance
                    </Link>
                    <Link href="/inspection-form" :class="{ active: isActive('/inspection-form') }">
                        <i class="fas fa-search"></i> ICT Equipment Inspection
                    </Link>
                    <!-- Hidden Modules
                    <Link href="/software-development"><i class="fas fa-code"></i> Software Development</Link>
                    <Link href="/documentation"><i class="fas fa-book"></i> Documentation</Link>
                    <Link href="/audio-visual"><i class="fas fa-video"></i> Audio Visual Editing</Link>
                    <Link href="/id-card"><i class="fas fa-id-card"></i> ID Card Printing</Link>
                    -->
                </div>

                <Link href="/status" :class="{ active: isActive('/status') }">
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
