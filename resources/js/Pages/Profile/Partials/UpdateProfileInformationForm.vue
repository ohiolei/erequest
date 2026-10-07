<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const page = usePage();
const user = page.props.auth.user;

const form = useForm({
    fname: user?.fname || '',
    mname: user?.mname || '',
    lname: user?.lname || '',
    email: user?.email || '',
});
</script>

<template>
    <section>
        <header class="mb-6 flex items-start gap-4 border-b border-gray-100 pb-5 dark:border-gray-700">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal information</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Keep your name and contact email up to date.</p>
            </div>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-5"
        >
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <InputLabel for="fname" value="First name" class="dark:!text-gray-300" />
                    <TextInput id="fname" type="text" class="mt-1 block w-full bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-white" v-model="form.fname" required autocomplete="given-name" />
                    <InputError class="mt-2" :message="form.errors.fname" />
                </div>
                <div>
                    <InputLabel for="mname" value="Middle name (optional)" class="dark:!text-gray-300" />
                    <TextInput id="mname" type="text" class="mt-1 block w-full bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-white" v-model="form.mname" autocomplete="additional-name" />
                    <InputError class="mt-2" :message="form.errors.mname" />
                </div>
                <div>
                    <InputLabel for="lname" value="Last name" class="dark:!text-gray-300" />
                    <TextInput id="lname" type="text" class="mt-1 block w-full bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-white" v-model="form.lname" autocomplete="family-name" />
                    <InputError class="mt-2" :message="form.errors.lname" />
                </div>
            </div>

            <div>
                <InputLabel for="profile_email" value="Email address" class="dark:!text-gray-300" />
                <TextInput id="profile_email" type="email" class="mt-1 block w-full bg-white text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-white sm:max-w-xl" v-model="form.email" required autocomplete="email" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="ml-1 font-semibold underline underline-offset-2 hover:text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 dark:hover:text-amber-100"
                    >
                        Resend verification email
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    role="status"
                    class="mt-2 text-sm font-medium text-emerald-700 dark:text-emerald-300"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 border-t border-gray-100 pt-5 dark:border-gray-700">
                <PrimaryButton :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Save changes' }}
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        role="status"
                        class="text-sm font-medium text-emerald-700 dark:text-emerald-300"
                    >
                        Changes saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
