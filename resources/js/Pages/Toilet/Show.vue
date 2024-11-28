<script setup lang="ts">
import Appbar from '@/Components/Appbar.vue';
import Avatar from '@/Components/Avatar.vue';
import Button from '@/Components/Button.vue';
import FacilityBadge from '@/Components/FacilityBadge.vue';
import ReviewCard from '@/Components/ReviewCard.vue';
import { Toilet } from '@/types';
import { formatDate } from '@/utils';
const { toilet } = defineProps<{ toilet: Toilet }>();

// const average =
//     typeof toilet.reviews_avg_rating === 'number'
//         ? toilet.reviews_avg_rating
//         : 0.0;
</script>

<template>
    <Appbar />
    <div v-if="toilet" class="flex flex-col pb-10">
        <div class="flex flex-col px-5 py-[20px] leading-6">
            <p class="truncate text-gray-700">{{ toilet.location }}</p>
            <h1 class="truncate text-[24px] font-bold">
                {{ toilet.name }}
            </h1>
            <div class="mt-2 flex w-full items-center gap-2">
                <!-- <span class="text-[20px]">{{ average }}</span> -->
                <img src="/toilet_star.svg" class="h-[26px] w-[21px]" />
                <!-- <span class="text-[20px]">({{ toilet.reviews_count }})</span> -->
                <img src="/info.svg" />
            </div>
        </div>
        <div class="flex w-full gap-2 px-5">
            <FacilityBadge
                v-for="facility in toilet.facilities"
                :key="toilet.id + facility.id"
                :active="true"
                :facility="facility"
            />
        </div>
        <div class="px-5 py-[20px]">
            {{ toilet.description }}
        </div>
        <div class="px-5 py-[20px] text-[20px]">
            <span class="font-bold">Discoverer</span>
            <div class="flex gap-2 py-[14px]">
                <Avatar
                    :shadows="true"
                    height="70px"
                    width="70px"
                    background="white"
                    :borders="true"
                />
                <div class="flex w-full flex-col justify-center leading-5">
                    <span class="text-[20px]">{{
                        toilet.discoverer.username
                    }}</span>
                    <span class="text-[14px] text-[#818181]">{{
                        formatDate(toilet.created_at)
                    }}</span>
                </div>
            </div>
        </div>
        <div class="py-[20px] text-[20px]" v-if="toilet.reviews.length > 0">
            <span class="px-5 font-bold">Reviews</span>
            <div
                class="no-scrollbar flex h-[300px] gap-[20px] overflow-auto px-5 py-[20px]"
            >
                <ReviewCard
                    v-for="review in toilet.reviews"
                    :key="review.id + review.created_at"
                    :review="review"
                />
            </div>
        </div>
        <div class="px-5">
            <a :href="'/toilets/' + toilet.id + '/reviews/add'"
                ><Button variant="y">Write Review</Button></a
            >
        </div>
    </div>
</template>
