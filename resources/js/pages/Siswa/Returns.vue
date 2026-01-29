<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { BookOpen, ArrowLeftRight, Clock, AlertTriangle, CheckCircle, Calendar } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

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
    status: string;
    denda: number;
    book: Book;
}

interface Member {
    no_anggota: string;
    status: string;
}

const props = defineProps<{
    activeBorrowings: Borrowing[];
    returnHistory: Borrowing[];
    member: Member;
}>();

const selectedBorrowing = ref<Borrowing | null>(null);
const showConfirmModal = ref(false);

const isOverdue = (borrowing: Borrowing) => {
    return new Date(borrowing.tanggal_kembali) < new Date();
};

const getDaysRemaining = (borrowing: Borrowing) => {
    const today = new Date();
    const returnDate = new Date(borrowing.tanggal_kembali);
    const diffTime = returnDate.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
};

const openConfirmModal = (borrowing: Borrowing) => {
    selectedBorrowing.value = borrowing;
    showConfirmModal.value = true;
};

const closeConfirmModal = () => {
    selectedBorrowing.value = null;
    showConfirmModal.value = false;
};

const confirmReturn = () => {
    if (!selectedBorrowing.value) return;
    router.post(`/siswa/returns/${selectedBorrowing.value.id}`, {}, {
        onSuccess: () => closeConfirmModal()
    });
};

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

const breadcrumbs = [
    { title: 'Dashboard Siswa', href: '/siswa' },
    { title: 'Pengembalian Buku', href: '/siswa/returns' },
];
</script>

<template>
    <Head title="Pengembalian Buku" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pengembalian Buku</h1>
                <p class="text-gray-600 dark:text-gray-400">Kembalikan buku yang sedang Anda pinjam</p>
            </div>

            <!-- Active Borrowings -->
            <div class="rounded-xl border bg-white p-6 dark:bg-gray-800 dark:border-gray-700">
                <h2 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                    <Clock class="h-5 w-5" />
                    Buku yang Harus Dikembalikan
                </h2>

                <div v-if="activeBorrowings.length > 0" class="space-y-4">
                    <div
                        v-for="borrowing in activeBorrowings"
                        :key="borrowing.id"
                        :class="[
                            'flex flex-col gap-4 rounded-xl border p-4 md:flex-row md:items-center md:justify-between',
                            isOverdue(borrowing) 
                                ? 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-900/20' 
                                : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-700/50'
                        ]"
                    >
                        <div class="flex items-start gap-4">
                            <div :class="[
                                'rounded-lg p-3',
                                isOverdue(borrowing) 
                                    ? 'bg-red-100 dark:bg-red-900/50' 
                                    : 'bg-blue-100 dark:bg-blue-900/50'
                            ]">
                                <BookOpen :class="[
                                    'h-6 w-6',
                                    isOverdue(borrowing) ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400'
                                ]" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ borrowing.book.judul }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ borrowing.book.pengarang }}</p>
                                <div class="mt-2 flex flex-wrap items-center gap-3 text-sm">
                                    <span class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                        <Calendar class="h-4 w-4" />
                                        Dipinjam: {{ formatDate(borrowing.tanggal_pinjam) }}
                                    </span>
                                    <span :class="[
                                        'flex items-center gap-1',
                                        isOverdue(borrowing) ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'
                                    ]">
                                        <Clock class="h-4 w-4" />
                                        Batas: {{ formatDate(borrowing.tanggal_kembali) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <template v-if="isOverdue(borrowing)">
                                <span class="flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-700 dark:bg-red-900/50 dark:text-red-400">
                                    <AlertTriangle class="h-4 w-4" />
                                    Terlambat {{ Math.abs(getDaysRemaining(borrowing)) }} hari
                                </span>
                            </template>
                            <template v-else>
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-700 dark:bg-green-900/50 dark:text-green-400">
                                    {{ getDaysRemaining(borrowing) }} hari tersisa
                                </span>
                            </template>
                            <button
                                @click="openConfirmModal(borrowing)"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700"
                            >
                                <ArrowLeftRight class="h-4 w-4" />
                                Kembalikan
                            </button>
                        </div>
                    </div>
                </div>

                <div v-else class="py-12 text-center">
                    <CheckCircle class="mx-auto mb-4 h-16 w-16 text-green-400" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak Ada Buku yang Dipinjam</h3>
                    <p class="text-gray-500 dark:text-gray-400">Semua buku telah dikembalikan</p>
                </div>
            </div>

            <!-- Return History -->
            <div class="rounded-xl border bg-white p-6 dark:bg-gray-800 dark:border-gray-700">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Riwayat Pengembalian</h2>

                <div v-if="returnHistory.length > 0" class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Buku</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tgl Pinjam</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tgl Kembali</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Denda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="item in returnHistory" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ item.book.judul }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ item.book.pengarang }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ formatDate(item.tanggal_pinjam) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ formatDate(item.tanggal_dikembalikan || item.tanggal_kembali) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        item.status === 'dikembalikan' 
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                            : 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'
                                    ]">
                                        {{ item.status === 'dikembalikan' ? 'Dikembalikan' : 'Terlambat' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="item.denda > 0" class="font-medium text-red-600 dark:text-red-400">
                                        {{ formatCurrency(item.denda) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="py-8 text-center text-gray-500 dark:text-gray-400">
                    Belum ada riwayat pengembalian
                </div>
            </div>
        </div>

        <!-- Confirm Modal -->
        <Teleport to="body">
            <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-800">
                    <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Konfirmasi Pengembalian</h3>
                    <div class="mb-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-700">
                        <p class="font-medium text-gray-900 dark:text-white">{{ selectedBorrowing?.book.judul }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ selectedBorrowing?.book.pengarang }}</p>
                    </div>
                    <template v-if="selectedBorrowing && isOverdue(selectedBorrowing)">
                        <div class="mb-4 rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                            <p class="flex items-center gap-2 text-sm font-medium text-red-700 dark:text-red-400">
                                <AlertTriangle class="h-4 w-4" />
                                Buku ini terlambat dikembalikan
                            </p>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-300">
                                Denda keterlambatan akan dihitung berdasarkan jumlah hari terlambat (Rp 1.000/hari).
                            </p>
                        </div>
                    </template>
                    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                        Apakah Anda yakin ingin mengembalikan buku ini?
                    </p>
                    <div class="flex gap-3">
                        <button
                            @click="closeConfirmModal"
                            class="flex-1 rounded-lg border px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Batal
                        </button>
                        <button
                            @click="confirmReturn"
                            class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 font-medium text-white transition-colors hover:bg-emerald-700"
                        >
                            Konfirmasi
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
