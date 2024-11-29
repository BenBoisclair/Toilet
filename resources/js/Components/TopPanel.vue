<script setup lang="ts">
import axios from 'axios';
import { Search } from 'lucide-vue-next';
import { onBeforeUnmount, onMounted, reactive, ref } from 'vue';

const props = defineProps<{
    coords: number[];
}>();

const emit = defineEmits(['update-coords']);

const searchResults = ref<
    Array<{
        place_name: string;
        coordinates: [number, number];
    }>
>([]);

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
        const response = await axios.get('/toilet/search', {
            params: {
                query: form.search,
                lat: form.lat,
                lng: form.lng,
            },
        });
        searchResults.value = response.data || [];
    } catch (error) {
        console.error('Error while fetching locations:', error);
    }
};

const debouncedSearch = () => {
    if (debounceTimeout) clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        performSearch();
    }, 300);
};

const handleKeyDown = (event: KeyboardEvent) => {
    if (event.key === 'Enter' && searchResults.value.length > 0) {
        event.preventDefault();
        const firstResult = searchResults.value[0];
        emit('update-coords', firstResult.coordinates);
        form.search = firstResult.place_name;
        searchResults.value = [];
        hasSearched.value = false;
    }
};

const handleClickOutside = (event: MouseEvent) => {
    if (
        clickOutside.value &&
        !clickOutside.value.contains(event.target as Node)
    ) {
        searchResults.value = [];
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
                    !searchResults.length && !hasSearched ? 'default-shadow' : '',
                    'relative z-50 flex h-full w-full max-w-[400px] items-center gap-2 rounded-3xl bg-white',
                ]"
            >
                <input
                    id="search"
                    type="text"
                    @input="debouncedSearch"
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
                class="default-shadow no-scrollbar absolute left-0 top-full z-20 -mt-10 max-h-[200px] w-full overflow-y-auto rounded-3xl bg-white pt-8"
            >
                <ul>
                    <li
                        v-for="(result, index) in searchResults"
                        :key="index"
                        class="pointer-events-auto cursor-pointer px-4 py-2 hover:bg-gray-100"
                        @click="
                            () => {
                                searchResults = [];
                                hasSearched = false;
                                form.search = '';
                                emit('update-coords', result.coordinates);
                            }
                        "
                    >
                        {{ result.place_name }}
                    </li>
                    <li v-if="searchResults.length === 0" class="px-4 py-2">
                        No Results
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
