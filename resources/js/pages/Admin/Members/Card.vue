<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Printer, IdCard } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface Member {
    id: number;
    no_anggota: string;
    kelas: string | null;
    alamat: string | null;
    telepon: string | null;
    tanggal_lahir: string | null;
    jenis_kelamin: string | null;
    status: string;
    foto_url: string | null;
    created_at: string;
    user: { name: string; email: string };
}

const props = defineProps<{ member: Member }>();

const formatDate = (d: string | null) => {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Anggota', href: '/admin/members' },
    { title: 'Kartu Anggota', href: `/admin/members/${props.member.id}/card` },
];

const printCard = () => window.print();
</script>

<template>
    <Head title="Kartu Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col items-center gap-6 p-6">

            <!-- Toolbar -->
            <div class="flex w-full max-w-2xl items-center justify-between no-print">
                <Link
                    :href="`/admin/members/${member.id}`"
                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700"
                >
                    <ArrowLeft class="h-4 w-4" />
                    Kembali
                </Link>
                <button
                    @click="printCard"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 active:scale-95 transition-all"
                >
                    <Printer class="h-4 w-4" />
                    Cetak Kartu
                </button>
            </div>

            <!-- Card Preview -->
            <div class="w-full max-w-2xl">
                <p class="mb-3 text-xs text-gray-500 text-center no-print">Preview kartu anggota — ukuran cetak: 85.6 × 54 mm</p>

                <Teleport to="body">
                    <div id="member-card" class="card-wrap mx-auto">
                        <!-- Front -->
                    <div class="card-front">
                        <!-- Header bar -->
                        <div class="card-header">
                            <IdCard class="h-5 w-5 text-white opacity-80" />
                            <span>KARTU ANGGOTA PERPUSTAKAAN</span>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Photo -->
                            <div class="card-photo">
                                <img v-if="member.foto_url" :src="member.foto_url" alt="Foto" class="card-photo-img" />
                                <div v-else class="card-photo-placeholder">
                                    <span>{{ member.user.name.charAt(0).toUpperCase() }}</span>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="card-info">
                                <p class="card-name">{{ member.user.name }}</p>
                                <p class="card-no">{{ member.no_anggota }}</p>
                                <div class="card-details">
                                    <span v-if="member.kelas" class="card-pill">{{ member.kelas }}</span>
                                    <span v-if="member.jenis_kelamin" class="card-pill">
                                        {{ member.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </div>
                                <p class="card-tgl">Berlaku sejak: {{ formatDate(member.created_at) }}</p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer">
                            <span>Status: <strong>{{ member.status === 'aktif' ? 'AKTIF' : 'NON-AKTIF' }}</strong></span>
                        </div>
                        </div>
                    </div>
                </Teleport>
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.card-wrap {
    width: 340px;
}

.card-front {
    width: 340px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: rgba(0,0,0,0.2);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: white;
}

.card-body {
    display: flex;
    gap: 14px;
    padding: 14px;
    align-items: center;
}

.card-photo {
    width: 72px;
    height: 80px;
    border-radius: 10px;
    overflow: hidden;
    border: 3px solid rgba(255,255,255,0.3);
    flex-shrink: 0;
    background: rgba(255,255,255,0.15);
}

.card-photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    color: white;
}

.card-info { flex: 1; }

.card-name {
    font-size: 15px;
    font-weight: 700;
    color: white;
    margin-bottom: 2px;
}

.card-no {
    font-size: 11px;
    color: rgba(255,255,255,0.7);
    font-family: monospace;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
}

.card-details {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 6px;
}

.card-pill {
    font-size: 9px;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(255,255,255,0.2);
    color: white;
    font-weight: 600;
}

.card-tgl {
    font-size: 9px;
    color: rgba(255,255,255,0.55);
}

.card-footer {
    padding: 7px 14px;
    background: rgba(0,0,0,0.25);
    font-size: 9px;
    color: rgba(255,255,255,0.7);
    text-align: right;
}

.card-footer strong { color: #86efac; }

/* ── Print styles ── */
@media print {
    #app { 
        display: none !important; 
    }

    .card-wrap {
        display: block !important;
        position: absolute;
        top: 0;
        left: 0;
        width: 85.6mm !important;
        visibility: visible !important;
    }

    .card-front {
        width: 85.6mm;
        border-radius: 4mm;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
