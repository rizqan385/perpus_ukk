<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Plus, Search, Edit, Trash2, Eye, AlertTriangle, CreditCard } from 'lucide-vue-next';
import { ref } from 'vue';
import Pagination from '@/components/Pagination.vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Member {
    id: number;
    no_anggota: string;
    kelas: string | null;
    alamat: string | null;
    telepon: string | null;
    status: string;
    foto_url: string | null;
    user: User;
    total_fines: number;
    pending_fines: number;
}

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    data: Member[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    members: Pagination;
    allKelas: string[];
    filters: { search?: string; status?: string; has_fine?: string; kelas?: string; jenis_kelamin?: string };
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const hasFine = ref(props.filters.has_fine || '');
const kelas = ref(props.filters.kelas || '');
const jenisKelamin = ref(props.filters.jenis_kelamin || '');

const searchMembers = () => {
    router.get('/admin/members', { 
        search: search.value, 
        status: status.value, 
        has_fine: hasFine.value,
        kelas: kelas.value,
        jenis_kelamin: jenisKelamin.value
    }, { preserveState: true });
};

const deleteMember = (member: Member) => {
    if (confirm(`Apakah Anda yakin ingin menghapus anggota "${member.user.name}"?`)) {
        router.delete(`/admin/members/${member.id}`);
    }
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
    { title: 'Kelola Anggota', href: '/admin/members' },
];
</script>

<template>
    <Head title="Kelola Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Kelola Anggota</h1>
                    <p class="text-gray-600 dark:text-gray-400">Manajemen anggota perpustakaan</p>
                </div>
                <Link
                    href="/admin/members/create"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-white transition-colors hover:bg-emerald-700"
                >
                    <Plus class="h-5 w-5" />
                    Tambah Anggota
                </Link>
            </div>

            <!-- Filters -->
            <div class="flex flex-col gap-4 md:flex-row">
                <div class="relative flex-1">
                    <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama, email, atau no. anggota..."
                        class="w-full rounded-lg border bg-white py-2 pl-10 pr-4 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                        @keyup.enter="searchMembers"
                    />
                </div>
                <select
                    v-model="status"
                    @change="searchMembers"
                    class="rounded-lg border bg-white px-4 py-2 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                >
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <select
                    v-model="kelas"
                    @change="searchMembers"
                    class="rounded-lg border bg-white px-4 py-2 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                >
                    <option value="">Semua Kelas</option>
                    <option v-for="k in allKelas" :key="k" :value="k">{{ k }}</option>
                </select>
                <select
                    v-model="jenisKelamin"
                    @change="searchMembers"
                    class="rounded-lg border bg-white px-4 py-2 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                >
                    <option value="">Semua Gender</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                <select
                    v-model="hasFine"
                    @change="searchMembers"
                    class="rounded-lg border bg-white px-4 py-2 focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                >
                    <option value="">Semua Denda</option>
                    <option value="yes">Memiliki Denda</option>
                </select>
                <button
                    @click="searchMembers"
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
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">No. Anggota</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Nama</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Kelas</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Denda</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="member in members.data" :key="member.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-full shadow-sm" style="background: linear-gradient(135deg, #10b981, #047857);">
                                        <img v-if="member.foto_url" :src="member.foto_url" class="h-full w-full object-cover" alt="foto" />
                                        <div v-else class="flex h-full items-center justify-center text-sm font-bold text-white">
                                            {{ member.user.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ member.no_anggota }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ member.user.name }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ member.user.email }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ member.kelas || '-' }}</td>
                            <td class="px-4 py-3">
                                <div v-if="member.total_fines > 0" class="flex items-center gap-1">
                                    <AlertTriangle class="h-4 w-4 text-red-500" />
                                    <span class="font-semibold text-red-600 dark:text-red-400">{{ formatCurrency(member.total_fines) }}</span>
                                </div>
                                <div v-else-if="member.pending_fines > 0" class="flex items-center gap-1">
                                    <AlertTriangle class="h-4 w-4 text-amber-500" />
                                    <span class="text-sm font-medium text-amber-600 dark:text-amber-400">Menunggu konfirmasi</span>
                                </div>
                                <span v-else class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">Lunas</span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    :class="[
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        member.status === 'aktif' 
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                            : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                    ]"
                                >
                                    {{ member.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="`/admin/members/${member.id}`"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-gray-700"
                                        title="Lihat"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </Link>
                                    <Link
                                        :href="`/admin/members/${member.id}/card`"
                                        class="rounded p-1.5 text-gray-500 hover:bg-blue-100 hover:text-blue-700 dark:hover:bg-gray-700"
                                        title="Kartu Anggota"
                                    >
                                        <CreditCard class="h-4 w-4" />
                                    </Link>
                                    <Link
                                        :href="`/admin/members/${member.id}/edit`"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-emerald-600 dark:hover:bg-gray-700"
                                        title="Edit"
                                    >
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                    <button
                                        @click="deleteMember(member)"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100 hover:text-red-600 dark:hover:bg-gray-700"
                                        title="Hapus"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="members.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data anggota
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="members.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Menampilkan {{ members.data.length }} dari {{ members.total }} anggota
                </p>
                <Pagination :links="members.links" />
            </div>
        </div>
    </AppLayout>
</template>
