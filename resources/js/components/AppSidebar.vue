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
import { BookOpen, LayoutGrid, CreditCard, Calendar, BarChart3, HelpCircle, Globe, User, Users, UserPlus, Shield, ChevronRight, PiggyBank, PhilippinePeso, Clipboard } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
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

// User Management sub-items
// const userManagementItems = [
//     {
//         title: 'All Users',
//         href: '/users',
//         icon: Users,
//     },
//     {
//         title: 'Add User',
//         href: '/users/create',
//         icon: UserPlus,
//     },
//     {
//         title: 'User Roles',
//         href: '/users/roles',
//         icon: Shield,
//     },
// ];

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
]

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
                
                <!-- Collapsible User Management
                <Collapsible class="group/collapsible">
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton>
                                <User />
                                <span>User Management</span>
                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem v-for="subItem in userManagementItems" :key="subItem.title">
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
                </Collapsible> -->

                <!-- User Management (simple tab, no collapsible) -->
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