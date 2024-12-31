<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: String,
    user: {
        required: true,
        type: Object
    }
});

const user = props.user.data;
</script>

<template>
    <Head :title="user.name"></Head>
    <h1 class="mb-8 text-3xl font-medium">{{ title }}</h1>
    <div class="px-4 py-6 bg-white rounded-lg">
        <h3 class="mb-6 text-xl text-gray-900 font-bold">
            {{ user.name }}
        </h3>
        <div class="mb-4 flex flex-col leading-7">
            <span class="text-gray-500">Email address</span>
            <span class="font-medium">{{ user.email }}</span>
        </div>
        <div class="mb-4 flex flex-col leading-7">
            <span class="text-gray-500">Created</span>
            <span class="font-medium">{{ user.created_at }}</span>
        </div>
        <div class="mb-4 flex-col leading-7">
            <span class="text-gray-500">Roles</span>
            <div class="mt-2 flex flex-col flex-wrap gap-2 sm:flex-row">
                <span
                    v-for="role of user.roles"
                    :key="role.id"
                    class="block w-max p-2 rounded-full bg-stone-100 text-gray-700 font-semibold text-sm leading-6"
                    >{{ role.name }}
                </span>
            </div>
        </div>
    </div>
</template>
