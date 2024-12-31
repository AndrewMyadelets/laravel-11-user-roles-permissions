<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { useRolesStore } from '@/Stores/roles.js';
import { watch } from 'vue';
import Pagination from '@/Components/Core/Pagination.vue';
import RoleTable from '@/Pages/Admin/Roles/Partials/RoleTable.vue';
import { useToast } from 'vue-toastification';
import { usePermissions } from '@/Composables/usePermissions.js';

const props = defineProps({
    title: String,
    roles: {
        required: true,
        type: Object
    }
});

const rolesStore = useRolesStore();
const { hasPermission } = usePermissions();

const canCreate = hasPermission('roles.create');
const toast = useToast();

rolesStore.filters.search = usePage().props.filters.search;
rolesStore.filters.sort = usePage().props.filters.sort;
rolesStore.filters.order = usePage().props.filters.order;
rolesStore.currentPage = props.roles.meta.current_page;

watch(
    rolesStore.filters,
    () => {
        rolesStore.currentPage = 1;
        updateUrl();
    },
    { deep: true }
);

const updateUrl = () => {
    router.visit(route('admin.roles.index', rolesStore.queryParams), {
        replace: true,
        preserveState: true,
        preserveScroll: true
    });
};

const changePage = (page) => {
    rolesStore.currentPage = page;

    updateUrl();
};

const deleteRole = (id) => {
    if (confirm('Are you sure you want to delete this role?')) {
        router.delete(route('admin.roles.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Role deleted.');
            },
            onError: (error) => {
                console.log(error);
            }
        });
    }
};

const form = useForm({
    ids: []
});

const massDelete = () => {
    if (rolesStore.selectedItems.length > 0) {
        const selectedItemsCount = rolesStore.selectedItems.length;
        if (confirm(`Are you sure you want to delete ${selectedItemsCount} roles?`)) {
            form.ids = [...rolesStore.selectedItems];
            form.post(route('admin.roles.mass_destroy', { ...rolesStore.queryParams }), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success(`${selectedItemsCount} roles deleted.`);
                    rolesStore.selectedItems = [];
                    rolesStore.selectAll = false;
                    form.reset();
                }
            });
        }
    }
};
</script>

<template>
    <Head :title="title"></Head>
    <div
        class="mb-8 flex flex-col space-y-2 justify-start items-start sm:flex-row sm:items-center sm:justify-between sm:space-y-0"
    >
        <h1 class="text-3xl font-medium">{{ title }}</h1>
        <Link
            v-if="canCreate"
            :href="route('admin.roles.create')"
            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
            Add role
        </Link>
    </div>
    <RoleTable
        v-model:search="rolesStore.filters.search"
        v-model:sort="rolesStore.filters.sort"
        v-model:order="rolesStore.filters.order"
        :roles="roles.data"
        @delete="deleteRole"
        @mass-delete="massDelete"
    />
    <Pagination
        :current="roles.meta.current_page"
        :total="roles.meta.total"
        :per-page="roles.meta.per_page"
        @change-page="changePage"
    />
</template>
