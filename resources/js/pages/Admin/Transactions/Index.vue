<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeftRight, Search, Eye, Trash2, RotateCcw, CheckCircle, Printer } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { ref, computed } from 'vue';

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
    allKelas: string[];
    filters: { search?: string; status?: string; kelas?: string };
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const kelas = ref(props.filters.kelas || '');

const filterType = ref<'daily' | 'monthly' | 'yearly' | ''>('');
const filterDate = ref('');
const filterMonth = ref('');
const filterYear = ref('');

const searchTransactions = () => {
    router.get('/admin/transactions', { search: search.value, status: status.value, kelas: kelas.value }, { preserveState: true });
};

const returnBook = (borrowing: Borrowing) => {
    if (confirm(`Konfirmasi pengembalian buku "${borrowing.book.judul}"?`)) {
        router.post(`/admin/transactions/${borrowing.id}/return`);
    }
};

const isOverdue = (borrowing: Borrowing) => {
    return new Date(borrowing.tanggal_kembali) < new Date() && ['dipinjam', 'menunggu_pengembalian'].includes(borrowing.status);
};

const getDaysLate = (borrowing: Borrowing) => {
    const today = new Date();
    const returnDate = new Date(borrowing.tanggal_kembali);
    const diffTime = today.getTime() - returnDate.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays > 0 ? diffDays : 0;
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

const exportUrl = (format: string) => {
    const params = new URLSearchParams();
    if (search.value) params.set('search', search.value);
    if (status.value) params.set('status', status.value);
    if (kelas.value) params.set('kelas', kelas.value);
    params.set('format', format);

    if (filterType.value) params.set('filter_type', filterType.value);
    if (filterType.value === 'daily' && filterDate.value) params.set('date', filterDate.value);
    if (filterType.value === 'monthly' && filterMonth.value) params.set('month', filterMonth.value);
    if (filterType.value === 'yearly' && filterYear.value) params.set('year', filterYear.value);

    return `/admin/transactions/export?${params.toString()}`;
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
                <div class="flex items-center gap-2">
                    <!-- Export button -->
                    <a
                        :href="exportUrl('pdf')"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-500 bg-white px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-500 dark:hover:bg-red-900/20"
                        title="Export PDF"
                    >
                        <Printer class="h-4 w-4" />
                        PDF
                    </a>
                    <Link
                        href="/admin/transactions/create"
                        class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-white transition-colors hover:bg-amber-600"
                    >
                        <ArrowLeftRight class="h-5 w-5" />
                        Transaksi Baru
                    </Link>
                </div>
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
                <select
                    v-model="kelas"
                    @change="searchTransactions"
                    class="rounded-lg border bg-white px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                >
                    <option value="">Semua Kelas</option>
                    <option v-for="k in allKelas" :key="k" :value="k">{{ k }}</option>
                </select>
                <button
                    @click="searchTransactions"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    Cari
                </button>
            </div>

            <!-- Filter Periode Laporan PDF -->
            <div class="flex flex-wrap items-center gap-3 rounded-xl border border-dashed border-red-300 bg-red-50 px-4 py-3 dark:border-red-700 dark:bg-red-900/10">
                <Printer class="h-4 w-4 text-red-600" />
                <span class="text-sm font-semibold text-red-700 dark:text-red-400">Filter Periode PDF:</span>
                <select
                    v-model="filterType"
                    class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 dark:bg-gray-800 dark:border-red-700 dark:text-white"
                >
                    <option value="">Semua Waktu</option>
                    <option value="daily">Harian</option>
                    <option value="monthly">Bulanan</option>
                    <option value="yearly">Tahunan</option>
                </select>
                <input
                    v-if="filterType === 'daily'"
                    v-model="filterDate"
                    type="date"
                    class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 dark:bg-gray-800 dark:border-red-700 dark:text-white"
                />
                <input
                    v-if="filterType === 'monthly'"
                    v-model="filterMonth"
                    type="month"
                    class="rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 dark:bg-gray-800 dark:border-red-700 dark:text-white"
                />
                <input
                    v-if="filterType === 'yearly'"
                    v-model="filterYear"
                    type="number"
                    placeholder="Tahun (mis. 2026)"
                    min="2020"
                    max="2099"
                    class="w-36 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 dark:bg-gray-800 dark:border-red-700 dark:text-white"
                />
                <span class="text-xs text-red-500 dark:text-red-400">Filter ini hanya berlaku saat cetak PDF</span>
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
                                    <span v-else-if="isOverdue(borrowing)" class="font-medium text-orange-600 dark:text-orange-400">
                                        Estimasi: {{ formatCurrency(getDaysLate(borrowing) * 1000) }}
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
                <Pagination :links="borrowings.links" />
            </div>
        </div>
    </AppLayout>
</template>
