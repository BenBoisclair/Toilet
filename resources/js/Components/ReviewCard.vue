<script setup lang="ts">
import { Review } from '@/types';
import { formatDate } from '@/utils';

const { review } = defineProps<{ review: Review }>();

const gender = review.gender.name;
console.log(gender);
const genderIcon =
    gender === 'Male'
        ? '/male.svg'
        : gender === 'Female'
          ? '/female.svg'
          : '/handicap.svg';
</script>

<template>
    <div
        class="default-shadow flex h-full w-[300px] shrink-0 flex-col gap-[14px] rounded-3xl bg-white p-[20px]"
    >
        <div class="flex gap-2">
            <div class="h-[55px] w-[55px] shrink-0 rounded-full bg-black"></div>
            <div class="flex w-full flex-col justify-center leading-5">
                <span class="text-[14px]">{{ review.reviewer.username }}</span>
                <span class="text-[12px] text-[#818181]">{{
                    formatDate(review.created_at)
                }}</span>
            </div>
            <div class="overflow-auto">
                <img :src="genderIcon" />
            </div>
        </div>
        <div class="flex gap-1">
            <div class="flex gap-1">
                <!-- Golden Toilets -->
                <img
                    v-for="index in review.rating"
                    :key="'gold-' + review.id + '-' + index"
                    src="/golden_toilet.svg"
                    alt="Golden Toilet"
                />

                <!-- Silver Toilets -->
                <img
                    v-for="index in 5 - review.rating"
                    :key="'silver-' + review.id + '-' + index"
                    src="/silver_toilet.svg"
                    alt="Silver Toilet"
                />
            </div>
        </div>
        <div>
            {{ review.content }}
        </div>
    </div>
</template>
