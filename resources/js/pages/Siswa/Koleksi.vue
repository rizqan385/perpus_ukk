<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import {
    Heart,
    Search,
    BookOpen,
    Clock,
    Calendar,
    Star,
    X,
    ChevronRight,
    Check,
} from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';
import Pagination from '@/components/Pagination.vue';
import SiswaLayout from '@/layouts/SiswaLayout.vue';

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

interface PaginationData {
    data: Book[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    total: number;
    current_page: number;
    last_page: number;
}

const props = defineProps<{
    books: PaginationData;
    search: string;
    status: string;
    borrowedBooksStatus: Record<number, string>;
}>();

const page = usePage();
const user = computed(() => (page.props as any).auth?.user ?? null);

const searchQuery = ref(props.search);
const activeTab = ref(props.status || 'semua');

const tabs = [
    { id: 'semua', label: 'Semua' },
    { id: 'tersedia', label: 'Tersedia' },
    { id: 'dipinjam', label: 'Dipinjam Saya' },
];

const doSearch = () => {
    router.get(
        '/koleksi-buku',
        { 
            search: searchQuery.value,
            status: activeTab.value
        },
        { 
            preserveState: true,
            preserveScroll: true,
            replace: true
        },
    );
};

watch(activeTab, (newVal) => {
    doSearch();
});

let searchTimeout: any = null;
watch(searchQuery, (newVal) => {
    if (newVal === '') {
        doSearch();
        return;
    }
    
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        doSearch();
    }, 500);
});

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
    <Head title="Koleksi Buku" />
    <SiswaLayout>
        <!-- ══════════════════ HERO HEADER ══════════════════ -->
        <section
            class="relative overflow-hidden py-16"
            style="background: #5c3d1e"
        >
            <div
                class="absolute top-0 right-0 h-64 w-64 translate-x-1/4 -translate-y-1/2 rounded-full"
                style="background: rgba(232, 160, 32, 0.1)"
            ></div>
            <div
                class="absolute bottom-0 left-1/4 h-40 w-40 translate-y-1/2 rounded-full"
                style="background: rgba(232, 160, 32, 0.05)"
            ></div>

            <div class="relative mx-auto max-w-7xl px-6">
                <!-- Breadcrumb -->
                <div
                    class="mb-4 flex items-center gap-2 text-sm"
                    style="color: #d4a060"
                >
                    <Link href="/" class="transition-colors hover:text-white"
                        >Beranda</Link
                    >
                    <ChevronRight class="h-4 w-4" />
                    <span class="font-bold text-white">Koleksi Buku</span>
                </div>

                <div
                    class="flex flex-col justify-between gap-8 md:flex-row md:items-end"
                >
                    <div>
                        <h1
                            class="mb-3 flex items-center gap-3 text-4xl font-bold text-white"
                            style="font-family: Georgia, serif"
                        >
                            <div
                                class="rounded-xl p-2.5"
                                style="
                                    background: linear-gradient(
                                        135deg,
                                        #e8a020,
                                        #c4781a
                                    );
                                "
                            >
                                <BookOpen class="h-8 w-8 text-white" />
                            </div>
                            Koleksi Buku
                        </h1>
                        <p class="text-lg" style="color: #d4a060">
                            {{ books.total }} judul buku tersedia untuk kamu
                        </p>
                    </div>

                    <!-- Search Input -->
                    <div class="flex w-full gap-3 md:w-96">
                        <div class="relative flex-1">
                            <Search
                                class="absolute top-1/2 left-4 h-5 w-5 -translate-y-1/2"
                                style="color: #9a7050"
                            />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari judul, pengarang, penerbit..."
                                class="w-full rounded-2xl border-none py-3.5 pr-4 pl-12 text-sm font-medium placeholder-gray-400 transition-all outline-none focus:ring-4 focus:ring-[#e8a020]/20"
                                style="
                                    background: #fff8f0;
                                    color: #5c3d1e;
                                "
                            />
                        </div>
                        <button
                            @click="doSearch"
                            class="rounded-2xl px-6 py-3.5 text-sm font-bold text-white shadow-lg transition-transform hover:scale-105 active:scale-95"
                            style="background: #e8a020"
                        >
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══════════════════ FILTER TABS ══════════════════ -->
        <div
            class="sticky top-0 z-30 border-b bg-white/80 backdrop-blur-md"
            style="border-color: #f0d6a8"
        >
            <div
                class="no-scrollbar mx-auto flex max-w-7xl items-center gap-6 overflow-x-auto px-6 py-4"
            >
                <span
                    class="flex items-center gap-2 font-bold"
                    style="color: #7a5230"
                    ><Filter class="h-4 w-4" /> Filter:</span
                >
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    class="rounded-full px-5 py-2 text-sm font-bold whitespace-nowrap transition-all"
                    :style="
                        activeTab === tab.id
                            ? 'background: #E8A020; color: white; box-shadow: 0 4px 12px rgba(232,160,32,0.3);'
                            : 'background: #FFF8F0; color: #9A7050; border: 1px solid #F0D6A8; hover:background: #FFE0B2;'
                    "
                >
                    {{ tab.label }}
                </button>

                <div
                    class="ml-auto text-sm font-semibold"
                    style="color: #9a7050"
                >
                    {{ books.total }} buku
                </div>
            </div>
        </div>

        <!-- ══════════════════ GRID BUKU ══════════════════ -->
        <main class="min-h-screen py-10" style="background: #fff8f0">
            <div class="mx-auto max-w-7xl px-6">
                <!-- Empty State -->
                <div
                    v-if="books.data.length === 0"
                    class="flex flex-col items-center justify-center rounded-3xl border-2 border-dashed py-24 text-center"
                    style="border-color: #f0d6a8; background: white"
                >
                    <div
                        class="mb-4 flex h-24 w-24 items-center justify-center rounded-full"
                        style="background: #fff3e0"
                    >
                        <BookOpen class="h-10 w-10" style="color: #c4781a" />
                    </div>
                    <h3 class="text-xl font-bold" style="color: #5c3d1e">
                        Tidak ada buku ditemukan
                    </h3>
                    <p class="mt-2" style="color: #9a7050">
                        Coba dengan kata kunci pencarian atau filter yang lain.
                    </p>
                </div>

                <!-- Grid -->
                <div
                    v-else
                    class="grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6"
                >
                    <div
                        v-for="book in books.data"
                        :key="book.id"
                        class="book-card-wrapper group cursor-pointer"
                        @click="openBookModal(book)"
                    >
                        <!-- 3D Book Cover -->
                        <div
                            class="perspective-container mb-4 flex h-52 items-center justify-center"
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

                <!-- Pagination -->
                <div v-if="books.last_page > 1" class="mt-16 flex justify-center">
                    <Pagination :links="books.links" />
                </div>
            </div>
        </main>

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

/* Hide scrollbar */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
