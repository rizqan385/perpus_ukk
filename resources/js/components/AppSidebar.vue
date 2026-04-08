<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, ArrowLeftRight, DollarSign, Book, ArrowBigDownDash } from 'lucide-vue-next';
import { computed } from 'vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = page.props.auth.user as any;

// Dynamic navigation based on user role
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    if (user?.role === 'admin') {
        items.push(
            {
                title: 'Buku',
                href: '/admin/books',
                icon: Book,
            },
            {
                title: 'Anggota',
                href: '/admin/members',
                icon: Users,
            },
            {
                title: 'Transaksi',
                href: '/admin/transactions',
                icon: ArrowLeftRight,
            },
            {
                title: 'Denda',
                href: '/admin/fines',
                icon: DollarSign,
            }
        );
    } else if (user?.role === 'siswa') {
        items.push(
            {
                title: 'Pinjam Buku',
                href: '/siswa/borrow',
                icon: BookOpen,
            },
            {
                title: 'Kembalikan Buku',
                href: '/siswa/returns',
                icon: ArrowBigDownDash,
            },
            {
                title: 'Denda',
                href: '/siswa/fines',
                icon: DollarSign,
            }
        );
    }

    return items;
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
