<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Heart,
    BookOpen,
    Search,
    ArrowRight,
    Star,
    ChevronRight,
    X,
    Clock,
    Calendar,
    CheckCircle,
} from 'lucide-vue-next';
import SiswaLayout from '@/layouts/SiswaLayout.vue';
import { ref, computed } from 'vue';

interface Book {
    id: number;
    judul: string;
    pengarang: string;
    penerbit: string;
    tahun_terbit: number;
    stok: number;
    cover_image: string | null;
    deskripsi: string | null;
    is_favorited: boolean;
    available: boolean;
}

interface Stats {
    total_books: number;
    total_members: number;
    total_genres: number;
}

const props = defineProps<{
    books: Book[];
    newBooks: Book[];
    stats: Stats;
    search: string;
    borrowedBooksStatus: Record<number, string>;
}>();

const page = usePage();
const user = computed(() => (page.props as any).auth?.user ?? null);

const searchQuery = ref(props.search);

const doSearch = () => {
    router.get('/', { search: searchQuery.value }, { preserveState: false });
};

const toggleFavorite = (book: Book) => {
    if (!user.value) {
        router.visit('/siswa/masuk');
        return;
    }
    router.post(
        `/siswa/favorites/${book.id}/toggle`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                book.is_favorited = !book.is_favorited;
            },
        },
    );
};

const requestBorrow = (book: Book) => {
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

const coverUrl = (book: Book) =>
    book.cover_image ? `/storage/${book.cover_image}` : null;

// Modal State
const showBookModal = ref(false);
const selectedBook = ref<Book | null>(null);

const openBookModal = (book: Book) => {
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
    <Head title="E-Perpustakaan — Beranda" />
    <SiswaLayout>
        <!-- ══════════════════ HERO ══════════════════ -->
        <section
            class="relative overflow-hidden"
            style="
                background: linear-gradient(
                    135deg,
                    #fff3e0 0%,
                    #ffe0b2 40%,
                    #ffcc80 100%
                );
                min-height: 520px;
            "
        >
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div
                    class="absolute -top-10 -right-10 h-64 w-64 rounded-full opacity-20"
                    style="background: #e8a020"
                ></div>
                <div
                    class="absolute bottom-0 left-1/3 h-32 w-32 rounded-full opacity-10"
                    style="background: #c4781a"
                ></div>
                <svg
                    class="absolute top-8 right-8 opacity-30"
                    width="120"
                    height="80"
                    viewBox="0 0 120 80"
                >
                    <ellipse
                        cx="60"
                        cy="40"
                        rx="55"
                        ry="35"
                        fill="#E8A020"
                        transform="rotate(-20 60 40)"
                    />
                    <ellipse
                        cx="40"
                        cy="60"
                        rx="40"
                        ry="25"
                        fill="#C4781A"
                        transform="rotate(15 40 60)"
                    />
                    <ellipse
                        cx="90"
                        cy="20"
                        rx="30"
                        ry="18"
                        fill="#D4881A"
                        transform="rotate(-35 90 20)"
                    />
                </svg>
            </div>

            <div
                class="relative mx-auto flex max-w-7xl flex-col items-center gap-12 px-6 py-20 md:flex-row"
            >
                <!-- Left text -->
                <div class="flex-1 text-center md:text-left">
                    <p
                        class="mb-3 text-sm font-semibold tracking-widest uppercase"
                        style="color: #c4781a"
                    >
                        📚 Perpustakaan Digital
                    </p>
                    <h1
                        class="mb-6 text-4xl leading-tight font-bold md:text-5xl"
                        style="color: #5c3d1e; font-family: Georgia, serif"
                    >
                        Temukan Buku <br />
                        <span style="color: #e8a020">Favoritmu</span> &<br />
                        Mulai Membaca
                    </h1>
                    <p
                        class="mb-8 text-lg leading-relaxed"
                        style="color: #7a5230"
                    >
                        Ribuan judul buku tersedia untuk kamu pinjam. Daftar
                        sekarang dan nikmati kemudahan meminjam buku di mana
                        saja.
                    </p>
                    <div class="flex max-w-md gap-2">
                        <div class="relative flex-1">
                            <Search
                                class="absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2"
                                style="color: #c4781a"
                            />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari judul, pengarang..."
                                class="w-full rounded-xl border-2 bg-white py-3 pr-4 pl-10 text-sm transition-all outline-none focus:shadow-md"
                                style="border-color: #e8a020; color: #5c3d1e"
                                @keyup.enter="doSearch"
                            />
                        </div>
                        <button
                            @click="doSearch"
                            class="flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-white transition-all hover:opacity-90 active:scale-95"
                            style="
                                background: linear-gradient(
                                    135deg,
                                    #e8a020,
                                    #c4781a
                                );
                            "
                        >
                            Cari
                        </button>
                    </div>
                    <div class="mt-8 flex gap-6">
                        <Link
                            v-if="!user"
                            href="/siswa/daftar"
                            class="flex items-center gap-2 rounded-xl px-6 py-3 font-semibold text-white transition-all hover:opacity-90"
                            style="
                                background: linear-gradient(
                                    135deg,
                                    #e8a020,
                                    #c4781a
                                );
                            "
                        >
                            Daftar Gratis <ArrowRight class="h-4 w-4" />
                        </Link>
                        <a
                            href="/koleksi-buku"
                            class="flex items-center gap-2 rounded-xl border-2 px-6 py-3 font-semibold transition-all hover:bg-orange-50"
                            style="border-color: #e8a020; color: #c4781a"
                            >Jelajahi Koleksi</a
                        >
                    </div>
                </div>

                <!-- Right: Hero 3D Book -->
                <div
                    class="relative flex h-80 flex-shrink-0 items-center justify-center md:w-80"
                >
                    <div
                        class="book-container scale-[1.3] rotate-[-5deg] cursor-pointer transition-all duration-700 hover:scale-[1.4] hover:rotate-[0deg]"
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
                                <div
                                    class="flex h-full w-full flex-col items-center justify-center text-white"
                                >
                                    <BookOpen
                                        class="mb-2 h-12 w-12 opacity-90"
                                    />
                                    <p
                                        class="px-4 text-center text-sm font-bold opacity-90"
                                    >
                                        Koleksi<br />Perpustakaan
                                    </p>
                                </div>
                            </div>
                            <div class="back" style="background: #8b5e15"></div>
                            <div class="left" style="background: #a0641a"></div>
                            <div class="right" style="background: white"></div>
                            <div class="top" style="background: white"></div>
                            <div class="bottom" style="background: white"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════ STATS ══════════════════ -->
        <section class="py-10" style="background: #5c3d1e">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid grid-cols-3 gap-6 text-center">
                    <div>
                        <p
                            class="text-3xl font-bold sm:text-4xl"
                            style="color: #e8a020; font-family: Georgia, serif"
                        >
                            {{ stats.total_books }}
                        </p>
                        <p
                            class="mt-1 text-xs sm:text-sm"
                            style="color: #d4a060"
                        >
                            Koleksi Buku
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-3xl font-bold sm:text-4xl"
                            style="color: #e8a020; font-family: Georgia, serif"
                        >
                            {{ stats.total_members }}
                        </p>
                        <p
                            class="mt-1 text-xs sm:text-sm"
                            style="color: #d4a060"
                        >
                            Anggota Aktif
                        </p>
                    </div>
                    <div>
                        <p
                            class="text-3xl font-bold sm:text-4xl"
                            style="color: #e8a020; font-family: Georgia, serif"
                        >
                            {{ stats.total_genres }}
                        </p>
                        <p
                            class="mt-1 text-xs sm:text-sm"
                            style="color: #d4a060"
                        >
                            Penerbit
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════ TERBARU ══════════════════ -->
        <section class="px-6 py-16">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 flex items-center justify-between">
                    <div>
                        <p
                            class="mb-1 text-xs font-semibold tracking-widest uppercase"
                            style="color: #e8a020"
                        >
                            Terbaru
                        </p>
                        <h2
                            class="text-3xl font-bold"
                            style="color: #5c3d1e; font-family: Georgia, serif"
                        >
                            Buku Pilihan
                        </h2>
                    </div>
                    <a
                        href="/koleksi-buku"
                        class="flex items-center gap-1 text-sm font-medium hover:underline"
                        style="color: #c4781a"
                    >
                        Lihat semua <ChevronRight class="h-4 w-4" />
                    </a>
                </div>

                <div
                    class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
                >
                    <div
                        v-for="book in newBooks"
                        :key="book.id"
                        class="book-card-wrapper group cursor-pointer"
                        @click="openBookModal(book)"
                    >
                        <!-- 3D Book Model -->
                        <div
                            class="perspective-container mb-4 flex h-56 items-center justify-center"
                        >
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
                                                class="text-center text-xs font-bold"
                                            >
                                                {{ book.judul }}
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

                        <!-- Book Info -->
                        <div class="text-center md:text-left">
                            <h3
                                class="line-clamp-2 text-sm leading-tight font-bold group-hover:underline"
                                style="color: #5c3d1e"
                            >
                                {{ book.judul }}
                            </h3>
                            <p class="mt-1 text-xs" style="color: #9a7050">
                                {{ book.pengarang }}
                            </p>

                            <!-- Button Area -->
                            <div class="mt-3 flex flex-col gap-2">
                                <template v-if="borrowedBooksStatus[book.id]">
                                    <button
                                        v-if="
                                            borrowedBooksStatus[book.id] ===
                                            'dipinjam'
                                        "
                                        @click.stop="requestReturn"
                                        class="rounded-xl py-1.5 text-xs font-bold text-white transition hover:opacity-90"
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
                                        class="rounded-xl py-1.5 text-xs font-bold text-white opacity-80"
                                        style="background: #f59e0b"
                                    >
                                        ⏳ Menunggu Konfirmasi
                                    </button>
                                    <button
                                        v-else-if="
                                            borrowedBooksStatus[book.id] ===
                                            'menunggu_pengembalian'
                                        "
                                        disabled
                                        class="rounded-xl py-1.5 text-xs font-bold text-white opacity-80"
                                        style="background: #f59e0b"
                                    >
                                        ⏳ Menunggu Konfirmasi
                                    </button>
                                </template>
                                <template v-else>
                                    <button
                                        v-if="book.available"
                                        @click.stop="requestBorrow(book)"
                                        class="rounded-xl py-1.5 text-xs font-bold text-white transition group-hover:shadow-md hover:opacity-90"
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
                                        class="rounded-xl py-1.5 text-xs font-bold text-white opacity-40"
                                        style="background: #9a7050"
                                    >
                                        Habis
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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

                                    <button
                                        @click="toggleFavorite(selectedBook!)"
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border-2 transition hover:scale-105"
                                        :style="
                                            selectedBook?.is_favorited
                                                ? 'background:#FEE2E2; border-color:#FCA5A5;'
                                                : 'border-color:#F0D6A8; background:white;'
                                        "
                                    >
                                        <Heart
                                            class="h-5 w-5"
                                            :style="
                                                selectedBook?.is_favorited
                                                    ? 'color:#EF4444; fill:#EF4444'
                                                    : 'color:#E8A020'
                                            "
                                        />
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
    width: 130px;
    height: 190px;
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
    width: 130px;
    height: 190px;
    transform: translateZ(15px);
    border-radius: 2px 6px 6px 2px;
    overflow: hidden;
    box-shadow: inset 4px 0 10px rgba(0, 0, 0, 0.1);
}
/* Inside of back cover viewed from front */
.back {
    width: 130px;
    height: 190px;
    transform: rotateY(180deg) translateZ(15px);
    border-radius: 6px 2px 2px 6px;
}
/* Spine */
.left {
    width: 30px;
    height: 190px;
    transform: rotateY(-90deg) translateZ(15px);
    border-radius: 2px 0 0 2px;
}
/* Pages */
.right {
    width: 28px;
    height: 186px;
    transform: rotateY(90deg) translateZ(115px);
    top: 2px;
}
.top {
    width: 126px;
    height: 28px;
    transform: rotateX(90deg) translateZ(15px);
    left: 2px;
}
.bottom {
    width: 126px;
    height: 28px;
    transform: rotateX(-90deg) translateZ(175px);
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
