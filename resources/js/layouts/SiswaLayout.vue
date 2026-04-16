<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Home, Library, BookOpen, Heart, User, LogIn, UserPlus, LayoutDashboard, LogOut, BookMarked, ArrowLeftRight, DollarSign, CreditCard, Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => (page.props as any).auth?.user ?? null);
const isOpen = ref(false);
const mobileMenuOpen = ref(false);

const logout = () => {
    (window as any).location.href = '/logout'; // Fortify handles POST, so use Link approach
};
</script>

<template>
    <div class="min-h-screen" style="background: #FFF8F0; font-family: 'Georgia', serif;">
        <!-- Navbar -->
        <nav class="sticky top-0 z-50 shadow-sm" style="background: #FFFFFF; border-bottom: 2px solid #E8A020;">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-3">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2 no-underline">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg" style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                        <BookOpen class="h-5 w-5 text-white" />
                    </div>
                    <span class="text-xl font-bold" style="color: #C4781A; font-family: Georgia, serif;">E-Perpustakaan</span>
                </Link>

                <!-- Center Nav Links -->
                <div class="hidden items-center gap-8 md:flex">
                    <Link href="/" class="text-sm font-medium transition-colors hover:text-orange-600" style="color: #5C3D1E;">Beranda</Link>
                    <Link href="/koleksi-buku" class="text-sm font-medium transition-colors hover:text-orange-600" style="color: #5C3D1E;">Koleksi Buku</Link>
                    <Link v-if="user" href="/siswa/favorites" class="flex items-center gap-1 text-sm font-medium transition-colors hover:text-orange-600" style="color: #5C3D1E;">
                        <Heart class="h-4 w-4" />
                        Favorit
                    </Link>
                    <Link v-if="user" href="/siswa/returns" class="flex items-center gap-1 text-sm font-medium transition-colors hover:text-orange-600" style="color: #5C3D1E;">
                        <ArrowLeftRight class="h-4 w-4" />
                        Kembalikan
                    </Link>
                    <Link v-if="user" href="/siswa/fines" class="flex items-center gap-1 text-sm font-medium transition-colors hover:text-orange-600" style="color: #5C3D1E;">
                        <DollarSign class="h-4 w-4" />
                        Denda
                    </Link>
                    <Link v-if="user" href="/siswa/kartu" class="flex items-center gap-1 text-sm font-medium transition-colors hover:text-orange-600" style="color: #5C3D1E;">
                        <CreditCard class="h-4 w-4" />
                        Kartu Anggota
                    </Link>
                </div>

                <!-- Right: Auth Buttons or User Menu -->
                <div class="flex items-center gap-3">
                    <template v-if="!user">
                        <div class="hidden sm:flex items-center gap-2">
                            <Link
                                href="/siswa/masuk"
                                class="flex items-center gap-1.5 rounded-lg border px-4 py-2 text-sm font-semibold transition-all hover:bg-orange-50"
                                style="border-color: #E8A020; color: #C4781A;"
                            >
                                <LogIn class="h-4 w-4" />
                                Masuk
                            </Link>
                            <Link
                                href="/siswa/daftar"
                                class="flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white transition-all hover:opacity-90"
                                style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                            >
                                <UserPlus class="h-4 w-4" />
                                Daftar
                            </Link>
                        </div>
                    </template>
                    <template v-else>
                        <div class="relative hidden sm:block">
                            <button
                                @click="isOpen = !isOpen"
                                class="flex items-center gap-2 rounded-full border-2 px-3 py-1.5 text-sm font-medium transition-all hover:bg-orange-50"
                                style="border-color: #E8A020; color: #5C3D1E;"
                            >
                                <div class="flex h-7 w-7 items-center justify-center rounded-full text-white text-xs font-bold" style="background: #E8A020;">
                                    {{ user.name?.charAt(0).toUpperCase() }}
                                </div>
                                <span class="hidden sm:inline">{{ user.name?.split(' ')[0] }}</span>
                            </button>
                            <div
                                v-if="isOpen"
                                class="absolute right-0 mt-2 w-52 rounded-xl border bg-white py-2 shadow-lg"
                                style="border-color: #F0D6A8;"
                            >
                                <Link href="/siswa" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-orange-50" style="color: #5C3D1E;" @click="isOpen=false">
                                    <LayoutDashboard class="h-4 w-4 text-orange-500" />
                                    Dashboard
                                </Link>
                                <Link href="/siswa/favorites" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-orange-50" style="color: #5C3D1E;" @click="isOpen=false">
                                    <Heart class="h-4 w-4 text-orange-500" />
                                    Buku Favorit
                                </Link>
                                <Link href="/siswa/returns" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-orange-50" style="color: #5C3D1E;" @click="isOpen=false">
                                    <ArrowLeftRight class="h-4 w-4 text-orange-500" />
                                    Kembalikan Buku
                                </Link>
                                <Link href="/siswa/fines" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-orange-50" style="color: #5C3D1E;" @click="isOpen=false">
                                    <DollarSign class="h-4 w-4 text-orange-500" />
                                    Denda Saya
                                </Link>
                                <Link href="/siswa/kartu" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-orange-50" style="color: #5C3D1E;" @click="isOpen=false">
                                    <CreditCard class="h-4 w-4 text-orange-500" />
                                    Kartu Anggota
                                </Link>
                                <hr class="my-1" style="border-color: #F0D6A8;" />
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    class="flex w-full items-center gap-2 px-4 py-2 text-sm hover:bg-red-50"
                                    style="color: #DC2626;"
                                    @click="isOpen=false"
                                >
                                    <LogOut class="h-4 w-4" />
                                    Keluar
                                </Link>
                            </div>
                        </div>
                    </template>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="flex items-center justify-center p-2 rounded-lg text-amber-800 hover:bg-orange-50 transition md:hidden"
                    >
                        <Menu v-if="!mobileMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Sidebar (Right Drawer) -->
            <Teleport to="body">
                <Transition
                    enter-active-class="transition-opacity ease-linear duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition-opacity ease-linear duration-300"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="mobileMenuOpen" class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm md:hidden" @click="mobileMenuOpen = false"></div>
                </Transition>

                <Transition
                    enter-active-class="transition ease-in-out duration-300 transform"
                    enter-from-class="translate-x-full"
                    enter-to-class="translate-x-0"
                    leave-active-class="transition ease-in-out duration-300 transform"
                    leave-from-class="translate-x-0"
                    leave-to-class="translate-x-full"
                >
                    <div v-if="mobileMenuOpen" class="fixed inset-y-0 right-0 z-[110] w-4/5 max-w-[300px] flex flex-col bg-white shadow-2xl md:hidden overflow-y-auto" style="background: #FFFBF5; border-left: 2px solid #E8A020;">
                        <!-- Header Drawer -->
                        <div class="flex items-center justify-between px-6 py-4 border-b-2" style="border-color: #F0D6A8; background: #FFF8F0;">
                            <div class="flex items-center gap-2">
                                <BookOpen class="h-6 w-6" style="color: #E8A020;" />
                                <span class="text-lg font-bold" style="color: #5C3D1E; font-family: Georgia, serif;">Menu</span>
                            </div>
                            <button @click="mobileMenuOpen = false" class="rounded-full p-2 hover:bg-orange-100 transition" style="color: #5C3D1E;">
                                <X class="h-6 w-6" />
                            </button>
                        </div>

                        <!-- Menu Links -->
                        <div class="flex flex-col py-2 flex-grow">
                            <Link href="/" class="flex items-center gap-3 px-6 py-4 text-base font-bold transition-colors hover:bg-orange-50 border-b" style="color: #5C3D1E; border-color: #F0D6A8;" @click="mobileMenuOpen=false">
                                <Home class="h-5 w-5" style="color: #E8A020;" /> Beranda
                            </Link>
                            <Link href="/koleksi-buku" class="flex items-center gap-3 px-6 py-4 text-base font-bold transition-colors hover:bg-orange-50 border-b" style="color: #5C3D1E; border-color: #F0D6A8;" @click="mobileMenuOpen=false">
                                <Library class="h-5 w-5" style="color: #E8A020;" /> Koleksi Buku
                            </Link>
                            
                            <template v-if="user">
                                <Link href="/siswa/favorites" class="flex items-center gap-3 px-6 py-4 text-base font-bold border-b transition-colors hover:bg-orange-50" style="color: #5C3D1E; border-color: #F0D6A8;" @click="mobileMenuOpen=false">
                                    <Heart class="h-5 w-5" style="color: #E8A020;" /> Favorit
                                </Link>
                                <Link href="/siswa/returns" class="flex items-center gap-3 px-6 py-4 text-base font-bold border-b transition-colors hover:bg-orange-50" style="color: #5C3D1E; border-color: #F0D6A8;" @click="mobileMenuOpen=false">
                                    <ArrowLeftRight class="h-5 w-5" style="color: #E8A020;" /> Kembalikan
                                </Link>
                                <Link href="/siswa/fines" class="flex items-center gap-3 px-6 py-4 text-base font-bold border-b transition-colors hover:bg-orange-50" style="color: #5C3D1E; border-color: #F0D6A8;" @click="mobileMenuOpen=false">
                                    <DollarSign class="h-5 w-5" style="color: #E8A020;" /> Denda
                                </Link>
                                <Link href="/siswa/kartu" class="flex items-center gap-3 px-6 py-4 text-base font-bold border-b transition-colors hover:bg-orange-50" style="color: #5C3D1E; border-color: #F0D6A8;" @click="mobileMenuOpen=false">
                                    <CreditCard class="h-5 w-5" style="color: #E8A020;" /> Kartu Anggota
                                </Link>
                                
                                <div class="p-6 mt-auto flex flex-col gap-3 border-t-2" style="border-color: #F0D6A8; background: #FFF8F0;">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-white font-bold" style="background: #E8A020;">
                                            {{ user.name?.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold leading-tight truncate" style="color: #5C3D1E;">{{ user.name }}</p>
                                            <p class="text-xs truncate" style="color: #9A7050;">{{ user.email }}</p>
                                        </div>
                                    </div>
                                    <Link method="post" as="button" href="/logout" class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white transition hover:opacity-90 mt-1" style="background: linear-gradient(135deg, #EF4444, #B91C1C);" @click="mobileMenuOpen=false">
                                        <LogOut class="h-4 w-4" /> Keluar
                                    </Link>
                                </div>
                            </template>
                            <template v-else>
                                <div class="p-6 mt-auto flex flex-col gap-3">
                                    <Link href="/siswa/masuk" class="flex items-center justify-center w-full gap-2 rounded-xl border-2 px-4 py-2.5 text-sm font-bold transition hover:bg-orange-50" style="border-color: #E8A020; color: #5C3D1E;" @click="mobileMenuOpen=false">
                                        <LogIn class="h-4 w-4" /> Masuk
                                    </Link>
                                    <Link href="/siswa/daftar" class="flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white transition hover:opacity-90" style="background: linear-gradient(135deg, #E8A020, #C4781A);" @click="mobileMenuOpen=false">
                                        <UserPlus class="h-4 w-4" /> Daftar Akun
                                    </Link>
                                </div>
                            </template>
                        </div>
                    </div>
                </Transition>
            </Teleport>
        </nav>

        <!-- Page Content -->
        <slot />

        <!-- Footer -->
        <footer class="mt-20 border-t py-10" style="background: #5C3D1E; border-color: #7A5230;">
            <div class="mx-auto max-w-7xl px-6">
                <div class="flex flex-col items-center gap-4 md:flex-row md:justify-between">
                    <div class="flex items-center gap-2">
                        <BookOpen class="h-6 w-6" style="color: #E8A020;" />
                        <span class="text-lg font-bold text-white" style="font-family: Georgia, serif;">E-Perpustakaan</span>
                    </div>
                    <p class="text-sm" style="color: #D4A060;">© 2026 E-Perpustakaan. Semua hak dilindungi.</p>
                    <div class="flex gap-4">
                        <Link href="/" class="text-sm transition-colors hover:text-white" style="color: #D4A060;">Beranda</Link>
                        <Link href="/koleksi-buku" class="text-sm transition-colors hover:text-white" style="color: #D4A060;">Koleksi Buku</Link>
                        <Link v-if="user" href="/siswa/favorites" class="text-sm transition-colors hover:text-white" style="color: #D4A060;">Favorit</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
