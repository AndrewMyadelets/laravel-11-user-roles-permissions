<script setup>
import { router } from '@inertiajs/vue3';
import TableFilterableCell from '@/Components/Core/TableFilterableCell.vue';
import { ref, watch } from 'vue';
import { ChevronDownIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline/index.js';
import useClickOutside from '@/Composables/useClickOutside.js';
import { useRolesStore } from '@/Stores/roles.js';
import { usePermissions } from '@/Composables/usePermissions.js';

const rolesStore = useRolesStore();

const props = defineProps({
    roles: {
        type: Object,
        required: true
    }
});

const emits = defineEmits(['delete', 'mass-delete']);

const search = defineModel('search');
const sort = defineModel('sort');
const order = defineModel('order');

const sortRoles = (field) => {
    if (field === sort.value) {
        if (order.value === 'desc') {
            order.value = 'asc';
        } else {
            order.value = 'desc';
        }
    } else {
        sort.value = field;
        order.value = 'asc';
    }
};

const dropdownOpen = ref(false);
const toggleDropdown = ref();
const dropdown = ref();

const { hasPermission, hasAnyPermission } = usePermissions();

const canView = hasPermission('roles.view');
const canEdit = hasPermission('roles.update');
const canDelete = hasPermission('roles.delete');

const actionPermissions = ['roles.view', 'roles.update', 'roles.delete'];
const canAction = hasAnyPermission(actionPermissions);

const massActionPermissions = ['roles.massDelete'];
const canMassAction = hasAnyPermission(massActionPermissions);

useClickOutside(
    dropdown,
    () => {
        dropdownOpen.value = false;
    },
    toggleDropdown
);

const toggleSelectAll = () => {
    if (rolesStore.selectAll) {
        rolesStore.selectedItems = props.roles.map((role) => role.id);
    } else {
        rolesStore.selectedItems = [];
    }
};

watch(
    () => rolesStore.selectedItems,
    (newSelected) => {
        rolesStore.selectAll = newSelected.length === props.roles.length;
    }
);

watch(
    () => props.roles,
    () => {
        rolesStore.selectedItems = [];
        rolesStore.selectAll = false;
    }
);

const massDelete = () => {
    dropdownOpen.value = false;
    emits('mass-delete');
};
</script>

<template>
    <div v-if="roles.length || search" class="px-4 py-4 bg-white rounded-lg">
        <div
            class="mb-4 flex justify-between flex-col-reverse space-y-reverse space-y-4 flex-wrap sm:flex-row sm:space-y-0 sm:items-center relative"
        >
            <div v-if="canMassAction">
                <button
                    ref="toggleDropdown"
                    @click="dropdownOpen = !dropdownOpen"
                    class="px-3 py-2 flex space-x-2 items-center text-gray-700 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none hover:bg-gray-100 focus:ring-2 focus:ring-gray-100 select-none"
                    type="button"
                >
                    <span class="sr-only">Action button</span>
                    Action
                    <ChevronDownIcon
                        class="size-4 transition duration-150 ease-out transform"
                        :class="dropdownOpen && 'rotate-180'"
                    />
                </button>
                <div
                    ref="dropdown"
                    v-if="dropdownOpen"
                    class="absolute z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44"
                >
                    <div class="py-1">
                        <button
                            class="px-4 py-2 w-full flex text-sm text-gray-700 hover:bg-gray-100"
                            @click="massDelete"
                            :disabled="rolesStore.selectedItems.length === 0"
                        >
                            Delete roles
                        </button>
                    </div>
                </div>
            </div>
            <label for="table-search" class="sr-only">Search</label>
            <div class="relative">
                <div
                    class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none"
                >
                    <MagnifyingGlassIcon class="size-4 text-gray-500" />
                </div>
                <input
                    type="text"
                    id="table-search-roles"
                    class="p-2 pl-10 block text-sm text-gray-900 border border-gray-300 rounded-lg w-80 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search for roles"
                    v-model="search"
                />
            </div>
        </div>
        <div v-if="roles.length" class="relative overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-sm text-left text-gray- 00">
                <thead class="text-xs text-gray-800 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="p-4" v-if="canMassAction">
                            <div class="flex items-center">
                                <input
                                    id="checkbox-all-search"
                                    type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                    v-model="rolesStore.selectAll"
                                    @change="toggleSelectAll"
                                />
                                <label for="checkbox-all-search" class="sr-only">checkbox</label>
                            </div>
                        </th>
                        <TableFilterableCell
                            :class="{ 'p-4': !canMassAction }"
                            field="id"
                            :sort="sort"
                            :order="order"
                            @click="sortRoles('id')"
                            >ID
                        </TableFilterableCell>
                        <TableFilterableCell
                            field="name"
                            :sort="sort"
                            :order="order"
                            @click="sortRoles('name')"
                            >Name
                        </TableFilterableCell>
                        <TableFilterableCell
                            field="created_at"
                            :sort="sort"
                            :order="order"
                            @click="sortRoles('created_at')"
                            >Created
                        </TableFilterableCell>
                        <th scope="col" class="px-6 py-3" v-if="canAction">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="role of roles"
                        :key="role.id"
                        class="bg-white border-b hover:bg-gray-50"
                    >
                        <td class="w-4 p-4" v-if="canMassAction">
                            <div class="flex items-center">
                                <input
                                    id="checkbox-table-search-1"
                                    type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                    v-model="rolesStore.selectedItems"
                                    :value="role.id"
                                />
                                <label for="checkbox-table-search-1" class="sr-only"
                                    >checkbox</label
                                >
                            </div>
                        </td>
                        <td class="p-2" :class="{ 'p-4': !canMassAction }">{{ role.id }}</td>
                        <th scope="row" class="p-2 font-normal">{{ role.name }}</th>
                        <td class="p-2">{{ role.created_at }}</td>
                        <td class="px-6 py-3" v-if="canAction">
                            <div
                                class="flex items-center flex-col space-y-1 sm:flex-row sm:space-x-2 sm:space-y-0"
                            >
                                <span
                                    v-if="canView"
                                    @click="
                                        router.visit(route('admin.roles.show', { role: role.id }))
                                    "
                                    class="text-green-600 hover:underline cursor-pointer"
                                    >Show
                                </span>
                                <span
                                    v-if="canEdit"
                                    @click="
                                        router.visit(route('admin.roles.edit', { role: role.id }))
                                    "
                                    class="text-blue-600 hover:underline cursor-pointer"
                                    >Edit
                                </span>
                                <span
                                    v-if="canDelete"
                                    @click="emits('delete', role.id)"
                                    class="text-red-600 hover:underline cursor-pointer"
                                    >Delete
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-8 text-gray-700 text-center" v-else-if="!roles.length && search">
            There are no results for your request.
        </div>
    </div>
    <div v-else>No roles.</div>
</template>
