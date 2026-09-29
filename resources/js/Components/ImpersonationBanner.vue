<script setup>
import { hardPost } from '@/Composables/useHardVisit';
import { useSweetAlert } from '@/Composables/useSweetAlert';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const { confirm, toast } = useSweetAlert();

const previousUser = computed(() => page.props.auth?.previous_user);
const currentUser = computed(() => page.props.auth?.user);
const impersonatedInactive = computed(() => page.props.auth?.impersonated_inactive === true);

const currentName = computed(() => {
    const user = currentUser.value;
    if (! user) {
        return '';
    }

    return [user.fname, user.mname, user.lname].filter(Boolean).join(' ') || user.email || 'this user';
});

const returnToAccount = async () => {
    if (! previousUser.value?.id) {
        return;
    }

    const previousName = [previousUser.value.fname, previousUser.value.mname, previousUser.value.lname]
        .filter(Boolean)
        .join(' ') || 'your account';

    const confirmed = await confirm({
        icon: 'question',
        title: 'Return to your account?',
        text: `You will return to ${previousName}.`,
        confirmButtonText: 'Yes, return!',
    });

    if (!confirmed) {
        await toast('Cancelled', 'Operation cancelled.', 'info');
        return;
    }

    await hardPost(route('dologinback'), { id: previousUser.value.id });
};
</script>

<template>
    <div
        v-if="previousUser"
        :class="[
            'sticky top-0 z-40 text-white text-sm px-4 py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2',
            impersonatedInactive ? 'bg-amber-600' : 'bg-green-600',
        ]"
    >
        <span>
            You are currently logged in as <strong>{{ currentName }}</strong>
            <span v-if="impersonatedInactive" class="block sm:inline sm:ms-1 font-medium">
                (this account is inactive)
            </span>
        </span>
        <button
            type="button"
            @click="returnToAccount"
            :class="[
                'inline-flex items-center justify-center px-3 py-1 bg-white text-xs font-semibold rounded transition',
                impersonatedInactive ? 'text-amber-700 hover:bg-amber-50' : 'text-green-700 hover:bg-green-50',
            ]"
        >
            Return to my Account
        </button>
    </div>
</template>
