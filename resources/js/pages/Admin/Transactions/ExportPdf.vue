<script setup lang="ts">
import { onMounted } from 'vue';

interface Member {
    no_anggota: string;
    user: { name: string };
}

interface Book {
    judul: string;
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

const props = defineProps<{
    borrowings: Borrowing[];
    generatedAt: string;
    filterLabel: string;
}>();

const formatDate = (d: string | null) => {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const formatRp = (n: number) =>
    n > 0 ? 'Rp ' + new Intl.NumberFormat('id-ID').format(n) : '-';

const statusLabel = (s: string) => ({
    dipinjam: 'Dipinjam',
    dikembalikan: 'Dikembalikan',
    terlambat: 'Terlambat',
    menunggu_pengembalian: 'Menunggu',
}[s] ?? s);

const totalFine = props.borrowings.reduce((a, b) => a + (b.denda ?? 0), 0);
const activeCount = props.borrowings.filter(b => b.status === 'dipinjam').length;
const returnedCount = props.borrowings.filter(b => ['dikembalikan', 'terlambat'].includes(b.status)).length;

onMounted(() => {
    setTimeout(() => print(), 400);
});

const print = () => window.print();
</script>

<template>
    <div class="print-wrapper">
        <!-- Non-print toolbar -->
        <div class="toolbar no-print">
            <div class="toolbar-inner">
                <span class="toolbar-title">📄 Laporan Transaksi Peminjaman</span>
                <button class="btn-print" @click="print()">🖨️ Cetak / Simpan PDF</button>
            </div>
        </div>

        <!-- Print document -->
        <div class="doc">
            <div class="doc-header">
                <h1>LAPORAN TRANSAKSI PEMINJAMAN BUKU</h1>
                <p class="filter-label">Periode: {{ filterLabel }}</p>
                <p>Perpustakaan — Dicetak pada {{ generatedAt }}</p>
            </div>

            <!-- Summary cards -->
            <div class="summary">
                <div class="summary-card">
                    <div class="summary-label">Total Transaksi</div>
                    <div class="summary-value">{{ borrowings.length }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Sedang Dipinjam</div>
                    <div class="summary-value blue">{{ activeCount }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Dikembalikan</div>
                    <div class="summary-value green">{{ returnedCount }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Total Denda</div>
                    <div class="summary-value red">{{ formatRp(totalFine) }}</div>
                </div>
            </div>

            <!-- Table -->
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Anggota</th>
                        <th>No. Anggota</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Dikembalikan</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(b, i) in borrowings" :key="b.id">
                        <td>{{ i + 1 }}</td>
                        <td>{{ b.member?.user?.name ?? '-' }}</td>
                        <td>{{ b.member?.no_anggota ?? '-' }}</td>
                        <td>{{ b.book?.judul ?? '-' }}</td>
                        <td>{{ formatDate(b.tanggal_pinjam) }}</td>
                        <td>{{ formatDate(b.tanggal_kembali) }}</td>
                        <td>{{ formatDate(b.tanggal_dikembalikan) }}</td>
                        <td>
                            <span :class="['badge', `badge-${b.status}`]">{{ statusLabel(b.status) }}</span>
                        </td>
                        <td :class="b.denda > 0 ? 'denda' : ''">{{ formatRp(b.denda) }}</td>
                    </tr>
                    <tr v-if="!borrowings.length">
                        <td colspan="9" class="empty">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>

            <div class="doc-footer">
                Laporan dibuat otomatis oleh Sistem Perpustakaan
            </div>
        </div>
    </div>
</template>

<style scoped>
* { box-sizing: border-box; }

.toolbar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 50;
    background: #1d4ed8; color: white; padding: 10px 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
}
.toolbar-inner { display: flex; align-items: center; justify-content: space-between; max-width: 1100px; margin: 0 auto; }
.toolbar-title { font-weight: 600; font-size: 15px; }
.btn-print {
    background: white; color: #1d4ed8; border: none;
    padding: 8px 18px; border-radius: 8px; font-weight: 600;
    cursor: pointer; font-size: 13px;
}
.btn-print:hover { background: #eff6ff; }

.doc { max-width: 1100px; margin: 70px auto 40px; padding: 0 24px 40px; font-family: Arial, sans-serif; font-size: 13px; color: #1a1a1a; }

.doc-header { text-align: center; padding-bottom: 14px; border-bottom: 2px solid #1d4ed8; margin-bottom: 16px; }
.doc-header h1 { font-size: 17px; font-weight: bold; color: #1d4ed8; }
.doc-header .filter-label { font-size: 12px; color: #1d4ed8; font-weight: 600; margin-top: 4px; }
.doc-header p  { font-size: 11px; color: #6b7280; margin-top: 4px; }

.summary { display: flex; gap: 12px; margin-bottom: 16px; }
.summary-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 14px; }
.summary-label { font-size: 11px; color: #6b7280; }
.summary-value { font-size: 18px; font-weight: bold; color: #1d4ed8; margin-top: 2px; }
.summary-value.green { color: #059669; }
.summary-value.red   { color: #dc2626; }
.summary-value.blue  { color: #2563eb; }

table { width: 100%; border-collapse: collapse; font-size: 12px; }
thead tr { background: #1d4ed8; color: white; }
thead th { padding: 8px 7px; text-align: left; font-weight: 600; }
tbody tr:nth-child(even) { background: #f0f4ff; }
tbody td { padding: 6px 7px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }

.badge { display: inline-block; padding: 2px 8px; border-radius: 999px; font-size: 10px; font-weight: 600; }
.badge-dipinjam                { background: #fef3c7; color: #92400e; }
.badge-dikembalikan            { background: #d1fae5; color: #065f46; }
.badge-terlambat               { background: #fee2e2; color: #991b1b; }
.badge-menunggu_pengembalian   { background: #dbeafe; color: #1e40af; }

.denda { color: #dc2626; font-weight: 600; }
.empty { text-align: center; padding: 24px; color: #9ca3af; }
.doc-footer { margin-top: 20px; text-align: right; font-size: 10px; color: #9ca3af; }

@media print {
    .no-print { display: none !important; }
    .doc { margin: 0; padding: 0 10px 20px; }
    tbody tr:nth-child(even) { background: #f8f9ff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead tr { background: #1d4ed8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
