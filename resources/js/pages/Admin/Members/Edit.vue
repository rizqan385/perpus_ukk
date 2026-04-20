<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Save, ArrowLeft, User, Mail, GraduationCap, MapPin, Phone, ToggleLeft, Camera, X } from 'lucide-vue-next';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Member {
    id: number;
    no_anggota: string;
    kelas: string | null;
    alamat: string | null;
    telepon: string | null;
    status: string;
    foto_url: string | null;
    user: User;
}

const props = defineProps<{
    member: Member;
}>();

const form = useForm({
    name: props.member.user.name,
    email: props.member.user.email,
    kelas: props.member.kelas || '',
    alamat: props.member.alamat || '',
    telepon: props.member.telepon || '',
    status: props.member.status,
    foto: null as File | null,
    _method: 'put',
});

const fotoPreview = ref<string | null>(props.member.foto_url || null);

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
    form.post(`/admin/members/${props.member.id}`, { forceFormData: true });
};

const breadcrumbs = [
    { title: 'Admin', href: '/admin' },
    { title: 'Kelola Anggota', href: '/admin/members' },
    { title: 'Edit Anggota', href: `/admin/members/${props.member.id}/edit` },
];
</script>

<template>
    <Head title="Edit Anggota" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="mx-auto w-full max-w-2xl">
                <!-- Header -->
                <div class="mb-6 flex items-center gap-4">
                    <Link
                        href="/admin/members"
                        class="rounded-lg border p-2 transition-colors hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-700"
                    >
                        <ArrowLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Anggota</h1>
                        <p class="text-gray-600 dark:text-gray-400">No. Anggota: {{ member.no_anggota }}</p>
                    </div>
                </div>

                <!-- Form -->
                <div class="rounded-xl border bg-white p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Account Info Section -->
                        <div class="border-b pb-6 dark:border-gray-700">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Informasi Akun</h3>

                            <!-- Foto Upload -->
                            <div class="mb-6 flex flex-col items-center gap-4 sm:flex-row">
                                <div class="relative h-24 w-24 overflow-hidden rounded-full border-4 border-white object-cover shadow-lg dark:border-gray-800" style="background: linear-gradient(135deg, #10b981, #047857);">
                                    <img v-if="fotoPreview" :src="fotoPreview" class="h-full w-full object-cover" />
                                    <div v-else class="flex h-full items-center justify-center font-bold text-white text-3xl">
                                        {{ form.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <button
                                        v-if="fotoPreview"
                                        type="button"
                                        @click="removeFoto"
                                        class="absolute right-0 top-0 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white hover:bg-red-600 transition-colors"
                                    >
                                        <X class="h-3 w-3" />
                                    </button>
                                </div>
                                <div class="flex-1">
                                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Profil (Opsional)</label>
                                    <div class="relative">
                                        <input
                                            type="file"
                                            accept="image/*"
                                            @change="onFotoChange"
                                            class="hidden"
                                            id="foto-upload"
                                        />
                                        <label
                                            for="foto-upload"
                                            class="inline-flex cursor-pointer items-center gap-2 rounded-lg border bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                                        >
                                            <Camera class="h-4 w-4" />
                                            Pilih Foto
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: JPG, PNG, max 2MB. Resolusi 3x4 direkomendasikan.</p>
                                    <p v-if="form.errors.foto" class="mt-1 text-sm text-red-600">{{ form.errors.foto }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <User class="h-4 w-4" />
                                        Nama Lengkap *
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label for="email" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <Mail class="h-4 w-4" />
                                        Email *
                                    </label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Member Info Section -->
                        <div>
                            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Informasi Anggota</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="kelas" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <GraduationCap class="h-4 w-4" />
                                        Kelas
                                    </label>
                                    <input
                                        id="kelas"
                                        v-model="form.kelas"
                                        type="text"
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.kelas" class="mt-1 text-sm text-red-600">{{ form.errors.kelas }}</p>
                                </div>

                                <div>
                                    <label for="alamat" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <MapPin class="h-4 w-4" />
                                        Alamat
                                    </label>
                                    <textarea
                                        id="alamat"
                                        v-model="form.alamat"
                                        rows="3"
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.alamat" class="mt-1 text-sm text-red-600">{{ form.errors.alamat }}</p>
                                </div>

                                <div>
                                    <label for="telepon" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <Phone class="h-4 w-4" />
                                        Telepon
                                    </label>
                                    <input
                                        id="telepon"
                                        v-model="form.telepon"
                                        type="text"
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    />
                                    <p v-if="form.errors.telepon" class="mt-1 text-sm text-red-600">{{ form.errors.telepon }}</p>
                                </div>

                                <div>
                                    <label for="status" class="mb-2 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <ToggleLeft class="h-4 w-4" />
                                        Status *
                                    </label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        required
                                        class="w-full rounded-lg border bg-white px-4 py-2.5 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    >
                                        <option value="aktif">Aktif</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                    <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-3 pt-4">
                            <Link
                                href="/admin/members"
                                class="flex-1 rounded-lg border px-4 py-2.5 text-center font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 font-medium text-white transition-colors hover:bg-emerald-700 disabled:opacity-50"
                            >
                                <Save class="h-5 w-5" />
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
