<script setup>
import { Head } from '@inertiajs/vue3';
import PermissionTree from '@/Pages/Admin/Roles/Partials/PermissionTree.vue';
import { usePermissions } from '@/Composables/usePermissions.js';

const props = defineProps({
    title: String,
    role: {
        required: true,
        type: Object
    }
});

const role = props.role.data;
const { buildPermissionTree } = usePermissions();
const permissions = buildPermissionTree(role.permissions);
</script>

<template>
    <Head :title="title"></Head>
    <h1 class="mb-8 text-3xl font-medium">{{ title }}</h1>
    <div class="px-4 py-6 bg-white rounded-lg">
        <h3 class="mb-6 text-xl text-gray-900 font-bold">
            {{ role.name }}
        </h3>
        <div class="mb-4 flex flex-col leading-7">
            <span class="text-gray-500">Created</span>
            <span class="font-medium">{{ role.created_at }}</span>
        </div>
        <div class="mb-4 flex-col leading-7">
            <span class="text-gray-500">Permissions</span>
            <PermissionTree
                v-if="permissions.length > 0"
                :permissions="permissions"
                :editable="false"
            />
            <span class="block" v-else>There are no permissions.</span>
        </div>
    </div>
</template>
