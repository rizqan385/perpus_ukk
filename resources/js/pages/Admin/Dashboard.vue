<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { BookOpen, Users, ArrowLeftRight, TrendingUp, Clock, AlertTriangle, DollarSign } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Stats {
    total_books: number;
    total_members: number;
    active_borrowings: number;
    overdue_borrowings: number;
}

interface Book {
    id: number;
    judul: string;
    pengarang: string;
    borrowings_count: number;
}

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    status: string;
    book: {
        judul: string;
    };
    member: {
        no_anggota: string;
        user: {
            name: string;
        };
    };
}

const props = defineProps<{
    stats: Stats;
    recentBorrowings: Borrowing[];
    popularBooks: Book[];
}>();

const breadcrumbs = [
    { title: 'Admin Dashboard', href: '/admin' },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Admin</h1>
                    <p class="text-gray-600 dark:text-gray-400">Selamat datang di panel administrasi perpustakaan</p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Buku -->
                <div class="rounded-xl border bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:from-blue-900/20 dark:to-blue-800/20 dark:border-blue-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Buku</p>
                            <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">{{ stats.total_books }}</p>
                        </div>
                        <div class="rounded-full bg-blue-500 p-3">
                            <BookOpen class="h-6 w-6 text-white" />
                        </div>
                    </div>
                </div>

                <!-- Total Anggota -->
                <div class="rounded-xl border bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 dark:from-emerald-900/20 dark:to-emerald-800/20 dark:border-emerald-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Total Anggota</p>
                            <p class="text-3xl font-bold text-emerald-900 dark:text-emerald-100">{{ stats.total_members }}</p>
                        </div>
                        <div class="rounded-full bg-emerald-500 p-3">
                            <Users class="h-6 w-6 text-white" />
                        </div>
                    </div>
                </div>

                <!-- Peminjaman Aktif -->
                <div class="rounded-xl border bg-gradient-to-br from-amber-50 to-amber-100 p-6 dark:from-amber-900/20 dark:to-amber-800/20 dark:border-amber-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-amber-600 dark:text-amber-400">Peminjaman Aktif</p>
                            <p class="text-3xl font-bold text-amber-900 dark:text-amber-100">{{ stats.active_borrowings }}</p>
                        </div>
                        <div class="rounded-full bg-amber-500 p-3">
                            <ArrowLeftRight class="h-6 w-6 text-white" />
                        </div>
                    </div>
                </div>

                <!-- Terlambat -->
                <div class="rounded-xl border bg-gradient-to-br from-red-50 to-red-100 p-6 dark:from-red-900/20 dark:to-red-800/20 dark:border-red-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">Terlambat</p>
                            <p class="text-3xl font-bold text-red-900 dark:text-red-100">{{ stats.overdue_borrowings }}</p>
                        </div>
                        <div class="rounded-full bg-red-500 p-3">
                            <AlertTriangle class="h-6 w-6 text-white" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Link
                    href="/admin/books"
                    class="flex items-center gap-4 rounded-xl border bg-white p-4 transition-all hover:shadow-lg hover:border-blue-500 dark:bg-gray-800 dark:hover:border-blue-400"
                >
                    <div class="rounded-lg bg-blue-100 p-3 dark:bg-blue-900/50">
                        <BookOpen class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Kelola Buku</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">CRUD data buku perpustakaan</p>
                    </div>
                </Link>

                <Link
                    href="/admin/members"
                    class="flex items-center gap-4 rounded-xl border bg-white p-4 transition-all hover:shadow-lg hover:border-emerald-500 dark:bg-gray-800 dark:hover:border-emerald-400"
                >
                    <div class="rounded-lg bg-emerald-100 p-3 dark:bg-emerald-900/50">
                        <Users class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Kelola Anggota</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">CRUD data anggota perpustakaan</p>
                    </div>
                </Link>

                <Link
                    href="/admin/transactions"
                    class="flex items-center gap-4 rounded-xl border bg-white p-4 transition-all hover:shadow-lg hover:border-amber-500 dark:bg-gray-800 dark:hover:border-amber-400"
                >
                    <div class="rounded-lg bg-amber-100 p-3 dark:bg-amber-900/50">
                        <ArrowLeftRight class="h-5 w-5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Transaksi</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Kelola peminjaman & pengembalian</p>
                    </div>
                </Link>

                <Link
                    href="/admin/fines"
                    class="flex items-center gap-4 rounded-xl border bg-white p-4 transition-all hover:shadow-lg hover:border-red-500 dark:bg-gray-800 dark:hover:border-red-400"
                >
                    <div class="rounded-lg bg-red-100 p-3 dark:bg-red-900/50">
                        <DollarSign class="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Kelola Denda</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Konfirmasi pembayaran denda</p>
                    </div>
                </Link>
            </div>

            <!-- Recent Activity & Popular Books -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Recent Borrowings -->
                <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Peminjaman Terbaru</h2>
                        <Clock class="h-5 w-5 text-gray-400" />
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="borrowing in recentBorrowings"
                            :key="borrowing.id"
                            class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50"
                        >
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.judul }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ borrowing.member.user.name }}</p>
                            </div>
                            <span
                                :class="[
                                    'rounded-full px-2 py-1 text-xs font-medium',
                                    borrowing.status === 'dipinjam' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' :
                                    borrowing.status === 'dikembalikan' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' :
                                    'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                ]"
                            >
                                {{ borrowing.status }}
                            </span>
                        </div>
                        <div v-if="recentBorrowings.length === 0" class="text-center text-gray-500 py-4">
                            Belum ada peminjaman
                        </div>
                    </div>
                </div>

                <!-- Popular Books -->
                <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Buku Populer</h2>
                        <TrendingUp class="h-5 w-5 text-gray-400" />
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="(book, index) in popularBooks"
                            :key="book.id"
                            class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50"
                        >
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">
                                {{ index + 1 }}
                            </span>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ book.judul }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ book.pengarang }}</p>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ book.borrowings_count }}x dipinjam
                            </span>
                        </div>
                        <div v-if="popularBooks.length === 0" class="text-center text-gray-500 py-4">
                            Belum ada data
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
