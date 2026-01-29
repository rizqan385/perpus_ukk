<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { UserPlus, AlertCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    { title: 'Dashboard Siswa', href: '/siswa' },
];
</script>

<template>
    <Head title="Perlu Keanggotaan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 items-center justify-center p-6">
            <div class="max-w-md text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-orange-500">
                    <AlertCircle class="h-10 w-10 text-white" />
                </div>
                <h1 class="mb-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Pendaftaran Diperlukan
                </h1>
                <p class="mb-6 text-gray-600 dark:text-gray-400">
                    Untuk menggunakan fitur ini, Anda harus terdaftar sebagai anggota perpustakaan terlebih dahulu.
                </p>
                <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <Link
                        href="/siswa/register"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-3 font-semibold text-white transition-all hover:from-blue-600 hover:to-indigo-700"
                    >
                        <UserPlus class="h-5 w-5" />
                        Daftar Sekarang
                    </Link>
                    <Link
                        href="/siswa"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-100 px-6 py-3 font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
