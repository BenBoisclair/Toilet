<script setup lang="ts">
import Appbar from '@/Components/Appbar.vue';
import Button from '@/Components/Button.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PageLayout from '@/Layouts/PageLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>

<template>
    <Head title="Log in" />
    <Appbar title="Login" />
    <PageLayout>
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="flex flex-col gap-3 rounded-xl bg-gray-100 p-5 text-sm">
            <span
                >Everybody uses toilets. Wouldn't it be nice to find the closest
                one with your preferences?</span
            >
            <span class="text-[18px] font-bold"
                ><a href="/register" class="text-blue-500 underline">Join us</a>
                and you'll be able to</span
            >
            <ul class="flex flex-col gap-2">
                <li class="flex items-center gap-1">
                    <img src="/golden_toilet.svg" class="h-5 w-5" />Expand the
                    Toilet Network
                </li>
                <li class="flex items-center gap-1">
                    <img src="/golden_toilet.svg" class="h-5 w-5" />Write
                    reviews
                </li>
            </ul>
            <span>The Golden Toilet awaits you.</span>
        </div>

        <form @submit.prevent="submit" class="mt-5">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <!-- <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link> -->

                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </Button>
            </div>
        </form>
        <div class="flex w-full justify-center gap-1 py-2">
            No Account?<a href="/register" class="text-blue-500 underline"
                >Join now!</a
            >
        </div>
    </PageLayout>
</template>
