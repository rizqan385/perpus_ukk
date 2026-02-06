<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { BookOpen, Calendar, Search, X } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch } from 'vue';

interface Book {
    id: number;
    judul: string;
    pengarang: string;
    penerbit: string;
    tahun_terbit: number;
    isbn: string | null;
    stok: number;
    cover_image: string | null;
    created_at: string;
}

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    data: Book[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    books: Pagination;
    filters: { search?: string; from_date?: string; to_date?: string };
}>();

const search = ref(props.filters.search || '');
const fromDate = ref(props.filters.from_date || '');
const toDate = ref(props.filters.to_date || '');

const applyFilters = () => {
    router.get('/admin/books', { 
        search: search.value, 
        from_date: fromDate.value,
        to_date: toDate.value
    }, { preserveState: true });
};

const clearFilters = () => {
    search.value = '';
    fromDate.value = '';
    toDate.value = '';
    router.get('/admin/books', {}, { preserveState: true });
};

const deleteBook = (book: Book) => {
    if (confirm(`Apakah Anda yakin ingin menghapus buku "${book.judul}"?`)) {
        router.delete(`/admin/books/${book.id}`);
    }
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Buku', href: '/admin/books' },
];
</script>

<template>
    <Head title="Kelola Buku" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kelola Data Buku</h1>
                    <p class="text-gray-600 dark:text-gray-400">Manajemen koleksi buku perpustakaan</p>
                </div>
                <a
                    href="/admin/books/create"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                >
                    <BookOpen class="h-5 w-5" />
                    Tambah Buku
                </a>
            </div>

            <!-- Filters -->
            <div class="rounded-xl border bg-white p-4 dark:bg-gray-800">
                <div class="flex flex-col gap-4 md:flex-row md:items-end">
                    <!-- Search -->
                    <div class="flex-1">
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pencarian</label>
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari judul, pengarang, atau ISBN..."
                                class="w-full rounded-lg border bg-white py-2 pl-10 pr-4 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                @keyup.enter="applyFilters"
                            />
                        </div>
                    </div>
                    
                    <!-- From Date -->
                    <div class="w-full md:w-48">
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Dari Tanggal</label>
                        <div class="relative">
                            <Calendar class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="fromDate"
                                type="date"
                                class="w-full rounded-lg border bg-white py-2 pl-10 pr-4 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                        </div>
                    </div>
                    
                    <!-- To Date -->
                    <div class="w-full md:w-48">
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai Tanggal</label>
                        <div class="relative">
                            <Calendar class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                            <input
                                v-model="toDate"
                                type="date"
                                class="w-full rounded-lg border bg-white py-2 pl-10 pr-4 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                        </div>
                    </div>
                    
                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button
                            @click="applyFilters"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                        >
                            Filter
                        </button>
                        <button
                            v-if="search || fromDate || toDate"
                            @click="clearFilters"
                            class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border bg-white dark:bg-gray-800">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Judul</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Pengarang</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Penerbit</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tahun</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Stok</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="book in books.data" :key="book.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-8 items-center justify-center rounded bg-blue-100 dark:bg-blue-900/50">
                                        <BookOpen class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ book.judul }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.pengarang }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.penerbit }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ book.tahun_terbit }}</td>
                            <td class="px-4 py-3">
                                <span
                                    :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        book.stok > 5 ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' :
                                        book.stok > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' :
                                        'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                    ]"
                                >
                                    {{ book.stok }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a
                                        :href="`/admin/books/${book.id}`"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700"
                                        title="Lihat"
                                    >
                                        👁️
                                    </a>
                                    <a
                                        :href="`/admin/books/${book.id}/edit`"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-blue-600 dark:hover:bg-gray-700"
                                        title="Edit"
                                    >
                                        ✏️
                                    </a>
                                    <button
                                        @click="deleteBook(book)"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-700"
                                        title="Hapus"
                                    >
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="books.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data buku
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="books.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Menampilkan {{ books.data.length }} dari {{ books.total }} buku
                </p>
                <div class="flex gap-1">
                    <template v-for="link in books.links" :key="link.label">
                        <a
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'rounded px-3 py-1 text-sm',
                                link.active
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'
                            ]"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="rounded px-3 py-1 text-sm text-gray-400"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
