<script setup lang="ts">
import DefaultPanel from '@/Components/DefaultPanel.vue';
import InfoPanel from '@/Components/InfoPanel.vue';
import TopPanel from '@/Components/TopPanel.vue';
import { Toilet } from '@/types';
import L from 'leaflet';
import { onMounted, ref } from 'vue';
const { toilets } = defineProps<{ toilets: Toilet[] }>();

const activeToilet = ref<Toilet | null>(null);
const isLoading = ref(false);
let map: L.Map;

onMounted(() => {
    map = L.map('map', {
        zoomControl: false,
    }).setView(
        [
            // toilets[0].latitude ??
            13.736717,
            // toilets[0].longitude ??
            100.523186,
        ],
        13,
    );

    const mapTile = '4BeojtqFUL3MyeiTDljY';

    L.tileLayer(
        `https://api.maptiler.com/maps/basic-v2-light/{z}/{x}/{y}.png?key=` +
            mapTile,
        {
            attribution:
                '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>',
        },
    ).addTo(map);

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
});

const clearActiveMarker = () => {
    activeToilet.value = null;
};

const fetchRandomToilet = async () => {
    if (isLoading.value) return;
    isLoading.value = true;
    console.log(`Searching for Toilet... \nLoading Status: ${isLoading.value}`);
    try {
        const response = await fetch('/toilet/random');
        const randomToilet = await response.json();
        if (randomToilet) {
            activeToilet.value = randomToilet;
            map.setView([randomToilet.latitude, randomToilet.longitude], 13);
        }
    } catch (error) {
        console.error('Error fetching random toilet:', error);
    } finally {
        isLoading.value = false;
        console.log(`Search Completed! \nLoading Status: ${isLoading.value}`);
    }
};
</script>

<template>
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
            <TopPanel />

            <div class="flex-1"></div>

            <div class="flex max-h-[240px] flex-1 flex-col p-5">
                <InfoPanel
                    v-if="activeToilet"
                    :toilet="activeToilet"
                    @dismiss="clearActiveMarker"
                />
                <DefaultPanel
                    v-else
                    :onClick="fetchRandomToilet"
                    :isLoading="isLoading"
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
