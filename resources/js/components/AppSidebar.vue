<script setup lang="ts">
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
    SidebarMenuSub,
    SidebarMenuSubItem
} from '@/components/ui/sidebar';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, CreditCard, Calendar, BarChart3, HelpCircle, Globe, User, Users, UserPlus, Shield, ChevronRight } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Payments',
        href: '/payments',
        icon: CreditCard,
    },
    {
        title: 'Events',
        href: '/events',
        icon: Calendar,
    },
    {
        title: 'Reports',
        href: '/reports',
        icon: BarChart3,
    },
    {
        title: 'Help & Support',
        href: '/support',
        icon: HelpCircle,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'PSITS Portal',
        href: 'https://psits.org',
        icon: Globe,
    },
    {
        title: 'Platform Guide',
        href: '/guide',
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
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent>
            <SidebarMenu>
                <!-- Dashboard item -->
                <SidebarMenuItem>
                    <SidebarMenuButton as-child>
                        <Link :href="mainNavItems[0].href">
                            <component :is="mainNavItems[0].icon" />
                            <span>{{ mainNavItems[0].title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

                <!-- User Management -->
                <SidebarMenuItem>
                    <SidebarMenuButton as-child>
                        <Link :href="route('user-management')">
                            <User />
                            <span>User Management</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>

                <!-- Remaining nav items -->
                <SidebarMenuItem v-for="item in mainNavItems.slice(1)" :key="item.title">
                    <SidebarMenuButton as-child>
                        <Link :href="item.href">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarContent>
        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>