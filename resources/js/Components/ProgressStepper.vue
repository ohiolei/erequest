<script setup>
const props = defineProps({
    steps: {
        type: Array,
        required: true,
    },
    size: {
        type: String,
        default: 'md',
    },
});

const circleClass = (step) => {
    if (step.state === 'declined') {
        return 'bg-red-500 text-white';
    }

    if (step.state === 'upcoming') {
        return 'bg-slate-100 text-slate-400';
    }

    if (step.state === 'current') {
        return 'bg-[#7C3AED] text-white';
    }

    return 'bg-emerald-50 text-emerald-700';
};
</script>

<template>
    <ol class="flex items-start">
        <li
            v-for="(step, index) in steps"
            :key="step.key || step.label"
            class="relative flex flex-1 flex-col items-center"
        >
            <div
                v-if="index < steps.length - 1"
                class="absolute"
                :class="[
                    props.size === 'sm'
                        ? 'left-[calc(50%+14px)] right-[calc(-50%+14px)] top-3.5 h-[2px]'
                        : 'left-[calc(50%+20px)] right-[calc(-50%+20px)] top-5 h-[3px]',
                    step.state === 'done' ? 'bg-[#7C3AED]' : 'bg-slate-200',
                ]"
            />
            <span
                class="relative z-10 flex items-center justify-center rounded-full font-semibold"
                :class="[
                    props.size === 'sm' ? 'h-7 w-7 text-[11px]' : 'h-10 w-10 text-sm',
                    circleClass(step),
                ]"
            >
                <svg
                    v-if="step.state === 'done'"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    :class="props.size === 'sm' ? 'h-3.5 w-3.5' : 'h-5 w-5'"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 13l4 4L19 7" />
                </svg>
                <span v-else>{{ index + 1 }}</span>
            </span>
            <p
                class="mt-2 text-center font-semibold leading-tight sm:max-w-none"
                :class="[
                    props.size === 'sm'
                        ? 'max-w-[4.75rem] text-[10px] sm:text-[11px]'
                        : 'mt-3 max-w-[5.5rem] text-[11px] sm:text-xs',
                    step.state === 'upcoming' ? 'text-slate-400' : 'text-slate-700',
                ]"
            >
                {{ step.label }}
            </p>
        </li>
    </ol>
</template>
