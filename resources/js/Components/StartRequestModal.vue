<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import Modal from '@/Components/Modal.vue';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    services: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const search = ref('');
const selectedType = ref('all');
const selectedService = ref(null);
const acceptedTerms = ref(false);
const initiateForm = useForm({
    service_id: null,
    accepted_terms: false,
});

const typeOptions = computed(() => {
    const types = [...new Set(props.services.map((service) => service.type).filter(Boolean))];
    return ['all', ...types];
});

const filteredServices = computed(() => {
    const term = search.value.trim().toLowerCase();

    return props.services.filter((service) => {
        const matchesType = selectedType.value === 'all' || service.type === selectedType.value;
        if (!matchesType) {
            return false;
        }

        if (!term) {
            return true;
        }

        return [service.name, service.short_name, service.type, service.description]
            .filter(Boolean)
            .some((value) => String(value).toLowerCase().includes(term));
    });
});

const formatNaira = (value) => {
    const amount = Number(value || 0);
    return new Intl.NumberFormat('en-NG', {
        style: 'currency',
        currency: 'NGN',
        maximumFractionDigits: amount % 1 === 0 ? 0 : 2,
    }).format(amount);
};

const iconKind = (service) => {
    const haystack = `${service?.name || ''} ${service?.type || ''}`.toLowerCase();
    if (haystack.includes('transcript')) return 'transcript';
    if (haystack.includes('certificate')) return 'certificate';
    if (haystack.includes('proficiency') || haystack.includes('english')) return 'proficiency';
    if (haystack.includes('notification') || haystack.includes('result')) return 'result';
    if (haystack.includes('admission') || haystack.includes('clearance')) return 'clearance';
    return 'document';
};

const showsInPersonNotice = (service) => {
    const haystack = `${service?.name || ''} ${service?.short_name || ''} ${service?.type || ''}`.toLowerCase();
    return haystack.includes('certificate') || haystack.includes('notification');
};

const resetState = () => {
    search.value = '';
    selectedType.value = 'all';
    selectedService.value = null;
    acceptedTerms.value = false;
    initiateForm.reset();
    initiateForm.clearErrors();
};

const closeCatalog = () => {
    resetState();
    emit('close');
};

const openService = (service) => {
    selectedService.value = service;
};

const closeService = () => {
    selectedService.value = null;
    acceptedTerms.value = false;
    initiateForm.reset();
    initiateForm.clearErrors();
};

watch(selectedService, () => {
    acceptedTerms.value = false;
    initiateForm.clearErrors();
});

watch(
    () => props.show,
    (visible) => {
        if (!visible) {
            resetState();
        }
    },
);

const startRequest = () => {
    if (!selectedService.value) {
        return;
    }

    initiateForm.service_id = selectedService.value.id;
    initiateForm.accepted_terms = acceptedTerms.value;
    initiateForm.post(route('requests.store'), {
        preserveScroll: true,
        onSuccess: () => closeCatalog(),
    });
};

const continueRequest = (service, event) => {
    event?.stopPropagation();
    if (!service?.latest_open_id) {
        return;
    }

    router.visit(route('requests.progress', service.latest_open_id));
};
</script>

<template>
    <Modal :show="show" max-width="5xl" @close="closeCatalog">
        <div class="p-6 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-violet-500">New request</p>
                    <h2 class="mt-1 text-xl font-semibold text-slate-900">Choose a request type</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Select a service below to review fees and start your application.
                    </p>
                </div>
                <button
                    type="button"
                    class="rounded-lg p-2 text-slate-400 hover:bg-slate-50 hover:text-slate-600"
                    aria-label="Close"
                    @click="closeCatalog"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 flex-1 flex-wrap gap-2">
                    <button
                        v-for="type in typeOptions"
                        :key="type"
                        type="button"
                        class="rounded-full px-3.5 py-1.5 text-sm font-medium transition"
                        :class="
                            selectedType === type
                                ? 'bg-[#7C3AED] text-white shadow-[0_8px_20px_rgba(124,58,237,0.24)]'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        "
                        @click="selectedType = type"
                    >
                        {{ type === 'all' ? 'All requests' : type }}
                    </button>
                </div>
                <div class="relative w-full sm:max-w-xs">
                    <label class="sr-only" for="start-request-search">Search requests</label>
                    <svg
                        class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"
                        />
                    </svg>
                    <input
                        id="start-request-search"
                        v-model="search"
                        type="search"
                        placeholder="Search requests…"
                        class="w-full rounded-full border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-800 placeholder:text-slate-400 focus:border-purple-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-purple-200"
                    />
                </div>
            </div>

            <div class="mt-5 max-h-[min(28rem,60vh)] overflow-y-auto pr-1">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <button
                        v-for="service in filteredServices"
                        :key="service.id"
                        type="button"
                        class="group flex cursor-pointer flex-col overflow-hidden rounded-[24px] border border-slate-100 bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:border-violet-100 hover:shadow-[0_12px_32px_rgba(124,58,237,0.12)] focus:outline-none focus:ring-2 focus:ring-purple-200"
                        @click="openService(service)"
                    >
                        <div class="border-b border-slate-100 bg-gradient-to-br from-violet-50/80 via-white to-white px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex min-w-0 items-start gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white text-[#7C3AED] shadow-sm ring-1 ring-slate-100">
                                        <svg v-if="iconKind(service) === 'transcript'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h8M8 11h8M8 15h5M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                                        </svg>
                                        <svg v-else-if="iconKind(service) === 'certificate'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l8 4v5c0 5-3.4 8.4-8 9.5C7.4 20.4 4 17 4 12V7l8-4z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4" />
                                        </svg>
                                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h8l4 4v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                        </svg>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-violet-500">
                                            {{ service.type }}
                                        </p>
                                        <h3 class="mt-1 text-sm font-semibold leading-snug text-slate-900">
                                            {{ service.short_name || service.name }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="shrink-0 rounded-2xl bg-white px-3 py-2 text-right shadow-sm ring-1 ring-slate-100">
                                    <p class="text-[10px] font-medium uppercase tracking-wide text-slate-400">Fee</p>
                                    <p class="text-sm font-bold tabular-nums text-slate-900">
                                        {{ service.fee > 0 ? formatNaira(service.fee) : 'On request' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col p-4 text-left">
                            <p class="line-clamp-2 text-sm leading-relaxed text-slate-500">
                                {{ service.description }}
                            </p>
                            <div
                                v-if="service.my_open || service.latest_open_id"
                                class="mt-3 flex flex-wrap items-center gap-2"
                            >
                                <span
                                    v-if="service.my_open"
                                    class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800"
                                >
                                    {{ service.my_open }} in progress
                                </span>
                            </div>
                        </div>
                    </button>
                </div>

                <div
                    v-if="!filteredServices.length"
                    class="rounded-[24px] border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center text-sm text-slate-400"
                >
                    {{
                        search || selectedType !== 'all'
                            ? 'No request types match your filters.'
                            : 'No request types are available at the moment.'
                    }}
                </div>
            </div>
        </div>
    </Modal>

    <Modal :show="Boolean(selectedService)" max-width="xl" @close="closeService">
        <div v-if="selectedService" class="p-6 sm:p-7">
            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-violet-500">
                {{ selectedService.type }}
            </p>
            <h2 class="mt-1 text-xl font-semibold text-slate-900">
                {{ selectedService.short_name || selectedService.name }}
            </h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-500">
                {{ selectedService.description }}
            </p>

            <p
                v-if="showsInPersonNotice(selectedService)"
                class="mt-4 text-sm font-medium leading-relaxed text-red-600"
            >
                Kindly come along with your Student/School ID card.<br />
                Please be informed that this request must be obtained in person.
            </p>

            <div class="mt-5 rounded-2xl border border-slate-100 bg-slate-50/80 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm font-semibold text-slate-800">Payables</p>
                    <p class="text-sm font-bold tabular-nums text-slate-900">
                        {{ selectedService.fee > 0 ? formatNaira(selectedService.fee) : 'On request' }}
                    </p>
                </div>
                <ul v-if="selectedService.payables?.length" class="mt-3 divide-y divide-slate-200/80">
                    <li
                        v-for="payable in selectedService.payables"
                        :key="`${selectedService.id}-payable-${payable.id ?? payable.label}`"
                        class="flex items-start justify-between gap-4 py-2.5 first:pt-0 last:pb-0 text-sm"
                    >
                        <span class="min-w-0 leading-snug text-slate-700">{{ payable.label }}</span>
                        <span class="shrink-0 font-semibold tabular-nums text-slate-900">{{ formatNaira(payable.amount) }}</span>
                    </li>
                </ul>
                <p v-else class="mt-2 text-sm text-slate-500">
                    The exact charge will be confirmed when you start this request.
                </p>
            </div>

            <div
                v-if="selectedService.latest_open_id"
                class="mt-5 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-3 text-sm leading-relaxed text-amber-800"
            >
                You already have an incomplete request for this document. Finish it before starting the same request again.
            </div>

            <p v-if="initiateForm.errors.service_id" class="mt-4 text-sm text-red-600">
                {{ initiateForm.errors.service_id }}
            </p>
            <p v-if="initiateForm.errors.accepted_terms" class="mt-4 text-sm text-red-600">
                {{ initiateForm.errors.accepted_terms }}
            </p>

            <label
                v-if="!selectedService.latest_open_id"
                class="mt-5 flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50/80 p-4"
            >
                <Checkbox v-model:checked="acceptedTerms" class="mt-0.5" />
                <span class="text-sm leading-relaxed text-slate-600">
                    I have read the terms and conditions and wish to proceed. Payments must be completed before the application form can be filled.
                </span>
            </label>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                    @click="closeService"
                >
                    Back
                </button>
                <button
                    v-if="selectedService.latest_open_id"
                    type="button"
                    class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-2.5 text-sm font-semibold text-[#7C3AED] hover:bg-violet-100"
                    @click="continueRequest(selectedService)"
                >
                    Continue existing request
                </button>
                <button
                    v-if="!selectedService.latest_open_id"
                    type="button"
                    class="rounded-xl bg-[#7C3AED] px-4 py-2.5 text-sm font-semibold text-white shadow-[0_8px_20px_rgba(124,58,237,0.22)] hover:bg-[#6D28D9] disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!acceptedTerms || initiateForm.processing"
                    @click="startRequest"
                >
                    {{ initiateForm.processing ? 'Starting…' : 'Start request' }}
                </button>
            </div>
        </div>
    </Modal>
</template>
