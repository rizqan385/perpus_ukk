<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ClipboardList, Check, X, BookOpen, Clock, User } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Member { id: number; no_anggota: string; name: string; }
interface Book   { id: number; judul: string; pengarang: string; cover_image: string | null; }
interface Request { id: number; tanggal_pinjam: string; tanggal_kembali: string; member: Member; book: Book; }

const props = defineProps<{ pending: Request[]; totalPending: number; }>();

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Persetujuan Pinjam', href: '/admin/borrow-approvals' },
];

const formatDate = (d: string) => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

const approve = (id: number) => router.post(`/admin/borrow-approvals/${id}/approve`, {}, { preserveScroll: true });
const reject  = (id: number) => { if (confirm('Tolak permintaan ini?')) router.post(`/admin/borrow-approvals/${id}/reject`, {}, { preserveScroll: true }); };

const coverUrl = (book: Book) => book.cover_image ? `/storage/${book.cover_image}` : null;
</script>

<template>
    <Head title="Persetujuan Pinjam" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Persetujuan Pinjam</h1>
                    <p class="text-gray-600 dark:text-gray-400">Konfirmasi permintaan peminjaman buku dari siswa</p>
                </div>
                <div class="flex items-center gap-2 rounded-xl px-4 py-2" style="background: linear-gradient(135deg, #FEF9C3, #FDE68A);">
                    <Clock class="h-5 w-5 text-amber-600" />
                    <span class="font-bold text-amber-800">{{ totalPending }} menunggu</span>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="pending.length === 0" class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed bg-white py-24 dark:bg-gray-800" style="border-color: #E8A020;">
                <ClipboardList class="mb-4 h-16 w-16" style="color: #E8A020;" />
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Tidak ada permintaan pending</p>
                <p class="text-sm text-gray-500">Semua permintaan pinjam sudah diproses 🎉</p>
            </div>

            <!-- Table -->
            <div v-else class="overflow-hidden rounded-2xl border bg-white shadow dark:bg-gray-800">
                <div class="border-b px-5 py-4" style="background: linear-gradient(135deg, #FFF3E0, #FFE0B2); border-color: #E8A020;">
                    <div class="flex items-center gap-2">
                        <Clock class="h-5 w-5 text-orange-600" />
                        <h2 class="font-semibold text-orange-900">Menunggu Persetujuan Admin</h2>
                    </div>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Buku</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Peminjam</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Tanggal Permintaan</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Batas Kembali</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="req in pending" :key="req.id" class="hover:bg-orange-50/30 dark:hover:bg-gray-700/30 transition-colors">
                            <!-- Book -->
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
                            <!-- Member -->
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
                            <!-- Dates -->
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(req.tanggal_pinjam) }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(req.tanggal_kembali) }}</td>
                            <!-- Actions -->
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="approve(req.id)"
                                        class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:opacity-90"
                                        style="background: #16A34A;"
                                    >
                                        <Check class="h-4 w-4" />
                                        Setujui
                                    </button>
                                    <button
                                        @click="reject(req.id)"
                                        class="flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50 dark:hover:bg-red-900/20"
                                        style="border-color: #EF4444;"
                                    >
                                        <X class="h-4 w-4" />
                                        Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
