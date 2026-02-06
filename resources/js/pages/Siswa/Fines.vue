<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { AlertTriangle, CreditCard, Clock, CheckCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Book {
    id: number;
    judul: string;
    pengarang: string;
}

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    tanggal_dikembalikan: string | null;
    denda: number;
    payment_status: string | null;
    book: Book;
}

interface Member {
    no_anggota: string;
}

const props = defineProps<{
    unpaidFines: Borrowing[];
    pendingFines: Borrowing[];
    paidFines: Borrowing[];
    totalUnpaid: number;
    totalPending: number;
    member: Member;
}>();

const breadcrumbs = [
    { title: 'Dashboard Siswa', href: '/siswa' },
    { title: 'Denda', href: '/siswa/fines' },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

const payFine = (borrowing: Borrowing) => {
    if (confirm(`Apakah Anda yakin ingin membayar denda ${formatCurrency(borrowing.denda)} untuk buku "${borrowing.book.judul}"?`)) {
        router.post(`/siswa/fines/${borrowing.id}/pay`);
    }
};
</script>

<template>
    <Head title="Denda Saya" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Denda Saya</h1>
                <p class="text-gray-600 dark:text-gray-400">Kelola dan bayar denda keterlambatan</p>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border bg-gradient-to-br from-red-50 to-red-100 p-6 dark:from-red-900/20 dark:to-red-800/20 dark:border-red-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">Denda Belum Dibayar</p>
                            <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ formatCurrency(totalUnpaid) }}</p>
                        </div>
                        <div class="rounded-full bg-red-500 p-3">
                            <AlertTriangle class="h-6 w-6 text-white" />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border bg-gradient-to-br from-amber-50 to-amber-100 p-6 dark:from-amber-900/20 dark:to-amber-800/20 dark:border-amber-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-600 dark:text-amber-400">Menunggu Konfirmasi</p>
                            <p class="text-2xl font-bold text-amber-900 dark:text-amber-100">{{ formatCurrency(totalPending) }}</p>
                        </div>
                        <div class="rounded-full bg-amber-500 p-3">
                            <Clock class="h-6 w-6 text-white" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unpaid Fines -->
            <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-2">
                    <AlertTriangle class="h-5 w-5 text-red-500" />
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Denda Belum Dibayar</h2>
                </div>
                <div class="space-y-3">
                    <div
                        v-for="fine in unpaidFines"
                        :key="fine.id"
                        class="flex items-center justify-between rounded-lg bg-red-50 p-4 dark:bg-red-900/20"
                    >
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ fine.book.judul }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Kembali: {{ formatDate(fine.tanggal_dikembalikan || fine.tanggal_kembali) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold text-red-600 dark:text-red-400">
                                {{ formatCurrency(fine.denda) }}
                            </span>
                            <button
                                @click="payFine(fine)"
                                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-green-700"
                            >
                                <CreditCard class="h-4 w-4" />
                                Bayar
                            </button>
                        </div>
                    </div>
                    <div v-if="unpaidFines.length === 0" class="py-8 text-center text-gray-500">
                        Tidak ada denda yang belum dibayar 🎉
                    </div>
                </div>
            </div>

            <!-- Pending Fines -->
            <div v-if="pendingFines.length > 0" class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-2">
                    <Clock class="h-5 w-5 text-amber-500" />
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Menunggu Konfirmasi Admin</h2>
                </div>
                <div class="space-y-3">
                    <div
                        v-for="fine in pendingFines"
                        :key="fine.id"
                        class="flex items-center justify-between rounded-lg bg-amber-50 p-4 dark:bg-amber-900/20"
                    >
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ fine.book.judul }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Kembali: {{ formatDate(fine.tanggal_dikembalikan || fine.tanggal_kembali) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold text-amber-600 dark:text-amber-400">
                                {{ formatCurrency(fine.denda) }}
                            </span>
                            <span class="rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-700 dark:bg-amber-900/50 dark:text-amber-400">
                                Menunggu
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid Fines (History) -->
            <div v-if="paidFines.length > 0" class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                <div class="mb-4 flex items-center gap-2">
                    <CheckCircle class="h-5 w-5 text-green-500" />
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Pembayaran</h2>
                </div>
                <div class="space-y-3">
                    <div
                        v-for="fine in paidFines"
                        :key="fine.id"
                        class="flex items-center justify-between rounded-lg bg-green-50 p-4 dark:bg-green-900/20"
                    >
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ fine.book.judul }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ fine.book.pengarang }}
                            </p>
                        </div>
                        <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-700 dark:bg-green-900/50 dark:text-green-400">
                            Lunas
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
