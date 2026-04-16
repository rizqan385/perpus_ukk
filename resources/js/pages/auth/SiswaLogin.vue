<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { BookOpen, Mail, Lock, Eye, EyeOff, BookMarked, Search, Bell } from 'lucide-vue-next';
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
        <!-- ── LEFT PANEL ── -->
        <div class="relative hidden flex-col justify-between overflow-hidden p-12 lg:flex lg:w-1/2"
             style="background: linear-gradient(160deg, #5C3D1E 0%, #3D2810 55%, #1E1408 100%);">

            <!-- Animated background blobs -->
            <div class="pointer-events-none absolute inset-0">
                <div class="siswa-blob-1 absolute -right-24 -top-24 h-96 w-96 rounded-full" style="background: radial-gradient(circle, #E8A020 0%, transparent 70%); opacity: 0.12;"></div>
                <div class="siswa-blob-2 absolute -left-16 bottom-10 h-80 w-80 rounded-full" style="background: radial-gradient(circle, #C4781A 0%, transparent 70%); opacity: 0.12;"></div>
                <div class="siswa-blob-3 absolute left-1/2 top-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full" style="background: radial-gradient(circle, #E8A020 0%, transparent 70%); opacity: 0.06;"></div>

                <!-- Decorative grid dots -->
                <svg class="absolute inset-0 h-full w-full opacity-5" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="dots" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.5" fill="#E8A020"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#dots)"/>
                </svg>

                <!-- Floating book shapes -->
                <div class="siswa-book-float-1 absolute" style="bottom: 130px; right: 40px;">
                    <div style="width:90px; height:120px; background: linear-gradient(135deg,#E8A020 0%,#8B5E15 100%); border-radius: 4px 12px 12px 4px; box-shadow: 4px 4px 20px rgba(0,0,0,0.4); position:relative; overflow:hidden;">
                        <div style="position:absolute; left:8px; top:0; bottom:0; width:6px; background: rgba(0,0,0,0.2); border-radius:2px;"></div>
                        <div style="position:absolute; top:20px; left:18px; right:8px; height:3px; background: rgba(255,255,255,0.3); border-radius:2px;"></div>
                        <div style="position:absolute; top:30px; left:18px; right:16px; height:3px; background: rgba(255,255,255,0.2); border-radius:2px;"></div>
                        <div style="position:absolute; top:40px; left:18px; right:20px; height:3px; background: rgba(255,255,255,0.15); border-radius:2px;"></div>
                    </div>
                </div>
                <div class="siswa-book-float-2 absolute" style="bottom: 110px; right: 100px; transform: rotate(-12deg);">
                    <div style="width:70px; height:100px; background: linear-gradient(135deg,#C4781A 0%,#5C3D1E 100%); border-radius: 4px 10px 10px 4px; box-shadow: 4px 4px 16px rgba(0,0,0,0.35); position:relative; overflow:hidden;">
                        <div style="position:absolute; left:6px; top:0; bottom:0; width:5px; background: rgba(0,0,0,0.2); border-radius:2px;"></div>
                        <div style="position:absolute; top:18px; left:15px; right:6px; height:2px; background: rgba(255,255,255,0.25); border-radius:2px;"></div>
                        <div style="position:absolute; top:26px; left:15px; right:10px; height:2px; background: rgba(255,255,255,0.15); border-radius:2px;"></div>
                    </div>
                </div>
                <div class="siswa-book-float-3 absolute" style="bottom: 150px; right: 155px; transform: rotate(6deg);">
                    <div style="width:55px; height:80px; background: linear-gradient(135deg,#D4881A 0%,#7A4A10 100%); border-radius: 4px 8px 8px 4px; box-shadow: 3px 3px 12px rgba(0,0,0,0.3); position:relative; overflow:hidden;">
                        <div style="position:absolute; left:5px; top:0; bottom:0; width:4px; background: rgba(0,0,0,0.2); border-radius:2px;"></div>
                    </div>
                </div>
            </div>

            <!-- Logo top -->
            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl" style="background: linear-gradient(135deg, #E8A020, #C4781A); box-shadow: 0 4px 14px rgba(232,160,32,0.4);">
                    <BookOpen class="h-6 w-6 text-white" />
                </div>
                <div>
                    <span class="text-xl font-bold text-white">E-Perpustakaan</span>
                    <p class="text-xs opacity-50 text-white">Portal Siswa</p>
                </div>
            </div>

            <!-- Center content -->
            <div class="relative space-y-8">
                <!-- Quote -->
                <div>
                    <div style="width: 36px; height: 3px; background: linear-gradient(90deg,#E8A020,#C4781A); border-radius: 2px; margin-bottom: 20px;"></div>
                    <p class="text-3xl font-bold leading-snug text-white">
                        Membaca adalah jendela<br/>menuju dunia yang<br/>tak terbatas.
                    </p>
                    <p class="mt-4 text-sm opacity-50 text-white italic">— Bergabung dengan ribuan pembaca aktif</p>
                </div>

                <!-- Feature cards -->
                <div class="space-y-3">
                    <!-- Card 1 -->
                    <div class="flex items-center gap-4 rounded-2xl p-4" style="background: rgba(255,255,255,0.07); backdrop-filter: blur(8px); border: 1px solid rgba(232,160,32,0.2);">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl" style="background: rgba(232,160,32,0.2);">
                            <BookMarked class="h-5 w-5" style="color: #E8A020;" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Koleksi Lengkap</p>
                            <p class="text-xs opacity-50 text-white">Akses ribuan buku dari berbagai kategori</p>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="flex items-center gap-4 rounded-2xl p-4" style="background: rgba(255,255,255,0.07); backdrop-filter: blur(8px); border: 1px solid rgba(232,160,32,0.2);">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl" style="background: rgba(232,160,32,0.2);">
                            <Search class="h-5 w-5" style="color: #E8A020;" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Cari & Pinjam Mudah</p>
                            <p class="text-xs opacity-50 text-white">Temukan dan ajukan peminjaman online</p>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="flex items-center gap-4 rounded-2xl p-4" style="background: rgba(255,255,255,0.07); backdrop-filter: blur(8px); border: 1px solid rgba(232,160,32,0.2);">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl" style="background: rgba(232,160,32,0.2);">
                            <Bell class="h-5 w-5" style="color: #E8A020;" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Notifikasi Otomatis</p>
                            <p class="text-xs opacity-50 text-white">Pengingat jatuh tempo via WhatsApp</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom badge -->
            <div class="relative">
                <div class="inline-flex items-center gap-2 rounded-full px-4 py-2" style="background: rgba(232,160,32,0.15); border: 1px solid rgba(232,160,32,0.3);">
                    <div class="h-2 w-2 rounded-full" style="background: #E8A020; box-shadow: 0 0 6px #E8A020;"></div>
                    <span class="text-xs text-white opacity-70">Sistem aktif & siap digunakan</span>
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
                                :style="(form.errors.email ? 'border-color:#EF4444' : 'border-color:#E8A020') + ';color:#1a1a1a;'"
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
                                :style="(form.errors.password ? 'border-color:#EF4444' : 'border-color:#E8A020') + ';color:#1a1a1a;'"
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

<style scoped>
@keyframes blobFloat {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-18px) scale(1.04); }
}
@keyframes bookFloat1 {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-12px) rotate(2deg); }
}
@keyframes bookFloat2 {
    0%, 100% { transform: rotate(-12deg) translateY(0px); }
    50% { transform: rotate(-10deg) translateY(-8px); }
}
@keyframes bookFloat3 {
    0%, 100% { transform: rotate(6deg) translateY(0px); }
    50% { transform: rotate(8deg) translateY(-10px); }
}

.siswa-blob-1 { animation: blobFloat 7s ease-in-out infinite; }
.siswa-blob-2 { animation: blobFloat 9s ease-in-out infinite 1s; }
.siswa-blob-3 { animation: blobFloat 11s ease-in-out infinite 2s; }
.siswa-book-float-1 { animation: bookFloat1 5s ease-in-out infinite; }
.siswa-book-float-2 { animation: bookFloat2 6s ease-in-out infinite 0.5s; }
.siswa-book-float-3 { animation: bookFloat3 7s ease-in-out infinite 1s; }
</style>
