<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import UserForm from '@/Pages/Admin/Users/Partials/UserForm.vue';
import { useToast } from 'vue-toastification';
import { useUsersStore } from '@/Stores/users.js';

const usersStore = useUsersStore();

const toast = useToast();

const props = defineProps({
    title: String,
    roles: {
        required: true,
        type: Object
    }
});

const user = usePage().props.user.data;

const form = useForm({
    name: user.name,
    email: user.email,
    roles: user.roles.map((role) => role.id),
    password: '',
    password_confirmation: ''
});

const saveUser = () => {
    form.put(route('admin.users.update', { user: user.id, ...usersStore.queryParams }), {
        onSuccess: () => {
            toast.success('User updated.');
            form.reset();
        }
    });
};

const cancelUserEdit = () => {
    router.visit(route('admin.users.index', { ...usersStore.queryParams }));
};
</script>

<template>
    <Head :title="title" />
    <div
        class="mb-8 flex flex-col space-y-2 justify-start items-start sm:flex-row sm:items-center sm:justify-between sm:space-y-0"
    >
        <h1 class="text-3xl font-medium">{{ title }}</h1>
    </div>

    <UserForm v-model="form" :roles="roles.data" @submit="saveUser" @cancel="cancelUserEdit">
        <template v-slot:header>
            <div class="mb-8">
                <h2 class="text-xl leading-6 font-medium text-gray-800">User information</h2>
                <p class="mt-2 text-sm text-gray-500">Use this form to edit the user.</p>
            </div>
        </template>
    </UserForm>
</template>
