<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { AlertTriangle, CreditCard, Clock, CheckCircle, ShieldCheck, ChevronRight, BookOpen, TrendingUp } from 'lucide-vue-next';
import { computed } from 'vue';
import Pagination from '@/components/Pagination.vue';
import SiswaLayout from '@/layouts/SiswaLayout.vue';

interface Book { id: number; judul: string; pengarang: string; }

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    tanggal_dikembalikan: string | null;
    denda: number;
    payment_status: string | null;
    book: Book;
}

interface PaginationData {
    data: Borrowing[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

interface Member { no_anggota: string; }

const props = defineProps<{
    activeFines: Borrowing[];
    unpaidFines: Borrowing[];
    pendingFines: Borrowing[];
    paidFines: PaginationData;
    totalUnpaid: number;
    totalPending: number;
    totalPaidHistory: number;
    member: Member;
}>();

const allUnpaid = computed(() => [...props.unpaidFines, ...props.pendingFines]);
const totalAll = computed(() => props.totalUnpaid + props.totalPending);
const totalPaid = computed(() => props.totalPaidHistory);

const formatDate = (date: string) => new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

const formatCurrency = (amount: number) => new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
}).format(amount);

const getDaysLate = (borrowing: Borrowing) => {
    const today = new Date();
    const returnDate = new Date(borrowing.tanggal_kembali);
    const diff = Math.ceil((today.getTime() - returnDate.getTime()) / (1000 * 60 * 60 * 24));
    return diff > 0 ? diff : 0;
};

const payFine = (borrowing: Borrowing) => {
    if (confirm(`Konfirmasi pembayaran denda ${formatCurrency(borrowing.denda)} untuk buku "${borrowing.book.judul}"?\n\nPembayaran dilakukan secara tunai di kasir perpustakaan.`)) {
        router.post(`/siswa/fines/${borrowing.id}/pay`);
    }
};

const payOnline = async (borrowing: Borrowing) => {
    try {
        const response = await fetch(`/siswa/payment/${borrowing.id}/token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '',
            }
        });
        
        const data = await response.json();
        
        if (data.token) {
            (window as any).snap.pay(data.token, {
                onSuccess: () => {
                    router.get(`/siswa/payment/${borrowing.id}/success`);
                },
                onPending: () => {
                    alert('Pembayaran tertunda, silakan selesaikan di aplikasi atau outlet pilihan Anda.');
                    router.reload();
                },
                onError: () => alert('Pembayaran gagal, silakan coba lagi.'),
                onClose: () => console.log('Popup ditutup')
            });
        } else {
            alert(data.error || 'Gagal menyiapkan pembayaran.');
        }
    } catch (e) {
        console.error('Payment Error:', e);
        alert('Gagal terhubung ke server. Pastikan Anda sudah login ulang atau refresh halaman.');
    }
};
</script>

<template>
    <Head title="Denda Saya — E-Perpustakaan" />
    <SiswaLayout>
        <section style="background: linear-gradient(135deg, #5C3D1E 0%, #3D2810 100%);" class="relative overflow-hidden py-12 px-6">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute right-0 top-0 h-48 w-48 rounded-full opacity-10" style="background: #E8A020;"></div>
            </div>
            <div class="relative mx-auto max-w-4xl">
                <div class="flex items-center gap-2 mb-4">
                    <a href="/" class="text-sm text-white opacity-60 hover:opacity-100 transition-opacity">Beranda</a>
                    <ChevronRight class="h-4 w-4 text-white opacity-40" />
                    <span class="text-sm text-white font-semibold">Denda Saya</span>
                </div>
                <h1 class="text-3xl font-bold text-white" style="font-family: Georgia, serif;">💰 Denda Saya</h1>
                <p class="mt-1 text-white opacity-70">Riwayat dan pembayaran denda keterlambatan</p>
            </div>
        </section>

        <div class="mx-auto max-w-4xl px-6 py-8 flex flex-col gap-6 pb-16">

            <!-- ══ ALERT: Peminjaman diblokir ══ -->
            <div v-if="totalAll > 0" class="flex items-start gap-4 rounded-2xl border-2 border-red-200 bg-red-50 p-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                    <AlertTriangle class="h-5 w-5 text-red-600" />
                </div>
                <div class="flex-1">
                    <p class="font-bold text-red-800">⛔ Peminjaman Anda Diblokir</p>
                    <p class="mt-1 text-sm text-red-700">
                        Anda memiliki total denda belum lunas sebesar <strong>{{ formatCurrency(totalAll) }}</strong>.
                        Lunasi seluruh denda di bawah agar dapat meminjam buku kembali.
                    </p>
                </div>
            </div>
            <div v-else class="flex items-center gap-4 rounded-2xl border-2 border-green-200 bg-green-50 p-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100">
                    <ShieldCheck class="h-5 w-5 text-green-600" />
                </div>
                <div>
                    <p class="font-bold text-green-800">✅ Status Bersih</p>
                    <p class="text-sm text-green-700">Anda tidak memiliki denda. Bebas meminjam buku!</p>
                </div>
            </div>

            <!-- ══ STAT CARDS ══ -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border-2 p-5 text-center transition hover:shadow-md" style="border-color: #EF4444; background: #FFF5F5;">
                    <p class="text-xs font-bold uppercase tracking-wider text-red-500 mb-1">Belum Lunas</p>
                    <p class="text-2xl font-black" style="color: #DC2626;">{{ formatCurrency(totalAll) }}</p>
                    <p class="mt-1 text-xs text-red-400">{{ allUnpaid.length }} item</p>
                </div>
                <div class="rounded-2xl border-2 p-5 text-center transition hover:shadow-md" style="border-color: #F59E0B; background: #FFFBEB;">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-500 mb-1">Denda Berjalan</p>
                    <p class="text-2xl font-black text-amber-600">{{ activeFines.length }}</p>
                    <p class="mt-1 text-xs text-amber-400">buku terlambat</p>
                </div>
                <div class="rounded-2xl border-2 p-5 text-center transition hover:shadow-md" style="border-color: #22C55E; background: #F0FDF4;">
                    <p class="text-xs font-bold uppercase tracking-wider text-green-500 mb-1">Sudah Lunas</p>
                    <p class="text-2xl font-black text-green-600">{{ formatCurrency(totalPaid) }}</p>
                    <p class="mt-1 text-xs text-green-400">Total riwayat</p>
                </div>
            </div>

            <!-- ══ ESTIMASI DENDA BERJALAN ══ -->
            <div v-if="activeFines.length > 0" class="rounded-2xl border-2 bg-white overflow-hidden" style="border-color: #FED7AA;">
                <div class="flex items-center gap-3 px-6 py-4 border-b" style="background: #FFF7ED; border-color: #FED7AA;">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full" style="background: #FFEDD5;">
                        <Clock class="h-5 w-5 text-orange-500" />
                    </div>
                    <div>
                        <h2 class="font-bold" style="color: #5C3D1E;">Estimasi Denda Berjalan</h2>
                        <p class="text-xs" style="color: #9A7050;">Buku terlambat dikembalikan — denda terus bertambah</p>
                    </div>
                </div>
                <div class="divide-y" style="border-color: #FFF3E0;">
                    <div v-for="fine in activeFines" :key="'active-' + fine.id" class="flex items-center justify-between px-6 py-4 hover:bg-orange-50 transition">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl" style="background: #FFF3E0;">
                                <BookOpen class="h-5 w-5" style="color: #E8A020;" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm" style="color: #5C3D1E;">{{ fine.book.judul }}</p>
                                <p class="text-xs mt-0.5" style="color: #9A7050;">Batas: {{ formatDate(fine.tanggal_kembali) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-bold bg-red-100 text-red-700 mb-1">
                                +{{ getDaysLate(fine) }} hari
                            </span>
                            <p class="text-base font-black" style="color: #C4781A;">
                                {{ formatCurrency(getDaysLate(fine) * 1000) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ DENDA HARUS DIBAYAR ══ -->
            <div class="rounded-2xl border-2 bg-white overflow-hidden" style="border-color: #FCA5A5;">
                <div class="flex items-center gap-3 px-6 py-4 border-b" style="background: #FFF5F5; border-color: #FCA5A5;">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-red-100">
                        <AlertTriangle class="h-5 w-5 text-red-500" />
                    </div>
                    <div>
                        <h2 class="font-bold text-red-800">Denda Harus Dibayar</h2>
                        <p class="text-xs text-red-500">Bayar di kasir perpustakaan untuk membuka akses pinjam</p>
                    </div>
                </div>
                <!-- Has fines -->
                <div v-if="allUnpaid.length > 0" class="divide-y" style="border-color: #FEE2E2;">
                    <div v-for="fine in allUnpaid" :key="fine.id" class="flex items-center justify-between px-6 py-4 hover:bg-red-50 transition">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100">
                                <BookOpen class="h-5 w-5 text-red-500" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm" style="color: #5C3D1E;">{{ fine.book.judul }}</p>
                                <p class="text-xs" style="color: #9A7050;">{{ fine.book.pengarang }}</p>
                                <p class="text-xs mt-0.5 text-gray-400">Dikembalikan: {{ formatDate(fine.tanggal_dikembalikan || fine.tanggal_kembali) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-lg font-black text-red-600">{{ formatCurrency(fine.denda) }}</p>
                            <div class="flex flex-col gap-2">
                                <button
                                    @click="payOnline(fine)"
                                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-bold text-white transition hover:opacity-90 active:scale-95 shadow-sm"
                                    style="background: linear-gradient(135deg, #10B981, #059669);"
                                >
                                    <TrendingUp class="h-4 w-4" />
                                    Bayar Online
                                </button>
                                <button
                                    @click="payFine(fine)"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold text-gray-500 hover:bg-gray-100 transition"
                                >
                                    <CreditCard class="h-3 w-3" />
                                    Bayar Tunai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- No fines -->
                <div v-else class="flex flex-col items-center py-12 text-center">
                    <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                        <ShieldCheck class="h-8 w-8 text-green-500" />
                    </div>
                    <p class="font-bold" style="color: #5C3D1E;">Tidak ada denda yang harus dibayar 🎉</p>
                    <p class="text-sm mt-1" style="color: #9A7050;">Anda bebas meminjam buku!</p>
                </div>
            </div>

            <!-- ══ RIWAYAT PEMBAYARAN ══ -->
            <div v-if="paidFines.data.length > 0" class="rounded-2xl border-2 bg-white overflow-hidden" style="border-color: #86EFAC;">
                <div class="flex items-center gap-3 px-6 py-4 border-b" style="background: #F0FDF4; border-color: #86EFAC;">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-green-100">
                        <CheckCircle class="h-5 w-5 text-green-500" />
                    </div>
                    <div>
                        <h2 class="font-bold text-green-800">Riwayat Pembayaran</h2>
                        <p class="text-xs text-green-600">Denda yang sudah dilunasi</p>
                    </div>
                    <div class="ml-auto">
                        <span class="rounded-full px-3 py-1 text-xs font-bold bg-green-100 text-green-700">
                            Total {{ formatCurrency(totalPaid) }}
                        </span>
                    </div>
                </div>
                <div class="divide-y" style="border-color: #DCFCE7;">
                    <div v-for="fine in paidFines.data" :key="'paid-' + fine.id" class="flex items-center justify-between px-6 py-4 hover:bg-green-50 transition">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100">
                                <BookOpen class="h-5 w-5 text-green-600" />
                            </div>
                            <div>
                                <p class="font-semibold text-sm" style="color: #5C3D1E;">{{ fine.book.judul }}</p>
                                <p class="text-xs" style="color: #9A7050;">{{ fine.book.pengarang }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold" style="color: #5C3D1E;">{{ formatCurrency(fine.denda) }}</span>
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700">✓ Lunas</span>
                        </div>
                    </div>
                </div>
                <!-- Pagination -->
                <div class="p-4 border-t flex justify-center bg-green-50">
                    <Pagination :links="paidFines.links" />
                </div>
            </div>
        </div>
    </SiswaLayout>
</template>
