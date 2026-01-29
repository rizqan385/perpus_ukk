<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Users, BookOpen, Calendar, Clock, AlertTriangle, RotateCcw } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    name: string;
    email: string;
}

interface Member {
    id: number;
    no_anggota: string;
    kelas: string | null;
    user: User;
}

interface Book {
    id: number;
    judul: string;
    pengarang: string;
    penerbit: string;
    tahun_terbit: number;
}

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    tanggal_dikembalikan: string | null;
    status: string;
    denda: number;
    member: Member;
    book: Book;
}

const props = defineProps<{
    borrowing: Borrowing;
}>();

const isOverdue = () => {
    if (props.borrowing.status !== 'dipinjam') return false;
    return new Date(props.borrowing.tanggal_kembali) < new Date();
};

const returnBook = () => {
    if (confirm(`Konfirmasi pengembalian buku "${props.borrowing.book.judul}"?`)) {
        router.post(`/admin/transactions/${props.borrowing.id}/return`);
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
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
    { title: 'Admin', href: '/admin' },
    { title: 'Transaksi', href: '/admin/transactions' },
    { title: 'Detail Transaksi', href: `/admin/transactions/${props.borrowing.id}` },
];
</script>

<template>
    <Head title="Detail Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link
                    href="/admin/transactions"
                    class="rounded-lg border p-2 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Transaksi</h1>
                    <p class="text-gray-600 dark:text-gray-400">ID: #{{ borrowing.id }}</p>
                </div>
                <button
                    v-if="borrowing.status === 'dipinjam'"
                    @click="returnBook"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-white transition-colors hover:bg-emerald-700"
                >
                    <RotateCcw class="h-5 w-5" />
                    Kembalikan Buku
                </button>
            </div>

            <!-- Status Alert -->
            <div v-if="borrowing.status === 'dipinjam' && isOverdue()" class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <div class="flex items-center gap-3">
                    <AlertTriangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                    <div>
                        <h3 class="font-semibold text-red-800 dark:text-red-300">Buku Terlambat Dikembalikan</h3>
                        <p class="text-sm text-red-600 dark:text-red-400">Buku ini sudah melewati batas waktu pengembalian.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Member Info -->
                <div class="rounded-xl border bg-white p-6 dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                        <Users class="h-5 w-5" />
                        Informasi Anggota
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nama</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.member.user.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No. Anggota</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.member.no_anggota }}</p>
                        </div>
                        <div v-if="borrowing.member.kelas">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kelas</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.member.kelas }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.member.user.email }}</p>
                        </div>
                    </div>
                    <Link
                        :href="`/admin/members/${borrowing.member.id}`"
                        class="mt-4 block text-center text-sm text-blue-600 hover:underline dark:text-blue-400"
                    >
                        Lihat profil anggota →
                    </Link>
                </div>

                <!-- Book Info -->
                <div class="rounded-xl border bg-white p-6 dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                        <BookOpen class="h-5 w-5" />
                        Informasi Buku
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Judul</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.judul }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pengarang</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.pengarang }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Penerbit</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.penerbit }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tahun Terbit</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.tahun_terbit }}</p>
                        </div>
                    </div>
                    <Link
                        :href="`/admin/books/${borrowing.book.id}`"
                        class="mt-4 block text-center text-sm text-blue-600 hover:underline dark:text-blue-400"
                    >
                        Lihat detail buku →
                    </Link>
                </div>

                <!-- Transaction Details -->
                <div class="rounded-xl border bg-white p-6 lg:col-span-2 dark:bg-gray-800 dark:border-gray-700">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                        <Clock class="h-5 w-5" />
                        Detail Transaksi
                    </h3>
                    <div class="grid gap-6 md:grid-cols-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Pinjam</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(borrowing.tanggal_pinjam) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Batas Pengembalian</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(borrowing.tanggal_kembali) }}</p>
                        </div>
                        <div v-if="borrowing.tanggal_dikembalikan">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tanggal Dikembalikan</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(borrowing.tanggal_dikembalikan) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                            <span
                                :class="[
                                    'inline-block rounded-full px-3 py-1 text-sm font-medium mt-1',
                                    borrowing.status === 'dipinjam' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' :
                                    borrowing.status === 'dikembalikan' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' :
                                    'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                ]"
                            >
                                {{ borrowing.status }}
                            </span>
                        </div>
                    </div>

                    <!-- Fine Section -->
                    <div v-if="borrowing.denda > 0" class="mt-6 rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-semibold text-red-800 dark:text-red-300">Denda Keterlambatan</h4>
                                <p class="text-sm text-red-600 dark:text-red-400">Denda dihitung berdasarkan jumlah hari keterlambatan</p>
                            </div>
                            <p class="text-2xl font-bold text-red-700 dark:text-red-400">{{ formatCurrency(borrowing.denda) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
