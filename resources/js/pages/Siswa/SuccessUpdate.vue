<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle, ArrowRight, Home, LayoutDashboard, User } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import SiswaLayout from '@/layouts/SiswaLayout.vue';

const showAnimation = ref(false);

onMounted(() => {
    setTimeout(() => {
        showAnimation.value = true;
    }, 100);
});
</script>

<template>
    <Head title="Pembaruan Berhasil" />

    <SiswaLayout>
        <main class="min-h-[80vh] flex items-center justify-center py-12 px-6" style="background: #FFF8F0;">
            <div 
                class="max-w-md w-full bg-white rounded-[2.5rem] shadow-2xl shadow-[#5C3D1E]/10 p-10 text-center border border-[#F0D6A8] transition-all duration-700 transform"
                :class="showAnimation ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'"
            >
                <!-- Animated Icon Container -->
                <div class="relative mb-8 flex justify-center">
                    <div 
                        class="w-24 h-24 rounded-full bg-green-50 flex items-center justify-center transition-all duration-1000 delay-300 transform"
                        :class="showAnimation ? 'scale-100 shadow-lg shadow-green-200' : 'scale-0'"
                    >
                        <CheckCircle class="w-16 h-16 text-green-500" />
                    </div>
                    
                    <!-- Floating decorations -->
                    <div 
                        class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#E8A020] animate-bounce transition-all duration-1000 delay-700"
                        :class="showAnimation ? 'opacity-100' : 'opacity-0'"
                    ></div>
                    <div 
                        class="absolute bottom-2 -left-4 w-4 h-4 rounded-full bg-[#5C3D1E] transition-all duration-1000 delay-1000"
                        :class="showAnimation ? 'opacity-60 scale-100' : 'opacity-0 scale-0'"
                    ></div>
                </div>

                <!-- Text Content -->
                <h1 
                    class="text-3xl font-bold text-[#5C3D1E] mb-4 transition-all duration-700 delay-500 transform"
                    :class="showAnimation ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
                >
                    Berhasil Diperbarui!
                </h1>
                
                <p 
                    class="text-[#9A7050] leading-relaxed mb-10 transition-all duration-700 delay-700 transform"
                    :class="showAnimation ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
                >
                    Profil dan pengaturan keamanan akun Anda telah berhasil disimpan. Sekarang Anda dapat menggunakan password baru untuk sesi selanjutnya.
                </p>

                <!-- Action Buttons -->
                <div 
                    class="space-y-4 transition-all duration-700 delay-1000 transform"
                    :class="showAnimation ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'"
                >
                    <Link 
                        href="/siswa"
                        class="w-full py-4 bg-[#5C3D1E] hover:bg-[#452E17] text-white font-bold rounded-2xl shadow-xl transition-all flex items-center justify-center gap-3 group"
                    >
                        <LayoutDashboard class="w-5 h-5 group-hover:scale-110 transition-transform" />
                        Kembali ke Dashboard
                    </Link>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <Link 
                            href="/siswa/profile"
                            class="py-3 bg-white border-2 border-[#F0D6A8] hover:border-[#E8A020] text-[#5C3D1E] font-bold rounded-xl transition-all flex items-center justify-center gap-2"
                        >
                            <User class="w-4 h-4" />
                            Lihat Profil
                        </Link>
                        <Link 
                            href="/"
                            class="py-3 bg-[#FFF8F0] hover:bg-orange-100 text-[#5C3D1E] font-bold rounded-xl transition-all flex items-center justify-center gap-2"
                        >
                            <Home class="w-4 h-4" />
                            Beranda
                        </Link>
                    </div>
                </div>

                <!-- Footer Tip -->
                <div 
                    class="mt-10 pt-8 border-t border-[#F0D6A8] transition-all duration-1000 delay-[1200ms]"
                    :class="showAnimation ? 'opacity-100' : 'opacity-0'"
                >
                    <p class="text-[10px] text-[#9A7050] uppercase tracking-widest font-bold">
                        Pesan Keamanan
                    </p>
                    <p class="text-[11px] text-[#C4781A] mt-1">
                        Jangan pernah bagikan informasi akun Anda kepada siapa pun demi keamanan data pribadi.
                    </p>
                </div>
            </div>
        </main>
    </SiswaLayout>
</template>

<style scoped>
/* Optional: specific fonts or refine animations */
</style>
