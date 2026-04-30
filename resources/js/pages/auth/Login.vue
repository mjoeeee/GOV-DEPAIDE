<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
});

const showAnimatedText = ref(false);

setTimeout(() => {
    if (window.innerWidth > 768) {
        showAnimatedText.value = true;
    }
}, 500);

function submit() {
    form.post('/login', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Login" />

    <div class="login-page">
        <div class="login-body">
            <div v-if="showAnimatedText" class="login-animated-text">
                Department of Education Portal for Assisting
                <span class="highlight">ICT Diagnosis</span> and
                <span class="highlight">Enhancement</span>
            </div>

            <div class="login-card">
                <img src="/images/deped-ozamiz-2.png" alt="DepEd Ozamiz Logo" class="login-logo" />
                <h2>Log in to your account</h2>

                <div v-if="form.errors.email" class="login-error">
                    {{ form.errors.email }}
                </div>

                <form @submit.prevent="submit">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="form-control"
                            placeholder="Enter your email"
                            required
                            autofocus
                        />
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="form-control"
                            placeholder="Enter your password"
                            required
                        />
                    </div>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing">Logging in...</span>
                        <span v-else>Log in</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
