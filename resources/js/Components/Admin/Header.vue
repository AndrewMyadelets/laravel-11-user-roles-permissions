<script setup>
import { ref } from 'vue';
import { useSidebar } from '@/Composables/useSidebar.js';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Bars3Icon, ChevronDownIcon } from '@heroicons/vue/24/outline/index.js';
import useClickOutside from '@/Composables/useClickOutside.js';

const { sidebarOpen, toggleSidebar, sidebar } = useSidebar();
useClickOutside(sidebar, () => (sidebarOpen.value = false), toggleSidebar);

const dropdownOpen = ref(false);
const toggleDropdown = ref();
const dropdown = ref();

const page = usePage();

useClickOutside(
    dropdown,
    () => {
        dropdownOpen.value = false;
    },
    toggleDropdown
);
</script>

<template>
    <header class="px-6 py-4 flex items-center justify-between bg-white border-b">
        <div class="flex items-center">
            <button
                ref="toggleSidebar"
                class="size-6 text-gray-500 focus:outline-none lg:hidden"
                @click="sidebarOpen = true"
            >
                <Bars3Icon />
            </button>
        </div>

        <div class="flex items-center">
            <div class="relative">
                <div
                    ref="toggleDropdown"
                    class="flex space-x-2 items-center relative z-10 overflow-hidden focus:outline-none cursor-pointer"
                    @click="dropdownOpen = !dropdownOpen"
                >
                    <span class="block text-left">
                        <span class="block font-medium">{{ page.props.auth.user.name }}</span>
                        <span class="block text-sm text-gray-500">{{ page.props.auth.user.email }}</span>
                    </span>
                    <div>
                        <ChevronDownIcon
                            class="size-4 transition duration-150 ease-out transform"
                            :class="dropdownOpen && 'rotate-180'"
                        />
                    </div>
                </div>

                <transition
                    enter-active-class="transition duration-150 ease-out transform"
                    enter-from-class="scale-95 opacity-0"
                    enter-to-class="scale-100 opacity-100"
                    leave-active-class="transition duration-150 ease-in transform"
                    leave-from-class="scale-100 opacity-100"
                    leave-to-class="scale-95 opacity-0"
                >
                    <div
                        ref="dropdown"
                        v-if="dropdownOpen"
                        class="absolute right-0 z-20 w-48 py-2 mt-4 bg-white border shadow-md"
                    >
                        <Link
                            :href="route('profile.edit')"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white"
                            >Profile
                        </Link>
                        <span
                            @click="router.post(route('logout'))"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white cursor-pointer"
                        >
                            Log out
                        </span>
                    </div>
                </transition>
            </div>
        </div>
    </header>
</template>
