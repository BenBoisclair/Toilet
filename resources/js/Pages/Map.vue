<script setup lang="ts">
import DefaultPanel from '@/Components/DefaultPanel.vue';
import InfoPanel from '@/Components/InfoPanel.vue';
import TopPanel from '@/Components/TopPanel.vue';
import { Toilet } from '@/types';
import { Head } from '@inertiajs/vue3';
import L from 'leaflet';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    toilets: Toilet[];
    MAPTILER: string;
}>();

const { toilets, MAPTILER } = props;

const coords = ref<[number, number]>([13.736717, 100.523186]);
const activeToilet = ref<Toilet | null>(null);
const isLoading = ref(false);
const isLocationEnabled = ref(false);
let map: L.Map;

const mapTilerKey = computed(() => MAPTILER || 'zugey6qt9UEumntZvzIW');

const initMap = () => {
    map = L.map('map', {
        zoomControl: false,
    }).setView(coords.value, 16);

    L.tileLayer(
        `https://api.maptiler.com/maps/basic-v2-light/{z}/{x}/{y}.png?key=${mapTilerKey.value}`,
        {
            attribution:
                '<a href="https://www.maptiler.com/copyright/" target="_blank">' +
                '&copy; MapTiler</a> ' +
                '<a href="https://www.openstreetmap.org/copyright" target="_blank">' +
                '&copy; OpenStreetMap contributors</a>',
        },
    ).addTo(map);
};

const addToiletMarkers = () => {
    toilets.forEach((toilet) => {
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

        const marker = L.marker([toilet.latitude, toilet.longitude], {
            icon: toiletIcon,
        }).addTo(map);

        marker.on('click', () => {
            activeToilet.value = toilet;
            map.setView([toilet.latitude, toilet.longitude], map.getZoom());
        });
    });
};

const getCurrentPosition = (): Promise<GeolocationPosition> => {
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0,
        });
    });
};

const fetchUserLocation = async () => {
    isLoading.value = true;
    try {
        const position = await getCurrentPosition();
        const { latitude, longitude } = position.coords;
        isLocationEnabled.value = true;
        coords.value = [latitude, longitude];
        map.setView(coords.value, 16);
    } catch (error) {
        isLocationEnabled.value = false;
        console.error('Error fetching location:', error);
    } finally {
        isLoading.value = false;
    }
};

const fetchNearestToilet = async () => {
    if (isLoading.value) return;
    isLoading.value = true;
    isLocationEnabled.value = true;
    try {
        const position = await getCurrentPosition();
        const { latitude, longitude } = position.coords;

        const response = await fetch(
            `/toilet/nearest?latitude=${latitude}&longitude=${longitude}`,
        );
        const nearestToilet = await response.json();

        if (nearestToilet) {
            activeToilet.value = nearestToilet;
            map.setView([nearestToilet.latitude, nearestToilet.longitude], 13);
        }
    } catch (error) {
        isLocationEnabled.value = false;
        console.error('Error fetching nearest toilet:', error);
    } finally {
        isLoading.value = false;
    }
};

const clearActiveMarker = () => {
    activeToilet.value = null;
};

const updateCoords = (newCoords: [number, number]) => {
    const correctedCoords: [number, number] = [newCoords[1], newCoords[0]];
    coords.value = correctedCoords;
    map.setView(correctedCoords, map.getZoom());
};

const titles = [
    'Loo-cation Explorer',
    'Flush Finder Pro',
    'Toilet Treasure Map',
    'Potty Paradise Locator',
    'Where to Go? 💩',
    'The Great Loo Hunt',
    'Throne Tracker',
    'Restroom Radar',
    'Map My Throne',
    'Peeps & Seats',
];

const currentTitle = ref(titles[0]);

const rotateTitle = () => {
    let index = 0;
    setInterval(() => {
        index = (index + 1) % titles.length;
        currentTitle.value = titles[index];
    }, 10000);
};

onMounted(() => {
    initMap();
    addToiletMarkers();
    fetchUserLocation();
    rotateTitle();
});
</script>

<template>
    <Head :title="currentTitle" />
    <div class="relative h-screen w-full overflow-hidden">
        <div id="map" class="absolute inset-0 z-0 bg-blue-500"></div>

        <div
            v-if="activeToilet"
            @click="clearActiveMarker"
            class="absolute z-10 h-full w-full bg-black/10"
        ></div>

        <div
            class="absolute z-20 flex h-full w-full flex-col"
            style="pointer-events: none"
        >
            <TopPanel :coords="coords" @update-coords="updateCoords" />

            <div class="flex-1"></div>

            <div class="flex max-h-[240px] flex-1 flex-col p-5">
                <InfoPanel
                    v-if="activeToilet"
                    :toilet="activeToilet"
                    @dismiss="clearActiveMarker"
                />
                <DefaultPanel
                    v-else
                    :onClick="fetchNearestToilet"
                    :isLoading="isLoading"
                    :isLocationEnabled="isLocationEnabled"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
#map {
    height: 100vh;
}
</style>
