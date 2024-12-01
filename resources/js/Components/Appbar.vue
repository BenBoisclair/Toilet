<script setup lang="ts">
import { ArrowLeft } from 'lucide-vue-next';


defineProps<{title?:string}>();

</script>

<template>
    <div class="flex items-center p-5 max-w-[500px]">
        <a href="/" class="flex-1">
            <div
                class="flex p-1 w-fit items-center justify-center rounded-full border-2 border-black shadow-xl"
            >
                <ArrowLeft />
            </div>
        </a>
        <div class="text-[24px] font-bold">{{ title }}</div>
        <div class="flex-1"></div>
    </div>
</template>
