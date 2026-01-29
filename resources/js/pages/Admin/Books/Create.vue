<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
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

const submit = () => {
    form.post('/admin/books', {
        forceFormData: true,
    });
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.cover_image = target.files[0];
    }
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
                <Link
                    href="/admin/books"
                    class="rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                </Link>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tambah Buku Baru</h1>
                    <p class="text-gray-600 dark:text-gray-400">Isi data buku yang akan ditambahkan</p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="max-w-2xl">
                <div class="rounded-xl border bg-white p-6 dark:bg-gray-800">
                    <div class="space-y-4">
                        <!-- Judul -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Judul Buku <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.judul"
                                type="text"
                                required
                                class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Masukkan judul buku"
                            />
                            <p v-if="form.errors.judul" class="mt-1 text-sm text-red-500">{{ form.errors.judul }}</p>
                        </div>

                        <!-- Pengarang -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Pengarang <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.pengarang"
                                type="text"
                                required
                                class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Nama pengarang"
                            />
                            <p v-if="form.errors.pengarang" class="mt-1 text-sm text-red-500">{{ form.errors.pengarang }}</p>
                        </div>

                        <!-- Penerbit -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Penerbit <span class="text-red-500">*</span>
                            </label>
                            <input
                                v-model="form.penerbit"
                                type="text"
                                required
                                class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Nama penerbit"
                            />
                            <p v-if="form.errors.penerbit" class="mt-1 text-sm text-red-500">{{ form.errors.penerbit }}</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Tahun Terbit -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tahun Terbit <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.tahun_terbit"
                                    type="number"
                                    required
                                    min="1900"
                                    :max="new Date().getFullYear()"
                                    class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <p v-if="form.errors.tahun_terbit" class="mt-1 text-sm text-red-500">{{ form.errors.tahun_terbit }}</p>
                            </div>

                            <!-- Stok -->
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Stok <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.stok"
                                    type="number"
                                    required
                                    min="0"
                                    class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <p v-if="form.errors.stok" class="mt-1 text-sm text-red-500">{{ form.errors.stok }}</p>
                            </div>
                        </div>

                        <!-- ISBN -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                ISBN
                            </label>
                            <input
                                v-model="form.isbn"
                                type="text"
                                class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Nomor ISBN (opsional)"
                            />
                            <p v-if="form.errors.isbn" class="mt-1 text-sm text-red-500">{{ form.errors.isbn }}</p>
                        </div>

                        <!-- Cover Image -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cover Buku
                            </label>
                            <input
                                type="file"
                                accept="image/*"
                                @change="handleFileChange"
                                class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            />
                            <p v-if="form.errors.cover_image" class="mt-1 text-sm text-red-500">{{ form.errors.cover_image }}</p>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Deskripsi
                            </label>
                            <textarea
                                v-model="form.deskripsi"
                                rows="4"
                                class="w-full rounded-lg border px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="Deskripsi singkat tentang buku (opsional)"
                            ></textarea>
                            <p v-if="form.errors.deskripsi" class="mt-1 text-sm text-red-500">{{ form.errors.deskripsi }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Buku' }}
                        </button>
                        <Link
                            href="/admin/books"
                            class="rounded-lg border px-6 py-2 text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Batal
                        </Link>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
