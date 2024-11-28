<script setup lang="ts">
import Appbar from '@/Components/Appbar.vue';
import PageLayout from '@/Layouts/PageLayout.vue';
import { User } from '@/types';
import { Head, router } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    profile: User;
}>();

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head title="Your Profile" />
    <Appbar />
    <PageLayout>
        <div>
            <div class="flex flex-col items-center justify-center gap-2">
                <div
                    class="h-[100px] w-[100px] rounded-full border-2 border-black"
                ></div>
                <p>@{{ profile.username }}</p>
            </div>
            <div class="mt-4 text-center">
                <button
                    @click="logout"
                    class="rounded bg-red-500 px-4 py-2 text-white hover:bg-red-600 w-full"
                >
                    Log Out
                </button>
            </div>
        </div>
    </PageLayout>
</template>
