<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean, default: true },
    canRegister: { type: Boolean, default: true },
    laravelVersion: { type: String, default: '' },
    phpVersion: { type: String, default: '' },
});

const quickPortals = ref([
    {
        title: 'Request Transcript',
        description: 'Submit official academic transcript requests for employment, further studies, or personal use.',
        badge: 'Transcripts',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        href: '#'
    },
    {
        title: 'Certificate Processing',
        description: 'Apply for degree certificates, supplementary certificates, and replacement documents.',
        badge: 'Certificates',
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        href: '#'
    },
    {
        title: 'Track Requests',
        description: 'Monitor the status and progress of your certificate and transcript applications in real time.',
        badge: 'Tracking',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        href: '#'
    },
    {
        title: 'Talk to an admin desk officer',
        description: 'desk personnel for helping you with what ever problems you have',
        badge: 'Administration',
        icon: 'M17 20h5v-2a4 4 0 00-5.5-3.72M9 20H2v-2a4 4 0 015.5-3.72M16 3.13a4 4 0 010 7.75M8 3.13a4 4 0 000 7.75M12 14a4 4 0 100-8 4 4 0 000 8z',
        href: '#'
    }
]);

const stats = ref([
    { number: 'Fast', label: 'Certificate & transcript processing' },
    { number: 'Online', label: 'Request submission and tracking' },
    { number: 'Secure', label: 'Document approval workflow' },
    { number: '24/7', label: 'Access to request status' },
]);
</script>

<template>
    <Head title="Tasfued E Request" />

    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-emerald-500 selection:text-white relative overflow-hidden">
        
        <!-- Visual Accent Background Blurs -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-teal-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            
            <!-- Navigation Bar -->
            <header class="flex items-center justify-between py-8 border-b border-slate-800/80">
                <div class="flex items-center gap-3">
                    <img src="/assets/images/logo1.png" alt="" class="h-10 w-10 shrink-0 object-contain" />
                    <div>
                        <span class="text-lg font-bold text-white block leading-tight">Tasfued E Request</span>
                        <span class="text-[10px] uppercase text-emerald-400 font-semibold">Certificate & Transcript Processing</span>
                    </div>
                </div>

                <nav v-if="canLogin" class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition-all shadow-md shadow-emerald-500/20"
                    >
                        Go to Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition-colors"
                        >
                            Log in
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-4 py-2 text-sm font-medium rounded-lg bg-emerald-500 text-slate-950 hover:bg-emerald-400 transition-all shadow-md shadow-emerald-500/20"
                        >
                            Portal Access
                        </Link>
                    </template>
                </nav>
            </header>

            <!-- Hero Section -->
            <section class="py-20 lg:py-28 max-w-4xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Tai Solarin Federal University of Education
                </div>
                
                <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-[1.1]">
                    Certificate & Transcript <br />
                    <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">
                        Processing Hub.
                    </span>
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-slate-400 leading-relaxed max-w-2xl">
                    Request, track, and manage official academic certificates and transcripts online.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a
                        href="#portals"
                        class="px-6 py-3.5 rounded-xl bg-emerald-500 text-slate-950 font-semibold hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/25 flex items-center gap-2"
                    >
                        Start a Request
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a
                        href="#about"
                        class="px-6 py-3.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 font-semibold hover:bg-slate-800/80 hover:text-white transition-all"
                    >
                        Track Existing Request
                    </a>
                </div>
            </section>

            <!-- Key Statistics Grid -->
            <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 py-8 border-y border-slate-800/60">
                <div v-for="(stat, idx) in stats" :key="idx" class="p-4">
                    <div class="text-3xl lg:text-4xl font-extrabold text-white tracking-tight bg-gradient-to-r from-emerald-400 to-teal-300 bg-clip-text text-transparent">
                        {{ stat.number }}
                    </div>
                    <p class="mt-1 text-xs sm:text-sm text-slate-400 font-medium">
                        {{ stat.label }}
                    </p>
                </div>
            </section>

            <!-- Digital Portals & Resources Grid -->
            <section id="portals" class="py-20">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Document Services</h2>
                        <p class="text-slate-400 text-sm mt-1">Submit and manage certificate and transcript requests.</p>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <a
                        v-for="(portal, i) in quickPortals"
                        :key="i"
                        :href="portal.href"
                        class="group relative p-8 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-emerald-500/40 transition-all duration-300 hover:shadow-xl hover:shadow-emerald-500/5 flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-12 h-12 rounded-xl bg-slate-800/80 group-hover:bg-emerald-500/10 text-emerald-400 flex items-center justify-center transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="portal.icon"/>
                                    </svg>
                                </div>
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-800 text-slate-300 border border-slate-700/50">
                                    {{ portal.badge }}
                                </span>
                            </div>

                            <h3 class="text-xl font-semibold text-white group-hover:text-emerald-400 transition-colors">
                                {{ portal.title }}
                            </h3>

                            <p class="mt-2 text-sm text-slate-400 leading-relaxed">
                                {{ portal.description }}
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-800/50 flex items-center text-xs font-semibold text-emerald-400">
                            <span>Open Portal</span>
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                </div>
            </section>

            <!-- Minimal Footer -->
            <footer class="py-12 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <div>
                    <p>© TASFUED - Tai Solarin Federal University of Education, Ijagun, Ogun State.</p>
                    <p class="mt-1">Certificate & Transcript Processing System</p>
                </div>
                <div class="flex items-center gap-6">
                    <span v-if="laravelVersion">Laravel v{{ laravelVersion }}</span>
                    <span v-if="phpVersion">PHP v{{ phpVersion }}</span>
                </div>
            </footer>

        </div>
    </div>
</template>