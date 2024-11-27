<script setup lang="ts">
import Appbar from '@/Components/Appbar.vue';
import FacilityBadge from '@/Components/FacilityBadge.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import TextInput from '@/Components/TextInput.vue';
import PageLayout from '@/Layouts/PageLayout.vue';
import { Facility } from '@/types';
import { router } from '@inertiajs/vue3';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { onMounted, reactive, ref } from 'vue';

defineProps<{
    facilities: Facility[];
    errors: { name: string; description: string; facilities: string };
}>();

let form = reactive<{
    name: string;
    description: string;
    facilities: number[];
    latitude: number;
    longitude: number;
}>({
    name: '',
    description: '',
    facilities: [],
    latitude: 0,
    longitude: 0,
});

const mapContainer = ref(null);
const userCoords = ref({ lat: 13.736717, lng: 100.523186 }); // Default to Bangkok
const currentCoords = ref({ lat: 0, lng: 0 });
const selectedFacilities = ref(form.facilities || []);
const mapTile = '4BeojtqFUL3MyeiTDljY';

onMounted(() => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                userCoords.value = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                initializeMap();
            },
            () => {
                console.error('Geolocation failed, using default coords');
                initializeMap();
            },
        );
    } else {
        console.error('Geolocation is not supported');
        initializeMap();
    }
});

const initializeMap = () => {
    if (!mapContainer.value) return;

    const map = L.map(mapContainer.value, { zoomControl: false }).setView(
        [userCoords.value.lat, userCoords.value.lng],
        13,
    );

    L.tileLayer(
        'https://api.maptiler.com/maps/basic-v2-light/{z}/{x}/{y}.png?key=' + mapTile,
        {
            attribution:
                '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
        },
    ).addTo(map);

    const toiletIcon = L.divIcon({
        className: '',
        iconAnchor: [20, 0],
        iconSize: [32, 40],
        html: `
                    <div style="
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    overflow: hidden;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border: 2px solid #000;
                    background-color: #fff;
                    ">
                    <img src="/toilet.svg" alt="Toilet Icon" style="
                        width: 70%;
                        height: 70%;
                        object-fit: contain;
                    " />
                    </div>
                    `,
    });

    const marker = L.marker([userCoords.value.lat, userCoords.value.lng], {
        draggable: false,
        icon: toiletIcon,
    }).addTo(map);

    currentCoords.value = { ...userCoords.value };

    map.on('move', () => {
        const center = map.getCenter();
        marker.setLatLng(center);
        currentCoords.value = { lat: center.lat, lng: center.lng };
    });
};

const toggleFacility = (facilityId: number) => {
    if (selectedFacilities.value.includes(facilityId)) {
        selectedFacilities.value = selectedFacilities.value.filter(
            (id) => id !== facilityId,
        );
    } else {
        selectedFacilities.value.push(facilityId);
    }
};

function submit() {
    form.facilities = selectedFacilities.value;
    form.latitude = currentCoords.value.lat;
    form.longitude = currentCoords.value.lng;

    router.post('/toilets/add', {
        ...form,
    });
}
</script>

<template>
    <Appbar title="Add a Bathroom" />
    <PageLayout>
        <form @submit.prevent="submit">
            <div
                class="mb-2 h-[300px] w-full rounded-3xl"
                ref="mapContainer"
            ></div>
            <div class="flex w-full justify-center text-[14px] text-gray-400">
                Move map to correct pin location
            </div>
            <div class="mt-5 flex flex-col gap-4">
                <div class="flex flex-col">
                    <InputLabel for="name" value="Name" />
                    <!-- <input
                        v-model="form.name"
                        name="name"
                        placeholder="e.g, Red Bathroom"
                        id="name"
                        type="text"
                        class="mt-1 rounded-lg border-0 bg-gray-200"
                        required
                    /> -->
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                    />
                    <InputError v-if="errors.name" :message="form.name" />
                </div>
                <div class="flex flex-col">
                    <InputLabel
                        for="description"
                        value="How would you describe it?"
                    />
                    <TextArea
                        v-model="form.description"
                        name="description"
                        placeholder=""
                        rows="3"
                        id="description"
                        required
                    />
                    <InputError
                        v-if="errors.description"
                        :message="errors.description"
                    />
                </div>
                <div class="flex flex-col">
                    <InputLabel value="What amenities did you see?" />
                    <div class="mt-2 flex gap-4">
                        <FacilityBadge
                            :active="selectedFacilities.includes(facility.id)"
                            v-for="facility in facilities"
                            :key="facility.id"
                            :facility="facility"
                            @click="toggleFacility(facility.id)"
                        />
                    </div>
                    <InputError
                        v-if="errors.facilities"
                        :message="errors.facilities"
                    />
                </div>
            </div>
            <div class="mt-10 flex justify-center">
                <PrimaryButton class="ms-4"> Add Bathroom </PrimaryButton>
            </div>
        </form>
    </PageLayout>
</template>
