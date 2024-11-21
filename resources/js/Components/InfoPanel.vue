<script setup lang="ts">
import { Toilet } from '@/types';
import { ref } from 'vue';
import FacilityBadge from './FacilityBadge.vue';

const { toilet } = defineProps<{
    toilet: Toilet;
}>();

const emit = defineEmits(['dismiss']);

const startY = ref(0);
const currentY = ref(0);
const translateY = ref(0);
const isDismissing = ref(false);

const handleTouchStart = (e: TouchEvent) => {
    startY.value = e.touches[0].clientY;
    currentY.value = startY.value;
};

const handleTouchMove = (e: TouchEvent) => {
    currentY.value = e.touches[0].clientY;
    translateY.value = Math.max(0, currentY.value - startY.value);
    console.log(`translateY: ${translateY.value}`);
};

const handleTouchEnd = () => {
    if (translateY.value > 150) {
        isDismissing.value = true;
        setTimeout(() => {
            emit('dismiss');
        }, 200);
    } else {
        translateY.value = 0;
    }
};
</script>

<template>
    <div
        v-if="toilet"
        class="flex h-full w-full flex-col justify-between rounded-[24px] bg-white p-5 shadow-lg transition-transform"
        style="pointer-events: auto"
        :style="{
            transform: `translateY(${translateY}px)`,
            opacity: isDismissing ? 0 : 1,
        }"
        @touchstart="handleTouchStart"
        @touchmove="handleTouchMove"
        @touchend="handleTouchEnd"
    >
        <div >
            <p class="truncate text-gray-700">{{ toilet.location }}</p>
            <a :href="'/toilets/' + toilet.id">
                <h2 class="-mt-2 truncate text-[24px] font-bold">
                    {{ toilet.name }}
                </h2>
            </a>
            <div class="mt-2 flex w-full items-center">
                <span v-for="star in toilet.ratings" :key="toilet.id + star">
                    <img src="/toilet.svg" class="w-[30px]" />
                </span>
                <span
                    v-for="star in 5 - toilet.ratings"
                    :key="toilet.id + star"
                >
                    <img src="/toilet.svg" class="w-[30px] opacity-20" />
                </span>
            </div>
            <div class="mt-2 text-sm text-gray-500">Discovered by {{ toilet.discoverer.username }}</div>
        </div>
        <div class="flex w-full gap-2">
            <FacilityBadge
                v-for="facility in toilet.facilities"
                :key="toilet.id + facility.id"
                :facility="facility"
            />
        </div>
    </div>
</template>

<style scoped>
.transition-transform {
    transition:
        transform 0.3s ease,
        opacity 0.3s ease;
}
</style>
