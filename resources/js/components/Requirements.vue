<script setup lang="ts">
import { ref, computed } from "vue"
import {
    Search,
    Plus,
    Filter,
    LayoutGrid,
    List,
    ChevronDown,
    X,
    Pencil,
    Trash2,
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
const showDetailsModal = ref(false)
const editingRequirement = ref<Requirement | null>(null)
const viewingRequirement = ref<Requirement | null>(null)

const search = ref("")
const isFilterOpen = ref(false)
const isGridView = ref(true)
const requirementFilter = ref("All")

const filterOptions = ["All", "Pending", "Overdue"]

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

function downloadReceipt(req: Requirement) {
    alert(`Downloading receipt for: ${req.title}`)
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

</script>

<template>
    <div class="p-6 text-gray-200">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">Payment Requirements</h2>
            <button
                @click="openAddModal"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm font-medium"
            >
                <Plus class="w-4 h-4" /> Add Requirement
            </button>
        </div>

        <!-- Search + Filter + View Toggle -->
        <div class="flex items-center gap-3 mb-6">
            <!-- Search -->
            <div
                class="flex items-center flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2"
            >
                <Search class="w-4 h-4 text-gray-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search requirements..."
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
                    <span class="text-sm">{{ requirementFilter }}</span>
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
                            requirementFilter = option;
                            isFilterOpen = false
                        "
                        class="w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white"
                        :class="{ 'bg-gray-700 text-white': requirementFilter === option }"
                    >
                        {{ option }}
                    </button>
                </div>
            </div>

            <!-- Clear Filter -->
            <button
                v-if="requirementFilter !== 'All' || search"
                @click="requirementFilter = 'All'; search = ''"
                class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded text-sm"
            >
                Clear Filters
            </button>

            <!-- Toggle View -->
            <button
                @click="isGridView = !isGridView"
                class="px-3 py-2 bg-gray-700 hover:bg-gray-600 rounded"
            >
                <component :is="isGridView ? List : LayoutGrid" class="w-4 h-4" />
            </button>
        </div>

        <!-- Grid View -->
        <div v-if="isGridView" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                v-for="req in paginatedRequirements"
                :key="req.id"
                class="bg-gray-900 border border-gray-800 rounded-xl p-5 shadow-md"
            >
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-medium">{{ req.title }}</h3>
                        <p class="text-sm text-gray-400">{{ req.description }}</p>
                        <p class="text-sm text-gray-400">Amount: ₱{{ req.amount }}</p>
                        <p class="text-sm text-gray-400">
                            Deadline: {{ new Date(req.deadline).toLocaleDateString() }}
                        </p>
                        <p
                            class="text-xs mt-1"
                            :class="{
                                'text-yellow-400': getStatus(req) === 'Pending',
                                'text-red-400': getStatus(req) === 'Overdue',
                                'text-green-400': getStatus(req) === 'Done',
                            }"
                        >
                            Status: {{ getStatus(req) }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-green-400 font-bold text-lg">
                            {{ Math.round((req.paid / req.totalUsers) * 100) }}%
                        </p>
                        <p class="text-xs text-gray-400">
                            Progress: {{ req.paid }}/{{ req.totalUsers }}
                        </p>
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="w-full bg-gray-800 h-2 rounded-full mt-3 mb-4">
                    <div
                        class="h-2 rounded-full bg-green-500"
                        :style="{
                            width: ((req.paid / req.totalUsers) * 100).toFixed(0) + '%',
                        }"
                    ></div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="bg-blue-950 rounded-lg py-2">
                        <p class="text-blue-400 font-bold text-lg">{{ req.totalUsers }}</p>
                        <p class="text-xs">Total Users</p>
                    </div>
                    <div class="bg-green-950 rounded-lg py-2">
                        <p class="text-green-400 font-bold text-lg">{{ req.paid }}</p>
                        <p class="text-xs">Paid</p>
                    </div>
                    <div class="bg-red-950 rounded-lg py-2">
                        <p class="text-red-400 font-bold text-lg">{{ req.unpaid }}</p>
                        <p class="text-xs">Unpaid</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 mt-4">
                    <button
                        @click="openEditModal(req)"
                        class="flex items-center gap-2 px-3 py-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg text-sm font-medium transition"
                    >
                        <Pencil class="w-4 h-4" />
                        Edit
                    </button>
                    <button
                        @click="deleteRequirement(req.id)"
                        class="flex items-center gap-2 px-3 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-medium transition"
                    >
                        <Trash2 class="w-4 h-4" />
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- List View -->
        <div v-else class="overflow-x-auto rounded-lg border border-gray-800">
            <table class="w-full text-sm text-left text-gray-300">
                <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Deadline</th>
                        <th class="px-4 py-3">Progress</th>
                        <th class="px-4 py-3">Total Users</th>
                        <th class="px-4 py-3">Paid</th>
                        <th class="px-4 py-3">Unpaid</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="req in paginatedRequirements"
                        :key="req.id"
                        class="border-t border-gray-700 hover:bg-gray-800"
                    >
                        <td class="px-4 py-3">{{ req.title }}</td>
                        <td class="px-4 py-3">₱{{ req.amount }}</td>
                        <td class="px-4 py-3">
                            {{ new Date(req.deadline).toLocaleDateString() }}
                        </td>
                        <td class="px-4 py-3 w-48">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-800 h-2 rounded-full">
                                    <div
                                        class="h-2 rounded-full bg-green-500"
                                        :style="{ width: ((req.paid / req.totalUsers) * 100).toFixed(0) + '%' }"
                                    ></div>
                                </div>
                                <span class="text-xs text-gray-400">
                                    {{ req.paid }}/{{ req.totalUsers }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-blue-400">{{ req.totalUsers }}</td>
                        <td class="px-4 py-3 text-green-400">{{ req.paid }}</td>
                        <td class="px-4 py-3 text-red-400">
                            {{ req.totalUsers - req.paid }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="text-xs px-2 py-1 rounded-lg"
                                :class="{
                                    'bg-yellow-900 text-yellow-300': getStatus(req) === 'Pending',
                                    'bg-red-900 text-red-300': getStatus(req) === 'Overdue',
                                    'bg-green-900 text-green-300': getStatus(req) === 'Done',
                                }"
                            >
                                {{ getStatus(req) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <button
                                @click="openEditModal(req)"
                                class="p-2 rounded-lg hover:bg-yellow-600/20 transition"
                            >
                                <Pencil class="w-4 h-4 text-yellow-500" />
                            </button>
                            <button
                                @click="deleteRequirement(req.id)"
                                class="p-2 rounded-lg hover:bg-red-600/20 transition"
                            >
                                <Trash2 class="w-4 h-4 text-red-500" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Requirement Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50"
        >
            <div class="bg-gray-900 rounded-xl p-6 w-full max-w-md shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">
                        {{ editingRequirement ? "Edit Requirement" : "Add New Requirement" }}
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <form @submit.prevent="saveRequirement" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400">Title</label>
                        <input
                            v-model="newRequirement.title"
                            type="text"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Description</label>
                        <textarea
                            v-model="newRequirement.description"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        ></textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Amount</label>
                        <input
                            v-model.number="newRequirement.amount"
                            type="number"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Deadline</label>
                        <input
                            v-model="newRequirement.deadline"
                            type="date"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Total Users</label>
                        <input
                            v-model.number="newRequirement.totalUsers"
                            type="number"
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
                            {{ editingRequirement ? "Save Changes" : "Add" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Details Modal -->
        <div
            v-if="showDetailsModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50"
        >
            <div class="bg-gray-900 rounded-xl p-6 w-full max-w-md shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Requirement Details</h3>
                    <button @click="showDetailsModal = false" class="text-gray-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <div v-if="viewingRequirement">
                    <p class="mb-2"><strong>Title:</strong> {{ viewingRequirement.title }}</p>
                    <p class="mb-2"><strong>Description:</strong> {{ viewingRequirement.description }}</p>
                    <p class="mb-2"><strong>Amount:</strong> ₱{{ viewingRequirement.amount }}</p>
                    <p class="mb-2"><strong>Deadline:</strong> {{ new Date(viewingRequirement.deadline).toLocaleDateString() }}</p>
                    <p class="mb-2"><strong>Total Users:</strong> {{ viewingRequirement.totalUsers }}</p>
                    <p class="mb-2 text-green-400"><strong>Paid:</strong> {{ viewingRequirement.paid }}</p>
                    <p class="mb-2 text-red-400"><strong>Unpaid:</strong> {{ viewingRequirement.unpaid }}</p>
                    <p class="mb-2"><strong>Status:</strong> {{ getStatus(viewingRequirement) }}</p>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button
                        @click="downloadReceipt(viewingRequirement!)"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-sm"
                    >
                        Download Receipt
                    </button>
                    <button
                        @click="showDetailsModal = false"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination Footer -->
        <div class="flex items-center justify-between p-4 text-sm text-gray-400 mt-4">
            <span>
                Showing {{ filteredRequirements.length ? 1 : 0 }}–
                {{ filteredRequirements.length }} of {{ filteredRequirements.length }}
                requirements (page 1 of 1)
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