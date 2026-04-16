<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { UserPlus, AlertCircle, ArrowLeft } from 'lucide-vue-next';
import SiswaLayout from '@/layouts/SiswaLayout.vue';
</script>

<template>
    <Head title="Perlu Keanggotaan" />

    <SiswaLayout>
        <div class="flex flex-1 items-center justify-center p-6 py-20">
            <div class="max-w-md text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-orange-500/20">
                    <AlertCircle class="h-10 w-10 text-white" />
                </div>
                <h1 class="mb-3 text-3xl font-bold" style="color: #5C3D1E; font-family: Georgia, serif;">
                    Pendaftaran Diperlukan
                </h1>
                <p class="mb-8 text-lg" style="color: #7A5230;">
                    Untuk menggunakan fitur ini, Anda harus terdaftar sebagai anggota perpustakaan terlebih dahulu.
                </p>
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <Link
                        href="/siswa/register"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-8 py-3.5 font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95"
                        style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                    >
                        <UserPlus class="h-5 w-5" />
                        Daftar Sebagai Anggota
                    </Link>
                    <Link
                        href="/siswa"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border-2 px-8 py-3.5 font-bold transition-colors hover:bg-orange-50"
                        style="border-color: #E8A020; color: #5C3D1E;"
                    >
                        <ArrowLeft class="h-5 w-5" />
                        Kembali
                    </Link>
                </div>
            </div>
        </div>
    </SiswaLayout>
</template>
