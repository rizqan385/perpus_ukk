<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Users, ArrowLeftRight, DollarSign, Book, ArrowBigDownDash, Settings, BookOpen, ClipboardList, Heart } from 'lucide-vue-next';
import { computed } from 'vue';
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
const pendingBorrowCount = computed(() => (page.props as any).pendingBorrowCount ?? 0);

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
                title: 'Persetujuan Pinjam',
                href: '/admin/borrow-approvals',
                icon: ClipboardList,
                badge: pendingBorrowCount.value > 0 ? String(pendingBorrowCount.value) : undefined,
            },
            {
                title: 'Denda',
                href: '/admin/fines',
                icon: DollarSign,
            },
            {
                title: 'Pengaturan',
                href: '/admin/settings',
                icon: Settings,
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
                title: 'Favorit',
                href: '/siswa/favorites',
                icon: Heart,
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
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
