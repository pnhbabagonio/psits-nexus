<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <Form
            method="post"
            :action="route('register')"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="first_name">First Name <span class="text-red-500">*</span></Label>
                        <Input 
                            id="first_name" 
                            type="text" 
                            required 
                            autofocus 
                            :tabindex="1" 
                            autocomplete="given-name" 
                            name="first_name" 
                            placeholder="First name" 
                        />
                        <InputError :message="errors.first_name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="last_name">Last Name <span class="text-red-500">*</span></Label>
                        <Input 
                            id="last_name" 
                            type="text" 
                            required 
                            :tabindex="2" 
                            autocomplete="family-name" 
                            name="last_name" 
                            placeholder="Last name" 
                        />
                        <InputError :message="errors.last_name" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address <span class="text-red-500">*</span></Label>
                    <Input 
                        id="email" 
                        type="email" 
                        required 
                        :tabindex="3" 
                        autocomplete="email" 
                        name="email" 
                        placeholder="email@example.com" 
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="student_id">Student ID</Label>
                    <Input 
                        id="student_id" 
                        type="text" 
                        :tabindex="4" 
                        autocomplete="off" 
                        name="student_id" 
                        placeholder="Student ID" 
                    />
                    <InputError :message="errors.student_id" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="department">Department</Label>
                        <Input 
                            id="department" 
                            type="text" 
                            :tabindex="5" 
                            autocomplete="off" 
                            name="department" 
                            placeholder="Department" 
                        />
                        <InputError :message="errors.department" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="year_level">Year Level</Label>
                        <Select name="year_level" :tabindex="6">
                            <SelectTrigger>
                                <SelectValue placeholder="Select year level" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="1st Year">1st Year</SelectItem>
                                <SelectItem value="2nd Year">2nd Year</SelectItem>
                                <SelectItem value="3rd Year">3rd Year</SelectItem>
                                <SelectItem value="4th Year">4th Year</SelectItem>
                                <SelectItem value="5th Year">5th Year</SelectItem>
                                <SelectItem value="Graduate">Graduate</SelectItem>
                                <SelectItem value="Other">Other</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.year_level" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="contact_number">Contact Number</Label>
                    <Input 
                        id="contact_number" 
                        type="tel" 
                        :tabindex="7" 
                        autocomplete="tel" 
                        name="contact_number" 
                        placeholder="Contact number" 
                    />
                    <InputError :message="errors.contact_number" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password <span class="text-red-500">*</span></Label>
                    <Input 
                        id="password" 
                        type="password" 
                        required 
                        :tabindex="8" 
                        autocomplete="new-password" 
                        name="password" 
                        placeholder="Password" 
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password <span class="text-red-500">*</span></Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="9"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="10" :disabled="processing">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="11">Log in</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>