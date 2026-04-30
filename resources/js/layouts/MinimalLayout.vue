<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <nav class="minimal-navbar">
            <Link href="/dashboard">
                <img src="/images/logo.png" alt="DepEd Logo" class="navbar-logo" />
            </Link>
        </nav>

        <div class="minimal-content">
            <div class="breadcrumb">
                <Link href="/status">Go back</Link>
                <span>/</span>
                <span>Request Form</span>
            </div>

            <slot />
        </div>
    </div>
</template>
