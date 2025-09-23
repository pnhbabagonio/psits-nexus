<script setup lang="ts">
import { ref, computed } from "vue"
import {
    Search,
    Plus,
    Filter,
    ChevronDown,
    X,
    Pencil,
    Trash2,
    QrCode,
} from "lucide-vue-next"
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

// --- Types ---
interface PaymentRecord {
    id: number
    firstName: string
    middleName: string
    lastName: string
    requirement: string
    amount: number
    date: string
    method?: string
    status: "Paid" | "Unpaid"
}

// --- State ---
const showModal = ref(false)
const editingRecord = ref<PaymentRecord | null>(null)

const search = ref("")
const isFilterOpen = ref(false)
const paymentFilter = ref("All")
const filterOptions = ["All", "Paid", "Unpaid"]

// Mock Data
const records = ref<PaymentRecord[]>([
    {
        id: 1,
        firstName: "John",
        middleName: "M.",
        lastName: "Doe",
        requirement: "Membership Fee - Semester 1",
        amount: 500,
        date: "2025-09-01",
        method: "GCash",
        status: "Paid",
    },
    {
        id: 2,
        firstName: "Jane",
        middleName: "A.",
        lastName: "Smith",
        requirement: "Event Fee - Annual Convention",
        amount: 300,
        date: "",
        status: "Unpaid",
    },
])

// --- Modal Form Data ---
const newRecord = ref({
    firstName: "",
    middleName: "",
    lastName: "",
    requirement: "",
    amount: 0,
    date: "",
    method: "",
    status: "Unpaid" as "Paid" | "Unpaid",
})

// Requirement Options (dropdown)
const requirementOptions = [
    { title: "Membership Fee - Semester 1", amount: 500 },
    { title: "Event Fee - Annual Convention", amount: 300 },
    { title: "Sports Fest Fee", amount: 200 },
]

// --- Methods ---
function openAddModal() {
    editingRecord.value = null
    newRecord.value = {
        firstName: "",
        middleName: "",
        lastName: "",
        requirement: "",
        amount: 0,
        date: "",
        method: "",
        status: "Unpaid",
    }
    showModal.value = true
}

function openEditModal(record: PaymentRecord) {
    editingRecord.value = record
    newRecord.value = {
        firstName: record.firstName,
        middleName: record.middleName,
        lastName: record.lastName,
        requirement: record.requirement,
        amount: record.amount,
        date: record.date,
        method: record.method || "",
        status: record.status,
    }
    showModal.value = true
}

function saveRecord() {
    if (!newRecord.value.firstName || !newRecord.value.lastName || !newRecord.value.requirement) {
        alert("Please fill in all required fields")
        return
    }

    if (editingRecord.value) {
        Object.assign(editingRecord.value, { ...newRecord.value })
    } else {
        records.value.push({
        id: records.value.length + 1,
        ...newRecord.value,
        })
    }

    showModal.value = false
}

function deleteRecord(id: number) {
    records.value = records.value.filter((r) => r.id !== id)
}

// --- Filtered List ---
const filteredRecords = computed(() => {
    return records.value.filter((rec) => {
        const matchesSearch =
        `${rec.firstName} ${rec.middleName} ${rec.lastName} ${rec.requirement}`
            .toLowerCase()
            .includes(search.value.toLowerCase())

        if (paymentFilter.value === "All") return matchesSearch
        return rec.status === paymentFilter.value && matchesSearch
    })
})

// --- QR Scan (instant add record) ---
function scanQR() {
    const scannedData = {
        firstName: "Scanned",
        middleName: "QR",
        lastName: "Student",
        requirement: "Membership Fee - Semester 1",
        amount: 500,
    }

    const newId = records.value.length + 1

    records.value.push({
        id: newId,
        ...scannedData,
        status: "Paid",
        method: "QR Scan",
        date: new Date().toISOString().split("T")[0],
    })

    alert("QR payment recorded instantly!")
}
</script>

<template>
    <div class="p-6 text-foreground">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold">Payment Records</h2>
                <p class="text-muted-foreground">Manage and track all payment transactions</p>
            </div>
            <div class="flex gap-2">
                <Button
                    @click="scanQR"
                    variant="outline"
                    class="gap-2"
                >
                    <QrCode class="w-4 h-4" /> Scan QR
                </Button>
                <Button
                    @click="openAddModal"
                    class="gap-2"
                >
                    <Plus class="w-4 h-4" /> Add Record
                </Button>
            </div>
        </div>

        <!-- Search + Filter -->
        <div class="flex items-center gap-3 mb-6">
        
            <!-- Search -->
            <div
                class="flex items-center flex-1 bg-muted border border-border rounded-lg px-3 py-2"
            >
                <Search class="w-4 h-4 text-muted-foreground" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search name..."
                    class="ml-2 flex-1 bg-transparent text-sm focus:outline-none placeholder:text-muted-foreground"
                />
            </div>

            <!-- Filter -->
            <div class="relative">
                <Button
                    @click="isFilterOpen = !isFilterOpen"
                    variant="outline"
                    class="gap-2"
                >
                    <Filter class="w-4 h-4" />
                    <span>{{ paymentFilter }}</span>
                    <ChevronDown
                        class="w-4 h-4 transition-transform"
                        :class="{ 'rotate-180': isFilterOpen }"
                    />
                </Button>

                <!-- Dropdown -->
                <div
                v-if="isFilterOpen"
                class="absolute right-0 mt-2 w-32 bg-card border border-border rounded-lg shadow-lg z-10 overflow-hidden"
                >
                    <button
                        v-for="option in filterOptions"
                        :key="option"
                        @click="
                        paymentFilter = option;
                        isFilterOpen = false
                        "
                        class="w-full text-left px-3 py-2 text-sm hover:bg-muted"
                        :class="{ 'bg-muted': paymentFilter === option }"
                    >
                        {{ option }}
                    </button>
                </div>
            </div>

            <!-- Clear Filter -->
            <Button
                v-if="paymentFilter !== 'All' || search"
                @click="paymentFilter = 'All'; search = ''"
                variant="outline"
            >
                Clear Filters
            </Button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-border bg-card">
            <table class="w-full text-sm text-left">
                <thead class="bg-muted uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-muted-foreground">Student</th>
                        <th class="px-4 py-3 text-muted-foreground">Requirement</th>
                        <th class="px-4 py-3 text-muted-foreground">Amount</th>
                        <th class="px-4 py-3 text-muted-foreground">Date</th>
                        <th class="px-4 py-3 text-muted-foreground">Method</th>
                        <th class="px-4 py-3 text-muted-foreground">Status</th>
                        <th class="px-4 py-3 text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="record in filteredRecords"
                        :key="record.id"
                        class="border-t border-border hover:bg-muted/50 transition-colors"
                    >
                        <td class="px-4 py-3 font-medium">
                        {{ record.firstName }} {{ record.middleName }} {{ record.lastName }}
                        </td>
                        <td class="px-4 py-3">{{ record.requirement }}</td>
                        <td class="px-4 py-3">₱{{ record.amount }}</td>
                        <td class="px-4 py-3">{{ record.date || "—" }}</td>
                        <td class="px-4 py-3">{{ record.status === "Paid" ? record.method : "—" }}</td>
                        <td class="px-4 py-3">
                            <Badge
                                :class="{
                                'bg-green-100 text-green-800 hover:bg-green-100 dark:bg-green-900 dark:text-green-300': record.status === 'Paid',
                                'bg-red-100 text-red-800 hover:bg-red-100 dark:bg-red-900 dark:text-red-300': record.status === 'Unpaid',
                                }"
                            >
                                {{ record.status }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <Button
                                @click="openEditModal(record)"
                                variant="ghost"
                                size="icon"
                            >
                                <Pencil class="w-4 h-4" />
                            </Button>
                            <Button
                                @click="deleteRecord(record.id)"
                                variant="ghost"
                                size="icon"
                            >
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Empty state -->
            <div v-if="filteredRecords.length === 0" class="text-center py-8 text-muted-foreground">
                <Search class="h-12 w-12 mx-auto mb-4 opacity-50" />
                <p>No payment records found</p>
            </div>
        </div>

        <!-- Add/Edit Record Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-background/80 backdrop-blur-sm z-50"
        >
            <div class="bg-card border border-border rounded-xl p-6 w-full max-w-md shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">
                        {{ editingRecord ? "Edit Payment Record" : "Add Payment Record" }}
                    </h3>
                    <Button @click="showModal = false" variant="ghost" size="icon">
                        <X class="w-5 h-5" />
                    </Button>
                </div>
                <form @submit.prevent="saveRecord" class="space-y-4">
                    <div>
                        <label class="block text-sm text-muted-foreground mb-1">First Name</label>
                        <input
                            v-model="newRecord.firstName"
                            type="text"
                            class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-muted-foreground mb-1">Middle Name</label>
                        <input
                            v-model="newRecord.middleName"
                            type="text"
                            class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-muted-foreground mb-1">Last Name</label>
                        <input
                            v-model="newRecord.lastName"
                            type="text"
                            class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-muted-foreground mb-1">Requirement</label>
                        <select
                            v-model="newRecord.requirement"
                            @change="
                                newRecord.amount =
                                requirementOptions.find((r) => r.title === newRecord.requirement)?.amount || 0
                            "
                            class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm"
                        >
                            <option disabled value="">Select requirement</option>
                            <option v-for="req in requirementOptions" :key="req.title" :value="req.title">
                                {{ req.title }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-muted-foreground mb-1">Amount</label>
                        <input
                            v-model.number="newRecord.amount"
                            type="number"
                            disabled
                            class="w-full px-3 py-2 bg-muted border border-border rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-muted-foreground mb-1">Status</label>
                        <select
                            v-model="newRecord.status"
                            class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm"
                        >
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                        </select>
                    </div>
                    <div v-if="newRecord.status === 'Paid'">
                        <label class="block text-sm text-muted-foreground mb-1">Payment Method</label>
                        <select
                            v-model="newRecord.method"
                            class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm"
                        >
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="PayMaya">PayMaya</option>
                        </select>
                    </div>
                    <div v-if="newRecord.status === 'Paid'">
                        <label class="block text-sm text-muted-foreground mb-1">Date Paid</label>
                        <input
                            v-model="newRecord.date"
                            type="date"
                            class="w-full px-3 py-2 bg-background border border-border rounded-lg text-sm"
                        />
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <Button
                            type="button"
                            @click="showModal = false"
                            variant="outline"
                        >
                        Cancel
                        </Button>
                        <Button
                            type="submit"
                        >
                            {{ editingRecord ? "Save Changes" : "Add" }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pagination Footer -->
        <div class="flex items-center justify-between p-4 text-sm text-muted-foreground mt-4">
            <span>
                Showing {{ filteredRecords.length ? 1 : 0 }}–
                {{ filteredRecords.length }} of {{ filteredRecords.length }} records (page 1 of 1)
            </span>

            <div class="flex items-center gap-2">
                <span>Rows per page:</span>
                <select
                class="bg-background border border-border rounded px-2 py-1"
                >
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <Button
                    variant="outline"
                    size="sm"
                    disabled
                >
                Previous
                </Button>
                <Button size="sm">1</Button>
                <Button
                    variant="outline"
                    size="sm"
                    disabled
                >
                Next
                </Button>
            </div>
        </div>
    </div>
</template>