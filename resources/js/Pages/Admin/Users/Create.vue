<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import UserForm from '@/Pages/Admin/Users/Partials/UserForm.vue';
import { useToast } from 'vue-toastification';
import { useUsersStore } from '@/Stores/users.js';

const toast = useToast();
const usersStore = useUsersStore();

const props = defineProps({
    title: String,
    roles: {
        required: true,
        type: Object
    }
});

const form = useForm({
    name: '',
    email: '',
    roles: [],
    password: '',
    password_confirmation: ''
});

const saveUser = () => {
    form.post(route('admin.users.store', { ...usersStore.queryParams }), {
        onSuccess: () => {
            toast.success('User added.');
            form.reset();
        }
    });
};

const cancelUserAdd = () => {
    router.visit(route('admin.users.index', { ...usersStore.queryParams }));
};
</script>

<template>
    <Head :title="title"></Head>

    <h1 class="mb-8 text-3xl font-medium">{{ title }}</h1>

    <UserForm v-model="form" :roles="roles.data" @submit="saveUser" @cancel="cancelUserAdd">
        <template v-slot:header>
            <div>
                <h2 class="text-xl leading-6 font-medium text-gray-800">User information</h2>
                <p class="mt-2 text-sm text-gray-500">Use this form to create a new user.</p>
            </div>
        </template>
    </UserForm>
</template>
