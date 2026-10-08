<script setup>
import { defineAsyncComponent, ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const ChangePasswordModal = defineAsyncComponent(() => import('@/Components/ChangePasswordModal.vue'));
const TwoFactorModal = defineAsyncComponent(() => import('@/Components/TwoFactorModal.vue'));
const SettingsModal = defineAsyncComponent(() => import('@/Components/SettingsModal.vue'));

defineProps({
    userIsActive: {
        type: Boolean,
        default: true,
    },
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '56',
    },
    inline: {
        type: Boolean,
        default: false,
    },
});

const showPasswordModal = ref(false);
const showTwoFactorModal = ref(false);
const showSettingsModal = ref(false);

const menuItemClass =
    'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out';
</script>

<template>
    <div>
        <template v-if="inline">
            <ResponsiveNavLink v-if="userIsActive" :href="route('profile.edit')">
                Manage Profile
            </ResponsiveNavLink>
            <!-- <button
                v-if="userIsActive"
                type="button"
                class="w-full flex items-start ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out"
                @click="showPasswordModal = true"
            >
                Change Password
            </button>
            <button
                v-if="userIsActive"
                type="button"
                class="w-full flex items-start ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out"
                @click="showTwoFactorModal = true"
            >
                Two-Factor Auth
            </button> -->
            <button
                type="button"
                class="w-full flex items-start ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out"
                @click="showSettingsModal = true"
            >
                Settings
            </button>
            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                Log Out
            </ResponsiveNavLink>
        </template>

        <Dropdown v-else :align="align" :width="width">
            <template #trigger>
                <slot name="trigger" />
            </template>

            <template #content>
                <div class="py-1">
                    <p class="px-4 pt-2 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">
                        Activity
                    </p>

                    <DropdownLink v-if="userIsActive" :href="route('profile.edit')">
                        Manage Profile
                    </DropdownLink>

                    <!-- <button
                        v-if="userIsActive"
                        type="button"
                        :class="menuItemClass"
                        @click="showPasswordModal = true"
                    >
                        Change Password
                    </button>

                    <button
                        v-if="userIsActive"
                        type="button"
                        :class="menuItemClass"
                        @click="showTwoFactorModal = true"
                    >
                        Two-Factor Auth
                    </button> -->

                    <button type="button" :class="menuItemClass" @click="showSettingsModal = true">
                        Settings
                    </button>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-600 py-1">
                    <DropdownLink :href="route('logout')" method="post" as="button">
                        Log Out
                    </DropdownLink>
                </div>
            </template>
        </Dropdown>

        <ChangePasswordModal
            :show="showPasswordModal"
            @close="showPasswordModal = false"
        />
        <TwoFactorModal
            :show="showTwoFactorModal"
            @close="showTwoFactorModal = false"
        />
        <SettingsModal
            :show="showSettingsModal"
            @close="showSettingsModal = false"
        />
    </div>
</template>
