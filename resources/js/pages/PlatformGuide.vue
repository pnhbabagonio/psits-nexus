<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { BookOpen, Calendar, Clipboard, LayoutGrid, LogIn, LogOut, PhilippinePeso, PiggyBank, Users } from 'lucide-vue-next';
</script>

<template>
    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="space-y-2 text-center">
            <BookOpen class="mx-auto h-12 w-12 text-blue-600" />
            <h1 class="text-3xl font-bold">Platform Guide</h1>
            <p class="text-gray-600">Learn how to use <span class="font-semibold">PSITS Nexus</span> step by step</p>
        </div>

        <!-- Overview Card -->
        <Card>
            <CardHeader>
                <CardTitle>About PSITS Nexus</CardTitle>
            </CardHeader>
            <CardContent class="text-gray-700">
                PSITS Nexus is a digital platform for the Philippine Society of Information Technology Students. It streamlines payment recording,
                automates financial processes, and provides engagement analytics through QR-based technology.
            </CardContent>
        </Card>

        <!-- Step by Step Guide -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"> <LogIn class="h-5 w-5 text-green-600" /> Login / Register </CardTitle>
                </CardHeader>
                <CardContent>Use your registered account to access the platform securely.</CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"> <LayoutGrid class="h-5 w-5 text-blue-600" /> Dashboard </CardTitle>
                </CardHeader>
                <CardContent>Get an overview of your account and quick access to modules.</CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"> <PiggyBank class="h-5 w-5 text-purple-600" /> Transactions </CardTitle>
                </CardHeader>
                <CardContent>Record and track payments using the QR-based system.</CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"> <PhilippinePeso class="h-5 w-5 text-yellow-600" /> Expense Tracking </CardTitle>
                </CardHeader>
                <CardContent>Monitor organization expenses and maintain financial transparency.</CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"> <Clipboard class="h-5 w-5 text-red-600" /> Financial Reports </CardTitle>
                </CardHeader>
                <CardContent>Generate analytics and reports for smarter decision-making.</CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"> <Users class="h-5 w-5 text-indigo-600" /> User Management </CardTitle>
                </CardHeader>
                <CardContent>Manage student records, memberships, and roles (Admin only).</CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"> <Calendar class="h-5 w-5 text-pink-600" /> Event Management </CardTitle>
                </CardHeader>
                <CardContent>Organize events, track attendance, and analyze engagement.</CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2"> <LogOut class="h-5 w-5 text-gray-600" /> Logout </CardTitle>
                </CardHeader>
                <CardContent>Always log out after using the platform to secure your account.</CardContent>
            </Card>
        </div>
    </div>
</template>
