<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { BookOpen, ArrowLeftRight, Clock, AlertTriangle, UserPlus, Calendar, CreditCard } from 'lucide-vue-next';
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
    status: string;
    denda: number;
    book: Book;
}

interface Member {
    no_anggota: string;
    kelas: string | null;
    status: string;
}

interface Stats {
    active_borrowings: number;
    total_borrowed: number;
    total_fines: number;
}

const props = defineProps<{
    hasMembership: boolean;
    member?: Member;
    stats?: Stats;
    activeBorrowings: Borrowing[];
    borrowingHistory: Borrowing[];
    unpaidFines?: Borrowing[];
}>();

const breadcrumbs = [
    { title: 'Dashboard Siswa', href: '/siswa' },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const formatCurrency = (amount: number | string) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(Number(amount));
};

const payFine = (borrowing: Borrowing) => {
    if (confirm(`Apakah Anda yakin ingin membayar denda ${formatCurrency(borrowing.denda)} untuk buku "${borrowing.book.judul}"?`)) {
        router.post(`/siswa/fines/${borrowing.id}/pay`);
    }
};

const isOverdue = (borrowing: Borrowing) => {
    return new Date(borrowing.tanggal_kembali) < new Date() && borrowing.status === 'dipinjam';
};

const getDaysLate = (borrowing: Borrowing) => {
    const today = new Date();
    const returnDate = new Date(borrowing.tanggal_kembali);
    const diffTime = today.getTime() - returnDate.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays > 0 ? diffDays : 0;
};
</script>

<template>
    <Head title="Dashboard Siswa" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Siswa</h1>
                    <p class="text-gray-600 dark:text-gray-400">Selamat datang di portal perpustakaan</p>
                </div>
            </div>

            <!-- Not a Member Yet -->
            <template v-if="!hasMembership">
                <div class="rounded-xl border-2 border-dashed border-amber-300 bg-gradient-to-br from-amber-50 to-orange-50 p-8 text-center dark:from-amber-900/20 dark:to-orange-900/20 dark:border-amber-700">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/50">
                        <UserPlus class="h-8 w-8 text-amber-600 dark:text-amber-400" />
                    </div>
                    <h2 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">Anda Belum Terdaftar Sebagai Anggota</h2>
                    <p class="mb-6 text-gray-600 dark:text-gray-400">
                        Untuk dapat meminjam buku di perpustakaan, Anda harus mendaftar sebagai anggota terlebih dahulu.
                    </p>
                    <Link
                        href="/siswa/register"
                        class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-6 py-3 font-semibold text-white transition-colors hover:bg-amber-600"
                    >
                        <UserPlus class="h-5 w-5" />
                        Daftar Sekarang
                    </Link>
                </div>
            </template>

            <!-- Member Dashboard -->
            <template v-else>
                <!-- Member Info Card -->
                <div class="rounded-xl border bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm text-blue-100">No. Anggota</p>
                            <p class="text-2xl font-bold">{{ member?.no_anggota }}</p>
                            <p v-if="member?.kelas" class="mt-1 text-blue-100">Kelas: {{ member.kelas }}</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <span
                                :class="[
                                    'rounded-full px-3 py-1 text-sm font-medium',
                                    member?.status === 'aktif' ? 'bg-green-400/20 text-green-100' : 'bg-red-400/20 text-red-100'
                                ]"
                            >
                                {{ member?.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-xl border bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:from-blue-900/20 dark:to-blue-800/20 dark:border-blue-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Buku Dipinjam</p>
                                <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ stats?.active_borrowings || 0 }}</p>
                            </div>
                            <div class="rounded-full bg-blue-500 p-3">
                                <BookOpen class="h-6 w-6 text-white" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 dark:from-emerald-900/20 dark:to-emerald-800/20 dark:border-emerald-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Total Peminjaman</p>
                                <p class="text-3xl font-bold text-emerald-900 dark:text-emerald-100">{{ stats?.total_borrowed || 0 }}</p>
                            </div>
                            <div class="rounded-full bg-emerald-500 p-3">
                                <ArrowLeftRight class="h-6 w-6 text-white" />
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border bg-gradient-to-br from-red-50 to-red-100 p-6 dark:from-red-900/20 dark:to-red-800/20 dark:border-red-800">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-red-600 dark:text-red-400">Total Denda</p>
                                <p class="text-2xl font-bold text-red-900 dark:text-red-100">{{ formatCurrency(stats?.total_fines || 0) }}</p>
                            </div>
                            <div class="rounded-full bg-red-500 p-3">
                                <AlertTriangle class="h-6 w-6 text-white" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Unpaid Fines Alert Section -->
                <div v-if="unpaidFines && unpaidFines.length > 0" class="rounded-xl border border-red-200 bg-red-50 p-6 dark:bg-red-900/20 dark:border-red-800">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <AlertTriangle class="h-5 w-5 text-red-600" />
                            <h2 class="text-lg font-semibold text-red-900 dark:text-red-100">Denda Perlu Dibayar</h2>
                        </div>
                        <Link href="/siswa/fines" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400">
                            Lihat Semua &rarr;
                        </Link>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="fine in unpaidFines"
                            :key="fine.id"
                            class="flex items-center justify-between rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800"
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
                                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700"
                                >
                                    <CreditCard class="h-4 w-4" />
                                    Bayar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid gap-4 md:grid-cols-3">
                    <Link
                        href="/siswa/borrow"
                        class="flex items-center gap-4 rounded-xl border bg-white p-4 transition-all hover:shadow-lg hover:border-blue-500 dark:bg-gray-800 dark:hover:border-blue-400"
                    >
                        <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                            <BookOpen class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Pinjam Buku</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Jelajahi dan pinjam buku</p>
                        </div>
                    </Link>

                    <Link
                        href="/siswa/returns"
                        class="flex items-center gap-4 rounded-xl border bg-white p-4 transition-all hover:shadow-lg hover:border-emerald-500 dark:bg-gray-800 dark:hover:border-emerald-400"
                    >
                        <div class="rounded-lg bg-emerald-100 p-3 dark:bg-emerald-900/50">
                            <ArrowLeftRight class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Kembalikan Buku</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kembalikan buku yang dipinjam</p>
                        </div>
                    </Link>

                    <Link
                        href="/siswa/fines"
                        class="flex items-center gap-4 rounded-xl border bg-white p-4 transition-all hover:shadow-lg hover:border-red-500 dark:bg-gray-800 dark:hover:border-red-400"
                    >
                        <div class="rounded-lg bg-red-100 p-3 dark:bg-red-900/50">
                            <CreditCard class="h-5 w-5 text-red-600 dark:text-red-400" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">Lihat Denda</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola dan bayar denda</p>
                        </div>
                    </Link>
                </div>

                <!-- Active Borrowings -->
                <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Buku yang Sedang Dipinjam</h2>
                        <Clock class="h-5 w-5 text-gray-400" />
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="borrowing in activeBorrowings"
                            :key="borrowing.id"
                            class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50"
                        >
                            <div class="flex items-center gap-3">
                                <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900/50">
                                    <BookOpen class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.judul }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ borrowing.book.pengarang }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
                                    <Calendar class="h-4 w-4" />
                                    <span>Kembali: {{ formatDate(borrowing.tanggal_kembali) }}</span>
                                </div>
                                <div v-if="isOverdue(borrowing)" class="mt-1 flex items-center justify-end gap-1 text-xs font-semibold text-red-600 dark:text-red-400">
                                    <span>Terlambat {{ getDaysLate(borrowing) }} hari (Denda: {{ formatCurrency(getDaysLate(borrowing) * 1000) }})</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="activeBorrowings.length === 0" class="py-8 text-center text-gray-500">
                            Tidak ada buku yang sedang dipinjam
                        </div>
                    </div>
                </div>

                <!-- Borrowing History -->
                <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Peminjaman</h2>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="borrowing in borrowingHistory"
                            :key="borrowing.id"
                            class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50"
                        >
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.judul }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Dikembalikan: {{ formatDate(borrowing.tanggal_dikembalikan || borrowing.tanggal_kembali) }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    v-if="borrowing.denda > 0"
                                    class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-700 dark:bg-red-900/50 dark:text-red-400"
                                >
                                    Denda: {{ formatCurrency(borrowing.denda) }}
                                </span>
                                <span
                                    :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        borrowing.status === 'dikembalikan' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' :
                                        'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'
                                    ]"
                                >
                                    {{ borrowing.status === 'dikembalikan' ? 'Dikembalikan' : 'Terlambat' }}
                                </span>
                            </div>
                        </div>
                        <div v-if="borrowingHistory.length === 0" class="py-8 text-center text-gray-500">
                            Belum ada riwayat peminjaman
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
