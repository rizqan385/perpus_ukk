<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { User, Mail, Phone, MapPin, Camera, Lock, Save, ArrowLeft, CheckCircle } from 'lucide-vue-next';
import SiswaLayout from '@/layouts/SiswaLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps<{
    user: any;
}>();

const page = usePage();
const user = computed(() => (page.props as any).auth?.user ?? null);
const flash = computed(() => (page.props as any).flash ?? {});

const goBack = () => {
    window.history.back();
};

const photoInput = ref<HTMLInputElement | null>(null);
const photoPreview = ref<string | null>(props.user.member?.foto_url || null);

const form = useForm({
    _method: 'POST', // For file uploads with POST
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    alamat: props.user.member?.alamat || '',
    telepon: props.user.member?.telepon || '',
    password: '',
    password_confirmation: '',
    foto: null as File | null,
});

const selectPhoto = () => {
    photoInput.value?.click();
};

const updatePhotoPreview = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.foto = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post('/siswa/profile', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};

const formatDate = (date: string) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="Profil Saya" />

    <SiswaLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <button @click="goBack" class="p-2 hover:bg-white/10 rounded-full transition-colors text-white">
                    <ArrowLeft class="w-6 h-6" />
                </button>
                <h2 class="font-bold text-2xl text-white">Profil Saya</h2>
            </div>
        </template>

        <main class="min-h-screen py-12 px-6" style="background: #FFF8F0;">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-[#F0D6A8]">
                    <div class="md:flex">
                        <!-- Sidebar Info -->
                        <div class="md:w-1/3 bg-[#5C3D1E] p-8 text-white">
                            <div class="flex flex-col items-center text-center">
                                <div class="relative group">
                                    <div class="w-32 h-32 rounded-3xl overflow-hidden border-4 border-[#E8A020] mb-4 bg-white/20">
                                        <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover">
                                        <div v-else class="w-full h-full flex items-center justify-center">
                                            <User class="w-16 h-16 text-white/50" />
                                        </div>
                                    </div>
                                    <button 
                                        @click="selectPhoto"
                                        class="absolute bottom-2 right-2 p-2 bg-[#E8A020] rounded-xl shadow-lg hover:scale-110 transition-transform"
                                    >
                                        <Camera class="w-4 h-4 text-white" />
                                    </button>
                                    <input 
                                        type="file" 
                                        ref="photoInput" 
                                        class="hidden" 
                                        accept="image/*"
                                        @change="updatePhotoPreview"
                                    >
                                </div>
                                
                                <h3 class="text-xl font-bold mt-2">{{ user.name }}</h3>
                                <div v-if="user.member" class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-white/10 text-[#E8A020] text-xs font-bold border border-white/20">
                                    {{ user.member.no_anggota }}
                                </div>

                                <div class="mt-8 space-y-4 text-sm text-white/80 w-full">
                                    <div class="flex items-center gap-3">
                                        <Mail class="w-4 h-4 text-[#E8A020]" />
                                        <span>{{ user.email }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Phone class="w-4 h-4 text-[#E8A020]" />
                                        <span>{{ user.phone || '-' }}</span>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <MapPin class="w-4 h-4 text-[#E8A020] mt-0.5" />
                                        <span class="text-left">{{ user.member?.alamat || 'Alamat belum diatur' }}</span>
                                    </div>
                                </div>

                                <div class="mt-12 pt-8 border-t border-white/10 w-full text-xs text-white/50">
                                    Member Literasi Sejak<br>
                                    <span class="text-white font-semibold">{{ formatDate(user.member?.created_at) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <div class="md:w-2/3 p-8 md:p-12">
                            <form @submit.prevent="submit" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Full Name -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-[#5C3D1E]">Nama Lengkap</label>
                                        <div class="relative">
                                            <User class="absolute left-3 top-3 w-5 h-5 text-[#9A7050]" />
                                            <input 
                                                v-model="form.name"
                                                type="text" 
                                                class="w-full pl-10 pr-4 py-3 rounded-2xl border-[#F0D6A8] focus:border-[#E8A020] focus:ring-[#E8A020] bg-[#FFF8F0] transition-all"
                                                placeholder="Masukkan nama lengkap"
                                            >
                                        </div>
                                        <p v-if="form.errors.name" class="text-red-500 text-xs">{{ form.errors.name }}</p>
                                    </div>

                                    <!-- Email -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-[#5C3D1E]">Email Address</label>
                                        <div class="relative">
                                            <Mail class="absolute left-3 top-3 w-5 h-5 text-[#9A7050]" />
                                            <input 
                                                v-model="form.email"
                                                type="email" 
                                                class="w-full pl-10 pr-4 py-3 rounded-2xl border-[#F0D6A8] focus:border-[#E8A020] focus:ring-[#E8A020] bg-[#FFF8F0] transition-all"
                                                placeholder="email@example.com"
                                            >
                                        </div>
                                        <p v-if="form.errors.email" class="text-red-500 text-xs">{{ form.errors.email }}</p>
                                    </div>

                                    <!-- Phone -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-[#5C3D1E]">Nomor HP (WhatsApp)</label>
                                        <div class="relative">
                                            <Phone class="absolute left-3 top-3 w-5 h-5 text-[#9A7050]" />
                                            <input 
                                                v-model="form.phone"
                                                type="text" 
                                                class="w-full pl-10 pr-4 py-3 rounded-2xl border-[#F0D6A8] focus:border-[#E8A020] focus:ring-[#E8A020] bg-[#FFF8F0] transition-all"
                                                placeholder="+628..."
                                            >
                                        </div>
                                        <p v-if="form.errors.phone" class="text-red-500 text-xs">{{ form.errors.phone }}</p>
                                    </div>

                                    <!-- Member Phone (Optional if different) -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-[#5C3D1E]">Telepon Tambahan</label>
                                        <div class="relative">
                                            <Phone class="absolute left-3 top-3 w-5 h-5 text-[#9A7050]" />
                                            <input 
                                                v-model="form.telepon"
                                                type="text" 
                                                class="w-full pl-10 pr-4 py-3 rounded-2xl border-[#F0D6A8] focus:border-[#E8A020] focus:ring-[#E8A020] bg-[#FFF8F0] transition-all"
                                                placeholder="Nomor telepon lainnya"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Alamat -->
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-[#5C3D1E]">Alamat Lengkap</label>
                                    <div class="relative">
                                        <MapPin class="absolute left-3 top-3 w-5 h-5 text-[#9A7050]" />
                                        <textarea 
                                            v-model="form.alamat"
                                            rows="3"
                                            class="w-full pl-10 pr-4 py-3 rounded-2xl border-[#F0D6A8] focus:border-[#E8A020] focus:ring-[#E8A020] bg-[#FFF8F0] transition-all resize-none"
                                            placeholder="Tuliskan alamat lengkap tempat tinggal Anda..."
                                        ></textarea>
                                    </div>
                                </div>

                                <div class="border-t border-[#F0D6A8] pt-6 flex flex-col gap-4">
                                    <h4 class="font-bold text-[#5C3D1E] flex items-center gap-2">
                                        <Lock class="w-4 h-4" />
                                        Ganti Password
                                        <span class="text-[10px] font-normal text-[#9A7050]">(Kosongkan jika tidak ingin ganti)</span>
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label class="text-sm font-bold text-[#5C3D1E]">Password Baru</label>
                                            <input 
                                                v-model="form.password"
                                                type="password" 
                                                class="w-full px-4 py-3 rounded-2xl border-[#F0D6A8] focus:border-[#E8A020] focus:ring-[#E8A020] bg-[#FFF8F0] transition-all"
                                                autocomplete="new-password"
                                            >
                                            <p v-if="form.errors.password" class="text-red-500 text-xs">{{ form.errors.password }}</p>
                                        </div>
                                        <div class="space-y-2">
                                            <label class="text-sm font-bold text-[#5C3D1E]">Konfirmasi Password</label>
                                            <input 
                                                v-model="form.password_confirmation"
                                                type="password" 
                                                class="w-full px-4 py-3 rounded-2xl border-[#F0D6A8] focus:border-[#E8A020] focus:ring-[#E8A020] bg-[#FFF8F0] transition-all"
                                                autocomplete="new-password"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button 
                                        type="submit" 
                                        :disabled="form.processing"
                                        class="w-full md:w-auto px-12 py-4 bg-[#E8A020] hover:bg-[#D4921C] text-white font-bold rounded-2xl shadow-lg shadow-[#E8A020]/30 transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <Save class="w-5 h-5" v-if="!form.processing" />
                                        <div v-else class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>

                            <div v-if="flash.success" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center gap-3 text-green-700 font-semibold">
                                <CheckCircle class="w-5 h-5" />
                                {{ flash.success }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </SiswaLayout>
</template>

<style scoped>
input:focus, textarea:focus {
    background: white;
}
</style>
