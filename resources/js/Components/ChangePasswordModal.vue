<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

watch(
    () => props.show,
    (visible) => {
        if (!visible) {
            form.reset();
            form.clearErrors();
        }
    }
);

const close = () => {
    emit('close');
};

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            close();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <Modal :show="show" max-width="md" @close="close">
        <form @submit.prevent="updatePassword" class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Change Password</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Enter your current password and choose a new one.
            </p>

            <div class="mt-6 space-y-4">
                <div>
                    <InputLabel for="modal_current_password" value="Old Password" />
                    <TextInput
                        id="modal_current_password"
                        ref="currentPasswordInput"
                        v-model="form.current_password"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="current-password"
                        required
                    />
                    <InputError :message="form.errors.current_password" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="modal_password" value="New Password" />
                    <TextInput
                        id="modal_password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                        required
                    />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="modal_password_confirmation" value="Confirm Password" />
                    <TextInput
                        id="modal_password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                        required
                    />
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton type="button" @click="close">Cancel</SecondaryButton>
                <PrimaryButton :disabled="form.processing">Change</PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
