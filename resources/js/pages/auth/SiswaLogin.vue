<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { BookOpen, Mail, Lock, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{ status?: string; canResetPassword: boolean }>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post('/siswa/masuk', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk — E-Perpustakaan" />

    <div class="flex min-h-screen" style="font-family: Georgia, serif;">
        <!-- ── LEFT PANEL: Orange warm illustration ── -->
        <div class="relative hidden flex-col justify-between overflow-hidden p-12 lg:flex lg:w-1/2" style="background: linear-gradient(160deg, #5C3D1E 0%, #3D2810 60%, #1E1408 100%);">
            <!-- Decorative circles -->
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -right-20 top-20 h-96 w-96 rounded-full opacity-10" style="background: #E8A020;"></div>
                <div class="absolute -left-10 bottom-20 h-64 w-64 rounded-full opacity-10" style="background: #C4781A;"></div>
                <!-- Leaves decoration -->
                <svg class="absolute right-10 top-1/3 opacity-20" width="200" height="140" viewBox="0 0 200 140">
                    <ellipse cx="100" cy="70" rx="90" ry="58" fill="#E8A020" transform="rotate(-25 100 70)"/>
                    <ellipse cx="60" cy="100" rx="65" ry="40" fill="#C4781A" transform="rotate(15 60 100)"/>
                    <ellipse cx="150" cy="30" rx="50" ry="30" fill="#D4881A" transform="rotate(-40 150 30)"/>
                </svg>
                <!-- Book illustration -->
                <div class="absolute bottom-32 right-12 opacity-20" style="width:140px; height:180px; background: linear-gradient(135deg,#E8A020,#8B5E15); border-radius: 8px 16px 16px 8px; transform: rotate(8deg);"></div>
                <div class="absolute bottom-40 right-20 opacity-30" style="width:120px; height:160px; background: linear-gradient(135deg,#C4781A,#5C3D1E); border-radius: 8px 16px 16px 8px; transform: rotate(-4deg);"></div>
            </div>

            <!-- Logo top -->
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl" style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                    <BookOpen class="h-6 w-6 text-white" />
                </div>
                <span class="text-2xl font-bold text-white">E-Perpustakaan</span>
            </div>

            <!-- Center quote -->
            <div class="relative">
                <p class="mb-4 text-5xl font-bold leading-tight text-white">"</p>
                <p class="text-2xl font-semibold leading-relaxed text-white">
                    Membaca adalah jendela<br/>menuju dunia yang<br/>tak terbatas.
                </p>
                <p class="mt-6 text-sm opacity-60 text-white">— Bergabung dengan ribuan pembaca aktif</p>
            </div>

            <!-- Stats bottom -->
            <div class="relative grid grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold" style="color: #E8A020;">500+</p>
                    <p class="text-xs text-white opacity-60">Koleksi Buku</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold" style="color: #E8A020;">1.2K</p>
                    <p class="text-xs text-white opacity-60">Anggota</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold" style="color: #E8A020;">FREE</p>
                    <p class="text-xs text-white opacity-60">Untuk Semua</p>
                </div>
            </div>
        </div>

        <!-- ── RIGHT PANEL: Form ── -->
        <div class="flex flex-1 flex-col items-center justify-center px-8 py-12" style="background: #FFF8F0;">
            <!-- Mobile logo -->
            <div class="mb-8 flex items-center gap-2 lg:hidden">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                    <BookOpen class="h-5 w-5 text-white" />
                </div>
                <span class="text-xl font-bold" style="color: #C4781A;">E-Perpustakaan</span>
            </div>

            <div class="w-full max-w-md">
                <h2 class="mb-1 text-3xl font-bold" style="color: #5C3D1E;">Selamat Datang!</h2>
                <p class="mb-8 text-sm" style="color: #9A7050;">Masuk ke akun E-Perpustakaan Anda</p>

                <div v-if="status" class="mb-5 rounded-xl p-3 text-sm" style="background: #D1FAE5; color: #166534;">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;" for="email">Email</label>
                        <div class="relative">
                            <Mail class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2" style="color: #C4781A;" />
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                placeholder="email@sekolah.ac.id"
                                class="w-full rounded-xl border-2 bg-white py-3 pl-11 pr-4 text-sm outline-none transition-all focus:shadow-md"
                                :style="form.errors.email ? 'border-color:#EF4444' : 'border-color:#E8A020'"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;" for="password">Password</label>
                        <div class="relative">
                            <Lock class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2" style="color: #C4781A;" />
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full rounded-xl border-2 bg-white py-3 pl-11 pr-12 text-sm outline-none transition-all focus:shadow-md"
                                :style="form.errors.password ? 'border-color:#EF4444' : 'border-color:#E8A020'"
                            />
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2" style="color: #C4781A;">
                                <Eye v-if="!showPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center justify-between">
                        <label class="flex cursor-pointer items-center gap-2 text-sm" style="color: #5C3D1E;">
                            <input v-model="form.remember" type="checkbox" class="rounded" style="accent-color: #E8A020;" />
                            Ingat saya
                        </label>
                        <a href="/forgot-password" class="text-sm font-medium hover:underline" style="color: #C4781A;">Lupa password?</a>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl py-3.5 text-sm font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50"
                        style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                    >
                        <span v-if="form.processing">Memproses…</span>
                        <span v-else>Masuk ke Akun</span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm" style="color: #9A7050;">
                    Belum punya akun?
                    <a href="/siswa/daftar" class="font-semibold hover:underline" style="color: #C4781A;">Daftar Sekarang</a>
                </p>
                <p class="mt-3 text-center text-xs" style="color: #C0A080;">
                    Admin?
                    <a href="/login" class="hover:underline" style="color: #9A7050;">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</template>
