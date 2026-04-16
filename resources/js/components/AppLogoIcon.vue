<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 40"
        fill="none"
        :class="className"
        v-bind="$attrs"
    >
        <!-- Halaman Belakang -->
        <rect x="6" y="6" width="28" height="28" rx="4" fill="#D97706" />
        <!-- Halaman Depan / Cover -->
        <path d="M6 10C6 7.79086 7.79086 6 10 6H34V34H10C7.79086 34 6 32.2091 6 30V10Z" fill="#F59E0B" />
        <!-- Garis-garis Buku -->
        <rect x="12" y="12" width="16" height="3" rx="1.5" fill="white" fill-opacity="0.3" />
        <rect x="12" y="19" width="12" height="3" rx="1.5" fill="white" fill-opacity="0.3" />
        <!-- Bookmark -->
        <path d="M26 6V18L30 15L34 18V6H26Z" fill="#FDE68A" />
        <!-- Shadow efek -->
        <path d="M10 6V34L6 30V10L10 6Z" fill="black" fill-opacity="0.1" />
    </svg>
</template>
