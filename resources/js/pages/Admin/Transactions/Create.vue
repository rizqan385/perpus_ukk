<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeftRight, ArrowLeft, Users, BookOpen, Calendar } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed } from 'vue';

interface User {
    name: string;
    email: string;
}

interface Member {
    id: number;
    no_anggota: string;
    user: User;
}

interface Book {
    id: number;
    judul: string;
    pengarang: string;
    penerbit: string;
    stok: number;
}

const props = defineProps<{
    members: Member[];
    books: Book[];
}>();

const searchMember = ref('');
const searchBook = ref('');

const filteredMembers = computed(() => {
    if (!searchMember.value) return props.members;
    const term = searchMember.value.toLowerCase();
    return props.members.filter(m => 
        m.user.name.toLowerCase().includes(term) ||
        m.no_anggota.toLowerCase().includes(term)
    );
});

const filteredBooks = computed(() => {
    if (!searchBook.value) return props.books;
    const term = searchBook.value.toLowerCase();
    return props.books.filter(b => 
        b.judul.toLowerCase().includes(term) ||
        b.pengarang.toLowerCase().includes(term)
    );
});

const today = new Date().toISOString().split('T')[0];
const defaultReturn = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

const form = useForm({
    member_id: '',
    book_id: '',
    tanggal_pinjam: today,
    tanggal_kembali: defaultReturn,
});

const selectedMember = computed(() => {
    return props.members.find(m => m.id === Number(form.member_id));
});

const selectedBook = computed(() => {
    return props.books.find(b => b.id === Number(form.book_id));
});

const submit = () => {
    form.post('/admin/transactions');
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Transaksi', href: '/admin/transactions' },
    { title: 'Transaksi Baru', href: '/admin/transactions/create' },
];
</script>

<template>
    <Head title="Transaksi Baru" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="mx-auto w-full max-w-4xl">
                <!-- Header -->
                <div class="mb-6 flex items-center gap-4">
                    <Link
                        href="/admin/transactions"
                        class="rounded-lg border p-2 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700"
                    >
                        <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transaksi Peminjaman Baru</h1>
                        <p class="text-gray-600 dark:text-gray-400">Buat transaksi peminjaman buku baru</p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="grid gap-6 lg:grid-cols-2">
                    <!-- Select Member -->
                    <div class="rounded-xl border bg-white p-6 dark:bg-gray-800 dark:border-gray-700">
                        <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                            <Users class="h-5 w-5" />
                            Pilih Anggota
                        </h3>
                        <input
                            v-model="searchMember"
                            type="text"
                            placeholder="Cari anggota..."
                            class="mb-4 w-full rounded-lg border bg-white px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div class="max-h-64 space-y-2 overflow-y-auto">
                            <label
                                v-for="member in filteredMembers"
                                :key="member.id"
                                :class="[
                                    'flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors',
                                    form.member_id === String(member.id) 
                                        ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' 
                                        : 'border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50'
                                ]"
                            >
                                <input
                                    v-model="form.member_id"
                                    type="radio"
                                    :value="String(member.id)"
                                    class="sr-only"
                                />
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/50">
                                    <Users class="h-5 w-5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ member.user.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ member.no_anggota }}</p>
                                </div>
                            </label>
                            <div v-if="filteredMembers.length === 0" class="py-4 text-center text-gray-500">
                                Tidak ada anggota ditemukan
                            </div>
                        </div>
                        <p v-if="form.errors.member_id" class="mt-2 text-sm text-red-600">{{ form.errors.member_id }}</p>
                    </div>

                    <!-- Select Book -->
                    <div class="rounded-xl border bg-white p-6 dark:bg-gray-800 dark:border-gray-700">
                        <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                            <BookOpen class="h-5 w-5" />
                            Pilih Buku
                        </h3>
                        <input
                            v-model="searchBook"
                            type="text"
                            placeholder="Cari buku..."
                            class="mb-4 w-full rounded-lg border bg-white px-4 py-2 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                        <div class="max-h-64 space-y-2 overflow-y-auto">
                            <label
                                v-for="book in filteredBooks"
                                :key="book.id"
                                :class="[
                                    'flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors',
                                    form.book_id === String(book.id) 
                                        ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' 
                                        : 'border-gray-200 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50'
                                ]"
                            >
                                <input
                                    v-model="form.book_id"
                                    type="radio"
                                    :value="String(book.id)"
                                    class="sr-only"
                                />
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/50">
                                    <BookOpen class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ book.judul }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ book.pengarang }}</p>
                                </div>
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900/50 dark:text-green-400">
                                    Stok: {{ book.stok }}
                                </span>
                            </label>
                            <div v-if="filteredBooks.length === 0" class="py-4 text-center text-gray-500">
                                Tidak ada buku tersedia
                            </div>
                        </div>
                        <p v-if="form.errors.book_id" class="mt-2 text-sm text-red-600">{{ form.errors.book_id }}</p>
                    </div>

                    <!-- Date Selection -->
                    <div class="rounded-xl border bg-white p-6 lg:col-span-2 dark:bg-gray-800 dark:border-gray-700">
                        <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                            <Calendar class="h-5 w-5" />
                            Tanggal Peminjaman
                        </h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="tanggal_pinjam" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tanggal Pinjam *
                                </label>
                                <input
                                    id="tanggal_pinjam"
                                    v-model="form.tanggal_pinjam"
                                    type="date"
                                    required
                                    class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <p v-if="form.errors.tanggal_pinjam" class="mt-1 text-sm text-red-600">{{ form.errors.tanggal_pinjam }}</p>
                            </div>
                            <div>
                                <label for="tanggal_kembali" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Tanggal Kembali *
                                </label>
                                <input
                                    id="tanggal_kembali"
                                    v-model="form.tanggal_kembali"
                                    type="date"
                                    required
                                    class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                />
                                <p v-if="form.errors.tanggal_kembali" class="mt-1 text-sm text-red-600">{{ form.errors.tanggal_kembali }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div v-if="selectedMember && selectedBook" class="rounded-xl border bg-gradient-to-r from-amber-50 to-orange-50 p-6 lg:col-span-2 dark:from-amber-900/20 dark:to-orange-900/20 dark:border-amber-700">
                        <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Ringkasan Transaksi</h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Anggota</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ selectedMember.user.name }}</p>
                                <p class="text-sm text-gray-500">{{ selectedMember.no_anggota }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Buku</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ selectedBook.judul }}</p>
                                <p class="text-sm text-gray-500">{{ selectedBook.pengarang }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3 lg:col-span-2">
                        <Link
                            href="/admin/transactions"
                            class="flex-1 rounded-lg border px-4 py-2.5 text-center font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing || !form.member_id || !form.book_id"
                            class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 font-medium text-white transition-colors hover:bg-amber-600 disabled:opacity-50"
                        >
                            <ArrowLeftRight class="h-5 w-5" />
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Buat Transaksi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
