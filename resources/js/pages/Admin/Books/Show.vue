<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, Calendar, Hash, User, Building, FileText, Package, Edit } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Member {
    id: number;
    no_anggota: string;
    user: User;
}

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    tanggal_dikembalikan: string | null;
    status: string;
    denda: number;
    member: Member;
}

interface Book {
    id: number;
    judul: string;
    pengarang: string;
    penerbit: string;
    tahun_terbit: number;
    isbn: string | null;
    stok: number;
    cover_image: string | null;
    deskripsi: string | null;
    created_at: string;
    borrowings: Borrowing[];
}

const props = defineProps<{
    book: Book;
}>();

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Buku', href: '/admin/books' },
    { title: 'Detail Buku', href: `/admin/books/${props.book.id}` },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const getCoverUrl = (path: string | null) => {
    if (!path) return undefined;
    return `/storage/${path}`;
};

const activeBorrowings = props.book.borrowings?.filter(b => b.status === 'dipinjam') || [];
const borrowingHistory = props.book.borrowings?.filter(b => b.status !== 'dipinjam') || [];
</script>

<template>
    <Head :title="`Detail Buku - ${book.judul}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        href="/admin/books"
                        class="rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        <ArrowLeft class="h-5 w-5" />
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Buku</h1>
                        <p class="text-gray-600 dark:text-gray-400">Informasi lengkap tentang buku</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="`/admin/books/${book.id}/edit`"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                    >
                        <Edit class="h-4 w-4" />
                        Edit
                    </Link>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Left Column - Book Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Cover & Basic Info Card -->
                    <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                        <div class="flex gap-6">
                            <!-- Cover Image -->
                            <div class="flex-shrink-0">
                                <div
                                    v-if="getCoverUrl(book.cover_image)"
                                    class="h-64 w-48 overflow-hidden rounded-lg border-2 border-gray-200 dark:border-gray-700"
                                >
                                    <img
                                        :src="getCoverUrl(book.cover_image)"
                                        :alt="book.judul"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <div
                                    v-else
                                    class="flex h-64 w-48 items-center justify-center rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/20 dark:to-blue-800/20"
                                >
                                    <BookOpen class="h-24 w-24 text-blue-400" />
                                </div>
                            </div>

                            <!-- Book Details -->
                            <div class="flex-1 space-y-4">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ book.judul }}</h2>
                                    <p class="mt-1 text-lg text-gray-600 dark:text-gray-400">oleh {{ book.pengarang }}</p>
                                </div>

                                <div class="grid gap-3">
                                    <div class="flex items-center gap-3">
                                        <Building class="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Penerbit</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ book.penerbit }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <Calendar class="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Tahun Terbit</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ book.tahun_terbit }}</p>
                                        </div>
                                    </div>

                                    <div v-if="book.isbn" class="flex items-center gap-3">
                                        <Hash class="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">ISBN</p>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ book.isbn }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <Package class="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Stok</p>
                                            <p class="font-medium">
                                                <span
                                                    :class="[
                                                        'rounded-full px-3 py-1 text-sm font-semibold',
                                                        book.stok > 5 ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' :
                                                        book.stok > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' :
                                                        'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                                    ]"
                                                >
                                                    {{ book.stok }} buku {{ book.stok === 0 ? '(Habis)' : 'tersedia' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div v-if="book.deskripsi" class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                        <div class="mb-3 flex items-center gap-2">
                            <FileText class="h-5 w-5 text-gray-400" />
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Deskripsi</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ book.deskripsi }}</p>
                    </div>

                    <!-- Borrowing History -->
                    <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Riwayat Peminjaman</h3>
                        
                        <div class="space-y-3">
                            <div
                                v-for="borrowing in borrowingHistory.slice(0, 5)"
                                :key="borrowing.id"
                                class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50"
                            >
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.member.user.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ borrowing.member.no_anggota }} • 
                                        {{ formatDate(borrowing.tanggal_pinjam) }}
                                    </p>
                                </div>
                                <span
                                    :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        borrowing.status === 'dikembalikan' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' :
                                        'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                    ]"
                                >
                                    {{ borrowing.status }}
                                </span>
                            </div>
                            <div v-if="borrowingHistory.length === 0" class="py-8 text-center text-gray-500">
                                Belum ada riwayat peminjaman
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Stats -->
                <div class="space-y-6">
                    <!-- Stats Card -->
                    <div class="rounded-xl border bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:from-blue-900/20 dark:to-blue-800/20 dark:border-blue-800">
                        <h3 class="mb-4 text-lg font-semibold text-blue-900 dark:text-blue-100">Statistik</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-blue-700 dark:text-blue-300">Dipinjam Saat Ini</span>
                                <span class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ activeBorrowings.length }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-blue-700 dark:text-blue-300">Total Peminjaman</span>
                                <span class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ book.borrowings?.length || 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-blue-700 dark:text-blue-300">Stok Tersedia</span>
                                <span class="text-xl font-bold text-blue-900 dark:text-blue-100">{{ book.stok }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Active Borrowings -->
                    <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Sedang Dipinjam</h3>
                        <div class="space-y-3">
                            <div
                                v-for="borrowing in activeBorrowings"
                                :key="borrowing.id"
                                class="rounded-lg bg-amber-50 p-3 dark:bg-amber-900/20"
                            >
                                <div class="flex items-center gap-2">
                                    <User class="h-4 w-4 text-amber-600 dark:text-amber-400" />
                                    <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.member.user.name }}</p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Kembali: {{ formatDate(borrowing.tanggal_kembali) }}
                                </p>
                            </div>
                            <div v-if="activeBorrowings.length === 0" class="py-4 text-center text-sm text-gray-500">
                                Tidak ada yang dipinjam
                            </div>
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Informasi</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Ditambahkan</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ formatDate(book.created_at) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">ID Buku</span>
                                <span class="font-medium text-gray-900 dark:text-white">#{{ book.id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
