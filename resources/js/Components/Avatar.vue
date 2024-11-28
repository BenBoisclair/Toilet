<template>
    <div
        :class="[
            shadows ? 'default-shadow' : '',
            'flex',
            'items-center',
            'justify-center',
            'rounded-full',
            borders ? 'border-2' : '',
            borders ? 'border-black' : '',
            'shrink-0',
        ]"
        :style="{
            pointerEvents: 'auto',
            height: height,
            width: width,
            background: background,
        }"
    >
        <img src="/toilet.svg" class="w-[{{ width }}] h-[{{ height }}]" />
    </div>
</template>

<script>
export default {
    props: {
        height: {
            type: String,
            default: '50px',
        },
        width: {
            type: String,
            default: '50px',
        },
        shadows: {
            type: Boolean,
            default: true,
        },
        background: {
            type: String,
            default: "white",
        },
        borders: {
            type: Boolean,
            default: false,
        }
    },
};
</script>
