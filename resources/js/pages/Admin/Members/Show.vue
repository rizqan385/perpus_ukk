<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Users, Mail, Phone, MapPin, GraduationCap, BookOpen, Calendar, Clock, CreditCard } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Book {
    id: number;
    judul: string;
    pengarang: string;
}

interface Borrowing {
    id: number;
    tanggal_pinjam: string;
    tanggal_kembali: string;
    tanggal_dikembalikan: string | null;
    status: string;
    denda: number;
    book: Book;
}

interface Member {
    id: number;
    no_anggota: string;
    kelas: string | null;
    alamat: string | null;
    telepon: string | null;
    tanggal_lahir: string | null;
    jenis_kelamin: string | null;
    foto_url: string | null;
    status: string;
    created_at: string;
    user: User;
    borrowings: Borrowing[];
}

const props = defineProps<{
    member: Member;
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Anggota', href: '/admin/members' },
    { title: 'Detail Anggota', href: `/admin/members/${props.member.id}` },
];
</script>

<template>
    <Head title="Detail Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link
                    href="/admin/members"
                    class="rounded-lg border p-2 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                </Link>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Anggota</h1>
                </div>
                <Link
                    :href="`/admin/members/${member.id}/edit`"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-white transition-colors hover:bg-emerald-700"
                >
                    Edit Anggota
                </Link>
                <Link
                    :href="`/admin/members/${member.id}/card`"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700"
                >
                    <CreditCard class="h-4 w-4" />
                    Kartu Anggota
                </Link>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Member Info -->
                <div class="lg:col-span-1">
                    <div class="rounded-xl border bg-white p-6 dark:bg-gray-800 dark:border-gray-700">
                        <div class="mb-6 flex flex-col items-center text-center">
                            <div class="mb-4 h-20 w-20 overflow-hidden rounded-full border-4 border-emerald-200">
                                <img v-if="member.foto_url" :src="member.foto_url" class="h-full w-full object-cover" alt="Foto" />
                                <div v-else class="flex h-full w-full items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-teal-600">
                                    <Users class="h-10 w-10 text-white" />
                                </div>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ member.user.name }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">{{ member.no_anggota }}</p>
                            <span
                                :class="[
                                    'mt-2 rounded-full px-3 py-1 text-sm font-medium',
                                    member.status === 'aktif' 
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400'
                                        : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                ]"
                            >
                                {{ member.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center gap-3 text-sm">
                                <Mail class="h-4 w-4 text-gray-400" />
                                <span class="text-gray-600 dark:text-gray-400">{{ member.user.email }}</span>
                            </div>
                            <div v-if="member.kelas" class="flex items-center gap-3 text-sm">
                                <GraduationCap class="h-4 w-4 text-gray-400" />
                                <span class="text-gray-600 dark:text-gray-400">{{ member.kelas }}</span>
                            </div>
                            <div v-if="member.telepon" class="flex items-center gap-3 text-sm">
                                <Phone class="h-4 w-4 text-gray-400" />
                                <span class="text-gray-600 dark:text-gray-400">{{ member.telepon }}</span>
                            </div>
                            <div v-if="member.alamat" class="flex items-start gap-3 text-sm">
                                <MapPin class="h-4 w-4 text-gray-400 mt-0.5" />
                                <span class="text-gray-600 dark:text-gray-400">{{ member.alamat }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm">
                                <Calendar class="h-4 w-4 text-gray-400" />
                                <span class="text-gray-600 dark:text-gray-400">Bergabung: {{ formatDate(member.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Borrowing History -->
                <div class="lg:col-span-2">
                    <div class="rounded-xl border bg-white p-6 dark:bg-gray-800 dark:border-gray-700">
                        <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-gray-900 dark:text-white">
                            <Clock class="h-5 w-5" />
                            Riwayat Peminjaman
                        </h3>

                        <div v-if="member.borrowings.length > 0" class="space-y-3">
                            <div
                                v-for="borrowing in member.borrowings"
                                :key="borrowing.id"
                                class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="rounded-lg bg-blue-100 p-2 dark:bg-blue-900/50">
                                        <BookOpen class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ borrowing.book.judul }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ borrowing.book.pengarang }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ formatDate(borrowing.tanggal_pinjam) }} - {{ formatDate(borrowing.tanggal_kembali) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span
                                        :class="[
                                            'rounded-full px-2 py-1 text-xs font-medium',
                                            borrowing.status === 'dipinjam' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' :
                                            borrowing.status === 'dikembalikan' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' :
                                            'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400'
                                        ]"
                                    >
                                        {{ borrowing.status }}
                                    </span>
                                    <p v-if="borrowing.denda > 0" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                        {{ formatCurrency(borrowing.denda) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="py-8 text-center text-gray-500 dark:text-gray-400">
                            Belum ada riwayat peminjaman
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
