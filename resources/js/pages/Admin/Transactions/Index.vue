<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeftRight, Search, Eye, Trash2, RotateCcw, CheckCircle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

interface User {
    name: string;
    email: string;
}

interface Member {
    id: number;
    no_anggota: string;
    user: User;
}

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
    member: Member;
    book: Book;
}

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    data: Borrowing[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    borrowings: Pagination;
    filters: { search?: string; status?: string };
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const searchTransactions = () => {
    router.get('/admin/transactions', { search: search.value, status: status.value }, { preserveState: true });
};

const returnBook = (borrowing: Borrowing) => {
    if (confirm(`Konfirmasi pengembalian buku "${borrowing.book.judul}"?`)) {
        router.post(`/admin/transactions/${borrowing.id}/return`);
    }
};

const approveReturn = (borrowing: Borrowing) => {
    if (confirm(`Setujui pengembalian buku "${borrowing.book.judul}" oleh ${borrowing.member.user.name}?`)) {
        router.post(`/admin/transactions/${borrowing.id}/approve-return`);
    }
};

const deleteTransaction = (borrowing: Borrowing) => {
    if (confirm(`Apakah Anda yakin ingin menghapus transaksi ini?`)) {
        router.delete(`/admin/transactions/${borrowing.id}`);
    }
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
    { title: 'Admin', href: '/admin' },
    { title: 'Transaksi', href: '/admin/transactions' },
];
</script>

<template>
    <Head title="Transaksi Peminjaman" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Transaksi Peminjaman</h1>
                    <p class="text-gray-600 dark:text-gray-400">Kelola peminjaman dan pengembalian buku</p>
                </div>
                <Link
                    href="/admin/transactions/create"
                    class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-white transition-colors hover:bg-amber-600"
                >
                    <ArrowLeftRight class="h-5 w-5" />
                    Transaksi Baru
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 md:flex-row">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama anggota, judul buku, atau no. anggota..."
                        class="w-full rounded-lg border bg-white py-2 pl-10 pr-4 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                        @keyup.enter="searchTransactions"
                    />
                </div>
                <select
                    v-model="status"
                    @change="searchTransactions"
                    class="rounded-lg border bg-white px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                >
                    <option value="">Semua Status</option>
                    <option value="dipinjam">Dipinjam</option>
                    <option value="menunggu_pengembalian">Menunggu Pengembalian</option>
                    <option value="dikembalikan">Dikembalikan</option>
                    <option value="terlambat">Terlambat</option>
                </select>
                <button
                    @click="searchTransactions"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    Cari
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border bg-white dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Anggota</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Buku</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tgl Pinjam</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tgl Kembali</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Denda</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="borrowing in borrowings.data" :key="borrowing.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.member.user.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ borrowing.member.no_anggota }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.judul }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ borrowing.book.pengarang }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ formatDate(borrowing.tanggal_pinjam) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ formatDate(borrowing.tanggal_kembali) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="[
                                            'rounded-full px-2 py-1 text-xs font-medium',
                                            borrowing.status === 'dipinjam' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' :
                                            borrowing.status === 'menunggu_pengembalian' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' :
                                            borrowing.status === 'dikembalikan' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' :
                                            'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                        ]"
                                    >
                                        {{ 
                                            borrowing.status === 'menunggu_pengembalian' ? 'Menunggu Pengembalian' :
                                            borrowing.status === 'dikembalikan' ? 'Dikembalikan' :
                                            borrowing.status === 'terlambat' ? 'Terlambat' :
                                            'Dipinjam'
                                        }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="borrowing.denda > 0" class="font-medium text-red-600 dark:text-red-400">
                                        {{ formatCurrency(borrowing.denda) }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <Link
                                            :href="`/admin/transactions/${borrowing.id}`"
                                            class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700"
                                            title="Lihat"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Link>
                                        <button
                                            v-if="borrowing.status === 'dipinjam'"
                                            @click="returnBook(borrowing)"
                                            class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-emerald-600 dark:hover:bg-gray-700"
                                            title="Kembalikan"
                                        >
                                            <RotateCcw class="h-4 w-4" />
                                        </button>
                                        <button
                                            v-if="borrowing.status === 'menunggu_pengembalian'"
                                            @click="approveReturn(borrowing)"
                                            class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-green-600 dark:hover:bg-gray-700"
                                            title="Setujui Pengembalian"
                                        >
                                            <CheckCircle class="h-4 w-4" />
                                        </button>
                                        <button
                                            @click="deleteTransaction(borrowing)"
                                            class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-700"
                                            title="Hapus"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="borrowings.data.length === 0">
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Tidak ada data transaksi
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="borrowings.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Menampilkan {{ borrowings.data.length }} dari {{ borrowings.total }} transaksi
                </p>
                <div class="flex gap-1">
                    <template v-for="link in borrowings.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'rounded px-3 py-1 text-sm',
                                link.active
                                    ? 'bg-amber-500 text-white'
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
