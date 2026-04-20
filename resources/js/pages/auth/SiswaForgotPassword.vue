<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { BookOpen, Mail, ArrowLeft, Send } from 'lucide-vue-next';

defineProps<{ status?: string }>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post('/forgot-password');
};
</script>

<template>
    <Head title="Lupa Password — E-Perpustakaan" />

    <div class="flex min-h-screen items-center justify-center p-6" style="background: #FFF8F0; font-family: Georgia, serif;">
        <div class="w-full max-w-md">
            <!-- Logo & Header -->
            <div class="mb-10 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl shadow-xl" 
                     style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                    <BookOpen class="h-8 w-8 text-white" />
                </div>
                <h2 class="text-3xl font-bold" style="color: #5C3D1E;">Lupa Password?</h2>
                <p class="mt-2 text-sm" style="color: #9A7050;">
                    Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password.
                </p>
            </div>

            <!-- Status Message -->
            <div v-if="status" class="mb-6 rounded-xl p-4 text-sm shadow-sm" style="background: #D1FAE5; color: #166534; border: 1px solid #A7F3D0;">
                {{ status }}
            </div>

            <div class="rounded-3xl border-2 bg-white p-8 shadow-sm" style="border-color: #FFE0B2;">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #5C3D1E;" for="email">Alamat Email</label>
                        <div class="relative">
                            <Mail class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-orange-400" />
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                placeholder="email@sekolah.ac.id"
                                class="w-full rounded-2xl border-2 bg-white py-3.5 pl-12 pr-4 text-sm outline-none transition-all focus:border-orange-400 focus:shadow-md"
                                :style="(form.errors.email ? 'border-color:#EF4444' : 'border-color:#FFE0B2') + ';color:#1a1a1a;'"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="group flex w-full items-center justify-center gap-2 rounded-2xl py-4 text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50"
                        style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                    >
                        <span v-if="form.processing">Mengirim...</span>
                        <template v-else>
                            <Send class="h-4 w-4 transition-transform group-hover:translate-x-1" />
                            Kirim Link Reset
                        </template>
                    </button>
                </form>

                <div class="mt-8 border-t pt-6 text-center" style="border-color: #FFF3E0;">
                    <a href="/siswa/masuk" class="inline-flex items-center gap-2 text-sm font-semibold transition-colors hover:text-orange-600" style="color: #C4781A;">
                        <ArrowLeft class="h-4 w-4" />
                        Kembali ke halaman Masuk
                    </a>
                </div>
            </div>

            <!-- Footer Decorative -->
            <p class="mt-10 text-center text-xs opacity-40" style="color: #5C3D1E;">
                &copy; 2026 E-Perpustakaan Sekolah. Hak Cipta Dilindungi.
            </p>
        </div>
    </div>
</template>
