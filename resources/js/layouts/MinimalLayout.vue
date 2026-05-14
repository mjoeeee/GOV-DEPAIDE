<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { appPath, assetPath } from '@/lib/basePath';

function goBack() {
    window.history.back();
}
</script>

<template>
    <div class="minimal-layout">
        <nav class="minimal-navbar">
            <button class="back-button" @click="goBack" title="Go Back">
                <i class="fas fa-arrow-left"></i>
            </button>
            <Link :href="appPath('/dashboard')" class="navbar-logo-link">
                <img :src="assetPath('/images/logo.png')" alt="DepEd Logo" class="navbar-logo" />
            </Link>
            <div class="navbar-spacer"></div>
        </nav>

        <div class="minimal-content">
            <slot />
        </div>
    </div>
</template>
