<script setup>
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import UserTable from '@/Pages/Admin/Users/Partials/UserTable.vue';
import { watch } from 'vue';
import { useToast } from 'vue-toastification';
import Pagination from '@/Components/Core/Pagination.vue';
import { useUsersStore } from '@/Stores/users.js';
import { usePermissions } from '@/Composables/usePermissions.js';

const props = defineProps({
    title: String,
    users: {
        required: true,
        type: Object
    }
});

const { hasPermission } = usePermissions();
const canCreate = hasPermission('users.create');
const toast = useToast();

const usersStore = useUsersStore();

usersStore.filters.search = usePage().props.filters.search;
usersStore.filters.sort = usePage().props.filters.sort;
usersStore.filters.order = usePage().props.filters.order;
usersStore.currentPage = props.users.meta.current_page;

watch(
    usersStore.filters,
    () => {
        usersStore.currentPage = 1;
        updateUrl();
    },
    { deep: true }
);

const updateUrl = () => {
    router.visit(route('admin.users.index', usersStore.queryParams), {
        replace: true,
        preserveState: true,
        preserveScroll: true
    });
};

const changePage = (page) => {
    usersStore.currentPage = page;

    updateUrl();
};

const deleteUser = (id) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.users.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('User deleted.');
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
    if (usersStore.selectedItems.length > 0) {
        const selectedItemsCount = usersStore.selectedItems.length;
        if (confirm(`Are you sure you want to delete ${selectedItemsCount} users?`)) {
            form.ids = [...usersStore.selectedItems];
            form.post(route('admin.users.mass_destroy', { ...usersStore.queryParams }), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success(`${selectedItemsCount} users deleted.`);
                    usersStore.selectedItems = [];
                    usersStore.selectAll = false;
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
            :href="route('admin.users.create')"
            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
            Add user
        </Link>
    </div>
    <UserTable
        v-model:search="usersStore.filters.search"
        v-model:sort="usersStore.filters.sort"
        v-model:order="usersStore.filters.order"
        :users="users.data"
        @delete="deleteUser"
        @mass-delete="massDelete"
    />
    <Pagination
        :current="users.meta.current_page"
        :total="users.meta.total"
        :per-page="users.meta.per_page"
        @change-page="changePage"
    />
</template>
