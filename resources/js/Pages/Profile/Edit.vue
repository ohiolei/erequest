<script setup>
import { ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import TwoFactorModal from '@/Components/TwoFactorModal.vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const page = usePage();
const showTwoFactorModal = ref(false);
const twoFactorEnabled = ref(page.props.auth.twoFactorEnabled);
const activeTab = ref('profile');
const { themes, themeId, setTheme, colorModes, colorMode, setColorMode } = useTheme();
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="mx-auto max-w-6xl py-6 sm:py-9">
            <header class="mb-8">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Settings</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your profile and account settings.</p>
            </header>

            <div class="grid gap-6 md:grid-cols-[220px_minmax(0,1fr)]">
                <nav
                    role="tablist"
                    aria-label="Profile settings"
                    class="flex gap-1 overflow-x-auto pb-1 md:flex-col md:overflow-visible"
                >
                    <button
                        id="profile-tab"
                        type="button"
                        role="tab"
                        aria-controls="profile-panel"
                        :aria-selected="activeTab === 'profile'"
                        :tabindex="activeTab === 'profile' ? 0 : -1"
                        :class="[
                            'shrink-0 rounded-lg px-4 py-3 text-left text-sm font-medium transition md:w-full',
                            activeTab === 'profile'
                                ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800',
                        ]"
                        @click="activeTab = 'profile'"
                    >
                        Profile
                    </button>
                    <button
                        id="password-tab"
                        type="button"
                        role="tab"
                        aria-controls="password-panel"
                        :aria-selected="activeTab === 'password'"
                        :tabindex="activeTab === 'password' ? 0 : -1"
                        :class="[
                            'shrink-0 rounded-lg px-4 py-3 text-left text-sm font-medium transition md:w-full',
                            activeTab === 'password'
                                ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800',
                        ]"
                        @click="activeTab = 'password'"
                    >
                        Password
                    </button>
                    <button
                        id="two-factor-tab"
                        type="button"
                        role="tab"
                        aria-controls="two-factor-panel"
                        :aria-selected="activeTab === 'two-factor'"
                        :tabindex="activeTab === 'two-factor' ? 0 : -1"
                        :class="[
                            'shrink-0 rounded-lg px-4 py-3 text-left text-sm font-medium transition md:w-full',
                            activeTab === 'two-factor'
                                ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800',
                        ]"
                        @click="activeTab = 'two-factor'"
                    >
                        Two-Factor Auth
                    </button>
                    <button
                        id="appearance-tab"
                        type="button"
                        role="tab"
                        aria-controls="appearance-panel"
                        :aria-selected="activeTab === 'appearance'"
                        :tabindex="activeTab === 'appearance' ? 0 : -1"
                        :class="[
                            'shrink-0 rounded-lg px-4 py-3 text-left text-sm font-medium transition md:w-full',
                            activeTab === 'appearance'
                                ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800',
                        ]"
                        @click="activeTab = 'appearance'"
                    >
                        Appearance
                    </button>
                </nav>

                <div class="min-w-0">
                    <section
                        id="profile-panel"
                        role="tabpanel"
                        aria-labelledby="profile-tab"
                        tabindex="0"
                        v-show="activeTab === 'profile'"
                        class="space-y-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8"
                    >
                        <UpdateProfileInformationForm
                            :must-verify-email="props.mustVerifyEmail"
                            :status="props.status"
                        />

                        <div class="border-t border-rose-100 pt-6 dark:border-rose-900/50">
                            <div class="mb-5">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Delete account</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Delete your account and all of its resources.</p>
                            </div>
                            <div class="rounded-xl border border-rose-200 bg-rose-50/70 p-5 dark:border-rose-900/60 dark:bg-rose-950/20">
                                <h3 class="font-semibold text-rose-700 dark:text-rose-300">Warning</h3>
                                <p class="mt-1 text-sm text-rose-700 dark:text-rose-300">Please proceed with caution, this cannot be undone.</p>
                                <div class="mt-4">
                                    <DeleteUserForm />
                                </div>
                            </div>
                        </div>
                    </section>

                    <section
                        id="password-panel"
                        role="tabpanel"
                        aria-labelledby="password-tab"
                        tabindex="0"
                        v-show="activeTab === 'password'"
                        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8"
                    >
                        <UpdatePasswordForm />
                    </section>

                    <section
                        id="two-factor-panel"
                        role="tabpanel"
                        aria-labelledby="two-factor-tab"
                        tabindex="0"
                        v-show="activeTab === 'two-factor'"
                        class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8"
                    >
                        <div class="flex flex-wrap items-start justify-between gap-5">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Two-factor authentication</h2>
                                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500 dark:text-gray-400">
                                    Add a verification code from an authenticator app when you sign in. Keep your recovery codes somewhere safe.
                                </p>
                                <span
                                    :class="[
                                        'mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                        twoFactorEnabled
                                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                                            : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                                    ]"
                                >
                                    {{ twoFactorEnabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                            <button
                                type="button"
                                class="inline-flex shrink-0 items-center justify-center rounded-lg bg-purple-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                @click="showTwoFactorModal = true"
                            >
                                {{ twoFactorEnabled ? 'Manage 2FA' : 'Set up 2FA' }}
                            </button>
                        </div>
                    </section>

                    <section
                        id="appearance-panel"
                        role="tabpanel"
                        aria-labelledby="appearance-tab"
                        tabindex="0"
                        v-show="activeTab === 'appearance'"
                        class="space-y-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8"
                    >
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Appearance</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose how the application looks.</p>
                            <div class="mt-5 grid max-w-md grid-cols-2 gap-3">
                                <button
                                    v-for="mode in colorModes"
                                    :key="mode.id"
                                    type="button"
                                    class="rounded-lg border px-4 py-3 text-sm font-medium transition"
                                    :class="colorMode === mode.id
                                        ? 'border-purple-600 bg-purple-50 text-purple-700 dark:border-purple-400 dark:bg-purple-900/40 dark:text-purple-200'
                                        : 'border-gray-200 text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700'"
                                    :aria-pressed="colorMode === mode.id"
                                    @click="setColorMode(mode.id)"
                                >
                                    {{ mode.label }} mode
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Theme color</h3>
                            <div class="mt-3 flex flex-wrap gap-3">
                                <button
                                    v-for="theme in themes"
                                    :key="theme.id"
                                    type="button"
                                    :title="theme.label"
                                    :aria-label="`Use ${theme.label} theme`"
                                    :aria-pressed="themeId === theme.id"
                                    class="h-9 w-9 rounded-full border-2 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 dark:focus:ring-offset-gray-800"
                                    :class="themeId === theme.id
                                        ? 'scale-110 border-gray-800 dark:border-white'
                                        : 'border-transparent hover:scale-105'"
                                    :style="{ backgroundColor: theme.swatch }"
                                    @click="setTheme(theme.id)"
                                />
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <TwoFactorModal
            :show="showTwoFactorModal"
            @close="showTwoFactorModal = false"
            @changed="twoFactorEnabled = $event"
        />
    </AuthenticatedLayout>
</template>
