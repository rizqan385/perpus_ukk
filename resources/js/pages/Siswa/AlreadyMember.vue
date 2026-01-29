<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, ArrowLeft } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Member {
    no_anggota: string;
    kelas: string | null;
    status: string;
}

defineProps<{
    member: Member;
}>();

const breadcrumbs = [
    { title: 'Dashboard Siswa', href: '/siswa' },
    { title: 'Pendaftaran', href: '/siswa/register' },
];
</script>

<template>
    <Head title="Sudah Terdaftar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 items-center justify-center p-6">
            <div class="max-w-md text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-emerald-600">
                    <CheckCircle class="h-10 w-10 text-white" />
                </div>
                <h1 class="mb-3 text-2xl font-bold text-gray-900 dark:text-white">
                    Anda Sudah Terdaftar!
                </h1>
                <p class="mb-6 text-gray-600 dark:text-gray-400">
                    Anda sudah terdaftar sebagai anggota perpustakaan dengan nomor anggota:
                </p>
                <div class="mb-6 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                    <p class="text-sm text-blue-100">No. Anggota</p>
                    <p class="text-2xl font-bold">{{ member.no_anggota }}</p>
                    <p v-if="member.kelas" class="mt-2 text-sm text-blue-100">Kelas: {{ member.kelas }}</p>
                </div>
                <Link
                    href="/siswa"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-6 py-3 font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    <ArrowLeft class="h-5 w-5" />
                    Kembali ke Dashboard
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
