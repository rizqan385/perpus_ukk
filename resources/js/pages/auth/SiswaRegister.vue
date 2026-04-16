<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { BookOpen, User, Mail, Lock, Eye, EyeOff, Phone } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post('/siswa/daftar', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Daftar — E-Perpustakaan" />

    <div class="flex min-h-screen" style="font-family: Georgia, serif;">
        <!-- LEFT: Orange decorative panel -->
        <div class="relative hidden flex-col justify-between overflow-hidden p-12 lg:flex lg:w-5/12" style="background: linear-gradient(160deg, #E8A020 0%, #C4781A 50%, #8B5015 100%);">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -right-12 -top-12 h-64 w-64 rounded-full opacity-20" style="background: white;"></div>
                <div class="absolute -left-8 bottom-16 h-48 w-48 rounded-full opacity-10" style="background: white;"></div>
                <!-- Stacked books -->
                <div class="absolute bottom-20 right-8" style="transform: rotate(-6deg);">
                    <div style="width:100px; height:130px; background:rgba(255,255,255,0.15); border-radius:6px 14px 14px 6px; margin-bottom:-100px; transform:rotate(8deg);"></div>
                    <div style="width:100px; height:130px; background:rgba(255,255,255,0.2); border-radius:6px 14px 14px 6px; margin-bottom:-100px;"></div>
                    <div style="width:100px; height:130px; background:rgba(255,255,255,0.25); border-radius:6px 14px 14px 6px;"></div>
                </div>
            </div>

            <div class="relative flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white">
                    <BookOpen class="h-6 w-6" style="color: #C4781A;" />
                </div>
                <span class="text-2xl font-bold text-white">E-Perpustakaan</span>
            </div>

            <div class="relative">
                <h2 class="mb-4 text-4xl font-bold leading-tight text-white">
                    Bergabung &amp;<br/>Mulai Membaca<br/>Hari Ini!
                </h2>
                <ul class="space-y-3 text-sm text-white opacity-90">
                    <li class="flex items-center gap-2">✅ Akses ribuan koleksi buku</li>
                    <li class="flex items-center gap-2">✅ Pinjam hingga 3 buku sekaligus</li>
                    <li class="flex items-center gap-2">✅ Simpan buku favorit</li>
                    <li class="flex items-center gap-2">✅ Gratis untuk semua siswa</li>
                </ul>
            </div>

            <p class="relative text-xs text-white opacity-50">© 2026 E-Perpustakaan</p>
        </div>

        <!-- RIGHT: Form -->
        <div class="flex flex-1 flex-col items-center justify-center px-8 py-12" style="background: #FFF8F0;">
            <div class="mb-8 flex items-center gap-2 lg:hidden">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                    <BookOpen class="h-5 w-5 text-white" />
                </div>
                <span class="text-xl font-bold" style="color: #C4781A;">E-Perpustakaan</span>
            </div>

            <div class="w-full max-w-md">
                <h2 class="mb-1 text-3xl font-bold" style="color: #5C3D1E;">Buat Akun Baru</h2>
                <p class="mb-8 text-sm" style="color: #9A7050;">Daftar gratis dan mulai meminjam buku</p>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;">Nama Lengkap</label>
                        <div class="relative">
                            <User class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2" style="color: #C4781A;" />
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Nama kamu"
                                class="w-full rounded-xl border-2 bg-white py-3 pl-11 pr-4 text-sm outline-none focus:shadow-md"
                                :style="(form.errors.name ? 'border-color:#EF4444' : 'border-color:#E8A020') + ';color:#1a1a1a;'"
                            />
                        </div>
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;">Email</label>
                        <div class="relative">
                            <Mail class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2" style="color: #C4781A;" />
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="email@sekolah.ac.id"
                                class="w-full rounded-xl border-2 bg-white py-3 pl-11 pr-4 text-sm outline-none focus:shadow-md"
                                :style="(form.errors.email ? 'border-color:#EF4444' : 'border-color:#E8A020') + ';color:#1a1a1a;'"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;">Nomor WhatsApp</label>
                        <div class="relative">
                            <Phone class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2" style="color: #C4781A;" />
                            <input
                                v-model="form.phone"
                                type="text"
                                placeholder="08xxxxxxxxx"
                                class="w-full rounded-xl border-2 bg-white py-3 pl-11 pr-4 text-sm outline-none focus:shadow-md"
                                :style="(form.errors.phone ? 'border-color:#EF4444' : 'border-color:#E8A020') + ';color:#1a1a1a;'"
                            />
                        </div>
                        <p v-if="form.errors.phone" class="mt-1 text-xs text-red-500">{{ form.errors.phone }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;">Password</label>
                        <div class="relative">
                            <Lock class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2" style="color: #C4781A;" />
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Min. 8 karakter"
                                class="w-full rounded-xl border-2 bg-white py-3 pl-11 pr-12 text-sm outline-none focus:shadow-md"
                                :style="(form.errors.password ? 'border-color:#EF4444' : 'border-color:#E8A020') + ';color:#1a1a1a;'"
                            />
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2" style="color: #C4781A;">
                                <Eye v-if="!showPassword" class="h-4 w-4" />
                                <EyeOff v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;">Konfirmasi Password</label>
                        <div class="relative">
                            <Lock class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2" style="color: #C4781A;" />
                            <input
                                v-model="form.password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Ulangi password"
                                class="w-full rounded-xl border-2 bg-white py-3 pl-11 pr-4 text-sm outline-none focus:shadow-md"
                                style="border-color: #E8A020; color:#1a1a1a;"
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-xl py-3.5 text-sm font-bold text-white transition-all hover:opacity-90 active:scale-[0.98] disabled:opacity-50"
                        style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                    >
                        {{ form.processing ? 'Mendaftarkan…' : 'Daftar Sekarang' }}
                    </button>
                </form>

                <p class="mt-6 text-center text-sm" style="color: #9A7050;">
                    Sudah punya akun?
                    <a href="/siswa/masuk" class="font-semibold hover:underline" style="color: #C4781A;">Masuk</a>
                </p>
            </div>
        </div>
    </div>
</template>
