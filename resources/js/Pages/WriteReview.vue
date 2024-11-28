<script setup lang="ts">
import Appbar from '@/Components/Appbar.vue';
import Button from '@/Components/Button.vue';
import FacilityBadge from '@/Components/FacilityBadge.vue';
import InputError from '@/Components/InputError.vue';
import TextArea from '@/Components/TextArea.vue';
import PageLayout from '@/Layouts/PageLayout.vue';
import { Facility, Gender, Toilet } from '@/types';
import { router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

interface Error {
    rating: string;
    gender: string;
    content: string;
    facilities: string;
}

const { toilet, errors } = defineProps<{
    facilities: Facility[];
    toilet: Toilet;
    genders: Gender[];
    errors: Error;
}>();

let form = reactive<{
    rating: number;
    gender: number | null;
    content: string;
    facilities: number[];
}>({
    rating: 0,
    content: '',
    facilities: [],
    gender: null,
});

const selectedFacilities = ref(form.facilities || []);

const toggleFacility = (facilityId: number) => {
    if (selectedFacilities.value.includes(facilityId)) {
        selectedFacilities.value = selectedFacilities.value.filter(
            (id) => id !== facilityId,
        );
    } else {
        selectedFacilities.value.push(facilityId);
    }
};

const setRating = (rating: number) => {
    form.rating = rating;
};

const setGender = (gender: number) => {
    form.gender = gender;
};

function submit() {
    form.facilities = selectedFacilities.value;

    console.log(form);

    router.post(`/toilets/${toilet.id}/reviews/add`, {
        ...form,
    });
}

console.log(errors);
</script>

<template>
    <Head title="Write Review" />
    <Appbar title="Write Review" />
    <PageLayout>
        <form @submit.prevent="submit">
            <!-- Location Info -->
            <div>
                <p class="text-gray-500">{{ toilet.location }}</p>
                <h2 class="text-lg font-bold">{{ toilet.name }}</h2>
            </div>

            <!-- Rating -->
            <div class="mt-5">
                <p>How many golden toilets do you give it?</p>
                <div class="mt-2 flex gap-4">
                    <img
                        v-for="i in 5"
                        :key="i"
                        :src="
                            form.rating >= i
                                ? '/golden_toilet.svg'
                                : '/silver_toilet.svg'
                        "
                        class="h-[44px] cursor-pointer"
                        @click="setRating(i)"
                    />
                </div>
                <InputError v-if="errors.rating">{{
                    errors.rating
                }}</InputError>
            </div>

            <!-- Gender -->
            <div class="mt-5">
                <p>Which did you use?</p>
                <div class="mt-2 flex gap-4">
                    <img
                        v-for="gender in genders"
                        :key="'Gender-' + gender.id + '-' + gender.name"
                        :src="
                            form.gender === gender.id
                                ? '/' + gender.name + '.svg'
                                : '/' + gender.name + '_inactive.svg'
                        "
                        alt="Gender"
                        class="h-12 cursor-pointer"
                        @click="setGender(gender.id)"
                    />
                </div>
                <InputError v-if="errors.gender">{{
                    errors.gender
                }}</InputError>
            </div>

            <div class="mt-5">
                <p>How was it? (optional)</p>
                <TextArea
                    v-model="form.content"
                    class="mt-2 w-full rounded-lg border border-gray-300 p-2"
                    rows="4"
                />
                <InputError v-if="errors.content">{{
                    errors.content
                }}</InputError>
            </div>

            <div class="mt-5">
                <p>What amenities did you see?</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <FacilityBadge
                        :active="selectedFacilities.includes(facility.id)"
                        v-for="facility in facilities"
                        :key="facility.id"
                        :facility="facility"
                        @click="toggleFacility(facility.id)"
                    />
                </div>
                <InputError v-if="errors.facilities">{{
                    errors.facilities
                }}</InputError>
            </div>
            <div class="mt-10 flex justify-center">
                <Button class="font-bold"> Post </Button>
            </div>
        </form></PageLayout
    >
</template>
