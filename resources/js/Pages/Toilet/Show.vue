<script setup lang="ts">
import { Toilet } from '@/types';
const { toilet } = defineProps<{ toilet: Toilet }>();
</script>

<template>
    <div v-if="toilet" class="flex flex-col p-5">
        <p class="truncate text-gray-700">{{ toilet.location }}</p>
        <h1 class="-mt-2 truncate text-[24px] font-bold">{{ toilet.name }}</h1>
    </div>
</template>
