<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({ code: '' });

const submit = () => {
    form.post(route('two-factor.challenge.verify'), {
        onFinish: () => form.reset('code'),
    });
};
</script>

<template>
    <Head title="Two-factor verification" />

    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-slate-950 px-6 py-12 font-sans text-slate-100">
        <div class="pointer-events-none absolute -left-40 -top-40 h-96 w-96 rounded-full bg-emerald-600/20 blur-3xl" />
        <div class="pointer-events-none absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-blue-600/15 blur-3xl" />

        <div class="relative z-10 w-full max-w-md">
            <div class="mb-8 text-center">
                <Link class="inline-flex items-center gap-3" href="/">
                    <img src="/assets/images/logo1.png" alt="" class="h-12 w-12 shrink-0 object-contain" />
                    <div class="text-left">
                        <span class="block text-2xl font-bold leading-none tracking-tight text-white">TASFUED</span>
                        <span class="text-[10px] font-semibold uppercase tracking-widest text-emerald-400">SECURE SIGN IN</span>
                    </div>
                </Link>
            </div>

            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/70 p-8 shadow-2xl backdrop-blur-md">
                <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l7 4v5c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V7l7-4z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                    </svg>
                </div>
                <h1 class="text-xl font-semibold text-white">Verify it's you</h1>
                <p class="mt-2 text-sm leading-6 text-slate-400">
                    Enter the six-digit code from your authenticator app, or use one of your recovery codes.
                </p>

                <form class="mt-6 space-y-5" @submit.prevent="submit">
                    <div>
                        <label for="two_factor_code" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Authentication or recovery code
                        </label>
                        <input
                            id="two_factor_code"
                            v-model="form.code"
                            type="text"
                            inputmode="text"
                            autocomplete="one-time-code"
                            autofocus
                            required
                            class="w-full rounded-xl border border-slate-800 bg-slate-950 px-4 py-3 text-center font-mono text-lg tracking-[0.2em] text-slate-100 placeholder-slate-600 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                        />
                        <p v-if="form.errors.code" role="alert" class="mt-2 text-xs font-medium text-rose-400">
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex w-full items-center justify-center rounded-xl bg-emerald-500 px-4 py-3.5 font-semibold text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/50 disabled:opacity-50"
                    >
                        {{ form.processing ? 'Verifying...' : 'Verify and sign in' }}
                    </button>
                </form>

                <p class="mt-6 border-t border-slate-800/80 pt-5 text-center text-xs text-slate-500">
                    Lost access to your authenticator?
                    <span class="text-slate-400">Use a recovery code instead.</span>
                </p>
            </div>
        </div>
    </div>
</template>
