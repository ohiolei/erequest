<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});
const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Student & Staff Login - TASFUED"/>

    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-emerald-500 selection:text-white relative flex flex-col justify-center items-center px-6 py-12 overflow-hidden">
        
        <!-- Visual Accent Background Blurs -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10">
            <!-- Brand Logo Header -->
            <div class="text-center mb-8">
                <Link class="inline-flex items-center gap-3 group" href="/">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center font-bold text-slate-950 shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                        T
                    </div>
                    <div class="text-left">
                        <span class="text-2xl font-bold tracking-tight text-white block leading-none">TASFUED</span>
                        <span class="text-[10px] tracking-widest uppercase text-emerald-400 font-semibold">PORTAL LOGIN</span>
                    </div>
                </Link>
            </div>

            <!-- Login Form Card -->
            <div class="p-8 rounded-2xl bg-slate-900/60 border border-slate-800/80 shadow-2xl backdrop-blur-md">
                <div v-if="status" class="mb-4 text-xs font-medium text-emerald-400 bg-emerald-500/10 p-3 rounded-lg border border-emerald-500/20">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">
                            Matric No. / Email Address
                        </label>
                        <input
                            id="email"
                            type="text"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Matric number or email address"
                            class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm"
                        />
                        <div v-if="form.errors.email" class="text-rose-400 text-xs mt-1 font-medium">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">
                                Password
                            </label>
                            <Link :href="route('password.request')" class="text-xs text-emerald-400 hover:text-emerald-300 transition-colors" v-if="canResetPassword">
                                Forgot password?
                            </Link>
                        </div>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-4 pr-12 py-3 rounded-xl bg-slate-950 border border-slate-800 text-slate-100 placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-sm"
                            />
                            <button
                                type="button"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                :title="showPassword ? 'Hide password' : 'Show password'"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 hover:text-emerald-400 focus:outline-none focus:text-emerald-400"
                                @click="showPassword = !showPassword"
                            >
                                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.1A10.9 10.9 0 0112 5c5 0 9 4 10 7-.4 1.3-1.3 2.6-2.5 3.6M6.2 6.2C4.1 7.5 2.5 9.4 2 12c1 3 5 7 10 7 1.1 0 2.1-.2 3.1-.5" />
                                </svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z" />
                                    <circle cx="12" cy="12" r="3" stroke-width="2" />
                                </svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="text-rose-400 text-xs mt-1 font-medium">
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-500 focus:ring-emerald-500/20 focus:ring-offset-slate-900 cursor-pointer"
                            />
                            <span class="text-xs text-slate-400 font-medium">Remember session</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-3.5 px-4 rounded-xl bg-emerald-500 text-slate-950 font-semibold hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 disabled:opacity-50"
                    >
                        <span>Sign In to Portal</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-800/80 text-center text-xs text-slate-400">
                    New student or applicant?
                    <Link :href="route('register')" class="text-emerald-400 font-semibold hover:text-emerald-300 ml-1">
                        Create Portal Account
                    </Link>
                </div>
            </div>

            <!-- Footer Note -->
            <p class="text-center text-xs text-slate-600 mt-8">
                © TASFUED - Tai Solarin Federal University of Education
            </p>
        </div>
    </div>
</template>