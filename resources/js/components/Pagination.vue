<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}>();
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center gap-1">
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="mb-1 mr-1 rounded border px-3 py-2 text-sm leading-4 text-gray-400"
                v-html="link.label"
            />
            <Link
                v-else
                class="mb-1 mr-1 rounded border px-3 py-2 text-sm leading-4 hover:bg-white focus:border-amber-500 focus:text-amber-500"
                :class="{ 'bg-amber-500 text-white hover:bg-amber-600': link.active }"
                :href="link.url"
            >
                <span v-html="link.label" />
            </Link>
        </template>
    </div>
</template>
