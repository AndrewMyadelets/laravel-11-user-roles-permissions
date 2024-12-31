<script setup>
import InputError from '@/Components/InputError.vue';
import PermissionTree from '@/Pages/Admin/Roles/Partials/PermissionTree.vue';

const props = defineProps({
    permissions: {
        required: true,
        type: Object
    }
});

const form = defineModel();

const emits = defineEmits(['submit', 'cancel']);
</script>

<template>
    <form class="px-4 py-6 bg-white rounded-lg" @submit.prevent="emits('submit')">
        <div class="mb-8">
            <slot name="header"></slot>
        </div>
        <div class="pb-12 border-b border-gray-900/10">
            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-4">
                    <label for="name" class="block text-sm font-medium text-gray-900">Name</label>
                    <div class="mt-2">
                        <input
                            type="text"
                            name="name"
                            id="name"
                            autocomplete="name"
                            class="py-1.5 block w-full rounded-md border-0 shadow-sm flex-1 bg-transparent text-gray-900 text-sm sm:leading-6 ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-600 sm:max-w-md"
                            :class="{ 'ring-red-300': form.errors.name }"
                            v-model="form.name"
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                </div>
                <div class="sm:col-span-4">
                    <h3 class="block font-medium text-sm text-gray-900">Select permissions</h3>
                    <div class="mt-2">
                        <PermissionTree :permissions="permissions" v-model="form.permissions" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <button
                type="button"
                class="text-sm font-semibold leading-6 text-gray-900"
                @click="emits('cancel')"
            >
                Cancel
            </button>
            <button
                type="submit"
                class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-gray-100 hover:text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                Save
            </button>
        </div>
    </form>
</template>
