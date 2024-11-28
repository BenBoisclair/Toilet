<script setup lang="ts">
import DefaultPanel from '@/Components/DefaultPanel.vue';
import InfoPanel from '@/Components/InfoPanel.vue';
import TopPanel from '@/Components/TopPanel.vue';
import { Toilet } from '@/types';
import { sleep } from '@/utils';
import L from 'leaflet';
import { onMounted, ref } from 'vue';
const { toilets } = defineProps<{ toilets: Toilet[] }>();

const activeToilet = ref<Toilet | null>(null);
const isLoading = ref(false);
const isLocationEnabled = ref(false);
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

const fetchNearestToilet = async () => {
    if (isLoading.value) return;
    isLoading.value = true;
    isLocationEnabled.value = true;
    try {
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                isLocationEnabled.value = true;

                const { latitude, longitude } = position.coords;

                console.log(
                    `User Location: Latitude ${latitude}, Longitude ${longitude}`,
                );

                // Fetch nearest toilet with user's location
                const response = await fetch(
                    `/toilet/nearest?latitude=${latitude}&longitude=${longitude}`,
                    {
                        method: 'GET',
                    },
                );

                const nearestToilet = await response.json();

                if (nearestToilet) {
                    activeToilet.value = nearestToilet;
                    map.setView(
                        [nearestToilet.latitude, nearestToilet.longitude],
                        13,
                    );
                }
            },
            async (error) => {
                isLocationEnabled.value = false;
                console.error('Error fetching location:', error);
            },
        );
    } catch (error) {
        console.error('Error fetching nearest toilet:', error);
    } finally {
        isLoading.value = false;
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
