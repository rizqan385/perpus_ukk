<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Settings, DollarSign, Clock, BookOpen, Save } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    settings: Record<string, string>;
}>();

const form = useForm({
    fine_per_day:      parseInt(props.settings.fine_per_day ?? '1000'),
    grace_period_days: parseInt(props.settings.grace_period_days ?? '0'),
    max_borrow_days:   parseInt(props.settings.max_borrow_days ?? '7'),
});

const submit = () => {
    form.post('/admin/settings');
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Pengaturan', href: '/admin/settings' },
];

const formatRupiah = (n: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);

// Live preview
const previewDays = [1, 3, 7, 14];
</script>

<template>
    <Head title="Pengaturan Sistem" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-3">
                <div class="rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 p-3 shadow-lg">
                    <Settings class="h-6 w-6 text-white" />
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pengaturan Sistem</h1>
                    <p class="text-gray-500 dark:text-gray-400">Konfigurasi aturan peminjaman dan denda perpustakaan</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">

                <!-- === FINE SETTINGS === -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Denda card -->
                    <div class="rounded-2xl border bg-white shadow-sm dark:bg-gray-800">
                        <div class="flex items-center gap-3 border-b px-6 py-4 dark:border-gray-700">
                            <div class="rounded-lg bg-red-100 p-2 dark:bg-red-900/40">
                                <DollarSign class="h-5 w-5 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <h2 class="font-semibold text-gray-900 dark:text-white">Pengaturan Denda</h2>
                                <p class="text-sm text-gray-500">Atur besaran dan kebijakan denda keterlambatan</p>
                            </div>
                        </div>
                        <div class="space-y-5 p-6">
                            <!-- Denda per hari -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Denda per Hari <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-500">Rp</span>
                                    <input
                                        v-model.number="form.fine_per_day"
                                        type="number"
                                        min="0"
                                        step="500"
                                        class="w-full rounded-xl border py-2.5 pl-10 pr-4 text-lg font-semibold focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                </div>
                                <p v-if="form.errors.fine_per_day" class="mt-1 text-sm text-red-500">{{ form.errors.fine_per_day }}</p>
                                <p class="mt-1.5 text-xs text-gray-400">Jumlah rupiah yang dikenakan setiap hari keterlambatan</p>
                            </div>

                            <!-- Grace period -->
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Grace Period (hari bebas denda)
                                </label>
                                <input
                                    v-model.number="form.grace_period_days"
                                    type="number"
                                    min="0"
                                    max="30"
                                    class="w-full rounded-xl border px-4 py-2.5 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <p v-if="form.errors.grace_period_days" class="mt-1 text-sm text-red-500">{{ form.errors.grace_period_days }}</p>
                                <p class="mt-1.5 text-xs text-gray-400">
                                    Misal: 2 berarti denda mulai dihitung setelah 2 hari dari tenggat waktu
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Peminjaman card -->
                    <div class="rounded-2xl border bg-white shadow-sm dark:bg-gray-800">
                        <div class="flex items-center gap-3 border-b px-6 py-4 dark:border-gray-700">
                            <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900/40">
                                <BookOpen class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h2 class="font-semibold text-gray-900 dark:text-white">Pengaturan Peminjaman</h2>
                                <p class="text-sm text-gray-500">Atur batas waktu peminjaman buku</p>
                            </div>
                        </div>
                        <div class="p-6">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Maksimal Hari Peminjaman <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input
                                        v-model.number="form.max_borrow_days"
                                        type="number"
                                        min="1"
                                        max="365"
                                        class="w-full rounded-xl border px-4 py-2.5 focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-gray-400">hari</span>
                                </div>
                                <p v-if="form.errors.max_borrow_days" class="mt-1 text-sm text-red-500">{{ form.errors.max_borrow_days }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Save button -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 px-6 py-3 font-semibold text-white shadow-lg transition-all hover:from-violet-700 hover:to-purple-700 hover:shadow-xl disabled:opacity-60"
                    >
                        <Save class="h-5 w-5" />
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                    </button>
                </div>

                <!-- === PREVIEW PANEL === -->
                <div class="space-y-4">
                    <div class="rounded-2xl border bg-gradient-to-br from-violet-50 to-purple-50 p-6 dark:from-violet-900/20 dark:to-purple-900/20 dark:border-violet-800">
                        <div class="mb-4 flex items-center gap-2">
                            <Clock class="h-5 w-5 text-violet-600" />
                            <h3 class="font-semibold text-violet-900 dark:text-violet-200">Preview Denda</h3>
                        </div>
                        <p class="mb-4 text-xs text-violet-700 dark:text-violet-300">
                            Dengan pengaturan saat ini:
                        </p>

                        <div class="space-y-2">
                            <div
                                v-for="day in previewDays"
                                :key="day"
                                class="flex items-center justify-between rounded-lg bg-white/70 px-3 py-2 dark:bg-gray-800/50"
                            >
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    Terlambat {{ day }} hari
                                </span>
                                <span class="text-sm font-bold text-red-600 dark:text-red-400">
                                    {{
                                        day <= form.grace_period_days
                                            ? 'Gratis'
                                            : formatRupiah((day - form.grace_period_days) * form.fine_per_day)
                                    }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 rounded-lg bg-violet-100 px-3 py-2 dark:bg-violet-900/30">
                            <p class="text-xs text-violet-700 dark:text-violet-300">
                                <strong>Formula:</strong><br/>
                                (Hari terlambat − {{ form.grace_period_days }} grace) × {{ formatRupiah(form.fine_per_day) }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-800">
                        <h3 class="mb-3 font-semibold text-gray-900 dark:text-white">Durasi Pinjam</h3>
                        <div class="rounded-lg bg-blue-50 px-4 py-3 text-center dark:bg-blue-900/20">
                            <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ form.max_borrow_days }}</span>
                            <span class="ml-1 text-gray-500 dark:text-gray-400">hari</span>
                        </div>
                        <p class="mt-2 text-center text-xs text-gray-400">
                            Tenggat: tanggal pinjam + {{ form.max_borrow_days }} hari
                        </p>
                    </div>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
