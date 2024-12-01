<script setup lang="ts">
import { Facility } from '@/types';
import { computed } from 'vue';

// Destructure props for easier access
const props = defineProps<{
    facility: Facility;
    active?: boolean;
    size?: 'sm' | 'md' | 'lg';
}>();

// Define size-specific class mappings
const sizeClasses = {
    sm: 'px-1 py-0.5 text-xs',
    md: 'px-2 py-1 text-sm',
    lg: 'px-3 py-1.5 text-base',
};

// Compute the size class based on the size prop, defaulting to 'md'
const computedSizeClass = computed(() => sizeClasses[props.size || 'md']);
</script>

<template>
    <span
        :class="[
            'facility-badge rounded-2xl font-medium',
            computedSizeClass,
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
