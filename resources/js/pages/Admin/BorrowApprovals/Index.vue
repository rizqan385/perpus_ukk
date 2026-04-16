<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ClipboardList, Check, X, BookOpen, Clock, Search, ArrowLeftRight, PartyPopper } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

interface Member { id: number; no_anggota: string; name: string; }
interface Book   { id: number; judul: string; pengarang: string; cover_image: string | null; }
interface Approval {
    id: number;
    type: 'borrow' | 'return';
    tanggal_pinjam: string;
    tanggal_kembali: string;
    member: Member;
    book: Book;
}

const props = defineProps<{
    pendingBorrows: Approval[];
    pendingReturns: Approval[];
    totalBorrows: number;
    totalReturns: number;
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');
const processingId = ref<number | null>(null);

const applySearch = () =>
    router.get('/admin/borrow-approvals', { search: search.value }, { preserveState: true });

const clearSearch = () => {
    search.value = '';
    router.get('/admin/borrow-approvals', {}, { preserveState: true });
};

const totalAll = computed(() => props.totalBorrows + props.totalReturns);
const allHandled = computed(() => totalAll.value === 0);

const formatDate = (d: string) =>
    new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

const coverUrl = (book: Book) => book.cover_image ? `/storage/${book.cover_image}` : null;

const approve = (id: number) => {
    processingId.value = id;
    router.post(`/admin/borrow-approvals/${id}/approve`, {}, { 
        preserveScroll: true,
        onFinish: () => processingId.value = null 
    });
};

const reject = (id: number) => {
    if (confirm('Tolak permintaan pinjam ini?')) {
        processingId.value = id;
        router.post(`/admin/borrow-approvals/${id}/reject`, {}, { 
            preserveScroll: true,
            onFinish: () => processingId.value = null 
        });
    }
};

const approveReturn = (id: number) => {
    processingId.value = id;
    router.post(`/admin/borrow-approvals/${id}/approve-return`, {}, { 
        preserveScroll: true,
        onFinish: () => processingId.value = null 
    });
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Persetujuan', href: '/admin/borrow-approvals' },
];
</script>

<template>
    <Head title="Persetujuan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Persetujuan</h1>
                    <p class="text-gray-600 dark:text-gray-400">Konfirmasi permintaan peminjaman dan pengembalian buku</p>
                </div>
                <!-- Badge total -->
                <div v-if="!allHandled" class="flex items-center gap-3">
                    <div v-if="totalBorrows > 0" class="flex items-center gap-2 rounded-xl px-4 py-2" style="background: linear-gradient(135deg, #FEF9C3, #FDE68A);">
                        <Clock class="h-5 w-5 text-amber-600" />
                        <span class="font-bold text-amber-800">{{ totalBorrows }} pinjam</span>
                    </div>
                    <div v-if="totalReturns > 0" class="flex items-center gap-2 rounded-xl px-4 py-2" style="background: linear-gradient(135deg, #EDE9FE, #DDD6FE);">
                        <ArrowLeftRight class="h-5 w-5 text-violet-600" />
                        <span class="font-bold text-violet-800">{{ totalReturns }} kembali</span>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="flex items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama siswa, no. anggota, atau judul buku..."
                        @keyup.enter="applySearch"
                        class="w-full rounded-lg border bg-white py-2 pl-9 pr-4 text-sm focus:border-amber-400 focus:outline-none focus:ring-1 focus:ring-amber-400 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                    />
                </div>
                <button @click="applySearch" class="rounded-lg bg-amber-500 px-3 py-2 text-sm font-medium text-white hover:bg-amber-600">Cari</button>
                <button v-if="search" @click="clearSearch" class="rounded-lg border px-3 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400">
                    <X class="h-4 w-4" />
                </button>
            </div>

            <!-- ══ Semua beres ═══════════════════════════════════════ -->
            <div v-if="allHandled && !search" class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed bg-white py-24 dark:bg-gray-800" style="border-color: #E8A020;">
                <PartyPopper class="mb-4 h-16 w-16 text-amber-400" />
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Semua sudah disetujui! 🎉</p>
                <p class="text-sm text-gray-500">Tidak ada permintaan yang menunggu konfirmasi</p>
            </div>

            <!-- ══ No result from search ══════════════════════════════ -->
            <div v-else-if="pendingBorrows.length === 0 && pendingReturns.length === 0 && search"
                 class="flex flex-col items-center justify-center rounded-2xl border bg-white py-16 dark:bg-gray-800">
                <ClipboardList class="mb-3 h-12 w-12 text-gray-300" />
                <p class="text-gray-500">Tidak ada hasil untuk "<strong>{{ search }}</strong>"</p>
            </div>

            <template v-else>

                <!-- ══ PERSETUJUAN PEMINJAMAN ══════════════════════════ -->
                <div v-if="pendingBorrows.length > 0" class="overflow-hidden rounded-2xl border-2 bg-white shadow dark:bg-gray-800" style="border-color: #E8A020;">
                    <div class="border-b px-5 py-4" style="background: linear-gradient(135deg, #FFF3E0, #FFE0B2); border-color: #E8A020;">
                        <div class="flex items-center gap-2">
                            <Clock class="h-5 w-5 text-orange-600" />
                            <h2 class="font-semibold text-orange-900">Persetujuan Peminjaman</h2>
                            <span class="ml-auto rounded-full bg-amber-500 px-2.5 py-0.5 text-xs font-bold text-white">{{ pendingBorrows.length }}</span>
                        </div>
                    </div>
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Buku</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Peminjam</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tgl Permintaan</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="req in pendingBorrows" :key="req.id" class="hover:bg-orange-50/30 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-14 w-10 flex-shrink-0 overflow-hidden rounded-lg" style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                                            <img v-if="coverUrl(req.book)" :src="coverUrl(req.book)!" :alt="req.book.judul" class="h-full w-full object-cover" />
                                            <div v-else class="flex h-full items-center justify-center">
                                                <BookOpen class="h-5 w-5 text-white opacity-70" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ req.book.judul }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ req.book.pengarang }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white" style="background: #E8A020;">
                                            {{ req.member.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ req.member.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ req.member.no_anggota }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(req.tanggal_pinjam) }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <button 
                                            @click="approve(req.id)" 
                                            :disabled="processingId === req.id"
                                            class="flex min-w-[90px] items-center justify-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:opacity-90 disabled:opacity-50" 
                                            style="background: #16A34A;"
                                        >
                                            <template v-if="processingId === req.id">
                                                <div class="h-3 w-3 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                            </template>
                                            <template v-else>
                                                <Check class="h-4 w-4" /> Setujui
                                            </template>
                                        </button>
                                        <button 
                                            @click="reject(req.id)" 
                                            :disabled="processingId === req.id"
                                            class="flex min-w-[80px] items-center justify-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50 disabled:opacity-50 dark:hover:bg-red-900/20" 
                                            style="border-color: #EF4444;"
                                        >
                                            <X class="h-4 w-4" /> Tolak
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ══ PERSETUJUAN PENGEMBALIAN ════════════════════════ -->
                <div v-if="pendingReturns.length > 0" class="overflow-hidden rounded-2xl border-2 bg-white shadow dark:bg-gray-800" style="border-color: #7C3AED;">
                    <div class="border-b px-5 py-4" style="background: linear-gradient(135deg, #F5F3FF, #EDE9FE); border-color: #7C3AED;">
                        <div class="flex items-center gap-2">
                            <ArrowLeftRight class="h-5 w-5 text-violet-600" />
                            <h2 class="font-semibold text-violet-900">Persetujuan Pengembalian</h2>
                            <span class="ml-auto rounded-full bg-violet-600 px-2.5 py-0.5 text-xs font-bold text-white">{{ pendingReturns.length }}</span>
                        </div>
                    </div>
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Buku</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Peminjam</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Batas Kembali</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="req in pendingReturns" :key="req.id" class="hover:bg-violet-50/30 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-14 w-10 flex-shrink-0 overflow-hidden rounded-lg" style="background: linear-gradient(135deg, #7C3AED, #6D28D9);">
                                            <img v-if="coverUrl(req.book)" :src="coverUrl(req.book)!" :alt="req.book.judul" class="h-full w-full object-cover" />
                                            <div v-else class="flex h-full items-center justify-center">
                                                <BookOpen class="h-5 w-5 text-white opacity-70" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ req.book.judul }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ req.book.pengarang }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold text-white" style="background: #7C3AED;">
                                            {{ req.member.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ req.member.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ req.member.no_anggota }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(req.tanggal_kembali) }}</td>
                                <td class="px-4 py-4">
                                    <button 
                                        @click="approveReturn(req.id)" 
                                        :disabled="processingId === req.id"
                                        class="flex min-w-[150px] items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-bold text-white transition hover:opacity-90 disabled:opacity-50" 
                                        style="background: #7C3AED;"
                                    >
                                        <template v-if="processingId === req.id">
                                            <div class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                                            Memproses...
                                        </template>
                                        <template v-else>
                                            <Check class="h-4 w-4" /> Konfirmasi Kembali
                                        </template>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </template>
        </div>
    </AppLayout>
</template>
