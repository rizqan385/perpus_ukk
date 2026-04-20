<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Printer, Search, Filter, CreditCard, ArrowLeft, Users, CheckSquare, Square, X } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface User { id: number; name: string; email: string; }
interface Member {
    id: number;
    no_anggota: string;
    kelas: string | null;
    foto_url: string | null;
    tanggal_lahir: string | null;
    jenis_kelamin: string | null;
    status: string;
    created_at: string;
    user: User;
}

const props = defineProps<{
    members: Member[];
    allKelas: string[];
    filters: { kelas?: string; search?: string };
}>();

const kelasFilter = ref(props.filters.kelas || '');
const searchFilter = ref(props.filters.search || '');

// Selected member IDs (empty = all)
const selectedIds = ref<Set<number>>(new Set());
const allSelected = computed(() =>
    props.members.length > 0 && selectedIds.value.size === props.members.length
);

const toggleAll = () => {
    if (allSelected.value) {
        selectedIds.value = new Set();
    } else {
        selectedIds.value = new Set(props.members.map(m => m.id));
    }
};

const toggleMember = (id: number) => {
    const s = new Set(selectedIds.value);
    s.has(id) ? s.delete(id) : s.add(id);
    selectedIds.value = s;
};

const applyFilter = () => {
    router.get('/admin/members/cards', {
        kelas: kelasFilter.value,
        search: searchFilter.value,
    }, { preserveState: true });
};

const resetFilter = () => {
    kelasFilter.value = '';
    searchFilter.value = '';
    router.get('/admin/members/cards', {}, { preserveState: true });
};

// Members to print: selected ones or all if none selected
const membersToPrint = computed(() =>
    selectedIds.value.size > 0
        ? props.members.filter(m => selectedIds.value.has(m.id))
        : props.members
);

const formatDate = (d: string | null) => {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const printCards = () => { window.print(); };

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Anggota', href: '/admin/members' },
    { title: 'Cetak Kartu', href: '/admin/members/cards' },
];
</script>

<template>
    <Head title="Cetak Kartu Anggota" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6 no-print">

            <!-- Header -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <Link href="/admin/members" class="rounded-lg border p-2 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700">
                        <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </Link>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Cetak Kartu Anggota</h1>
                        <p class="text-gray-600 dark:text-gray-400">Filter dan pilih anggota yang ingin dicetak kartunya</p>
                    </div>
                </div>
                <button
                    @click="printCards"
                    :disabled="membersToPrint.length === 0"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 font-semibold text-white transition-all hover:bg-blue-700 active:scale-95 disabled:opacity-40"
                >
                    <Printer class="h-5 w-5" />
                    Cetak {{ selectedIds.size > 0 ? `(${selectedIds.size} dipilih)` : `Semua (${members.length})` }}
                </button>
            </div>

            <!-- Filter Bar -->
            <div class="rounded-xl border bg-white p-4 dark:bg-gray-800 dark:border-gray-700">
                <div class="flex flex-wrap items-end gap-3">
                    <!-- Filter Kelas -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 flex items-center gap-1">
                            <Filter class="h-3 w-3" /> Filter Kelas
                        </label>
                        <select
                            v-model="kelasFilter"
                            @change="applyFilter"
                            class="rounded-lg border bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Semua Kelas</option>
                            <option v-for="k in allKelas" :key="k" :value="k">{{ k }}</option>
                        </select>
                    </div>

                    <!-- Cari Siswa -->
                    <div class="flex flex-col gap-1.5 flex-1 min-w-48">
                        <label class="text-xs font-semibold text-gray-600 dark:text-gray-400 flex items-center gap-1">
                            <Search class="h-3 w-3" /> Cari Siswa
                        </label>
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                            <input
                                v-model="searchFilter"
                                type="text"
                                placeholder="Nama atau No. Anggota..."
                                @keyup.enter="applyFilter"
                                class="w-full rounded-lg border bg-white pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                        </div>
                    </div>

                    <button @click="applyFilter" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Terapkan</button>
                    <button @click="resetFilter" class="rounded-lg border px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700">
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <!-- Info -->
                <div class="mt-3 flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                    <span>Menampilkan <strong class="text-gray-900 dark:text-white">{{ members.length }}</strong> anggota</span>
                    <span v-if="selectedIds.size > 0" class="text-blue-600 dark:text-blue-400 font-medium">
                        · {{ selectedIds.size }} dipilih
                    </span>
                    <button v-if="selectedIds.size > 0" @click="selectedIds = new Set()" class="text-red-500 hover:underline text-xs">Batalkan pilihan</button>
                </div>
            </div>

            <!-- Select All + Member Grid -->
            <div v-if="members.length > 0">
                <!-- Select all bar -->
                <div class="mb-4 flex items-center gap-3 rounded-lg border bg-gray-50 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700">
                    <button @click="toggleAll" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600">
                        <CheckSquare v-if="allSelected" class="h-4 w-4 text-blue-600" />
                        <Square v-else class="h-4 w-4" />
                        {{ allSelected ? 'Batalkan Semua' : 'Pilih Semua' }}
                    </button>
                    <span class="text-xs text-gray-500">|</span>
                    <span class="text-xs text-gray-500">Klik kartu untuk memilih/batal. Kosong = cetak semua.</span>
                </div>

                <!-- Cards grid -->
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div
                        v-for="member in members"
                        :key="member.id"
                        @click="toggleMember(member.id)"
                        :class="[
                            'cursor-pointer rounded-xl border-2 p-1 transition-all',
                            selectedIds.has(member.id)
                                ? 'border-blue-500 ring-2 ring-blue-300 dark:ring-blue-700'
                                : 'border-transparent hover:border-gray-300 dark:hover:border-gray-600'
                        ]"
                    >
                        <!-- Mini card preview -->
                        <div class="card-mini">
                            <div class="card-mini-body">
                                <div class="card-mini-photo">
                                    <img v-if="member.foto_url" :src="member.foto_url" class="h-full w-full object-cover" alt="foto" />
                                    <span v-else class="card-mini-initial">{{ member.user.name.charAt(0).toUpperCase() }}</span>
                                </div>
                                <div class="card-mini-info">
                                    <p class="card-mini-name">{{ member.user.name }}</p>
                                    <p class="card-mini-no">{{ member.no_anggota }}</p>
                                    <p v-if="member.kelas" class="card-mini-kelas">{{ member.kelas }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- checkbox indicator -->
                        <div class="mt-1 flex items-center justify-center gap-1 text-xs py-0.5" :class="selectedIds.has(member.id) ? 'text-blue-600' : 'text-gray-400'">
                            <CheckSquare v-if="selectedIds.has(member.id)" class="h-3.5 w-3.5" />
                            <Square v-else class="h-3.5 w-3.5" />
                            {{ selectedIds.has(member.id) ? 'Dipilih' : 'Klik untuk pilih' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty -->
            <div v-else class="flex flex-col items-center justify-center rounded-xl border bg-white py-16 dark:bg-gray-800 dark:border-gray-700">
                <Users class="h-12 w-12 text-gray-300 mb-3" />
                <p class="text-gray-500">Tidak ada anggota ditemukan</p>
                <button @click="resetFilter" class="mt-3 text-sm text-blue-600 hover:underline">Reset filter</button>
            </div>

        </div>

        <!-- ── PRINT AREA ── Only shows when printing ── -->
        <Teleport to="body">
            <div class="print-area">
            <div class="print-grid">
                <div v-for="member in membersToPrint" :key="'print-' + member.id" class="print-card">
                    <!-- Body -->
                    <div class="print-card-body">
                        <div class="print-photo">
                            <img v-if="member.foto_url" :src="member.foto_url" class="print-photo-img" alt="foto" />
                            <div v-else class="print-photo-placeholder">{{ member.user.name.charAt(0).toUpperCase() }}</div>
                        </div>
                        <div class="print-info">
                            <p class="print-name">{{ member.user.name }}</p>
                            <p class="print-no">{{ member.no_anggota }}</p>
                            <div class="print-pills">
                                <span v-if="member.kelas" class="print-pill">{{ member.kelas }}</span>
                                <span v-if="member.jenis_kelamin" class="print-pill">{{ member.jenis_kelamin === 'L' ? 'L' : 'P' }}</span>
                            </div>
                            <p class="print-date">Berlaku: {{ formatDate(member.created_at) }}</p>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="print-card-footer">
                        Status: AKTIF
                    </div>
                </div>
            </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<style scoped>
/* ── Preview Card (mini) ── */
.card-mini {
    border-radius: 10px;
    overflow: hidden;
    background: linear-gradient(135deg, #E8A020 0%, #C4781A 60%, #92400E 100%);
}
.card-mini-body { display: flex; gap: 8px; padding: 8px; align-items: center; }
.card-mini-photo {
    width: 36px; height: 42px; border-radius: 6px; overflow: hidden;
    border: 2px solid rgba(255,255,255,0.3); flex-shrink: 0;
    background: rgba(255,255,255,0.1);
    display: flex; align-items: center; justify-content: center;
}
.card-mini-initial { font-size: 14px; font-weight: 700; color: white; }
.card-mini-info { flex: 1; min-width: 0; }
.card-mini-name { font-size: 8px; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
.card-mini-no { font-size: 7px; color: rgba(255,255,255,0.65); font-family: monospace; margin-bottom: 3px; }
.card-mini-kelas { font-size: 7px; padding: 1px 6px; border-radius: 999px; background: rgba(255,255,255,0.2); color: white; display: inline-block; }

/* ── Screen: hide print area ── */
.print-area { display: none; }

/* ── Print styles ── */
@media print {
    /* Sembunyikan container utama Vue */
    #app { 
        display: none !important; 
    }

    /* Paksa tampilkan area yang di-teleport ke body */
    .print-area {
        display: block !important;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        background: white;
    }

    .print-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6mm;
        padding: 5mm;
    }

    .print-card {
        width: 85.6mm;
        border-radius: 4mm;
        overflow: hidden;
        background: linear-gradient(135deg, #E8A020 0%, #C4781A 60%, #92400E 100%);
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        page-break-inside: avoid;
    }

    .print-card-header {
        padding: 3mm 4mm;
        background: rgba(0,0,0,0.25);
        font-size: 6pt;
        font-weight: 700;
        letter-spacing: 0.06em;
        color: white;
        text-align: center;
    }

    .print-card-body {
        display: flex;
        gap: 4mm;
        padding: 4mm;
        align-items: center;
    }

    .print-photo {
        width: 18mm;
        height: 22mm;
        border-radius: 2mm;
        overflow: hidden;
        border: 0.5mm solid rgba(255,255,255,0.3);
        flex-shrink: 0;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
    }

    .print-photo-img { width: 100%; height: 100%; object-fit: cover; }

    .print-photo-placeholder {
        font-size: 16pt; font-weight: 700; color: white;
    }

    .print-info { flex: 1; }
    .print-name { font-size: 9pt; font-weight: 700; color: white; margin-bottom: 1mm; }
    .print-no { font-size: 6pt; color: rgba(255,255,255,0.7); font-family: monospace; margin-bottom: 2mm; }
    .print-pills { display: flex; gap: 1mm; flex-wrap: wrap; margin-bottom: 1.5mm; }
    .print-pill { font-size: 5pt; padding: 0.5mm 2mm; border-radius: 999px; background: rgba(255,255,255,0.2); color: white; font-weight: 600; }
    .print-date { font-size: 5pt; color: rgba(255,255,255,0.55); }

    .print-card-footer {
        padding: 2mm 4mm;
        background: rgba(0,0,0,0.25);
        font-size: 5pt;
        color: rgba(255,255,255,0.7);
        text-align: right;
    }
}


</style>
