<script setup lang="ts">
import { Search } from 'lucide-vue-next';
import { reactive } from 'vue';

let form = reactive<{
    search: string;
}>({
    search: '',
});

function submit() {
    console.log('Search');
    // router.post('/map/search', {
    //     ...form,
    // });
}
</script>

<template>
    <div class="flex h-[90px] items-center justify-between gap-4 p-5">
        <!-- <span class="font-bold">V.0.5</span> -->
        <form
            @submit.prevent="submit"
            class="default-shadow flex h-full w-full max-w-[400px] items-center gap-2 rounded-3xl bg-white hidden"
        >
            <input
                id="search"
                type="text"
                v-model="form.search"
                class="pointer-events-auto h-full w-full border-none bg-transparent ring-0 focus:ring-0 my-2 ml-2"
            />
            <button
                type="submit"
                class="flex h-full w-full max-w-12 items-center justify-center rounded-full hover:bg-gray-400"
            >
                <Search />
            </button>
        </form>
        <a href="/profile"
            ><div
                class="default-shadow flex h-[50px] w-[50px] items-center justify-center rounded-full border-2 border-black bg-white"
                style="pointer-events: auto"
            >
                <img src="golden_toilet.svg" /></div
        ></a>
    </div>
</template>
