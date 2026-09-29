<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white dark:bg-gray-700',
    },
});

const open = ref(false);
const triggerRef = ref(null);
const menuStyle = ref({});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

const updateMenuPosition = async () => {
    await nextTick();

    const trigger = triggerRef.value;
    if (!trigger || !open.value) {
        return;
    }

    const rect = trigger.getBoundingClientRect();
    const menuWidth = Number(props.width) * 4; // Tailwind w-48 => 12rem => 192px; width prop is in 0.25rem units
    const gap = 8;

    const style = {
        position: 'fixed',
        top: `${rect.bottom + gap}px`,
        zIndex: 80,
        width: `${menuWidth}px`,
    };

    if (props.align === 'left') {
        style.left = `${Math.max(8, rect.left)}px`;
    } else if (props.align === 'right') {
        style.left = `${Math.max(8, rect.right - menuWidth)}px`;
    } else {
        style.left = `${Math.max(8, rect.left + rect.width / 2 - menuWidth / 2)}px`;
    }

    menuStyle.value = style;
};

watch(open, (isOpen) => {
    if (isOpen) {
        updateMenuPosition();
        window.addEventListener('resize', updateMenuPosition);
        window.addEventListener('scroll', updateMenuPosition, true);
    } else {
        window.removeEventListener('resize', updateMenuPosition);
        window.removeEventListener('scroll', updateMenuPosition, true);
    }
});

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    window.removeEventListener('resize', updateMenuPosition);
    window.removeEventListener('scroll', updateMenuPosition, true);
});

const widthClass = computed(() => {
    return {
        48: 'w-48',
        56: 'w-56',
        64: 'w-64',
        72: 'w-72',
        80: 'w-80',
    }[props.width.toString()] || 'w-48';
});
</script>

<template>
    <div class="relative">
        <div ref="triggerRef" @click="open = !open">
            <slot name="trigger" />
        </div>

        <Teleport to="body">
            <div
                v-show="open"
                class="fixed inset-0 z-[70]"
                @click="open = false"
            />

            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-show="open"
                    class="rounded-md shadow-lg"
                    :class="widthClass"
                    :style="menuStyle"
                    @click="open = false"
                >
                    <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
                        <slot name="content" />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>
