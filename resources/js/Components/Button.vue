<template>
    <button
        :class="[
            'inline-flex w-full items-center justify-center rounded-[10px] px-[20px] py-[10px] tracking-widest transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 font-bold',
            variant === 'primary'
                ? 'border border-transparent bg-[#2563EB] text-white hover:bg-gray-700 focus:bg-gray-700 focus:ring-indigo-500 active:bg-gray-900'
                : 'border-2 border-black bg-transparent text-black',
        ]"
    >
        <slot />
    </button>
</template>

<script>
export default {
    props: {
        variant: {
            type: String,
            default: 'primary',
        },
    },
};
</script>
