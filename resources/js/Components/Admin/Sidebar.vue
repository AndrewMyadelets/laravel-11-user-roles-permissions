<script setup>
import { computed } from 'vue';
import { useSidebar } from '@/Composables/useSidebar.js';
import { Link } from '@inertiajs/vue3';
import { ChartBarIcon, UsersIcon, PencilSquareIcon } from '@heroicons/vue/24/outline/index.js';
import { usePage } from '@inertiajs/vue3';
import { usePermissions } from '@/Composables/usePermissions.js';

const { sidebarOpen, sidebar } = useSidebar();
const routeName = computed(() => usePage().props.routeName);
const { hasPermission } = usePermissions();

const activeClass = 'bg-gray-600 bg-opacity-25 text-gray-100 border-gray-100';
const inactiveClass =
    'border-gray-900 text-gray-500 hover:bg-gray-600 hover:bg-opacity-25 hover:text-gray-100';
</script>

<template>
    <aside ref="sidebar" class="flex h-full">
        <div
            :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
            class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-gray-900 lg:translate-x-0 lg:static lg:inset-0"
        >
            <div class="mt-8 flex items-center justify-center">
                <div class="flex items-center">
                    <div class="flex items-center justify-between">
                        <span class="mx-2 text-2xl font-semibold text-white">Dashboard</span>
                    </div>
                </div>
            </div>

            <nav class="mt-10">
                <Link
                    :href="route('admin.dashboard.index')"
                    class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4"
                    :class="routeName.startsWith('admin.dashboard.') ? activeClass : inactiveClass"
                >
                    <ChartBarIcon class="size-5" />
                    <span class="mx-4">Dashboard</span>
                </Link>

                <Link
                    v-if="hasPermission('users')"
                    :href="route('admin.users.index')"
                    class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4"
                    :class="routeName.startsWith('admin.users.') ? activeClass : inactiveClass"
                >
                    <UsersIcon class="size-5" />

                    <span class="mx-4">Users</span>
                </Link>

                <Link
                    v-if="hasPermission('roles')"
                    :href="route('admin.roles.index')"
                    class="flex items-center px-6 py-2 mt-4 duration-200 border-l-4"
                    :class="routeName.startsWith('admin.roles.') ? activeClass : inactiveClass"
                >
                    <PencilSquareIcon class="size-5" />
                    <span class="mx-4">Roles</span>
                </Link>
            </nav>
        </div>
    </aside>
</template>
