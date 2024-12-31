<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import RoleForm from '@/Pages/Admin/Roles/Partials/RoleForm.vue';
import { useToast } from 'vue-toastification';
import { useRolesStore } from '@/Stores/roles.js';
import { usePermissions } from '@/Composables/usePermissions.js';

const toast = useToast();
const rolesStore = useRolesStore();

const props = defineProps({
    title: String,
    permissions: {
        required: true,
        type: Object
    }
});

const { buildPermissionTree } = usePermissions();
const permissions = buildPermissionTree(props.permissions);

const form = useForm({
    name: '',
    permissions: []
});

const saveRole = () => {
    form.post(route('admin.roles.store', { ...rolesStore.queryParams }), {
        onSuccess: () => {
            toast.success('Role added.');
            form.reset();
        }
    });
};

const cancelRoleAdd = () => {
    router.visit(route('admin.roles.index', { ...rolesStore.queryParams }));
};
</script>

<template>
    <Head :title="title"></Head>

    <h1 class="mb-8 text-3xl font-medium">{{ title }}</h1>

    <RoleForm v-model="form" :permissions="permissions" @submit="saveRole" @cancel="cancelRoleAdd">
        <template v-slot:header>
            <div>
                <h2 class="text-xl leading-6 font-medium text-gray-800">Role information</h2>
                <p class="mt-2 text-sm text-gray-500">Use this form to create a new role.</p>
            </div>
        </template>
    </RoleForm>
</template>
