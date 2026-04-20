<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { AlertTriangle, Search, Check, DollarSign, MessageCircle } from 'lucide-vue-next';
import { ref } from 'vue';
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    name: string;
    email: string;
    phone: string | null;
}

interface Member {
    id: number;
    no_anggota: string;
    user: User;
}

interface Book {
    id: number;
    judul: string;
}

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    tanggal_dikembalikan: string | null;
    denda: number;
    payment_status: string | null;
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

interface Stats {
    total_unpaid: number;
    total_pending: number;
    total_paid: number;
}

const props = defineProps<{
    activeFines: Borrowing[];
    fines: Pagination;
    stats: Stats;
    filters: { search?: string; status?: string };
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');

const applyFilters = () => {
    router.get('/admin/fines', { search: search.value, status: status.value }, { preserveState: true });
};

const confirmPayment = (borrowing: Borrowing) => {
    if (window.confirm(`Konfirmasi pembayaran denda dari "${borrowing.member.user.name}" untuk buku "${borrowing.book.judul}"?`)) {
        router.post(`/admin/fines/${borrowing.id}/confirm`, {}, {
            preserveScroll: true,
            onFinish: () => window.alert("Tindakan konfirmasi selesai dilaksanakan!")
        });
    }
};

const remindFine = (borrowing: Borrowing) => {
    if (borrowing.payment_status === 'paid') {
        window.alert('Denda ini sudah lunas, tidak perlu diingatkan lagi.');
        return;
    }
    if (!borrowing.member.user.phone) {
        window.alert('Siswa ini belum memiliki nomor WhatsApp terdaftar.');
        return;
    }
    if (window.confirm(`Apakah Anda yakin admin akan mengingatkan user ${borrowing.member.user.name} untuk membayar denda dia?`)) {
        router.post(`/admin/fines/${borrowing.id}/remind`, {}, {
            preserveScroll: true,
            onFinish: () => window.alert("Proses pengingat WhatsApp ke siswa berhasil dijalankan!")
        });
    }
};



const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Denda', href: '/admin/fines' },
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
</script>

<template>
    <Head title="Kelola Denda" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kelola Denda</h1>
                <p class="text-gray-600 dark:text-gray-400">Konfirmasi pembayaran denda dari siswa</p>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Belum Dibayar -->
                <div class="flex flex-col justify-between rounded-xl border bg-gradient-to-br from-red-50 to-red-100 p-6 dark:from-red-900/20 dark:to-red-800/20 dark:border-red-800">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">Belum Dibayar</p>
                        <div class="rounded-full bg-red-500 p-3">
                            <AlertTriangle class="h-6 w-6 text-white" />
                        </div>
                    </div>
                    <p class="mt-4 text-3xl font-bold text-red-900 dark:text-red-100">{{ formatCurrency(stats.total_unpaid) }}</p>
                    <p class="mt-1 text-xs text-red-500 dark:text-red-400">Total denda belum dilunasi siswa</p>
                </div>

                <!-- Sudah Lunas -->
                <div class="flex flex-col justify-between rounded-xl border bg-gradient-to-br from-green-50 to-green-100 p-6 dark:from-green-900/20 dark:to-green-800/20 dark:border-green-800">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">Sudah Lunas</p>
                        <div class="rounded-full bg-green-500 p-3">
                            <DollarSign class="h-6 w-6 text-white" />
                        </div>
                    </div>
                    <p class="mt-4 text-3xl font-bold text-green-900 dark:text-green-100">{{ formatCurrency(stats.total_paid) }}</p>
                    <p class="mt-1 text-xs text-green-500 dark:text-green-400">Total pembayaran denda diterima</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 md:flex-row">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama atau no. anggota..."
                        class="w-full rounded-lg border bg-white py-2 pl-10 pr-4 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                        @keyup.enter="applyFilters"
                    />
                </div>
                <select
                    v-model="status"
                    @change="applyFilters"
                    class="rounded-lg border bg-white px-4 py-2 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                >
                    <option value="">Semua Status</option>
                    <option value="unpaid">Belum Dibayar</option>
                    <option value="pending">Menunggu Konfirmasi</option>
                    <option value="paid">Sudah Lunas</option>
                </select>
                <button
                    @click="applyFilters"
                    class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                >
                    Cari
                </button>
            </div>



            <!-- Table -->
            <div class="overflow-hidden rounded-xl border bg-white dark:bg-gray-800">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Anggota</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Buku</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tanggal Kembali</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Denda</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="fine in fines.data" :key="fine.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ fine.member.user.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ fine.member.no_anggota }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ fine.book.judul }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ formatDate(fine.tanggal_dikembalikan || fine.tanggal_kembali) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-red-600 dark:text-red-400">
                                    {{ formatCurrency(fine.denda) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        fine.payment_status === 'paid' 
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                            : fine.payment_status === 'pending'
                                            ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400'
                                            : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                    ]"
                                >
                                    {{ fine.payment_status === 'paid' ? 'Lunas' : fine.payment_status === 'pending' ? 'Menunggu' : 'Belum Bayar' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    <button
                                        v-if="fine.payment_status === 'pending'"
                                        @click="confirmPayment(fine)"
                                        class="inline-flex items-center gap-1 rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-green-700"
                                    >
                                        <Check class="h-4 w-4" />
                                        Konfirmasi
                                    </button>
                                    <button
                                        @click="remindFine(fine)"
                                        :disabled="fine.payment_status === 'paid'"
                                        :class="[
                                            'inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-white transition-colors',
                                            fine.payment_status === 'paid' 
                                                ? 'bg-gray-400 cursor-not-allowed' 
                                                : 'bg-green-500 hover:bg-green-600'
                                        ]"
                                        :title="fine.payment_status === 'paid' ? 'Sudah lunas' : 'Kirim pengingat via WhatsApp'"
                                    >
                                        <MessageCircle class="h-4 w-4" />
                                        Ingatkan WA
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="fines.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data denda
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="fines.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Menampilkan {{ fines.data.length }} dari {{ fines.total }} denda
                </p>
                <Pagination :links="fines.links" />
            </div>
        </div>
    </AppLayout>
</template>
