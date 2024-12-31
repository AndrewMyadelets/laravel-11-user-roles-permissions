<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import RoleForm from '@/Pages/Admin/Roles/Partials/RoleForm.vue';
import { useToast } from 'vue-toastification';
import { useRolesStore } from '@/Stores/roles.js';
import { usePermissions } from '@/Composables/usePermissions.js';

const toast = useToast();
const rolesStore = useRolesStore();

const props = defineProps({
    title: String,
    permissions: {
        type: Object,
        required: true
    }
});

const { buildPermissionTree } = usePermissions();
const permissions = buildPermissionTree(props.permissions);

const role = usePage().props.role.data;

const form = useForm({
    name: role.name,
    permissions: role.permissions.map((permission) => permission.id)
});

const saveRole = () => {
    form.put(route('admin.roles.update', { role: role.id, ...rolesStore.queryParams }), {
        onSuccess: () => {
            toast.success('Role updated.');
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
                <p class="mt-2 text-sm text-gray-500">Use this form to edit the role.</p>
            </div>
        </template>
    </RoleForm>
</template>
