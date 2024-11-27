<script setup lang="ts">
import { Facility } from '@/types';

defineProps<{
    facility: Facility;
    active?: boolean;
}>();
</script>

<template>
    <span
        :class="[
            'facility-badge rounded-2xl px-2 py-1 text-xs font-medium',
            !active
                ? 'border border-blue-600 bg-white text-blue-600'
                : 'bg-blue-100 text-blue-600 border-white border',
        ]"
    >
        {{ facility.name }}
    </span>
</template>

<style scoped>
.facility-badge {
    box-sizing: border-box;
}
</style>
