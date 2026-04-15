<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Heart, BookOpen, ArrowLeft, Star, Users, CheckCircle, AlertTriangle } from 'lucide-vue-next';
import SiswaLayout from '@/layouts/SiswaLayout.vue';
import { computed, ref } from 'vue';

interface Book {
    id: number;
    judul: string;
    pengarang: string;
    penerbit: string;
    tahun_terbit: number;
    isbn: string | null;
    stok: number;
    cover_image: string | null;
    deskripsi: string | null;
    is_favorited: boolean;
    available: boolean;
}

interface RelatedBook {
    id: number;
    judul: string;
    pengarang: string;
    cover_image: string | null;
    stok: number;
}

const props = defineProps<{
    book: Book;
    relatedBooks: RelatedBook[];
    alreadyBorrowed: boolean;
    pendingBorrow: boolean;
}>();

const page = usePage();
const user = computed(() => (page.props as any).auth?.user ?? null);
const isFav = ref(props.book.is_favorited);

const coverUrl = (b: { cover_image: string | null }) => b.cover_image ? `/storage/${b.cover_image}` : null;

const toggleFavorite = () => {
    if (!user.value) { router.visit('/siswa/masuk'); return; }
    router.post(`/siswa/favorites/${props.book.id}/toggle`, {}, {
        preserveScroll: true,
        onSuccess: () => { isFav.value = !isFav.value; },
    });
};

const requestBorrow = () => {
    if (!user.value) { router.visit('/siswa/masuk'); return; }
    router.post('/siswa/borrow', { book_id: props.book.id }, { preserveScroll: true });
};
</script>

<template>
    <Head :title="`${book.judul} — E-Perpustakaan`" />
    <SiswaLayout>
        <div class="mx-auto max-w-6xl px-6 py-10">
            <!-- Back button -->
            <Link href="/" class="mb-8 inline-flex items-center gap-2 text-sm font-medium transition-colors hover:text-orange-700" style="color: #C4781A;">
                <ArrowLeft class="h-4 w-4" />
                Kembali ke Katalog
            </Link>

            <!-- Main card - split panel like picture 2 -->
            <div class="overflow-hidden rounded-3xl shadow-2xl" style="min-height: 480px; display: grid; grid-template-columns: 320px 1fr;">
                <!-- LEFT: dark orange panel -->
                <div class="flex flex-col items-center justify-between p-8 pb-6" style="background: linear-gradient(160deg, #5C3D1E 0%, #3D2810 100%);">
                    <!-- Breadcrumb -->
                    <div class="self-start">
                        <p class="text-xs font-semibold uppercase tracking-widest" style="color: #E8A020;">Perpustakaan / Buku</p>
                    </div>

                    <!-- Book cover -->
                    <div class="relative my-6 w-44 shadow-2xl" style="border-radius: 12px; overflow: hidden;">
                        <div class="h-64 w-44" style="background: linear-gradient(135deg, #E8A020, #8B5E15);">
                            <img
                                v-if="coverUrl(book)"
                                :src="coverUrl(book)!"
                                :alt="book.judul"
                                class="h-full w-full object-cover"
                            />
                            <div v-else class="flex h-full w-full flex-col items-center justify-center gap-3 text-white">
                                <BookOpen class="h-16 w-16 opacity-80" />
                                <p class="px-4 text-center text-sm font-semibold">{{ book.judul }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social / share row (decorative, matching picture 2 style) -->
                    <div class="flex items-center gap-4 opacity-60 mt-2">
                        <span class="text-xs" style="color: #D4A060;">Tersedia:</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full" style="background: rgba(232,160,32,0.2);">
                            <BookOpen class="h-4 w-4" style="color: #E8A020;" />
                        </div>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full" style="background: rgba(232,160,32,0.2);">
                            <Star class="h-4 w-4" style="color: #E8A020;" />
                        </div>
                    </div>
                </div>

                <!-- RIGHT: light cream panel -->
                <div class="flex flex-col justify-between p-10" style="background: #FFF8F0;">
                    <!-- Top tabs (decorative, like picture 2) -->
                    <div class="mb-6 flex gap-6 border-b pb-3" style="border-color: #E8A020;">
                        <button class="text-sm font-bold pb-1 border-b-2" style="color: #C4781A; border-color: #E8A020;">INFO</button>
                        <button class="text-sm font-medium pb-1" style="color: #9A7050;">DETAIL</button>
                        <button class="text-sm font-medium pb-1" style="color: #9A7050;">BUKU TERKAIT</button>
                    </div>

                    <!-- Book info -->
                    <div class="flex-1">
                        <p class="mb-1 text-sm font-medium" style="color: #9A7050;">{{ book.tahun_terbit }}</p>
                        <h1 class="mb-1 text-4xl font-bold leading-tight" style="color: #5C3D1E; font-family: Georgia, serif;">{{ book.judul }}</h1>
                        <p class="mb-4 text-lg" style="color: #E8A020;">{{ book.pengarang }}</p>

                        <!-- Stars decorative -->
                        <div class="mb-5 flex gap-1">
                            <Star v-for="i in 4" :key="i" class="h-5 w-5" style="color: #E8A020; fill: #E8A020;" />
                            <Star class="h-5 w-5" style="color: #D4A060;" />
                        </div>

                        <!-- Description -->
                        <p class="mb-4 leading-relaxed text-sm" style="color: #5C3D1E; max-width: 480px;">
                            {{ book.deskripsi || `${book.judul} oleh ${book.pengarang}. Buku ini diterbitkan oleh ${book.penerbit} pada tahun ${book.tahun_terbit}.` }}
                        </p>

                        <!-- Meta -->
                        <div class="mb-6 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl p-3" style="background: rgba(232,160,32,0.1);">
                                <p class="text-xs font-semibold uppercase" style="color: #9A7050;">Penerbit</p>
                                <p class="font-medium" style="color: #5C3D1E;">{{ book.penerbit }}</p>
                            </div>
                            <div class="rounded-xl p-3" style="background: rgba(232,160,32,0.1);">
                                <p class="text-xs font-semibold uppercase" style="color: #9A7050;">Stok</p>
                                <p class="font-medium" :style="book.available ? 'color: #16A34A' : 'color: #DC2626'">
                                    {{ book.stok }} tersedia
                                </p>
                            </div>
                            <div v-if="book.isbn" class="rounded-xl p-3" style="background: rgba(232,160,32,0.1);">
                                <p class="text-xs font-semibold uppercase" style="color: #9A7050;">ISBN</p>
                                <p class="font-medium font-mono text-xs" style="color: #5C3D1E;">{{ book.isbn }}</p>
                            </div>
                        </div>

                        <!-- Status warnings -->
                        <div v-if="alreadyBorrowed" class="mb-4 flex items-center gap-2 rounded-xl p-3" style="background: #D1FAE5;">
                            <CheckCircle class="h-5 w-5 flex-shrink-0" style="color: #16A34A;" />
                            <p class="text-sm font-medium" style="color: #166534;">Buku ini sedang Anda pinjam.</p>
                        </div>
                        <div v-else-if="pendingBorrow" class="mb-4 flex items-center gap-2 rounded-xl p-3" style="background: #FEF9C3;">
                            <AlertTriangle class="h-5 w-5 flex-shrink-0" style="color: #CA8A04;" />
                            <p class="text-sm font-medium" style="color: #713F12;">Permintaan pinjam sedang menunggu konfirmasi admin.</p>
                        </div>
                    </div>

                    <!-- Action buttons (like READ/LISTEN in picture 2) -->
                    <div class="mt-4 flex gap-4">
                        <button
                            v-if="!alreadyBorrowed && !pendingBorrow"
                            @click="requestBorrow"
                            :disabled="!book.available"
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl py-3.5 text-sm font-bold text-white transition-all hover:opacity-90 disabled:opacity-40"
                            style="background: linear-gradient(135deg, #E8A020, #C4781A);"
                        >
                            <BookOpen class="h-5 w-5" />
                            {{ book.available ? 'Pinjam Buku' : 'Stok Habis' }}
                        </button>
                        <button
                            v-else-if="alreadyBorrowed || pendingBorrow"
                            disabled
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl py-3.5 text-sm font-bold text-white opacity-50"
                            style="background: #9A7050;"
                        >
                            <BookOpen class="h-5 w-5" />
                            {{ alreadyBorrowed ? 'Sedang Dipinjam' : 'Menunggu Konfirmasi' }}
                        </button>
                        <button
                            @click="toggleFavorite"
                            class="flex items-center justify-center gap-2 rounded-xl border-2 px-6 py-3.5 text-sm font-bold transition-all hover:bg-red-50"
                            :style="isFav ? 'border-color:#EF4444; color:#EF4444; background:#FEF2F2' : 'border-color:#E8A020; color:#C4781A;'"
                        >
                            <Heart class="h-5 w-5" :style="isFav ? 'fill:#EF4444' : ''" />
                            {{ isFav ? 'Favorit' : 'Simpan' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Related books -->
            <div v-if="relatedBooks.length > 0" class="mt-12">
                <h2 class="mb-6 text-2xl font-bold" style="color: #5C3D1E; font-family: Georgia, serif;">Buku Terkait</h2>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <Link
                        v-for="rb in relatedBooks"
                        :key="rb.id"
                        :href="`/books/${rb.id}`"
                        class="group flex flex-col overflow-hidden rounded-xl bg-white shadow hover:shadow-md transition-all"
                    >
                        <div class="h-36 overflow-hidden" style="background: linear-gradient(135deg, #E8A020, #C4781A);">
                            <img v-if="coverUrl(rb)" :src="coverUrl(rb)!" :alt="rb.judul" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            <div v-else class="flex h-full items-center justify-center">
                                <BookOpen class="h-8 w-8 text-white opacity-70" />
                            </div>
                        </div>
                        <div class="p-3">
                            <p class="line-clamp-2 text-xs font-semibold" style="color: #5C3D1E;">{{ rb.judul }}</p>
                            <p class="text-xs mt-1" style="color: #9A7050;">{{ rb.pengarang }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </SiswaLayout>
</template>
