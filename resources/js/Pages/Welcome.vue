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
        title: 'Student Portal',
        description: 'Course registration, fee payment, result checking, and academic transcript access.',
        badge: 'Primary Portal',
        icon: 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222',
        href: '#'
    },
    {
        title: 'Admissions Hub',
        description: 'Post-UTME screening application, admission list verification, and acceptance processing.',
        badge: 'Prospective',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        href: '#'
    },
    {
        title: 'E-Learning & LMS',
        description: 'Virtual lectures, continuous assessments, course modules, and online submission nodes.',
        badge: 'Academic',
        icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        href: '#'
    },
    {
        title: 'CBT Assessment Portal',
        description: 'Online examination platform, continuous assessment schedules, and instant test evaluation.',
        badge: 'Examinations',
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        href: '#'
    }
]);

const stats = ref([
    { number: '1st', label: 'Specialized University of Education in Nigeria' },
    { number: '5+', label: 'Academic Colleges & Schools' },
    { number: '20,000+', label: 'Active Scholars & Educators' },
    { number: '100%', label: 'ICT & Pedagogy Integrated Curriculum' },
]);
</script>

<template>
    <Head title="TASFUED - Premier University of Education" />

    <div class="min-h-screen bg-slate-950 text-slate-100 font-sans selection:bg-emerald-500 selection:text-white relative overflow-hidden">
        
        <!-- Visual Accent Background Blurs -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-teal-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            
            <!-- Navigation Bar -->
            <header class="flex items-center justify-between py-8 border-b border-slate-800/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center font-bold text-slate-950 shadow-lg shadow-emerald-500/20">
                        T
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-white block leading-none">TASFUED</span>
                        <span class="text-[10px] tracking-widest uppercase text-emerald-400 font-semibold">IJEBU-ODE</span>
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
                    Excellence in Pedagogy, <br />
                    <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">
                        Innovation & Leadership.
                    </span>
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-slate-400 leading-relaxed max-w-2xl">
                    Nigeria’s premier specialized university of education—combining academic rigorous teaching with modern vocational and technological skills.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <a
                        href="#portals"
                        class="px-6 py-3.5 rounded-xl bg-emerald-500 text-slate-950 font-semibold hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/25 flex items-center gap-2"
                    >
                        Access Student Services
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a
                        href="#about"
                        class="px-6 py-3.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 font-semibold hover:bg-slate-800/80 hover:text-white transition-all"
                    >
                        University Overview
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
                        <h2 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Essential Digital Portals</h2>
                        <p class="text-slate-400 text-sm mt-1">Direct access nodes for students, staff, and applicants.</p>
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
                </div>
                <div class="flex items-center gap-6">
                    <span v-if="laravelVersion">Laravel v{{ laravelVersion }}</span>
                    <span v-if="phpVersion">PHP v{{ phpVersion }}</span>
                </div>
            </footer>

        </div>
    </div>
</template>