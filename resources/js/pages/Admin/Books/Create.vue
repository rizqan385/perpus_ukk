<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Upload, X, ImageIcon } from 'lucide-vue-next';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const form = useForm({
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun_terbit: new Date().getFullYear(),
    isbn: '',
    stok: 0,
    cover_image: null as File | null,
    deskripsi: '',
});

const coverPreview = ref<string | null>(null);
const isDragging = ref(false);

const handleFile = (file: File) => {
    if (!file.type.startsWith('image/')) return;
    form.cover_image = file;
    coverPreview.value = URL.createObjectURL(file);
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files?.[0]) handleFile(target.files[0]);
};

const onDrop = (event: DragEvent) => {
    isDragging.value = false;
    const file = event.dataTransfer?.files?.[0];
    if (file) handleFile(file);
};

const clearCover = () => {
    form.cover_image = null;
    coverPreview.value = null;
};

const submit = () => {
    form.post('/admin/books', { forceFormData: true });
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Buku', href: '/admin/books' },
    { title: 'Tambah Buku', href: '/admin/books/create' },
];
</script>

<template>
    <Head title="Tambah Buku" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/admin/books" class="rounded-xl p-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                </Link>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tambah Buku Baru</h1>
                    <p class="text-gray-500 dark:text-gray-400">Isi data buku yang akan ditambahkan</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-3">

                <!-- Cover Upload -->
                <div class="lg:col-span-1">
                    <div class="sticky top-6">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Cover Buku</label>

                        <!-- Preview -->
                        <div v-if="coverPreview" class="relative mb-3 overflow-hidden rounded-2xl shadow-lg">
                            <img :src="coverPreview" alt="Preview" class="h-72 w-full object-cover" />
                            <button
                                type="button"
                                @click="clearCover"
                                class="absolute right-2 top-2 rounded-full bg-red-500 p-1 text-white shadow hover:bg-red-600"
                            >
                                <X class="h-4 w-4" />
                            </button>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent p-3">
                                <p class="truncate text-xs text-white">{{ form.cover_image?.name }}</p>
                            </div>
                        </div>

                        <!-- Drop zone -->
                        <label
                            v-else
                            @dragover.prevent="isDragging = true"
                            @dragleave="isDragging = false"
                            @drop.prevent="onDrop"
                            :class="[
                                'flex h-64 w-full cursor-pointer flex-col items-center justify-center gap-3 rounded-2xl border-2 border-dashed transition-all',
                                isDragging
                                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                    : 'border-gray-300 hover:border-blue-400 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700/50'
                            ]"
                        >
                            <div :class="['rounded-2xl p-4', isDragging ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-gray-100 dark:bg-gray-700']">
                                <ImageIcon :class="['h-8 w-8', isDragging ? 'text-blue-500' : 'text-gray-400']" />
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ isDragging ? 'Lepas untuk upload' : 'Drag & drop cover' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400">atau klik untuk pilih file</p>
                                <p class="mt-0.5 text-xs text-gray-400">PNG, JPG, WEBP · max 2MB</p>
                            </div>
                            <input type="file" accept="image/*" class="hidden" @change="handleFileChange" />
                        </label>

                        <p v-if="form.errors.cover_image" class="mt-1 text-sm text-red-500">{{ form.errors.cover_image }}</p>
                    </div>
                </div>

                <!-- Book Fields -->
                <div class="lg:col-span-2">
                    <div class="rounded-2xl border bg-white p-6 shadow-sm dark:bg-gray-800 space-y-4">
                        <!-- Judul -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Judul Buku <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.judul" type="text" required placeholder="Masukkan judul buku"
                                class="w-full rounded-xl border px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            <p v-if="form.errors.judul" class="mt-1 text-sm text-red-500">{{ form.errors.judul }}</p>
                        </div>

                        <!-- Pengarang -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Pengarang <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.pengarang" type="text" required placeholder="Nama pengarang"
                                class="w-full rounded-xl border px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            <p v-if="form.errors.pengarang" class="mt-1 text-sm text-red-500">{{ form.errors.pengarang }}</p>
                        </div>

                        <!-- Penerbit -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Penerbit <span class="text-red-500">*</span>
                            </label>
                            <input v-model="form.penerbit" type="text" required placeholder="Nama penerbit"
                                class="w-full rounded-xl border px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            <p v-if="form.errors.penerbit" class="mt-1 text-sm text-red-500">{{ form.errors.penerbit }}</p>
                        </div>

                        <!-- Tahun & Stok -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tahun Terbit <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.tahun_terbit" type="number" required min="1900" :max="new Date().getFullYear()"
                                    class="w-full rounded-xl border px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                <p v-if="form.errors.tahun_terbit" class="mt-1 text-sm text-red-500">{{ form.errors.tahun_terbit }}</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stok <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.stok" type="number" required min="0"
                                    class="w-full rounded-xl border px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                <p v-if="form.errors.stok" class="mt-1 text-sm text-red-500">{{ form.errors.stok }}</p>
                            </div>
                        </div>

                        <!-- ISBN -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">ISBN</label>
                            <input v-model="form.isbn" type="text" placeholder="Nomor ISBN (opsional)"
                                class="w-full rounded-xl border px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                            <p v-if="form.errors.isbn" class="mt-1 text-sm text-red-500">{{ form.errors.isbn }}</p>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea v-model="form.deskripsi" rows="4" placeholder="Deskripsi singkat buku (opsional)"
                                class="w-full rounded-xl border px-4 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            <p v-if="form.errors.deskripsi" class="mt-1 text-sm text-red-500">{{ form.errors.deskripsi }}</p>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="submit" :disabled="form.processing"
                                class="flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
                                <Upload class="h-4 w-4" />
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Buku' }}
                            </button>
                            <Link href="/admin/books"
                                class="rounded-xl border px-6 py-2.5 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                                Batal
                            </Link>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
