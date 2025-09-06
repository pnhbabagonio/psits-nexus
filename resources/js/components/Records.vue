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
} from "lucide-vue-next"

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
    <div class="p-6 text-gray-200">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Payment Records</h2>
            <div class="flex gap-2">
                <button
                    @click="scanQR"
                    class="px-3 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-sm font-medium"
                >
                    Scan QR
                </button>
                <button
                    @click="openAddModal"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm font-medium"
                >
                    <Plus class="w-4 h-4" /> Add Record
                </button>
            </div>
        </div>

        <!-- Search + Filter -->
        <div class="flex items-center gap-3 mb-6">
        
            <!-- Search -->
            <div
                class="flex items-center flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2"
            >
                <Search class="w-4 h-4 text-gray-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search name..."
                    class="ml-2 flex-1 bg-transparent text-sm focus:outline-none"
                />
            </div>

            <!-- Filter -->
            <div class="relative">
                <button
                    @click="isFilterOpen = !isFilterOpen"
                    class="flex items-center gap-2 px-3 py-2 bg-gray-700 rounded hover:bg-gray-600"
                >
                    <Filter class="w-4 h-4" />
                    <span class="text-sm">{{ paymentFilter }}</span>
                    <ChevronDown
                        class="w-4 h-4 transition-transform"
                        :class="{ 'rotate-180': isFilterOpen }"
                    />
                </button>

                <!-- Dropdown -->
                <div
                v-if="isFilterOpen"
                class="absolute right-0 mt-2 w-32 bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-10"
                >
                    <button
                        v-for="option in filterOptions"
                        :key="option"
                        @click="
                        paymentFilter = option;
                        isFilterOpen = false
                        "
                        class="w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white"
                        :class="{ 'bg-gray-700 text-white': paymentFilter === option }"
                    >
                        {{ option }}
                    </button>
                </div>
            </div>

            <!-- Clear Filter -->
            <button
                v-if="paymentFilter !== 'All' || search"
                @click="paymentFilter = 'All'; search = ''"
                class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-sm"
            >
                Clear Filters
            </button>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-800">
            <table class="w-full text-sm text-left text-gray-300">
                <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Student</th>
                        <th class="px-4 py-3">Requirement</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Method</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="record in filteredRecords"
                        :key="record.id"
                        class="border-t border-gray-700 hover:bg-gray-800"
                    >
                        <td class="px-4 py-3 font-medium">
                        {{ record.firstName }} {{ record.middleName }} {{ record.lastName }}
                        </td>
                        <td class="px-4 py-3">{{ record.requirement }}</td>
                        <td class="px-4 py-3">₱{{ record.amount }}</td>
                        <td class="px-4 py-3">{{ record.date || "—" }}</td>
                        <td class="px-4 py-3">{{ record.status === "Paid" ? record.method : "—" }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 text-xs rounded-full"
                                :class="{
                                'bg-green-900 text-green-300': record.status === 'Paid',
                                'bg-red-900 text-red-300': record.status === 'Unpaid',
                                }"
                            >
                                {{ record.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <button
                                @click="openEditModal(record)"
                                class="p-2 rounded-lg hover:bg-yellow-600/20 transition"
                            >
                                <Pencil class="w-4 h-4 text-yellow-500" />
                            </button>
                        <button
                            @click="deleteRecord(record.id)"
                            class="p-2 rounded-lg hover:bg-red-600/20 transition"
                        >
                            <Trash2 class="w-4 h-4 text-red-500" />
                        </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Record Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50"
        >
            <div class="bg-gray-900 rounded-xl p-6 w-full max-w-md shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">
                        {{ editingRecord ? "Edit Payment Record" : "Add Payment Record" }}
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <form @submit.prevent="saveRecord" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400">First Name</label>
                        <input
                            v-model="newRecord.firstName"
                            type="text"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Middle Name</label>
                        <input
                            v-model="newRecord.middleName"
                            type="text"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Last Name</label>
                        <input
                            v-model="newRecord.lastName"
                            type="text"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Requirement</label>
                        <select
                            v-model="newRecord.requirement"
                            @change="
                                newRecord.amount =
                                requirementOptions.find((r) => r.title === newRecord.requirement)?.amount || 0
                            "
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        >
                            <option disabled value="">Select requirement</option>
                            <option v-for="req in requirementOptions" :key="req.title" :value="req.title">
                                {{ req.title }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Amount</label>
                        <input
                            v-model.number="newRecord.amount"
                            type="number"
                            disabled
                            class="w-full px-3 py-2 mt-1 bg-gray-700 border border-gray-600 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Status</label>
                        <select
                            v-model="newRecord.status"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        >
                            <option value="Paid">Paid</option>
                            <option value="Unpaid">Unpaid</option>
                        </select>
                    </div>
                    <div v-if="newRecord.status === 'Paid'">
                        <label class="block text-sm text-gray-400">Payment Method</label>
                        <select
                            v-model="newRecord.method"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        >
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="PayMaya">PayMaya</option>
                        </select>
                    </div>
                    <div v-if="newRecord.status === 'Paid'">
                        <label class="block text-sm text-gray-400">Date Paid</label>
                        <input
                            v-model="newRecord.date"
                            type="date"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm"
                        >
                        Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm"
                        >
                            {{ editingRecord ? "Save Changes" : "Add" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pagination Footer -->
        <div class="flex items-center justify-between p-4 text-sm text-gray-400 mt-4">
            <span>
                Showing {{ filteredRecords.length ? 1 : 0 }}–
                {{ filteredRecords.length }} of {{ filteredRecords.length }} records (page 1 of 1)
            </span>

            <div class="flex items-center gap-2">
                <span>Rows per page:</span>
                <select
                class="bg-gray-800 border border-gray-700 rounded px-2 py-1 text-gray-300"
                >
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <button
                    class="px-2 py-1 rounded bg-gray-700 text-gray-400 cursor-not-allowed"
                    disabled
                >
                Previous
                </button>
                <button class="px-2 py-1 rounded bg-blue-600 text-white">1</button>
                <button
                    class="px-2 py-1 rounded bg-gray-700 text-gray-400 cursor-not-allowed"
                    disabled
                >
                Next
                </button>
            </div>
        </div>
    </div>
</template>