<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, ArrowLeft } from 'lucide-vue-next';
import SiswaLayout from '@/layouts/SiswaLayout.vue';

interface Member {
    no_anggota: string;
    kelas: string | null;
    status: string;
}

defineProps<{
    member: Member;
}>();
</script>

<template>
    <Head title="Sudah Terdaftar" />

    <SiswaLayout>
        <div class="flex flex-1 items-center justify-center p-6 py-20">
            <div class="max-w-md text-center">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-green-400 to-emerald-600 shadow-lg shadow-emerald-500/20">
                    <CheckCircle class="h-10 w-10 text-white" />
                </div>
                <h1 class="mb-3 text-3xl font-bold" style="color: #5C3D1E; font-family: Georgia, serif;">
                    Anda Sudah Terdaftar!
                </h1>
                <p class="mb-8 text-lg" style="color: #7A5230;">
                    Anda sudah terdaftar sebagai anggota perpustakaan dengan nomor anggota:
                </p>
                <div class="mb-10 rounded-2xl p-8 text-white shadow-xl" style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-70">No. Anggota Perpustakaan</p>
                    <p class="mt-2 text-4xl font-black">{{ member.no_anggota }}</p>
                    <div v-if="member.kelas" class="mt-4 inline-block rounded-lg bg-white/20 px-3 py-1 font-semibold">
                        Kelas: {{ member.kelas }}
                    </div>
                </div>
                <Link
                    href="/siswa"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border-2 px-8 py-3.5 font-bold transition-all hover:bg-orange-50 shadow-sm"
                    style="border-color: #E8A020; color: #5C3D1E;"
                >
                    <ArrowLeft class="h-5 w-5" />
                    Kembali ke Dashboard
                </Link>
            </div>
        </div>
    </SiswaLayout>
</template>
