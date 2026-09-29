<script setup>
import { computed, ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    currentImageUrl: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['close']);

const fileInput = ref(null);
const previewUrl = ref(null);
const resetting = ref(false);

const form = useForm({
    image: null,
});

watch(
    () => props.show,
    (visible) => {
        if (!visible) {
            form.reset();
            form.clearErrors();
            if (previewUrl.value) {
                URL.revokeObjectURL(previewUrl.value);
            }
            previewUrl.value = null;
            resetting.value = false;
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        }
    }
);

const displayImage = computed(() => previewUrl.value || props.currentImageUrl);
const canReset = computed(() => Boolean(props.currentImageUrl) && !previewUrl.value);

const onFileChange = (event) => {
    const file = event.target.files?.[0] ?? null;
    form.image = file;
    form.clearErrors('image');

    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
        previewUrl.value = null;
    }

    if (file) {
        previewUrl.value = URL.createObjectURL(file);
    }
};

const close = () => emit('close');

const submit = () => {
    form.post(route('profile.image'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => close(),
    });
};

const returnToDefault = () => {
    if (!canReset.value || resetting.value) return;

    resetting.value = true;
    router.delete(route('profile.image.reset'), {
        preserveScroll: true,
        onFinish: () => {
            resetting.value = false;
        },
        onSuccess: () => close(),
    });
};
</script>

<template>
    <Modal :show="show" max-width="md" @close="close">
        <form @submit.prevent="submit" class="p-6" enctype="multipart/form-data">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Change Profile Picture</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Upload a JPG, PNG, GIF, or WEBP image up to 2MB.
            </p>

            <div class="mt-6 flex flex-col items-center gap-4">
                <div
                    class="w-48 h-36 rounded-xl border-4 border-gray-100 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden shadow-sm"
                >
                    <img
                        v-if="displayImage"
                        :src="displayImage"
                        alt="Profile preview"
                        class="w-full h-full object-cover"
                    />
                    <svg v-else class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>

                <input
                    ref="fileInput"
                    type="file"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:rounded-md file:border-0 file:bg-purple-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-purple-700 hover:file:bg-purple-100 dark:file:bg-purple-900/40 dark:file:text-purple-200"
                    @change="onFileChange"
                />
                <InputError :message="form.errors.image" />

                <button
                    v-if="canReset"
                    type="button"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white underline-offset-2 hover:underline disabled:opacity-60"
                    :disabled="resetting || form.processing"
                    @click="returnToDefault"
                >
                    {{ resetting ? 'Restoring…' : 'Return to default' }}
                </button>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton type="button" :disabled="resetting" @click="close">Cancel</SecondaryButton>
                <PrimaryButton :disabled="form.processing || resetting || !form.image">Upload</PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
