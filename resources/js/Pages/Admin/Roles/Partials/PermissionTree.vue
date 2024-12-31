<script setup>
import { onMounted, ref } from 'vue';
import { ChevronDownIcon } from '@heroicons/vue/24/outline/index.js';

const props = defineProps({
    permissions: {
        type: Array,
        required: true
    },
    editable: {
        type: Boolean,
        default: true
    }
});

const selectedPermissions = defineModel();
const openNodes = ref({});

onMounted(() => {
    props.permissions.forEach((permission) => {
        if (permission.children && permission.children.length > 0 && !permission.parent) {
            openNodes.value[permission.id] = true;
        }
    });
});

const toggleNode = (id) => {
    openNodes.value[id] = !openNodes.value[id];
};

const setParentPermissions = (parent, isChecked) => {
    const index = selectedPermissions.value.indexOf(parent.id);
    if (isChecked && index === -1) {
        selectedPermissions.value.push(parent.id);
    }

    if (parent.parent) {
        setParentPermissions(parent.parent, isChecked);
    }
};

const setChildrenPermissions = (children, isChecked) => {
    children.forEach((child) => {
        const index = selectedPermissions.value.indexOf(child.id);
        if (isChecked && index === -1) {
            selectedPermissions.value.push(child.id);
        } else if (!isChecked && index !== -1) {
            selectedPermissions.value.splice(index, 1);
        }

        if (child.children && child.children.length > 0) {
            setChildrenPermissions(child.children, isChecked);
        }
    });
};

const togglePermission = (permission) => {
    const isChecked = selectedPermissions.value.includes(permission.id);

    if (!isChecked) {
        setChildrenPermissions(permission.children || [], false);
    } else {
        if (permission.parent) {
            setParentPermissions(permission.parent, true);
        }
    }
};
</script>

<template>
    <ul>
        <li v-for="permission in permissions" :key="permission.id">
            <div class="flex items-center">
                <div class="flex items-center w-6">
                    <ChevronDownIcon
                        v-if="permission.children && permission.children.length > 0"
                        class="size-4 transition duration-150 ease-out transform cursor-pointer"
                        :class="openNodes[permission.id] && 'rotate-180'"
                        @click="toggleNode(permission.id)"
                    />
                </div>
                <div class="flex-1">
                    <input
                        :checked="editable === false"
                        :disabled="editable === false"
                        id="permissions"
                        type="checkbox"
                        class="mr-2"
                        :value="permission.id"
                        v-model="selectedPermissions"
                        @change="togglePermission(permission)"
                    />
                    <label for="permissions" class="text-sm font-normal text-gray-900">
                        {{ permission.display_name }}
                    </label>
                </div>
            </div>
            <transition
                enter-active-class="transition duration-150 ease-out transform"
                enter-from-class="-translate-y-4 max-h-0 opacity-0"
                enter-to-class="translate-y-0 max-h-full opacity-100"
                leave-active-class="transition duration-0 ease-in transform"
                leave-from-class="translate-y-0 max-h-full opacity-100"
                leave-to-class="-translate-y-4 max-h-0 opacity-0"
            >
                <div v-if="openNodes[permission.id]" class="ml-2 pl-4 border-l border-gray-200">
                    <PermissionTree
                        :permissions="permission.children"
                        v-model="selectedPermissions"
                        :editable="editable"
                    />
                </div>
            </transition>
        </li>
    </ul>
</template>
