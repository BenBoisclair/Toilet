Can i combine and refactor this

<script setup lang="ts">
import { Toilet } from '@/types';
import { debounce } from '@/utils';
import axios from 'axios';
import { Search } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import FacilityBadge from './FacilityBadge.vue';

const props = defineProps<{
    coords: number[];
}>();

const emit = defineEmits(['update-poi-coords', 'update-toilet-coords']);

const searchPOI = ref<
    Array<{
        place_name: string;
        coordinates: [number, number];
    }>
>([]);

const searchToilet = ref<Toilet[]>([]);

const hasSearched = ref(false);
let debounceTimeout: ReturnType<typeof setTimeout> | null = null;
const clickOutside = ref<HTMLElement | null>(null);

const form = reactive({
    search: '',
    lat: props.coords[0],
    lng: props.coords[1],
});

const performSearch = async () => {
    if (form.search === '') {
        return;
    }

    hasSearched.value = true;
    form.lat = props.coords[0];
    form.lng = props.coords[1];

    try {
        const response_poi = await axios.get('/toilet/search/poi', {
            params: {
                query: form.search,
                lat: form.lat,
                lng: form.lng,
            },
        });

        const response_toilet = await axios.get('/toilet/search', {
            params: {
                query: form.search,
                lat: form.lat,
                lng: form.lng,
            },
        });
        searchPOI.value = response_poi.data || [];
        searchToilet.value = response_toilet.data || [];
        console.log(searchToilet.value)
    } catch (error) {
        console.error('Error while fetching locations:', error);
    }
};

const handleKeyDown = (event: KeyboardEvent) => {
    if (event.key === 'Enter' && searchPOI.value.length > 0) {
        event.preventDefault();
        const pois = searchPOI.value[0];
        emit('update-poi-coords', pois.coordinates);
        form.search = pois.place_name;
        searchPOI.value = [];
        hasSearched.value = false;
    }
};

const handleClickOutside = (event: MouseEvent) => {
    if (
        clickOutside.value &&
        !clickOutside.value.contains(event.target as Node)
    ) {
        searchPOI.value = [];
        hasSearched.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleKeyDown);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <div
        class="flex h-[90px] items-center justify-between gap-4 p-5"
        ref="clickOutside"
    >
        <div class="relative w-full">
            <form
                @submit.prevent="performSearch"
                :class="[
                    !searchPOI.length && !hasSearched ? 'default-shadow' : '',
                    'relative z-50 flex h-full w-full max-w-[400px] items-center gap-2 rounded-3xl bg-white',
                ]"
            >
                <input
                    id="search"
                    type="text"
                    placeholder="Find Toilet and Places"
                    @input="debounce(300, debounceTimeout, performSearch)"
                    v-model="form.search"
                    class="pointer-events-auto my-2 ml-2 h-full w-full border-none bg-transparent ring-0 focus:ring-0"
                />
                <button
                    type="submit"
                    class="flex h-full w-full max-w-12 items-center justify-center rounded-full hover:bg-gray-400"
                >
                    <Search />
                </button>
            </form>
            <div
                v-if="hasSearched"
                class=" default-shadow no-scrollbar absolute left-0 top-full z-20 -mt-10 max-h-[500px] w-full overflow-y-auto rounded-3xl bg-white pt-8"
            >
                <ul>
                    <span class="font-bold text-blue-500 px-4 text-lg">Places</span>
                    <!-- POI Results -->
                    <li
                        v-for="(result, index) in searchPOI"
                        :key="'poi-' + index"
                        class="pointer-events-auto cursor-pointer px-4 py-2 hover:bg-gray-100 text-sm"
                        @click="
                            () => {
                                searchPOI = [];
                                hasSearched = false;
                                form.search = '';
                                emit('update-poi-coords', result.coordinates);
                            }
                        "
                    >
                        {{ result.place_name }}
                    </li>
                    <li v-if="searchPOI.length === 0" class="px-4 py-2">
                        No Place Results
                    </li>
                </ul>

                <ul>
                    <span class="font-bold text-blue-500 px-4 text-lg">Toilets</span>
                    <!-- Toilet Results -->
                    <li
                        v-for="(toilet, index) in searchToilet"
                        :key="'toilet-' + index"
                        class="pointer-events-auto cursor-pointer px-4 py-2 hover:bg-gray-100 text-sm"
                        @click="
                            () => {
                                searchToilet = [];
                                hasSearched = false;
                                form.search = '';
                                emit('update-toilet-coords', [toilet.id]);
                            }
                        "
                    >
                        <span>{{ toilet.name }}</span>
                        <div class="flex mt-2 gap-2">
                            <FacilityBadge size="sm" v-for="facility in toilet.facilities" :facility="facility" :key="'Search - Toilet - Badge - ' + facility.id"/>
                        </div>
                    </li>
                    <li v-if="searchToilet.length === 0" class="px-4 py-2">
                        No Toilet Results
                    </li>
                </ul>
            </div>
        </div>

        <a href="/profile">
            <div
                class="default-shadow flex h-[50px] w-[50px] items-center justify-center rounded-full border-2 border-black bg-white"
                style="pointer-events: auto"
            >
                <img src="golden_toilet.svg" alt="Profile" />
            </div>
        </a>
    </div>
</template>
