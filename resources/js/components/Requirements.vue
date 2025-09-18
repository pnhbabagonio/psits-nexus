<script setup lang="ts">
import { ref, computed } from "vue"
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { Textarea } from '@/components/ui/textarea'
import { Progress } from '@/components/ui/progress'
import {
    Search,
    Plus,
    Filter,
    LayoutGrid,
    List,
    ChevronDown,
    Pencil,
    Trash2,
    Calendar,
    Users,
    DollarSign,
    CheckCircle,
    Clock,
    AlertTriangle,
} from "lucide-vue-next"

// --- Types ---
interface Requirement {
    id: number
    title: string
    description: string
    amount: number
    deadline: string
    totalUsers: number
    paid: number
    unpaid: number
}

// --- State ---
const showModal = ref(false)
const editingRequirement = ref<Requirement | null>(null)

const search = ref("")
const isFilterOpen = ref(false)
const isGridView = ref(true)
const requirementFilter = ref("All")

const filterOptions = ["All", "Pending", "Overdue", "Done"]

// Pagination
const rowsPerPage = ref(10)
const currentPage = ref(1)

// Mock Data
const requirements = ref<Requirement[]>([
    {
        id: 1,
        title: "Membership Fee - Semester 1",
        description: "Membership fee for the first semester.",
        amount: 500,
        deadline: "2025-10-15",
        totalUsers: 150,
        paid: 120,
        unpaid: 30,
    },
    {
        id: 2,
        title: "Event Fee - Annual Convention",
        description: "Payment required for annual convention participation.",
        amount: 300,
        deadline: "2025-07-30",
        totalUsers: 150,
        paid: 80,
        unpaid: 70,
    },
    {
        id: 3,
        title: "Sports Fest Fee",
        description: "Fee for joining the sports festival activities.",
        amount: 200,
        deadline: "2025-08-15",
        totalUsers: 150,
        paid: 150,
        unpaid: 0,
    },
])

// --- Modal Form Data ---
const newRequirement = ref({
    title: "",
    description: "",
    amount: 0,
    deadline: "",
    totalUsers: 0,
})

// --- Methods ---
function openAddModal() {
    editingRequirement.value = null
    newRequirement.value = {
        title: "",
        description: "",
        amount: 0,
        deadline: "",
        totalUsers: 0,
    }
    showModal.value = true
}

function openEditModal(req: Requirement) {
    editingRequirement.value = req
    newRequirement.value = {
        title: req.title,
        description: req.description,
        amount: req.amount,
        deadline: req.deadline,
        totalUsers: req.totalUsers,
    }
    showModal.value = true
}

function saveRequirement() {
    if (
        !newRequirement.value.title ||
        !newRequirement.value.amount ||
        !newRequirement.value.deadline ||
        !newRequirement.value.totalUsers
    ) {
        alert("Please fill in all fields")
        return
    }

    if (editingRequirement.value) {
        // Update existing requirement
        Object.assign(editingRequirement.value, {
            ...editingRequirement.value,
            ...newRequirement.value,
            unpaid: newRequirement.value.totalUsers - editingRequirement.value.paid,
        })
    } else {
        // Add new requirement
        requirements.value.push({
            id: requirements.value.length + 1,
            title: newRequirement.value.title,
            description: newRequirement.value.description,
            amount: newRequirement.value.amount,
            deadline: newRequirement.value.deadline,
            totalUsers: newRequirement.value.totalUsers,
            paid: 0,
            unpaid: newRequirement.value.totalUsers,
        })
    }

    showModal.value = false
}

function deleteRequirement(id: number) {
    requirements.value = requirements.value.filter((r) => r.id !== id)
}

function clearFilters() {
    requirementFilter.value = 'All'
    search.value = ''
}

// --- Status Logic ---
function getStatus(req: Requirement): "Pending" | "Overdue" | "Done" {
    const now = new Date()
    const deadline = new Date(req.deadline)

    if (req.paid < req.totalUsers) {
        return deadline >= now ? "Pending" : "Overdue"
    }
    return "Done"
}

function getStatusVariant(status: string): "default" | "secondary" | "destructive" | "outline" {
    switch (status) {
        case "Pending": return "outline"
        case "Overdue": return "destructive"
        case "Done": return "default"
        default: return "secondary"
    }
}

function getStatusIcon(status: string) {
    switch (status) {
        case "Pending": return Clock
        case "Overdue": return AlertTriangle
        case "Done": return CheckCircle
        default: return Clock
    }
}

// --- Filtered + Paginated List ---
const filteredRequirements = computed(() => {
    return requirements.value.filter((req) => {
        const status = getStatus(req)
        const matchesSearch = req.title
            .toLowerCase()
            .includes(search.value.toLowerCase())

        if (requirementFilter.value === "All") return matchesSearch
        return status === requirementFilter.value && matchesSearch
    })
})

const paginatedRequirements = computed(() => {
    const start = (currentPage.value - 1) * rowsPerPage.value
    return filteredRequirements.value.slice(
        start,
        start + rowsPerPage.value
    )
})

const totalPages = computed(() => {
    return Math.ceil(filteredRequirements.value.length / rowsPerPage.value)
})
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Payment Requirements</h1>
                <p class="text-muted-foreground">Manage and track payment requirements for members</p>
            </div>
            <Dialog v-model:open="showModal">
                <DialogTrigger as-child>
                    <Button @click="openAddModal" class="gap-2">
                        <Plus class="h-4 w-4" />
                        Add Requirement
                    </Button>
                </DialogTrigger>
            </Dialog>
        </div>

        <!-- Search + Filter + View Toggle -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="relative flex-1 min-w-[300px]">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="search"
                    placeholder="Search requirements..."
                    class="pl-9"
                />
            </div>

            <!-- Filter Dropdown -->
            <DropdownMenu v-model:open="isFilterOpen">
                <DropdownMenuTrigger as-child>
                    <Button variant="outline" class="gap-2">
                        <Filter class="h-4 w-4" />
                        {{ requirementFilter }}
                        <ChevronDown 
                            class="h-4 w-4 transition-transform"
                            :class="{ 'rotate-180': isFilterOpen }"
                        />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuItem 
                        v-for="option in filterOptions" 
                        :key="option"
                        @click="requirementFilter = option"
                        :class="{ 'bg-accent': requirementFilter === option }"
                    >
                        {{ option }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <!-- Clear Filters -->
            <Button
                v-if="requirementFilter !== 'All' || search"
                @click="clearFilters"
                variant="destructive"
                size="sm"
            >
                Clear Filters
            </Button>

            <!-- Toggle View -->
            <Button
                @click="isGridView = !isGridView"
                variant="outline"
                size="icon"
            >
                <component :is="isGridView ? List : LayoutGrid" class="h-4 w-4" />
            </Button>
        </div>

        <!-- Grid View -->
        <div v-if="isGridView" class="grid gap-6 md:grid-cols-2">
            <Card v-for="req in paginatedRequirements" :key="req.id" class="overflow-hidden">
                <CardHeader class="pb-4">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <CardTitle class="text-lg">{{ req.title }}</CardTitle>
                            <CardDescription>{{ req.description }}</CardDescription>
                        </div>
                        <Badge 
                            :variant="getStatusVariant(getStatus(req))"
                            class="gap-1"
                        >
                            <component :is="getStatusIcon(getStatus(req))" class="h-3 w-3" />
                            {{ getStatus(req) }}
                        </Badge>
                    </div>
                </CardHeader>
                
                <CardContent class="space-y-4">
                    <!-- Amount and Deadline -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center gap-2 text-muted-foreground">
                            <DollarSign class="h-4 w-4" />
                            <span>₱{{ req.amount.toLocaleString() }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-muted-foreground">
                            <Calendar class="h-4 w-4" />
                            <span>{{ new Date(req.deadline).toLocaleDateString() }}</span>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Progress</span>
                            <span class="font-medium">{{ Math.round((req.paid / req.totalUsers) * 100) }}%</span>
                        </div>
                        <Progress 
                            :value="(req.paid / req.totalUsers) * 100" 
                            class="h-2"
                        />
                        <div class="flex justify-between text-xs text-muted-foreground">
                            <span>{{ req.paid }}/{{ req.totalUsers }} paid</span>
                            <span>{{ req.unpaid }} remaining</span>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-lg bg-blue-50 dark:bg-blue-950 p-3 text-center">
                            <div class="flex items-center justify-center gap-1 text-blue-600 dark:text-blue-400">
                                <Users class="h-4 w-4" />
                                <span class="font-semibold text-lg">{{ req.totalUsers }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground">Total</p>
                        </div>
                        <div class="rounded-lg bg-green-50 dark:bg-green-950 p-3 text-center">
                            <div class="flex items-center justify-center gap-1 text-green-600 dark:text-green-400">
                                <CheckCircle class="h-4 w-4" />
                                <span class="font-semibold text-lg">{{ req.paid }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground">Paid</p>
                        </div>
                        <div class="rounded-lg bg-red-50 dark:bg-red-950 p-3 text-center">
                            <div class="flex items-center justify-center gap-1 text-red-600 dark:text-red-400">
                                <Clock class="h-4 w-4" />
                                <span class="font-semibold text-lg">{{ req.unpaid }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground">Unpaid</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-2 pt-2">
                        <Button 
                            @click="openEditModal(req)"
                            variant="outline"
                            size="sm"
                            class="gap-2"
                        >
                            <Pencil class="h-4 w-4" />
                            Edit
                        </Button>
                        <Button 
                            @click="deleteRequirement(req.id)"
                            variant="destructive"
                            size="sm"
                            class="gap-2"
                        >
                            <Trash2 class="h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- List View -->
        <Card v-else>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b">
                        <tr class="text-left">
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Title</th>
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Amount</th>
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Deadline</th>
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Progress</th>
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Total</th>
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Paid</th>
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Unpaid</th>
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-sm font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr 
                            v-for="req in paginatedRequirements" 
                            :key="req.id"
                            class="border-b hover:bg-muted/50"
                        >
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium">{{ req.title }}</p>
                                    <p class="text-xs text-muted-foreground">{{ req.description }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium">₱{{ req.amount.toLocaleString() }}</td>
                            <td class="px-4 py-3 text-sm">{{ new Date(req.deadline).toLocaleDateString() }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 min-w-[120px]">
                                    <Progress 
                                        :value="(req.paid / req.totalUsers) * 100" 
                                        class="flex-1 h-2"
                                    />
                                    <span class="text-xs text-muted-foreground whitespace-nowrap">
                                        {{ req.paid }}/{{ req.totalUsers }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-blue-600 dark:text-blue-400 font-medium">{{ req.totalUsers }}</td>
                            <td class="px-4 py-3 text-green-600 dark:text-green-400 font-medium">{{ req.paid }}</td>
                            <td class="px-4 py-3 text-red-600 dark:text-red-400 font-medium">{{ req.unpaid }}</td>
                            <td class="px-4 py-3">
                                <Badge 
                                    :variant="getStatusVariant(getStatus(req))"
                                    class="gap-1"
                                >
                                    <component :is="getStatusIcon(getStatus(req))" class="h-3 w-3" />
                                    {{ getStatus(req) }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-1">
                                    <Button 
                                        @click="openEditModal(req)"
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8"
                                    >
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button 
                                        @click="deleteRequirement(req.id)"
                                        variant="ghost"
                                        size="icon"
                                        class="h-8 w-8 text-red-500 hover:text-red-600"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <!-- Add/Edit Modal -->
        <Dialog v-model:open="showModal">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingRequirement ? "Edit Requirement" : "Add New Requirement" }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ editingRequirement ? "Update the requirement details below." : "Fill in the details for the new payment requirement." }}
                    </DialogDescription>
                </DialogHeader>
                
                <form @submit.prevent="saveRequirement" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="title">Title</Label>
                        <Input
                            id="title"
                            v-model="newRequirement.title"
                            placeholder="Enter requirement title"
                        />
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="description">Description</Label>
                        <Textarea
                            id="description"
                            v-model="newRequirement.description"
                            placeholder="Enter requirement description"
                            rows="3"
                        />
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="amount">Amount (₱)</Label>
                            <Input
                                id="amount"
                                v-model.number="newRequirement.amount"
                                type="number"
                                placeholder="0"
                                min="0"
                                step="0.01"
                            />
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="totalUsers">Total Users</Label>
                            <Input
                                id="totalUsers"
                                v-model.number="newRequirement.totalUsers"
                                type="number"
                                placeholder="0"
                                min="1"
                            />
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="deadline">Deadline</Label>
                        <Input
                            id="deadline"
                            v-model="newRequirement.deadline"
                            type="date"
                        />
                    </div>
                </form>
                
                <DialogFooter>
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="showModal = false"
                    >
                        Cancel
                    </Button>
                    <Button @click="saveRequirement">
                        {{ editingRequirement ? "Save Changes" : "Add Requirement" }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Empty State -->
        <div v-if="filteredRequirements.length === 0" class="text-center py-12">
            <div class="mx-auto mb-4 h-12 w-12 rounded-full bg-muted flex items-center justify-center">
                <DollarSign class="h-6 w-6 text-muted-foreground" />
            </div>
            <h3 class="text-lg font-medium mb-2">No requirements found</h3>
            <p class="text-muted-foreground mb-4">
                {{ search || requirementFilter !== 'All' 
                    ? 'Try adjusting your filters or search terms.' 
                    : 'Get started by adding your first payment requirement.'
                }}
            </p>
            <Button v-if="!search && requirementFilter === 'All'" @click="openAddModal" class="gap-2">
                <Plus class="h-4 w-4" />
                Add First Requirement
            </Button>
        </div>

        <!-- Pagination Footer -->
        <div v-if="filteredRequirements.length > 0" class="flex items-center justify-between pt-4 border-t">
            <div class="text-sm text-muted-foreground">
                Showing {{ ((currentPage - 1) * rowsPerPage) + 1 }} to 
                {{ Math.min(currentPage * rowsPerPage, filteredRequirements.length) }} of 
                {{ filteredRequirements.length }} requirements
            </div>

            <div class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">Rows per page:</span>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" size="sm">
                            {{ rowsPerPage }}
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem @click="rowsPerPage = 10">10</DropdownMenuItem>
                        <DropdownMenuItem @click="rowsPerPage = 25">25</DropdownMenuItem>
                        <DropdownMenuItem @click="rowsPerPage = 50">50</DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                <Button
                    :disabled="currentPage <= 1"
                    @click="currentPage--"
                    variant="outline"
                    size="sm"
                >
                    Previous
                </Button>
                
                <div class="flex items-center gap-1">
                    <Button
                        v-for="page in Math.min(totalPages, 5)"
                        :key="page"
                        :variant="currentPage === page ? 'default' : 'outline'"
                        size="sm"
                        class="w-8 h-8"
                        @click="currentPage = page"
                    >
                        {{ page }}
                    </Button>
                </div>
                
                <Button
                    :disabled="currentPage >= totalPages"
                    @click="currentPage++"
                    variant="outline"
                    size="sm"
                >
                    Next
                </Button>
            </div>
        </div>
    </div>
</template>