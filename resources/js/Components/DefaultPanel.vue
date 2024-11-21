<script setup lang="ts">
import { Plus } from 'lucide-vue-next';

defineProps<{ onClick: () => void; isLoading: boolean }>();
</script>

<template>
    <div class="flex flex-1 flex-col">
        <div class="flex flex-1 items-center justify-center">
            <div class="relative">
                <div
                    :class="[
                        'flex h-[100px] w-[100px] items-center justify-center rounded-full bg-white shadow-xl',
                        { 'animate-bounce': isLoading },
                    ]"
                    style="pointer-events: auto"
                    @click="onClick"
                >
                    <img src="/target.svg" />
                </div>
                <a href="/toilets/add">
                    <div
                        class="absolute -bottom-4 -right-4 flex h-[45px] w-[45px] items-center justify-center rounded-full border-4 border-black bg-white"
                        style="pointer-events: auto"
                    >
                        <Plus :size="32" />
                    </div>
                </a>
            </div>
        </div>
        <div class="mt-4">
            <div
                class="w-fit rounded-full bg-gray-200 px-6 py-2 shadow-xl"
                style="pointer-events: auto"
            >
                Filter
            </div>
        </div>
    </div>
</template>
