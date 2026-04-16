<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { BookOpen, ArrowLeftRight, Clock, AlertTriangle, CheckCircle, Calendar, ChevronRight, RotateCcw, Timer } from 'lucide-vue-next';
import SiswaLayout from '@/layouts/SiswaLayout.vue';
import { ref } from 'vue';

interface Book { id: number; judul: string; pengarang: string; }

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    tanggal_dikembalikan: string | null;
    status: string;
    denda: number;
    return_requested_at: string | null;
    admin_return_approved_at: string | null;
    book: Book;
}

interface Member { no_anggota: string; status: string; }

const props = defineProps<{
    activeBorrowings: Borrowing[];
    pendingReturns: Borrowing[];
    returnHistory: Borrowing[];
    member: Member;
}>();

const selectedBorrowing = ref<Borrowing | null>(null);
const showConfirmModal = ref(false);

const isOverdue = (b: Borrowing) => new Date(b.tanggal_kembali) < new Date();

const getDaysRemaining = (b: Borrowing) => {
    const diff = new Date(b.tanggal_kembali).getTime() - new Date().getTime();
    return Math.ceil(diff / (1000 * 60 * 60 * 24));
};

const openConfirmModal = (b: Borrowing) => { selectedBorrowing.value = b; showConfirmModal.value = true; };
const closeConfirmModal = () => { selectedBorrowing.value = null; showConfirmModal.value = false; };

const confirmReturn = () => {
    if (!selectedBorrowing.value) return;
    router.post(`/siswa/returns/${selectedBorrowing.value.id}/request`, {}, {
        onSuccess: () => closeConfirmModal()
    });
};

const formatDate = (date: string) => new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric'
});

const formatCurrency = (amount: number) => new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
}).format(amount);
</script>

<template>
    <Head title="Kembalikan Buku — E-Perpustakaan" />
    <SiswaLayout>
        <!-- ══ HERO HEADER ══ -->
        <section style="background: linear-gradient(135deg, #5C3D1E 0%, #3D2810 100%);" class="relative overflow-hidden py-12 px-6">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute right-0 top-0 h-48 w-48 rounded-full opacity-10" style="background: #E8A020;"></div>
            </div>
            <div class="relative mx-auto max-w-4xl">
                <div class="flex items-center gap-2 mb-4">
                    <a href="/" class="text-sm text-white opacity-60 hover:opacity-100 transition-opacity">Beranda</a>
                    <ChevronRight class="h-4 w-4 text-white opacity-40" />
                    <span class="text-sm text-white font-semibold">Kembalikan Buku</span>
                </div>
                <h1 class="text-3xl font-bold text-white" style="font-family: Georgia, serif;">↩ Kembalikan Buku</h1>
                <p class="mt-1 text-white opacity-70">Kelola pengembalian buku yang sedang kamu pinjam</p>
                <!-- Quick stats -->
                <div class="mt-6 flex gap-4 flex-wrap">
                    <div class="flex items-center gap-2 rounded-xl px-4 py-2" style="background: rgba(255,255,255,0.1);">
                        <BookOpen class="h-4 w-4 text-amber-300" />
                        <span class="text-sm text-white font-semibold">{{ activeBorrowings.length }} dipinjam</span>
                    </div>
                    <div v-if="pendingReturns.length > 0" class="flex items-center gap-2 rounded-xl px-4 py-2" style="background: rgba(255,255,255,0.1);">
                        <Clock class="h-4 w-4 text-amber-300" />
                        <span class="text-sm text-white font-semibold">{{ pendingReturns.length }} menunggu konfirmasi</span>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-4xl px-6 py-8 pb-16 flex flex-col gap-6">

            <!-- ══ NO ACTIVE BORROWINGS ══ -->
            <div v-if="activeBorrowings.length === 0 && pendingReturns.length === 0" class="flex flex-col items-center py-20 text-center rounded-2xl bg-white shadow-sm border-2" style="border-color: #F0D6A8;">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full" style="background: #DCFCE7;">
                    <CheckCircle class="h-10 w-10 text-green-500" />
                </div>
                <h3 class="text-xl font-bold mb-1" style="color: #5C3D1E;">Tidak Ada Buku yang Dipinjam</h3>
                <p class="text-sm mb-6" style="color: #9A7050;">Semua buku sudah dikembalikan dengan baik</p>
                <a href="/koleksi-buku" class="inline-flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-bold text-white" style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                    <BookOpen class="h-4 w-4" /> Pinjam Buku Baru
                </a>
            </div>

            <!-- ══ ACTIVE BORROWINGS ══ -->
            <div v-if="activeBorrowings.length > 0" class="rounded-2xl border-2 bg-white overflow-hidden" style="border-color: #F0D6A8;">
                <div class="flex items-center gap-3 px-6 py-4 border-b" style="background: #FFF8F0; border-color: #F0D6A8;">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full" style="background: #FFF3E0;">
                        <BookOpen class="h-5 w-5" style="color: #E8A020;" />
                    </div>
                    <div>
                        <h2 class="font-bold" style="color: #5C3D1E;">Buku yang Sedang Dipinjam</h2>
                        <p class="text-xs" style="color: #9A7050;">Klik "Kembalikan Buku" untuk request pengembalian</p>
                    </div>
                </div>
                <div class="divide-y" style="border-color: #FFF3E0;">
                    <div
                        v-for="borrowing in activeBorrowings"
                        :key="borrowing.id"
                        class="p-5 transition hover:bg-orange-50"
                        :style="isOverdue(borrowing) ? 'background: #FFF5F5;' : ''"
                    >
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <!-- Book info -->
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl shadow-sm"
                                    :style="isOverdue(borrowing) ? 'background: #FEE2E2;' : 'background: #FFF3E0;'">
                                    <BookOpen class="h-6 w-6" :style="isOverdue(borrowing) ? 'color: #EF4444;' : 'color: #E8A020;'" />
                                </div>
                                <div>
                                    <h3 class="font-bold" style="color: #5C3D1E;">{{ borrowing.book.judul }}</h3>
                                    <p class="text-sm" style="color: #9A7050;">{{ borrowing.book.pengarang }}</p>
                                    <div class="mt-2 flex flex-wrap gap-3 text-xs">
                                        <span class="flex items-center gap-1" style="color: #9A7050;">
                                            <Calendar class="h-3.5 w-3.5" />
                                            Dipinjam: {{ formatDate(borrowing.tanggal_pinjam) }}
                                        </span>
                                        <span class="flex items-center gap-1 font-semibold" :style="isOverdue(borrowing) ? 'color: #DC2626;' : 'color: #9A7050;'">
                                            <Clock class="h-3.5 w-3.5" />
                                            {{ isOverdue(borrowing) ? 'Terlambat sejak:' : 'Batas:' }} {{ formatDate(borrowing.tanggal_kembali) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Status + action -->
                            <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                                <!-- Overdue badge -->
                                <div v-if="isOverdue(borrowing)" class="text-center sm:text-right">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                        <AlertTriangle class="h-3.5 w-3.5" />
                                        Terlambat {{ Math.abs(getDaysRemaining(borrowing)) }} hari
                                    </span>
                                    <p class="mt-1 text-xs font-bold text-red-600">
                                        Est. denda: {{ formatCurrency(Math.abs(getDaysRemaining(borrowing)) * 1000) }}
                                    </p>
                                </div>
                                <!-- Days remaining badge -->
                                <span v-else class="rounded-full px-3 py-1 text-xs font-bold" style="background: #DCFCE7; color: #166534;">
                                    <Timer class="inline h-3.5 w-3.5 mr-1" />
                                    {{ getDaysRemaining(borrowing) }} hari tersisa
                                </span>
                                <button
                                    @click="openConfirmModal(borrowing)"
                                    class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white transition hover:opacity-90 active:scale-95 shadow-sm whitespace-nowrap"
                                    style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                                >
                                    <RotateCcw class="h-4 w-4" />
                                    Kembalikan Buku
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ PENDING RETURNS ══ -->
            <div v-if="pendingReturns.length > 0" class="rounded-2xl border-2 bg-white overflow-hidden" style="border-color: #FDE68A;">
                <div class="flex items-center gap-3 px-6 py-4 border-b" style="background: #FFFBEB; border-color: #FDE68A;">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full" style="background: #FEF3C7;">
                        <Clock class="h-5 w-5 text-amber-500" />
                    </div>
                    <div>
                        <h2 class="font-bold text-amber-800">Menunggu Konfirmasi Admin</h2>
                        <p class="text-xs text-amber-600">Request pengembalian sudah dikirim, menunggu persetujuan</p>
                    </div>
                </div>
                <div class="divide-y" style="border-color: #FEF9C3;">
                    <div v-for="borrowing in pendingReturns" :key="borrowing.id" class="flex items-center justify-between p-5 hover:bg-amber-50 transition">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" style="background: #FEF3C7;">
                                <BookOpen class="h-5 w-5 text-amber-500" />
                            </div>
                            <div>
                                <p class="font-semibold" style="color: #5C3D1E;">{{ borrowing.book.judul }}</p>
                                <p class="text-sm" style="color: #9A7050;">{{ borrowing.book.pengarang }}</p>
                                <p class="mt-1 text-xs text-amber-600">
                                    Request dikirim: {{ formatDate(borrowing.return_requested_at || '') }}
                                </p>
                            </div>
                        </div>
                        <span class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-bold" style="background: #FEF3C7; color: #92400E;">
                            <Clock class="h-3.5 w-3.5" />
                            Menunggu Admin
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ CONFIRM MODAL ══ -->
        <Teleport to="body">
            <Transition name="modal">
                <div v-if="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" @click.self="closeConfirmModal">
                    <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">
                        <!-- Modal header -->
                        <div class="px-6 pt-6 pb-4">
                            <h3 class="text-xl font-bold" style="color: #5C3D1E; font-family: Georgia, serif;">Request Pengembalian</h3>
                        </div>
                        <!-- Book info -->
                        <div class="mx-6 mb-4 rounded-2xl p-4" style="background: #FFF8F0; border: 1px solid #F0D6A8;">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl" style="background: #FFF3E0;">
                                    <BookOpen class="h-5 w-5" style="color: #E8A020;" />
                                </div>
                                <div>
                                    <p class="font-bold text-sm" style="color: #5C3D1E;">{{ selectedBorrowing?.book.judul }}</p>
                                    <p class="text-xs" style="color: #9A7050;">{{ selectedBorrowing?.book.pengarang }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Overdue warning -->
                        <div v-if="selectedBorrowing && isOverdue(selectedBorrowing)" class="mx-6 mb-4 rounded-2xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <AlertTriangle class="h-5 w-5 shrink-0 text-red-500 mt-0.5" />
                                <div>
                                    <p class="font-bold text-sm text-red-800">Buku Terlambat!</p>
                                    <p class="text-xs text-red-600 mt-1">
                                        Terlambat {{ Math.abs(getDaysRemaining(selectedBorrowing)) }} hari — Estimasi denda:
                                        <strong>{{ formatCurrency(Math.abs(getDaysRemaining(selectedBorrowing)) * 1000) }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!-- Info -->
                        <div class="mx-6 mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                            <p class="text-sm text-amber-700">
                                Request pengembalian akan dikirim ke admin untuk disetujui. Buku dinyatakan dikembalikan setelah admin menyetujui.
                            </p>
                        </div>
                        <!-- Actions -->
                        <div class="flex gap-3 px-6 pb-6">
                            <button @click="closeConfirmModal"
                                class="flex-1 rounded-2xl border-2 px-4 py-3 font-semibold transition hover:bg-gray-50"
                                style="border-color: #E5E7EB; color: #6B7280;">
                                Batal
                            </button>
                            <button @click="confirmReturn"
                                class="flex-1 flex items-center justify-center gap-2 rounded-2xl px-4 py-3 font-bold text-white transition hover:opacity-90"
                                style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                                <RotateCcw class="h-4 w-4" />
                                Konfirmasi
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </SiswaLayout>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
