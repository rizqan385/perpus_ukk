<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { BookOpen, Search, ShoppingCart, Clock, Calendar, Check } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

interface Book {
    id: number;
    judul: string;
    pengarang: string;
    penerbit: string;
    tahun_terbit: number;
    stok: number;
    deskripsi: string | null;
}

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    status: string;
    book: Book;
}

interface Member {
    no_anggota: string;
    status: string;
}

const props = defineProps<{
    books: Book[];
    activeBorrowings: Borrowing[];
    member: Member;
}>();

const search = ref('');
const selectedBook = ref<Book | null>(null);
const showConfirmModal = ref(false);

const filteredBooks = computed(() => {
    if (!search.value) return props.books;
    const term = search.value.toLowerCase();
    return props.books.filter(book =>
        book.judul.toLowerCase().includes(term) ||
        book.pengarang.toLowerCase().includes(term) ||
        book.penerbit.toLowerCase().includes(term)
    );
});

const borrowedBookIds = computed(() => {
    return props.activeBorrowings.map(b => b.book.id);
});

const openConfirmModal = (book: Book) => {
    selectedBook.value = book;
    showConfirmModal.value = true;
};

const closeConfirmModal = () => {
    selectedBook.value = null;
    showConfirmModal.value = false;
};

const confirmBorrow = () => {
    if (!selectedBook.value) return;
    router.post('/siswa/borrow', { book_id: selectedBook.value.id }, {
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

const breadcrumbs = [
    { title: 'Dashboard Siswa', href: '/siswa' },
    { title: 'Pinjam Buku', href: '/siswa/borrow' },
];
</script>

<template>
    <Head title="Pinjam Buku" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pinjam Buku</h1>
                    <p class="text-gray-600 dark:text-gray-400">Pilih buku yang ingin Anda pinjam</p>
                </div>
                <div class="flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 dark:bg-blue-900/30">
                    <ShoppingCart class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                    <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                        {{ activeBorrowings.length }}/3 Buku Dipinjam
                    </span>
                </div>
            </div>

            <!-- Active Borrowings -->
            <div v-if="activeBorrowings.length > 0" class="rounded-xl border bg-amber-50 p-4 dark:bg-amber-900/20 dark:border-amber-700">
                <h3 class="mb-3 flex items-center gap-2 font-semibold text-amber-800 dark:text-amber-300">
                    <Clock class="h-5 w-5" />
                    Buku yang Sedang Dipinjam
                </h3>
                <div class="grid gap-2 md:grid-cols-3">
                    <div
                        v-for="borrowing in activeBorrowings"
                        :key="borrowing.id"
                        class="flex items-center gap-3 rounded-lg bg-white p-3 dark:bg-gray-800"
                    >
                        <BookOpen class="h-5 w-5 text-amber-600" />
                        <div class="flex-1 min-w-0">
                            <p class="truncate font-medium text-gray-900 dark:text-white">{{ borrowing.book.judul }}</p>
                            <p class="flex items-center gap-1 text-xs text-gray-500">
                                <Calendar class="h-3 w-3" />
                                Kembali: {{ formatDate(borrowing.tanggal_kembali) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="relative">
                <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari judul, pengarang, atau penerbit..."
                    class="w-full rounded-lg border bg-white py-3 pl-10 pr-4 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                />
            </div>

            <!-- Books Grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="book in filteredBooks"
                    :key="book.id"
                    class="group relative overflow-hidden rounded-xl border bg-white transition-all hover:shadow-lg dark:bg-gray-800 dark:border-gray-700"
                >
                    <div class="p-4">
                        <div class="mb-3 flex h-32 items-center justify-center rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30">
                            <BookOpen class="h-12 w-12 text-blue-500 dark:text-blue-400" />
                        </div>
                        <h3 class="mb-1 line-clamp-2 font-semibold text-gray-900 dark:text-white">{{ book.judul }}</h3>
                        <p class="mb-1 text-sm text-gray-600 dark:text-gray-400">{{ book.pengarang }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">{{ book.penerbit }} ({{ book.tahun_terbit }})</p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900/50 dark:text-green-400">
                                Stok: {{ book.stok }}
                            </span>
                            <button
                                v-if="!borrowedBookIds.includes(book.id) && activeBorrowings.length < 3"
                                @click="openConfirmModal(book)"
                                class="rounded-lg bg-blue-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-blue-700"
                            >
                                Pinjam
                            </button>
                            <span
                                v-else-if="borrowedBookIds.includes(book.id)"
                                class="flex items-center gap-1 text-sm text-amber-600 dark:text-amber-400"
                            >
                                <Check class="h-4 w-4" />
                                Dipinjam
                            </span>
                            <span v-else class="text-xs text-gray-400">
                                Batas tercapai
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredBooks.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
                <BookOpen class="mb-4 h-16 w-16 text-gray-300 dark:text-gray-600" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak Ada Buku Tersedia</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    {{ search ? 'Tidak ada buku yang cocok dengan pencarian' : 'Semua buku sedang dipinjam' }}
                </p>
            </div>
        </div>

        <!-- Confirm Modal -->
        <Teleport to="body">
            <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-800">
                    <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Konfirmasi Peminjaman</h3>
                    <div class="mb-6 rounded-lg bg-gray-50 p-4 dark:bg-gray-700">
                        <p class="font-medium text-gray-900 dark:text-white">{{ selectedBook?.judul }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ selectedBook?.pengarang }}</p>
                    </div>
                    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                        Buku ini akan dipinjam selama <strong>7 hari</strong>. Pastikan Anda mengembalikan tepat waktu untuk menghindari denda.
                    </p>
                    <div class="flex gap-3">
                        <button
                            @click="closeConfirmModal"
                            class="flex-1 rounded-lg border px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Batal
                        </button>
                        <button
                            @click="confirmBorrow"
                            class="flex-1 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition-colors hover:bg-blue-700"
                        >
                            Konfirmasi
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
