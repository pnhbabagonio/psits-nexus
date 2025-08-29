<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavUser from '@/components/NavUser.vue';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BarChart3,
    BookOpen,
    Calendar,
    ChevronRight,
    Clipboard,
    CreditCard,
    Globe,
    HelpCircle,
    LayoutGrid,
    PhilippinePeso,
    PiggyBank,
    User,
    Users,
} from 'lucide-vue-next';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
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

// Financial management sub-items
const financialManagementItems = [
    {
        title: 'Transactions',
        href: '/transactions',
        icon: PiggyBank,
    },
    {
        title: 'Financial Reports',
        href: '/financial-reports',
        icon: Clipboard,
    },
    {
        title: 'Expenses Tracking',
        href: '/expenses-tracking',
        icon: PhilippinePeso,
    },
];

// Event Management sub-items
const eventManagementItems = [
    {
        title: 'Events',
        href: '/events',
        icon: Calendar,
    },
    {
        title: 'Attendee Management',
        href: '/attendees',
        icon: Users,
    },
    {
        title: 'Event Analysis',
        href: '/analysis',
        icon: BarChart3,
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

                <!-- Collapsible Financial Management -->
                <Collapsible class="group/collapsible">
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton>
                                <CreditCard />
                                <span>Financial Management</span>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in financialManagementItems" :key="subItem.title">
                                    <SidebarMenuButton as-child size="sm">
                                        <Link :href="subItem.href">
                                            <component :is="subItem.icon" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <!-- Collapsible Event Management -->
                <Collapsible class="group/collapsible">
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton>
                                <Calendar />
                                <span>Event Management</span>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in eventManagementItems" :key="subItem.title">
                                    <SidebarMenuButton as-child size="sm">
                                        <Link :href="subItem.href">
                                            <component :is="subItem.icon" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

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
