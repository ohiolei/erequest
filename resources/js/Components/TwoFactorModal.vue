<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'changed']);

const step = ref('status'); // status | setup | recovery | disable
const enabled = ref(false);
const loadError = ref('');
const qrSvg = ref(null);
const secret = ref(null);
const recoveryCodes = ref([]);
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);

const confirmCode = ref('');
const confirmError = ref('');
const disablePassword = ref('');
const disableError = ref('');

const resetLocalState = () => {
    step.value = 'status';
    loadError.value = '';
    qrSvg.value = null;
    secret.value = null;
    recoveryCodes.value = [];
    confirmCode.value = '';
    confirmError.value = '';
    disablePassword.value = '';
    disableError.value = '';
    enabling.value = false;
    confirming.value = false;
    disabling.value = false;
};

const applySetup = (payload) => {
    qrSvg.value = payload.qr_svg ?? null;
    secret.value = payload.secret ?? null;
    enabled.value = false;
    emit('changed', false);
    step.value = 'setup';
};

const loadStatus = async () => {
    loadError.value = '';
    try {
        const { data } = await axios.get(route('two-factor.show'), { skipLoading: true });
        enabled.value = !!data.two_factor_enabled;

        if (data.setup && data.qr_svg) {
            applySetup(data);
            return;
        }

        step.value = 'status';
    } catch (error) {
        loadError.value = error.response?.data?.message || 'Unable to load two-factor status.';
        step.value = 'status';
    }
};

watch(
    () => props.show,
    async (visible) => {
        if (!visible) {
            resetLocalState();
            return;
        }

        await loadStatus();
    }
);

const close = () => {
    emit('close');
    router.reload({ only: ['auth'] });
};

const beginSetup = async () => {
    enabling.value = true;
    confirmError.value = '';

    try {
        const { data } = await axios.post(route('two-factor.enable'));
        applySetup(data);
    } catch (error) {
        confirmError.value = error.response?.data?.message || 'Unable to start two-factor setup.';
    } finally {
        enabling.value = false;
    }
};

const confirmSetup = async () => {
    confirming.value = true;
    confirmError.value = '';

    try {
        const { data } = await axios.post(route('two-factor.confirm'), {
            code: confirmCode.value,
        });

        enabled.value = true;
        emit('changed', true);
        recoveryCodes.value = data.recovery_codes || [];
        confirmCode.value = '';
        step.value = 'recovery';
    } catch (error) {
        confirmError.value =
            error.response?.data?.errors?.code?.[0]
            || error.response?.data?.message
            || 'Invalid authentication code.';
    } finally {
        confirming.value = false;
    }
};

const startDisable = () => {
    disablePassword.value = '';
    disableError.value = '';
    step.value = 'disable';
};

const cancelDisable = () => {
    disablePassword.value = '';
    disableError.value = '';
    step.value = 'status';
};

const disableTwoFactor = async () => {
    disabling.value = true;
    disableError.value = '';

    try {
        await axios.delete(route('two-factor.disable'), {
            data: { current_password: disablePassword.value },
        });

        enabled.value = false;
        disablePassword.value = '';
        step.value = 'status';
        router.reload({ only: ['auth'] });
    } catch (error) {
        disableError.value =
            error.response?.data?.errors?.current_password?.[0]
            || error.response?.data?.message
            || 'Unable to disable two-factor authentication.';
    } finally {
        disabling.value = false;
    }
};

const copyRecoveryCodes = async () => {
    try {
        await navigator.clipboard.writeText(recoveryCodes.value.join('\n'));
    } catch {
        // ignore clipboard failures
    }
};
</script>

<template>
    <Modal :show="show" max-width="md" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                Two-Factor Authentication
            </h2>

            <template v-if="step === 'status'">
                <p v-if="loadError" role="alert" class="mt-3 rounded-md bg-rose-50 px-3 py-2 text-sm text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
                    {{ loadError }}
                </p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Add an extra layer of security to your account using an authenticator app.
                    After enabling, you will be asked for a code every time you log in.
                </p>

                <div
                    class="mt-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/60 px-4 py-3 text-sm text-gray-700 dark:text-gray-300"
                >
                    Status:
                    <span class="font-medium" :class="enabled ? 'text-green-600 dark:text-green-400' : 'text-gray-500'">
                        {{ enabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton type="button" @click="close">Close</SecondaryButton>
                    <PrimaryButton v-if="!enabled" type="button" :disabled="enabling" @click="beginSetup">
                        Enable
                    </PrimaryButton>
                    <button
                        v-else
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        @click="startDisable"
                    >
                        Disable
                    </button>
                </div>
            </template>

            <template v-else-if="step === 'setup'">
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Add an account manually in your authenticator app using this setup key, then enter the 6-digit code to confirm.
                </p>

                <div v-if="qrSvg" class="mt-4 flex justify-center">
                    <img :src="qrSvg" alt="Two-factor QR code" class="h-48 w-48 rounded-lg bg-white p-2" />
                </div>

                <p v-if="secret" class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400 break-all">
                    Setup key:
                    <span class="font-mono text-gray-800 dark:text-gray-200">{{ secret }}</span>
                </p>

                <form class="mt-6 space-y-4" @submit.prevent="confirmSetup">
                    <div>
                        <InputLabel for="two_factor_code" value="Authentication Code" />
                        <TextInput
                            id="two_factor_code"
                            v-model="confirmCode"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="confirmError" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <SecondaryButton type="button" @click="close">Cancel</SecondaryButton>
                        <PrimaryButton :disabled="confirming">Confirm</PrimaryButton>
                    </div>
                </form>
            </template>

            <template v-else-if="step === 'recovery'">
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Store these recovery codes in a safe place. Each code can be used once if you lose access to your authenticator app.
                </p>

                <div
                    class="mt-4 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 px-4 py-3 font-mono text-sm text-gray-800 dark:text-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-2"
                >
                    <div v-for="code in recoveryCodes" :key="code">{{ code }}</div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton type="button" @click="copyRecoveryCodes">Copy</SecondaryButton>
                    <PrimaryButton type="button" @click="close">Done</PrimaryButton>
                </div>
            </template>

            <template v-else-if="step === 'disable'">
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Enter your password to disable two-factor authentication.
                </p>

                <form class="mt-6 space-y-4" @submit.prevent="disableTwoFactor">
                    <div>
                        <InputLabel for="two_factor_current_password" value="Current Password" />
                        <TextInput
                            id="two_factor_current_password"
                            v-model="disablePassword"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="current-password"
                            required
                        />
                        <InputError :message="disableError" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <SecondaryButton type="button" @click="cancelDisable">Cancel</SecondaryButton>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                            :disabled="disabling"
                        >
                            Disable
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </Modal>
</template>
