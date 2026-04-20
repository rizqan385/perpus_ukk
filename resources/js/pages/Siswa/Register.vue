<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { UserPlus, MapPin, GraduationCap, Camera, ChevronRight, X, User } from 'lucide-vue-next';
import { ref } from 'vue';
import SiswaLayout from '@/layouts/SiswaLayout.vue';

interface UserData {
    id: number;
    name: string;
    email: string;
}

interface MemberData {
    kelas: string | null;
    alamat: string | null;
    tanggal_lahir: string | null;
    jenis_kelamin: string | null;
    foto_url: string | null;
}

const props = defineProps<{
    user: UserData;
    member?: MemberData | null;
    incomplete?: boolean;
}>();

const form = useForm({
    kelas         : props.member?.kelas         ?? '',
    alamat        : props.member?.alamat        ?? '',
    tanggal_lahir : props.member?.tanggal_lahir ?? '',
    jenis_kelamin : props.member?.jenis_kelamin ?? '',
    foto          : null as File | null,
});

const fotoPreview = ref<string | null>(props.member?.foto_url ?? null);

const onFotoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.foto = file;
    const reader = new FileReader();
    reader.onload = (ev) => { fotoPreview.value = ev.target?.result as string; };
    reader.readAsDataURL(file);
};

const removeFoto = () => {
    form.foto = null;
    fotoPreview.value = null;
};

const submit = () => {
    form.post('/siswa/register', { forceFormData: true });
};
</script>

<template>
    <Head :title="member ? 'Perbarui Profil Anggota' : 'Pendaftaran Anggota'" />
    <SiswaLayout>

        <!-- Hero Header -->
        <section style="background: linear-gradient(135deg, #5C3D1E 0%, #3D2810 100%);" class="relative overflow-hidden py-12 px-6">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute right-0 top-0 h-48 w-48 rounded-full opacity-10" style="background: #E8A020;"></div>
                <div class="absolute -left-12 bottom-0 h-32 w-32 rounded-full opacity-10" style="background: #E8A020;"></div>
            </div>
            <div class="relative mx-auto max-w-2xl">
                <div class="flex items-center gap-2 mb-4">
                    <a href="/siswa" class="text-sm text-white opacity-60 hover:opacity-100 transition-opacity">Dashboard</a>
                    <ChevronRight class="h-4 w-4 text-white opacity-40" />
                    <span class="text-sm text-white font-semibold">Pendaftaran Anggota</span>
                </div>
                <h1 class="text-3xl font-bold text-white" style="font-family: Georgia, serif;">
                    {{ member ? '✏️ Perbarui Profil' : '📋 Pendaftaran Anggota' }}
                </h1>
                <p class="mt-1 text-white opacity-70">
                    {{ member ? 'Perbarui data diri anggota perpustakaan' : 'Lengkapi data diri untuk menjadi anggota perpustakaan' }}
                </p>
            </div>
        </section>

        <!-- Alert jika redirect dari borrow -->
        <div v-if="incomplete" class="mx-auto max-w-2xl px-6 pt-6">
            <div class="flex items-start gap-3 rounded-2xl border-2 border-amber-200 bg-amber-50 p-4">
                <span class="text-xl">⚠️</span>
                <div>
                    <p class="font-bold text-amber-800">Data profil belum lengkap</p>
                    <p class="text-sm text-amber-700 mt-0.5">Lengkapi data diri terlebih dahulu sebelum dapat meminjam buku.</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="mx-auto max-w-2xl px-6 py-8 pb-16">
            <form @submit.prevent="submit" enctype="multipart/form-data" class="flex flex-col gap-5">

                <!-- Informasi Akun (read-only) -->
                <div class="rounded-2xl border-2 bg-white overflow-hidden" style="border-color: #F0D6A8;">
                    <div class="px-6 py-4 border-b" style="background: #FFF8F0; border-color: #F0D6A8;">
                        <h2 class="font-bold" style="color: #5C3D1E;">👤 Informasi Akun</h2>
                    </div>
                    <div class="grid gap-4 px-6 py-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9A7050;">Nama</p>
                            <p class="font-semibold" style="color: #5C3D1E;">{{ user.name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #9A7050;">Email</p>
                            <p class="font-semibold" style="color: #5C3D1E;">{{ user.email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Foto Profil -->
                <div class="rounded-2xl border-2 bg-white overflow-hidden" style="border-color: #F0D6A8;">
                    <div class="px-6 py-4 border-b" style="background: #FFF8F0; border-color: #F0D6A8;">
                        <h2 class="font-bold" style="color: #5C3D1E;">📷 Foto Profil</h2>
                    </div>
                    <div class="flex items-center gap-5 px-6 py-5">
                        <!-- Preview lingkaran -->
                        <div class="relative h-24 w-24 flex-shrink-0 overflow-hidden rounded-full border-4" style="border-color: #E8A020; background: #FFF3E0;">
                            <img v-if="fotoPreview" :src="fotoPreview" class="h-full w-full object-cover" alt="Preview" />
                            <div v-else class="flex h-full w-full items-center justify-center">
                                <User class="h-10 w-10" style="color: #C4781A; opacity: 0.5;" />
                            </div>
                            <button
                                v-if="fotoPreview"
                                type="button"
                                @click="removeFoto"
                                class="absolute right-0 top-0 rounded-full bg-red-500 p-0.5 text-white hover:bg-red-600"
                            >
                                <X class="h-3.5 w-3.5" />
                            </button>
                        </div>
                        <div class="flex-1">
                            <label class="block cursor-pointer rounded-xl border-2 border-dashed px-4 py-4 text-center transition-colors hover:bg-orange-50" style="border-color: #E8A020;">
                                <Camera class="mx-auto mb-1 h-6 w-6" style="color: #C4781A;" />
                                <span class="text-sm font-semibold" style="color: #C4781A;">Pilih Foto</span>
                                <p class="text-xs mt-0.5" style="color: #9A7050;">JPG, PNG maks. 2MB</p>
                                <input type="file" accept="image/*" class="hidden" @change="onFotoChange" />
                            </label>
                        </div>
                    </div>
                    <p v-if="form.errors.foto" class="px-6 pb-3 text-sm text-red-600">{{ form.errors.foto }}</p>
                </div>

                <!-- Data Diri -->
                <div class="rounded-2xl border-2 bg-white overflow-hidden" style="border-color: #F0D6A8;">
                    <div class="px-6 py-4 border-b" style="background: #FFF8F0; border-color: #F0D6A8;">
                        <h2 class="font-bold" style="color: #5C3D1E;">📝 Data Diri</h2>
                    </div>
                    <div class="px-6 py-5 flex flex-col gap-4">

                        <!-- Kelas -->
                        <div>
                            <label for="kelas" class="mb-1.5 flex items-center gap-1.5 text-sm font-semibold" style="color: #5C3D1E;">
                                <GraduationCap class="h-4 w-4" style="color: #C4781A;" />
                                Kelas <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="kelas"
                                v-model="form.kelas"
                                type="text"
                                placeholder="Contoh: XII RPL 1"
                                class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none transition focus:ring-2"
                                style="border-color: #F0D6A8; color: #5C3D1E; background: #FFFBF5;"
                                :style="{ borderColor: form.errors.kelas ? '#EF4444' : '#F0D6A8' }"
                            />
                            <p v-if="form.errors.kelas" class="mt-1 text-xs text-red-600">{{ form.errors.kelas }}</p>
                        </div>

                        <!-- Jenis Kelamin & Tanggal Lahir -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="jenis_kelamin" class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;">Jenis Kelamin</label>
                                <select
                                    id="jenis_kelamin"
                                    v-model="form.jenis_kelamin"
                                    class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none"
                                    style="border-color: #F0D6A8; color: #5C3D1E; background: #FFFBF5;"
                                >
                                    <option value="">-- Pilih --</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label for="tanggal_lahir" class="mb-1.5 block text-sm font-semibold" style="color: #5C3D1E;">Tanggal Lahir</label>
                                <input
                                    id="tanggal_lahir"
                                    v-model="form.tanggal_lahir"
                                    type="date"
                                    class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none"
                                    style="border-color: #F0D6A8; color: #5C3D1E; background: #FFFBF5;"
                                />
                            </div>
                        </div>



                        <!-- Alamat -->
                        <div>
                            <label for="alamat" class="mb-1.5 flex items-center gap-1.5 text-sm font-semibold" style="color: #5C3D1E;">
                                <MapPin class="h-4 w-4" style="color: #C4781A;" />
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="alamat"
                                v-model="form.alamat"
                                rows="3"
                                placeholder="Masukkan alamat lengkap"
                                class="w-full rounded-xl border-2 px-4 py-2.5 text-sm outline-none resize-none"
                                style="border-color: #F0D6A8; color: #5C3D1E; background: #FFFBF5;"
                            />
                            <p v-if="form.errors.alamat" class="mt-1 text-xs text-red-600">{{ form.errors.alamat }}</p>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex items-center justify-center gap-2 rounded-2xl py-3.5 font-bold text-white text-base transition-all hover:opacity-90 active:scale-95 disabled:opacity-50 shadow-lg"
                    style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                >
                    <span v-if="form.processing">Memproses...</span>
                    <span v-else class="flex items-center gap-2">
                        <UserPlus class="h-5 w-5" />
                        {{ member ? 'Simpan Perubahan' : 'Daftar Sebagai Anggota' }}
                    </span>
                </button>

            </form>
        </div>

    </SiswaLayout>
</template>
