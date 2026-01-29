<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { UserPlus, ArrowLeft, User, Mail, Lock, GraduationCap, MapPin, Phone } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    kelas: '',
    alamat: '',
    telepon: '',
});

const submit = () => {
    form.post('/admin/members');
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Anggota', href: '/admin/members' },
    { title: 'Tambah Anggota', href: '/admin/members/create' },
];
</script>

<template>
    <Head title="Tambah Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="mx-auto w-full max-w-2xl">
                <!-- Header -->
                <div class="mb-6 flex items-center gap-4">
                    <Link
                        href="/admin/members"
                        class="rounded-lg border p-2 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700"
                    >
                        <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Anggota Baru</h1>
                        <p class="text-gray-600 dark:text-gray-400">Buat akun dan profil anggota baru</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="rounded-xl border bg-white p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Account Info Section -->
                        <div class="border-b pb-6 dark:border-gray-700">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Informasi Akun</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <User class="h-4 w-4" />
                                        Nama Lengkap *
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label for="email" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <Mail class="h-4 w-4" />
                                        Email *
                                    </label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                                </div>

                                <div>
                                    <label for="password" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <Lock class="h-4 w-4" />
                                        Password *
                                    </label>
                                    <input
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        required
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Member Info Section -->
                        <div>
                            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Informasi Anggota</h3>
                            <div class="space-y-4">
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
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.kelas" class="mt-1 text-sm text-red-600">{{ form.errors.kelas }}</p>
                                </div>

                                <div>
                                    <label for="alamat" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <MapPin class="h-4 w-4" />
                                        Alamat
                                    </label>
                                    <textarea
                                        id="alamat"
                                        v-model="form.alamat"
                                        rows="3"
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.alamat" class="mt-1 text-sm text-red-600">{{ form.errors.alamat }}</p>
                                </div>

                                <div>
                                    <label for="telepon" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <Phone class="h-4 w-4" />
                                        Telepon
                                    </label>
                                    <input
                                        id="telepon"
                                        v-model="form.telepon"
                                        type="text"
                                        placeholder="Contoh: 08123456789"
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.telepon" class="mt-1 text-sm text-red-600">{{ form.errors.telepon }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 pt-4">
                            <Link
                                href="/admin/members"
                                class="flex-1 rounded-lg border px-4 py-2.5 text-center font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 font-medium text-white transition-colors hover:bg-emerald-700 disabled:opacity-50"
                            >
                                <UserPlus class="h-5 w-5" />
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Anggota</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
