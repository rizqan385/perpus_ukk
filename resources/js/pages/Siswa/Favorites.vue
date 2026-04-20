<script setup lang="ts">
import { Head, router, usePage, Link } from '@inertiajs/vue3';
import {
    Heart,
    BookOpen,
    ChevronRight,
    Trash2,
    Star,
    Search,
    Clock,
    Calendar,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import SiswaLayout from '@/layouts/SiswaLayout.vue';

interface FavBook {
    id: number;
    judul: string;
    pengarang: string;
    penerbit: string;
    tahun_terbit: number;
    deskripsi: string | null;
    cover_image: string | null;
    stok: number;
    available: boolean;
    is_favorited: boolean;
}

const props = defineProps<{
    favorites: FavBook[];
    borrowedBooksStatus: Record<number, string>;
}>();

const page = usePage();
const user = computed(() => (page.props as any).auth?.user ?? null);
const searchQ = ref('');

const filtered = computed(() =>
    props.favorites.filter(
        (b) =>
            b.judul.toLowerCase().includes(searchQ.value.toLowerCase()) ||
            b.pengarang.toLowerCase().includes(searchQ.value.toLowerCase()),
    ),
);

const coverUrl = (b: FavBook) =>
    b.cover_image ? `/storage/${b.cover_image}` : null;

// Actions
const removeFavorite = (id: number) => {
    router.post(
        `/siswa/favorites/${id}/toggle`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                if (selectedBook.value?.id === id) {
                    closeBookModal();
                }
            },
        },
    );
};

const requestBorrow = (book: FavBook) => {
    if (!user.value) {
        router.visit('/siswa/masuk');
        return;
    }
    if (props.borrowedBooksStatus[book.id]) return;
    router.post(
        '/siswa/borrow',
        { book_id: book.id },
        { preserveScroll: true },
    );
};

const requestReturn = () => {
    if (!user.value) {
        router.visit('/siswa/masuk');
        return;
    }
    router.visit('/siswa/returns');
};

// Modal State
const showBookModal = ref(false);
const selectedBook = ref<FavBook | null>(null);

const openBookModal = (book: FavBook) => {
    selectedBook.value = book;
    showBookModal.value = true;
};
const closeBookModal = () => {
    showBookModal.value = false;
    setTimeout(() => {
        selectedBook.value = null;
    }, 300);
};
</script>

<template>
    <Head title="Buku Favorit — E-Perpustakaan" />
    <SiswaLayout>
        <!-- ══ HERO HEADER ══ -->
        <section
            style="
                background: linear-gradient(135deg, #5c3d1e 0%, #3d2810 100%);
            "
            class="relative overflow-hidden px-6 py-12"
        >
            <div class="pointer-events-none absolute inset-0">
                <div
                    class="absolute top-0 right-0 h-48 w-48 rounded-full opacity-10"
                    style="background: #e8a020"
                ></div>
                <div
                    class="absolute bottom-0 left-1/3 h-32 w-32 rounded-full opacity-5"
                    style="background: #ffd580"
                ></div>
            </div>
            <div class="relative mx-auto max-w-7xl">
                <div class="mb-4 flex items-center gap-2">
                    <Link
                        href="/"
                        class="text-sm text-white opacity-60 transition-opacity hover:opacity-100"
                        >Beranda</Link
                    >
                    <ChevronRight class="h-4 w-4 text-white opacity-40" />
                    <span class="text-sm font-semibold text-white"
                        >Buku Favorit</span
                    >
                </div>
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <h1
                            class="text-3xl font-bold text-white"
                            style="font-family: Georgia, serif"
                        >
                            ❤️ Buku Favorit
                        </h1>
                        <p class="mt-1 text-white opacity-70">
                            {{ favorites.length }} buku tersimpan dalam koleksi
                            favoritmu
                        </p>
                    </div>
                    <!-- Search in favorites -->
                    <div
                        v-if="favorites.length > 0"
                        class="relative w-full sm:w-64"
                    >
                        <Search
                            class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                        />
                        <input
                            v-model="searchQ"
                            type="text"
                            placeholder="Cari di favorit..."
                            class="w-full rounded-xl bg-white/90 py-2.5 pr-4 pl-10 text-sm outline-none"
                            style="color: #5c3d1e"
                        />
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto min-h-[50vh] max-w-7xl px-6 py-10 pb-16">
            <!-- ══ EMPTY STATE ══ -->
            <div
                v-if="favorites.length === 0"
                class="flex flex-col items-center justify-center py-24 text-center"
            >
                <div class="relative mb-6">
                    <div
                        class="flex h-28 w-28 items-center justify-center rounded-full"
                        style="
                            background: linear-gradient(
                                135deg,
                                #fff3e0,
                                #ffe0b2
                            );
                        "
                    >
                        <Heart
                            class="h-14 w-14"
                            style="color: #e8a020; fill: #e8a020; opacity: 0.4"
                        />
                    </div>
                    <div
                        class="absolute -right-2 -bottom-2 flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-lg"
                    >
                        <BookOpen class="h-5 w-5" style="color: #c4781a" />
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold" style="color: #5c3d1e">
                    Belum ada buku favorit
                </h3>
                <p class="mx-auto mb-6 max-w-xs text-sm" style="color: #9a7050">
                    Klik ikon ❤️ di halaman katalog untuk menyimpan buku yang
                    kamu suka
                </p>
                <Link
                    href="/koleksi-buku"
                    class="inline-flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-bold text-white shadow-lg transition hover:opacity-90"
                    style="
                        background: linear-gradient(135deg, #e8a020, #c4781a);
                    "
                >
                    <BookOpen class="h-4 w-4" />
                    Jelajahi Koleksi Buku
                </Link>
            </div>

            <!-- ══ FILTER RESULT EMPTY ══ -->
            <div v-else-if="filtered.length === 0" class="py-16 text-center">
                <p class="font-semibold" style="color: #5c3d1e">
                    Tidak ada buku dengan kata kunci "{{ searchQ }}"
                </p>
                <button
                    @click="searchQ = ''"
                    class="mt-3 text-sm underline"
                    style="color: #c4781a"
                >
                    Tampilkan semua
                </button>
            </div>

            <!-- ══ BOOK GRID ══ -->
            <div
                v-else
                class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
            >
                <div
                    v-for="book in filtered"
                    :key="book.id"
                    class="book-card-wrapper group relative cursor-pointer"
                    @click="openBookModal(book)"
                >
                    <!-- 3D Book Cover -->
                    <div
                        class="perspective-container relative mb-4 flex h-52 items-center justify-center"
                    >
                        <!-- Quick Remove Button -->
                        <button
                            @click.stop="removeFavorite(book.id)"
                            class="absolute -top-2 -right-2 z-20 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 text-white opacity-0 shadow-lg transition-all group-hover:opacity-100 hover:scale-110 hover:bg-red-600"
                            title="Hapus dari favorit"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>

                        <div
                            class="book-container transition-transform duration-500 group-hover:scale-105 group-hover:rotate-y-[-15deg]"
                        >
                            <div class="book">
                                <div
                                    class="front"
                                    style="
                                        background: linear-gradient(
                                            135deg,
                                            #e8a020,
                                            #c4781a
                                        );
                                    "
                                >
                                    <img
                                        v-if="coverUrl(book)"
                                        :src="coverUrl(book)!"
                                        class="h-full w-full rounded-r border border-black/10 object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-full flex-col items-center justify-center p-2 text-white"
                                    >
                                        <BookOpen
                                            class="mb-2 h-10 w-10 opacity-70"
                                        />
                                        <p
                                            class="text-center text-[10px] font-bold"
                                        >
                                            {{ book.judul }}
                                        </p>
                                    </div>
                                    <div
                                        class="absolute top-0 left-0 h-full w-2 bg-gradient-to-r from-white/30 to-transparent"
                                    ></div>
                                </div>
                                <div
                                    class="back"
                                    style="background: #8b5e15"
                                ></div>
                                <div
                                    class="left"
                                    style="background: #a0641a"
                                ></div>
                                <div
                                    class="right"
                                    style="background: #fdfbf7"
                                ></div>
                                <div
                                    class="top"
                                    style="background: #fdfbf7"
                                ></div>
                                <div
                                    class="bottom"
                                    style="background: #fdfbf7"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Book Info -->
                    <div class="px-1 text-center lg:text-left">
                        <h3
                            class="line-clamp-2 text-sm leading-tight font-bold group-hover:underline"
                            style="color: #5c3d1e"
                        >
                            {{ book.judul }}
                        </h3>
                        <p
                            class="mt-1 mb-3 truncate text-xs"
                            style="color: #9a7050"
                        >
                            {{ book.pengarang }}
                        </p>

                        <!-- Button Area -->
                        <div class="flex flex-col gap-2">
                            <template v-if="borrowedBooksStatus[book.id]">
                                <button
                                    v-if="
                                        borrowedBooksStatus[book.id] ===
                                        'dipinjam'
                                    "
                                    @click.stop="requestReturn"
                                    class="w-full rounded-xl py-2 text-xs font-bold text-white transition hover:opacity-90"
                                    style="
                                        background: linear-gradient(
                                            135deg,
                                            #0ea5e9,
                                            #0369a1
                                        );
                                    "
                                >
                                    ↩ Kembalikan
                                </button>
                                <button
                                    v-else-if="
                                        borrowedBooksStatus[book.id] ===
                                        'menunggu_persetujuan'
                                    "
                                    disabled
                                    class="w-full rounded-xl py-2 text-xs font-bold text-white opacity-80"
                                    style="background: #f59e0b"
                                >
                                    ⏳ Menunggu Pinjam
                                </button>
                                <button
                                    v-else-if="
                                        borrowedBooksStatus[book.id] ===
                                        'menunggu_pengembalian'
                                    "
                                    disabled
                                    class="w-full rounded-xl py-2 text-xs font-bold text-white opacity-80"
                                    style="background: #f59e0b"
                                >
                                    ⏳ Menunggu Kembali
                                </button>
                            </template>
                            <template v-else>
                                <button
                                    v-if="book.available"
                                    @click.stop="requestBorrow(book)"
                                    class="w-full rounded-xl py-2 text-xs font-bold text-white shadow-md transition group-hover:shadow-lg hover:opacity-90"
                                    style="
                                        background: linear-gradient(
                                            135deg,
                                            #e8a020,
                                            #c4781a
                                        );
                                    "
                                >
                                    Pinjam
                                </button>
                                <button
                                    v-else
                                    disabled
                                    class="w-full rounded-xl py-2 text-xs font-bold text-white opacity-40"
                                    style="background: #9a7050"
                                >
                                    Habis
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ BOTTOM CTA ══ -->
            <div
                v-if="favorites.length > 0"
                class="mt-16 border-t pt-8 text-center"
                style="border-color: #f0d6a8"
            >
                <Link
                    href="/koleksi-buku"
                    class="inline-flex items-center gap-2 rounded-2xl px-6 py-3 text-sm font-bold transition hover:opacity-90"
                    style="
                        color: #c4781a;
                        border: 2px solid #f0d6a8;
                        background: #fff8f0;
                    "
                >
                    <BookOpen class="h-4 w-4" />
                    Jelajahi Lebih Banyak Buku
                    <ChevronRight class="h-4 w-4" />
                </Link>
            </div>
        </div>

        <!-- ══════════════════ MODAL ══════════════════ -->
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="showBookModal"
                    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm sm:p-6"
                    @click.self="closeBookModal"
                >
                    <div
                        class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl"
                    >
                        <div class="flex flex-col md:flex-row">
                            <!-- Left: 3D Book Cover -->
                            <div
                                class="relative flex h-72 items-center justify-center overflow-hidden p-8 md:h-auto md:w-2/5"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #fff3e0,
                                        #ffe0b2
                                    );
                                "
                            >
                                <div
                                    class="absolute inset-0 opacity-20"
                                    style="
                                        background: radial-gradient(
                                            circle at center,
                                            #e8a020 0%,
                                            transparent 70%
                                        );
                                    "
                                ></div>
                                <div
                                    class="book-container scale-[1.2] rotate-y-[-20deg]"
                                >
                                    <div class="book">
                                        <div
                                            class="front"
                                            style="
                                                background: linear-gradient(
                                                    135deg,
                                                    #e8a020,
                                                    #c4781a
                                                );
                                            "
                                        >
                                            <img
                                                v-if="coverUrl(selectedBook!)"
                                                :src="coverUrl(selectedBook!)!"
                                                class="h-full w-full rounded-r border border-black/10 object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-full flex-col items-center justify-center text-white"
                                            >
                                                <BookOpen
                                                    class="mb-2 h-10 w-10 opacity-70"
                                                />
                                                <p
                                                    class="px-2 text-center text-xs font-bold"
                                                >
                                                    {{ selectedBook?.judul }}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="back"
                                            style="background: #8b5e15"
                                        ></div>
                                        <div
                                            class="left"
                                            style="background: #a0641a"
                                        ></div>
                                        <div
                                            class="right"
                                            style="background: #fdfbf7"
                                        ></div>
                                        <div
                                            class="top"
                                            style="background: #fdfbf7"
                                        ></div>
                                        <div
                                            class="bottom"
                                            style="background: #fdfbf7"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Info -->
                            <div
                                class="flex flex-1 flex-col justify-between p-6 md:p-8"
                            >
                                <div>
                                    <div
                                        class="flex items-start justify-between"
                                    >
                                        <div>
                                            <span
                                                class="mb-2 inline-block rounded-full px-3 py-1 text-xs font-bold tracking-wider uppercase"
                                                :style="
                                                    selectedBook?.available
                                                        ? 'background:#DCFCE7; color:#166534'
                                                        : 'background:#FEE2E2; color:#991B1B'
                                                "
                                            >
                                                {{
                                                    selectedBook?.available
                                                        ? 'Tersedia'
                                                        : 'Stok Habis'
                                                }}
                                            </span>
                                            <h2
                                                class="mb-1 text-2xl leading-tight font-bold md:text-3xl"
                                                style="
                                                    color: #5c3d1e;
                                                    font-family: Georgia, serif;
                                                "
                                            >
                                                {{ selectedBook?.judul }}
                                            </h2>
                                            <p
                                                class="text-base font-semibold"
                                                style="color: #d4881a"
                                            >
                                                {{ selectedBook?.pengarang }}
                                            </p>
                                        </div>
                                        <button
                                            @click="closeBookModal"
                                            class="rounded-full bg-orange-50 p-2 transition-colors hover:bg-orange-100"
                                            style="color: #c4781a"
                                        >
                                            <X class="h-5 w-5" />
                                        </button>
                                    </div>

                                    <div class="my-6 grid grid-cols-2 gap-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-500"
                                            >
                                                <Star class="h-5 w-5" />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs font-semibold tracking-wider uppercase"
                                                    style="color: #9a7050"
                                                >
                                                    Penerbit
                                                </p>
                                                <p
                                                    class="text-sm font-bold"
                                                    style="color: #5c3d1e"
                                                >
                                                    {{ selectedBook?.penerbit }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-500"
                                            >
                                                <Calendar class="h-5 w-5" />
                                            </div>
                                            <div>
                                                <p
                                                    class="text-xs font-semibold tracking-wider uppercase"
                                                    style="color: #9a7050"
                                                >
                                                    Tahun Terbit
                                                </p>
                                                <p
                                                    class="text-sm font-bold"
                                                    style="color: #5c3d1e"
                                                >
                                                    {{
                                                        selectedBook?.tahun_terbit
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="selectedBook?.deskripsi"
                                        class="mb-4"
                                    >
                                        <p
                                            class="text-sm leading-relaxed"
                                            style="color: #7a5230"
                                        >
                                            {{ selectedBook.deskripsi }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="mt-6 flex flex-col items-center gap-3 border-t pt-6 sm:flex-row"
                                    style="
                                        border-color: #fee2e2;
                                        border-top-color: rgba(
                                            232,
                                            160,
                                            32,
                                            0.2
                                        );
                                    "
                                >
                                    <template
                                        v-if="
                                            borrowedBooksStatus[
                                                selectedBook?.id ?? -1
                                            ]
                                        "
                                    >
                                        <button
                                            v-if="
                                                borrowedBooksStatus[
                                                    selectedBook!.id
                                                ] === 'dipinjam'
                                            "
                                            @click="
                                                requestReturn();
                                                closeBookModal();
                                            "
                                            class="w-full flex-1 rounded-2xl py-3.5 text-sm font-bold text-white shadow-lg transition hover:opacity-90"
                                            style="
                                                background: linear-gradient(
                                                    135deg,
                                                    #0ea5e9,
                                                    #0369a1
                                                );
                                            "
                                        >
                                            ↩ Kembalikan Buku Ini
                                        </button>
                                        <button
                                            v-else-if="
                                                borrowedBooksStatus[
                                                    selectedBook!.id
                                                ] === 'menunggu_persetujuan'
                                            "
                                            disabled
                                            class="flex w-full flex-1 items-center justify-center gap-2 rounded-2xl py-3.5 text-sm font-bold text-white opacity-90 shadow-lg"
                                            style="background: #f59e0b"
                                        >
                                            <Clock class="h-4 w-4" /> Menunggu
                                            Konfirmasi Pinjam
                                        </button>
                                        <button
                                            v-else-if="
                                                borrowedBooksStatus[
                                                    selectedBook!.id
                                                ] === 'menunggu_pengembalian'
                                            "
                                            disabled
                                            class="flex w-full flex-1 items-center justify-center gap-2 rounded-2xl py-3.5 text-sm font-bold text-white opacity-90 shadow-lg"
                                            style="background: #f59e0b"
                                        >
                                            <Clock class="h-4 w-4" /> Menunggu
                                            Konfirmasi Kembali
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button
                                            v-if="selectedBook?.available"
                                            @click="
                                                requestBorrow(selectedBook!);
                                                closeBookModal();
                                            "
                                            class="w-full flex-1 rounded-2xl py-3.5 text-sm font-bold text-white shadow-lg transition hover:opacity-90 hover:shadow-orange-500/30"
                                            style="
                                                background: linear-gradient(
                                                    135deg,
                                                    #e8a020,
                                                    #c4781a
                                                );
                                            "
                                        >
                                            Pinjam Buku Ini
                                        </button>
                                        <button
                                            v-else
                                            disabled
                                            class="w-full flex-1 cursor-not-allowed rounded-2xl py-3.5 text-sm font-bold text-white opacity-40"
                                            style="background: #9a7050"
                                        >
                                            Stok Habis
                                        </button>
                                    </template>

                                    <!-- Action Hapus from Favorite in Modal -->
                                    <button
                                        @click="
                                            removeFavorite(selectedBook!.id)
                                        "
                                        class="flex h-12 shrink-0 items-center justify-center rounded-2xl border-2 px-4 transition hover:scale-105 hover:bg-red-50"
                                        style="
                                            border-color: #ef4444;
                                            background: white;
                                        "
                                        title="Hapus dari favorit"
                                    >
                                        <Trash2
                                            class="mr-1 h-5 w-5"
                                            style="color: #ef4444"
                                        />
                                        <span
                                            class="text-sm font-bold text-red-500"
                                            >Hapus</span
                                        >
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </SiswaLayout>
</template>

<style>
/* 3D Book CSS */
.perspective-container {
    perspective: 1000px;
}
.book-container {
    width: 120px;
    height: 180px;
    transform-style: preserve-3d;
    transform: rotateY(-20deg);
}
.book {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
}
.book > div {
    position: absolute;
}
/* Cover */
.front {
    width: 120px;
    height: 180px;
    transform: translateZ(12px);
    border-radius: 2px 6px 6px 2px;
    overflow: hidden;
    box-shadow: inset 4px 0 10px rgba(0, 0, 0, 0.1);
}
/* Inside of back cover viewed from front */
.back {
    width: 120px;
    height: 180px;
    transform: rotateY(180deg) translateZ(12px);
    border-radius: 6px 2px 2px 6px;
}
/* Spine */
.left {
    width: 24px;
    height: 180px;
    transform: rotateY(-90deg) translateZ(12px);
    border-radius: 2px 0 0 2px;
}
/* Pages */
.right {
    width: 22px;
    height: 176px;
    transform: rotateY(90deg) translateZ(108px);
    top: 2px;
}
.top {
    width: 116px;
    height: 22px;
    transform: rotateX(90deg) translateZ(12px);
    left: 2px;
}
.bottom {
    width: 116px;
    height: 22px;
    transform: rotateX(-90deg) translateZ(168px);
    left: 2px;
}
/* Page texture */
.right,
.top,
.bottom {
    background-image: repeating-linear-gradient(
        to bottom,
        transparent 0px,
        transparent 2px,
        rgba(0, 0, 0, 0.05) 2px,
        rgba(0, 0, 0, 0.05) 3px
    );
}
.right {
    background-image: repeating-linear-gradient(
        to right,
        transparent 0px,
        transparent 2px,
        rgba(0, 0, 0, 0.05) 2px,
        rgba(0, 0, 0, 0.05) 3px
    );
}

/* Modal transition */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
