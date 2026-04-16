<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { BookOpen, Users, ArrowLeftRight, AlertTriangle } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Chart, registerables } from 'chart.js';
import { onMounted, ref } from 'vue';

Chart.register(...registerables);

interface Stats {
    total_books: number;
    total_members: number;
    active_borrowings: number;
    overdue_borrowings: number;
    total_borrowings: number;
}

interface MemberByClass {
    kelas: string;
    total: number;
}

const props = defineProps<{
    stats: Stats;
    membersByClass: MemberByClass[];
}>();

// ── Chart 1: Overview (Doughnut) ────────────────────────────────────────────
const overviewCanvas = ref<HTMLCanvasElement | null>(null);

// ── Chart 2: Anggota per Kelas (Bar) ────────────────────────────────────────
const classCanvas = ref<HTMLCanvasElement | null>(null);

const CLASS_COLORS = [
    '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
    '#06b6d4', '#f97316', '#ec4899', '#14b8a6', '#84cc16',
];

onMounted(() => {
    // Doughnut: Buku vs Anggota vs Total Transaksi
    if (overviewCanvas.value) {
        new Chart(overviewCanvas.value, {
            type: 'doughnut',
            data: {
                labels: ['Total Buku', 'Total Anggota', 'Total Transaksi'],
                datasets: [{
                    data: [
                        props.stats.total_books,
                        props.stats.total_members,
                        props.stats.total_borrowings,
                    ],
                    backgroundColor: ['#3b82f6cc', '#10b981cc', '#f59e0bcc'],
                    borderColor:     ['#3b82f6',   '#10b981',   '#f59e0b'],
                    borderWidth: 2,
                    hoverOffset: 12,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: { size: 13 },
                            color: '#6b7280',
                            usePointStyle: true,
                            pointStyleWidth: 10,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.label}: ${ctx.parsed}`,
                        },
                    },
                },
            },
        });
    }

    // Bar: Anggota per Kelas
    if (classCanvas.value && props.membersByClass.length > 0) {
        new Chart(classCanvas.value, {
            type: 'bar',
            data: {
                labels: props.membersByClass.map(m => m.kelas),
                datasets: [{
                    label: 'Jumlah Anggota',
                    data: props.membersByClass.map(m => m.total),
                    backgroundColor: props.membersByClass.map((_, i) => CLASS_COLORS[i % CLASS_COLORS.length] + 'cc'),
                    borderColor:     props.membersByClass.map((_, i) => CLASS_COLORS[i % CLASS_COLORS.length]),
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.parsed.y} anggota`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 12 }, color: '#6b7280' },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, color: '#6b7280' },
                        grid: { color: '#f3f4f6' },
                    },
                },
            },
        });
    }
});

const breadcrumbs = [
    { title: 'Admin Dashboard', href: '/admin' },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6 lg:p-8 bg-gray-50/50 dark:bg-gray-950">

            <!-- ══ HERO WELCOME ══ -->
            <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <div class="relative z-10">
                    <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-3xl">
                        Selamat Datang, Admin! 👋
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Pantau aktivitas perpustakaan hari ini. Semua sistem berjalan optimal.
                    </p>
                </div>
                <!-- Dekorasi dipudarkan agar cocok dengan background putih -->
                <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-blue-50 dark:bg-blue-900/20 blur-3xl"></div>
                <div class="absolute -bottom-10 right-20 h-40 w-40 rounded-full bg-amber-50 dark:bg-amber-900/20 blur-3xl"></div>
            </div>

            <!-- ══ STATS CARDS ══ -->
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Books -->
                <div class="group relative overflow-hidden rounded-3xl bg-white p-6 shadow-sm transition-all hover:shadow-md dark:bg-gray-800 border-b-4 border-blue-500">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Arsip Buku</p>
                            <h3 class="mt-1 text-4xl font-black text-gray-900 dark:text-white line-clamp-1">{{ stats.total_books }}</h3>
                            <p class="mt-2 text-xs font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> Koleksi Aktif
                            </p>
                        </div>
                        <div class="rounded-2xl bg-blue-50 p-3 text-blue-600 transition-transform group-hover:scale-110 dark:bg-blue-900/40">
                            <BookOpen class="h-8 w-8" />
                        </div>
                    </div>
                </div>

                <!-- Members -->
                <div class="group relative overflow-hidden rounded-3xl bg-white p-6 shadow-sm transition-all hover:shadow-md dark:bg-gray-800 border-b-4 border-emerald-500">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Anggota Aktif</p>
                            <h3 class="mt-1 text-4xl font-black text-gray-900 dark:text-white line-clamp-1">{{ stats.total_members }}</h3>
                            <p class="mt-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Member Terverifikasi
                            </p>
                        </div>
                        <div class="rounded-2xl bg-emerald-50 p-3 text-emerald-600 transition-transform group-hover:scale-110 dark:bg-emerald-900/40">
                            <Users class="h-8 w-8" />
                        </div>
                    </div>
                </div>

                <!-- Transactions -->
                <div class="group relative overflow-hidden rounded-3xl bg-white p-6 shadow-sm transition-all hover:shadow-md dark:bg-gray-800 border-b-4 border-amber-500">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transaksi</p>
                            <h3 class="mt-1 text-4xl font-black text-gray-900 dark:text-white line-clamp-1">{{ stats.total_borrowings }}</h3>
                            <p class="mt-2 text-xs font-semibold text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Riwayat Hubungan
                            </p>
                        </div>
                        <div class="rounded-2xl bg-amber-50 p-3 text-amber-600 transition-transform group-hover:scale-110 dark:bg-amber-900/40">
                            <ArrowLeftRight class="h-8 w-8" />
                        </div>
                    </div>
                </div>

                <!-- Overdue -->
                <div class="group relative overflow-hidden rounded-3xl bg-white p-6 shadow-sm transition-all hover:shadow-md dark:bg-gray-800 border-b-4 border-red-500">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Buku Terlambat</p>
                            <h3 class="mt-1 text-4xl font-black text-gray-900 dark:text-white line-clamp-1">{{ stats.overdue_borrowings }}</h3>
                            <p class="mt-2 text-xs font-semibold text-red-600 dark:text-red-400 flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Perlu Tindakan
                            </p>
                        </div>
                        <div class="rounded-2xl bg-red-50 p-3 text-red-600 transition-transform group-hover:scale-110 dark:bg-red-900/40">
                            <AlertTriangle class="h-8 w-8" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid gap-6 lg:grid-cols-2">

                <!-- Chart 1: Overview Doughnut -->
                <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="mb-1 flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Ringkasan Data Perpustakaan</h2>
                    </div>
                    <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">Perbandingan jumlah buku, anggota, dan transaksi</p>

                    <!-- Doughnut center labels -->
                    <div class="relative h-72">
                        <canvas ref="overviewCanvas"></canvas>
                        <!-- Center text -->
                        <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                {{ stats.total_books + stats.total_members + stats.total_borrowings }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total Data</p>
                        </div>
                    </div>

                    <!-- Info list -->
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span> Total Buku
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ stats.total_books }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Total Anggota
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ stats.total_members }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span> Total Transaksi
                            </span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ stats.total_borrowings }}</span>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Anggota per Kelas Bar -->
                <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-gray-800">
                    <div class="mb-1 flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Anggota per Kelas</h2>
                    </div>
                    <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">Distribusi {{ stats.total_members }} anggota berdasarkan kelas</p>

                    <!-- Empty state -->
                    <div v-if="membersByClass.length === 0" class="flex h-72 flex-col items-center justify-center text-gray-400">
                        <Users class="mb-2 h-10 w-10 opacity-30" />
                        <p class="text-sm">Belum ada data anggota</p>
                    </div>

                    <div v-else class="relative h-72">
                        <canvas ref="classCanvas"></canvas>
                    </div>

                    <!-- Legend badges -->
                    <div v-if="membersByClass.length > 0" class="mt-4 flex flex-wrap gap-2">
                        <span
                            v-for="(item, i) in membersByClass"
                            :key="item.kelas"
                            class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium text-white"
                            :style="{ backgroundColor: ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#f97316','#ec4899','#14b8a6','#84cc16'][i % 10] }"
                        >
                            {{ item.kelas }}: {{ item.total }}
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
