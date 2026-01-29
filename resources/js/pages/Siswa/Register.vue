<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { UserPlus, User, MapPin, Phone, GraduationCap } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    name: string;
    email: string;
}

defineProps<{
    user: User;
}>();

const form = useForm({
    kelas: '',
    alamat: '',
    telepon: '',
});

const submit = () => {
    form.post('/siswa/register');
};

const breadcrumbs = [
    { title: 'Dashboard Siswa', href: '/siswa' },
    { title: 'Pendaftaran Anggota', href: '/siswa/register' },
];
</script>

<template>
    <Head title="Pendaftaran Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="mx-auto w-full max-w-2xl">
                <!-- Header -->
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600">
                        <UserPlus class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pendaftaran Anggota</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Lengkapi data berikut untuk menjadi anggota perpustakaan
                    </p>
                </div>

                <!-- Registration Form -->
                <div class="rounded-xl border bg-white p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- User Info (Read Only) -->
                        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">
                            <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Informasi Akun</h3>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm text-gray-500 dark:text-gray-400">Nama</label>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-500 dark:text-gray-400">Email</label>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label for="kelas" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <GraduationCap class="h-4 w-4" />
                                Kelas
                            </label>
                            <input
                                id="kelas"
                                v-model="form.kelas"
                                type="text"
                                placeholder="Contoh: XII RPL 1"
                                class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                            <p v-if="form.errors.kelas" class="mt-1 text-sm text-red-600">{{ form.errors.kelas }}</p>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label for="alamat" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <MapPin class="h-4 w-4" />
                                Alamat
                            </label>
                            <textarea
                                id="alamat"
                                v-model="form.alamat"
                                rows="3"
                                placeholder="Masukkan alamat lengkap"
                                class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                            <p v-if="form.errors.alamat" class="mt-1 text-sm text-red-600">{{ form.errors.alamat }}</p>
                        </div>

                        <!-- Telepon -->
                        <div>
                            <label for="telepon" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <Phone class="h-4 w-4" />
                                Nomor Telepon
                            </label>
                            <input
                                id="telepon"
                                v-model="form.telepon"
                                type="text"
                                placeholder="Contoh: 08123456789"
                                class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                            <p v-if="form.errors.telepon" class="mt-1 text-sm text-red-600">{{ form.errors.telepon }}</p>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-3 font-semibold text-white transition-all hover:from-blue-600 hover:to-indigo-700 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else class="flex items-center justify-center gap-2">
                                <UserPlus class="h-5 w-5" />
                                Daftar Sebagai Anggota
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
