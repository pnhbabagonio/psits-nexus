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
    Eye,
    Download,
} from "lucide-vue-next"

// --- Types ---
interface UserProfile {
    id: number
    firstName: string
    lastName: string
    course: string
    year: string
    section: string
    totalBalance: number
    totalPaid: number
    totalUnpaid: number
    paidRequirements: string[]
    unpaidRequirements: { name: string; overdue: boolean }[]
}

// --- State (original) ---
const search = ref("")
const isFilterOpen = ref(false)
const courseFilter = ref("All")
const filterOptions = ["All", "Computer Science", "Information Tech", "Engineering"]

// Modal states
const showModal = ref(false)
const showDetailsModal = ref(false)
const editingUser = ref<UserProfile | null>(null)
const selectedUser = ref<UserProfile | null>(null)

// Records for QR scan (mock)
const records = ref<any[]>([])

// requirement-specific states (new)
const requirementSearch = ref("")
const requirementFilter = ref("All") // All | Recent | Old | Paid | Unpaid
const isRequirementFilterOpen = ref(false)

// Mock Data (original)
const users = ref<UserProfile[]>([
    {
        id: 1,
        firstName: "John",
        lastName: "Doe",
        course: "Computer Science",
        year: "3rd Year",
        section: "A",
        totalBalance: 1050,
        totalPaid: 800,
        totalUnpaid: 250,
        paidRequirements: ["Membership Fee", "ID Fee"],
        unpaidRequirements: [
        { name: "Graduation Fee", overdue: true },
        { name: "Library Fee", overdue: false },
        ],
    },
    {
        id: 2,
        firstName: "Jane",
        lastName: "Smith",
        course: "Information Tech",
        year: "2nd Year",
        section: "B",
        totalBalance: 900,
        totalPaid: 500,
        totalUnpaid: 400,
        paidRequirements: ["Membership Fee"],
        unpaidRequirements: [
        { name: "Sports Fee", overdue: false },
        { name: "Graduation Fee", overdue: true },
        ],
    },
])

// Form data (original)
const newUser = ref<UserProfile>({
    id: 0,
    firstName: "",
    lastName: "",
    course: "",
    year: "",
    section: "",
    totalBalance: 0,
    totalPaid: 0,
    totalUnpaid: 0,
    paidRequirements: [],
    unpaidRequirements: [],
})

// --- Filtered users (original) ---
const filteredUsers = computed(() => {
    return users.value.filter((user) => {
        const matchesSearch =
        `${user.firstName} ${user.lastName} ${user.course}`
            .toLowerCase()
            .includes(search.value.toLowerCase())

        if (courseFilter.value === "All") return matchesSearch
        return user.course === courseFilter.value && matchesSearch
    })
})

// padded mock data (ensures at least 10 requirements) — keep original behaviour
users.value.forEach((u) => {
    while (u.paidRequirements.length < 10) {
        u.paidRequirements.push("Paid Requirement " + (u.paidRequirements.length + 1))
    }
    while (u.unpaidRequirements.length < 10) {
        u.unpaidRequirements.push({
        name: "Unpaid Requirement " + (u.unpaidRequirements.length + 1),
        overdue: Math.random() > 0.5,
        })
    }
})

// --- Methods (original) ---
function openAddModal() {
    editingUser.value = null
    newUser.value = {
        id: 0,
        firstName: "",
        lastName: "",
        course: "",
        year: "",
        section: "",
        totalBalance: 0,
        totalPaid: 0,
        totalUnpaid: 0,
        paidRequirements: [],
        unpaidRequirements: [],
    }
    showModal.value = true
}

function openEditModal(user: UserProfile) {
    editingUser.value = user
    newUser.value = { ...user }
    showModal.value = true
}

function saveUser() {
    if (!newUser.value.firstName || !newUser.value.lastName || !newUser.value.course) {
        alert("Please fill in required fields")
        return
    }

    if (editingUser.value) {
        Object.assign(editingUser.value, { ...newUser.value })
    } else {
        users.value.push({
        ...newUser.value,
        id: users.value.length + 1,
        })
    }

    showModal.value = false
}

function deleteUser(id: number) {
    users.value = users.value.filter((u) => u.id !== id)
}

function openDetails(user: UserProfile) {
    selectedUser.value = user
    // reset requirement filters/search when opening details
    requirementSearch.value = ""
    requirementFilter.value = "All"
    isRequirementFilterOpen.value = false
    showDetailsModal.value = true
}

function exportReceipt(user: UserProfile | null) {
    if (!user) return
    alert(`Exporting receipt for ${user.firstName} ${user.lastName}`)
}

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

type CombinedReq = {
    name: string
    amount: number
    status: "paid" | "unpaid"
    overdue?: boolean
    date: string
}

const combinedRequirements = computed<CombinedReq[]>(() => {
    if (!selectedUser.value) return []

    // paid items: amount = 100 + index*50
    const paid: CombinedReq[] = selectedUser.value.paidRequirements.map((name, i) => ({
        name,
        amount: 100 + i * 50,
        status: "paid",
        overdue: false,
        date: new Date(Date.now() - i * 86400000).toISOString(), // recent past dates
    }))

    // unpaid items: amount = 150 + index*75
        const unpaid: CombinedReq[] = selectedUser.value.unpaidRequirements.map((obj, i) => ({
        name: obj.name,
        amount: 150 + i * 75,
        status: "unpaid",
        overdue: obj.overdue,
        date: new Date(Date.now() - (i + paid.length) * 86400000).toISOString(),
    }))

    // keep paid first (so Paid shows on top), then unpaid
    return [...paid, ...unpaid]
})

// computed filteredRequirements used by the combined single-list UI
const filteredRequirements = computed<CombinedReq[]>(() => {
    const all = combinedRequirements.value

    // apply search
    const searched = all.filter((r) =>
        r.name.toLowerCase().includes(requirementSearch.value.toLowerCase())
    )

    // apply filter
    if (requirementFilter.value === "Paid") {
        return searched.filter((r) => r.status === "paid")
    } else if (requirementFilter.value === "Unpaid") {
        return searched.filter((r) => r.status === "unpaid")
    } else if (requirementFilter.value === "Recent") {
        return [...searched].sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()).slice(0, 5)
    } else if (requirementFilter.value === "Old") {
        return [...searched].sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime()).slice(0, 5)
    }

    // All
    return searched
})
</script>

<template>
    <div class="p-6 text-gray-200">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">User Payment Profiles</h2>
            <div class="flex items-center gap-3">
                <button
                    @click="scanQR"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium"
                >
                Scan QR
                </button>

                <button
                    @click="openAddModal"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium"
                >
                    <Plus class="w-4 h-4" /> Add User
                </button>
            </div>
        </div>

        <!-- Search + Filter -->
        <div class="flex items-center gap-3 mb-6">
        
            <!-- Search -->
            <div
                class="flex items-center flex-1 bg-[#1E293B] border border-gray-700 rounded-lg px-3 py-2"
            >
                <Search class="w-4 h-4 text-gray-400" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search users..."
                    class="ml-2 flex-1 bg-transparent text-sm focus:outline-none text-gray-200"
                />
            </div>

            <!-- Filter (course) -->
            <div class="relative">
                <button
                    @click="isFilterOpen = !isFilterOpen"
                    class="flex items-center gap-2 px-3 py-2 bg-[#1E293B] hover:bg-gray-700 rounded text-sm"
                >
                <Filter class="w-4 h-4 text-gray-400" />
                <span>{{ courseFilter }}</span>
                <ChevronDown
                    class="w-4 h-4 transition-transform"
                    :class="{ 'rotate-180': isFilterOpen }"
                />
                </button>

                <!-- Dropdown -->
                <div
                    v-if="isFilterOpen"
                    class="absolute right-0 mt-2 w-40 bg-[#1E293B] border border-gray-700 rounded-lg shadow-lg z-10"
                >
                    <button
                        v-for="option in filterOptions"
                        :key="option"
                        @click="courseFilter = option; isFilterOpen = false"
                        class="w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-gray-700"
                        :class="{ 'bg-gray-700 text-white': courseFilter === option }"
                    >
                        {{ option }}
                    </button>
                </div>
            </div>

            <!-- Clear Filter -->
            <button
                v-if="courseFilter !== 'All' || search"
                @click="courseFilter = 'All'; search = ''"
                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"
            >
                Clear
            </button>
        </div>

        <!-- User Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="user in filteredUsers"
                :key="user.id"
                class="bg-[#0F172A] rounded-xl border border-gray-700 shadow p-5"
            >

                <!-- Profile Info -->
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center">
                        <span class="text-blue-300 font-bold text-sm">
                        {{ user.firstName.charAt(0) }}{{ user.lastName.charAt(0) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-100">
                        {{ user.firstName }} {{ user.lastName }}
                        </h3>
                        <p class="text-sm text-gray-400">{{ user.course }}</p>
                    </div>
                </div>

                <!-- Payment Stats -->
                <div class="text-sm space-y-1 mb-4">
                    <p>
                        <span class="font-medium text-gray-300">Balance:</span>
                        <span class="ml-2 font-semibold text-gray-100">₱{{ user.totalBalance }}</span>
                    </p>
                    <p>
                        <span class="font-medium text-gray-300">Paid:</span>
                        <span class="ml-2 text-green-400 font-semibold">₱{{ user.totalPaid }}</span>
                    </p>
                    <p>
                        <span class="font-medium text-gray-300">Unpaid:</span>
                        <span class="ml-2 text-red-400 font-semibold">₱{{ user.totalUnpaid }}</span>
                    </p>
                </div>

                <!-- Requirement Progress -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                
                    <!-- Paid -->
                    <div
                        class="bg-green-900/30 text-green-400 rounded-lg flex flex-col items-center justify-center py-3"
                    >
                        <span class="text-lg font-bold">✔ {{ user.paidRequirements.length }}</span>
                        <span class="text-xs">Paid Requirements</span>
                    </div>

                    <!-- Unpaid -->
                    <div
                        class="bg-red-900/30 text-red-400 rounded-lg flex flex-col items-center justify-center py-3"
                    >
                        <span class="text-lg font-bold">✘ {{ user.unpaidRequirements.length }}</span>
                        <span class="text-xs">Unpaid Requirements</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button
                        @click="openDetails(user)"
                        class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm"
                    >
                        <Eye class="w-4 h-4" /> View Details
                    </button>
                    <button
                        @click="openEditModal(user)"
                        class="p-2 rounded-lg hover:bg-yellow-600/20 transition"
                    >
                        <Pencil class="w-4 h-4 text-yellow-500" />
                    </button>
                    <button
                        @click="deleteUser(user.id)"
                        class="p-2 rounded-lg hover:bg-red-600/20 transition"
                    >
                        <Trash2 class="w-4 h-4 text-red-500" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Add/Edit User Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50"
        >
            <div class="bg-gray-900 rounded-xl p-6 w-full max-w-md shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">
                        {{ editingUser ? "Edit User" : "Add User" }}
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-white">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                <form @submit.prevent="saveUser" class="space-y-4">
                    <div>
                        <label class="block text-sm text-gray-400">First Name</label>
                        <input
                            v-model="newUser.firstName"
                            type="text"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Last Name</label>
                        <input
                            v-model="newUser.lastName"
                            type="text"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Course</label>
                        <select
                            v-model="newUser.course"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        >
                            <option disabled value="">Select course</option>
                            <option v-for="option in filterOptions.slice(1)" :key="option" :value="option">
                                {{ option }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Year</label>
                        <input
                            v-model="newUser.year"
                            type="text"
                            placeholder="e.g. 2nd Year"
                            class="w-full px-3 py-2 mt-1 bg-gray-800 border border-gray-700 rounded-lg text-sm"
                        />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400">Section</label>
                        <input
                            v-model="newUser.section"
                            type="text"
                            placeholder="e.g. A"
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
                        {{ editingUser ? "Save Changes" : "Add" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- View Details Modal -->
        <div
            v-if="showDetailsModal && selectedUser"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div
                class="bg-gray-900 p-8 rounded-2xl shadow-2xl w-[950px] max-h-[95vh] overflow-y-auto flex flex-col"
            >
                
                <!-- Header -->
                <div class="flex justify-between items-center border-b border-gray-700 pb-4 mb-6">
                    <h2 class="text-2xl font-bold text-white">
                        {{ selectedUser.firstName }} {{ selectedUser.lastName }}
                    </h2>
                    <button
                        @click="showDetailsModal = false"
                        class="text-gray-400 hover:text-gray-200"
                    >
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <!-- Two Column Layout -->
                <div class="grid grid-cols-2 gap-6">
                
                    <!-- Left Column: Student Info -->
                    <div class="space-y-3 bg-gray-800 p-5 rounded-lg border border-gray-700">
                        <p class="text-lg">
                            <span class="font-semibold text-gray-400">Course:</span>
                            <span class="ml-2 font-bold text-white">{{ selectedUser.course }}</span>
                        </p>
                        <p class="text-lg">
                            <span class="font-semibold text-gray-400">Year & Section:</span>
                            <span class="ml-2 font-bold text-white">{{ selectedUser.year }} - {{ selectedUser.section }}</span>
                        </p>
                        <p class="text-lg">
                            <span class="font-semibold text-gray-400">Total Balance:</span>
                            <span class="ml-2 font-bold text-blue-400">₱{{ selectedUser.totalBalance }}</span>
                        </p>
                        <p class="text-lg">
                            <span class="font-semibold text-gray-400">Total Paid:</span>
                            <span class="ml-2 font-bold text-green-400">₱{{ selectedUser.totalPaid }}</span>
                        </p>
                        <p class="text-lg">
                            <span class="font-semibold text-gray-400">Total Unpaid:</span>
                            <span class="ml-2 font-bold text-red-400">₱{{ selectedUser.totalUnpaid }}</span>
                        </p>
                        <p class="text-lg">
                            <span class="font-semibold text-gray-400">Paid Requirements:</span>
                            <span class="ml-2 font-bold text-green-400">{{ selectedUser.paidRequirements.length }}</span>
                        </p>
                        <p class="text-lg">
                            <span class="font-semibold text-gray-400">Unpaid Requirements:</span>
                            <span class="ml-2 font-bold text-red-400">{{ selectedUser.unpaidRequirements.length }}</span>
                        </p>
                    </div>

                    <!-- Right Column: Requirements -->
                    <div class="space-y-4">
                        <!-- Search + Filter -->
                        <div class="flex items-center justify-between mb-2">
                            <input
                                v-model="requirementSearch"
                                type="text"
                                placeholder="Search requirements..."
                                class="bg-gray-800 text-gray-200 border border-gray-700 rounded px-3 py-2 text-sm w-2/3"
                            />
                            <div class="relative">
                                <button
                                    @click="isRequirementFilterOpen = !isRequirementFilterOpen"
                                    class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 px-3 py-2 rounded-lg text-sm"
                                >
                                    <Filter class="w-4 h-4" /> {{ requirementFilter }}
                                    <ChevronDown class="w-4 h-4" />
                                </button>
                                <div
                                    v-if="isRequirementFilterOpen"
                                    class="absolute right-0 mt-2 bg-gray-800 border border-gray-700 rounded-lg shadow-lg w-40 z-10"
                                >
                                    <button
                                        v-for="opt in ['All','Recent','Old','Paid','Unpaid']"
                                        :key="opt"
                                        @click="requirementFilter = opt; isRequirementFilterOpen = false"
                                        class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-700"
                                    >
                                        {{ opt }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Combined Requirements List -->
                        <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
                            <div
                                v-for="(req, i) in filteredRequirements"
                                :key="i"
                                class="p-4 rounded-lg border border-gray-700 bg-gray-800 flex justify-between items-center"
                            >
                                <div>
                                    <p class="font-medium">
                                        <span v-if="req.status === 'paid'" class="text-green-400">✔</span>
                                        <span v-else class="text-red-400">✘</span>
                                        {{ req.name }}
                                    </p>
                                    <p class="text-sm text-gray-400">
                                        Date: {{ new Date(req.date).toLocaleDateString() }}
                                    </p>
                                    <p v-if="req.overdue" class="text-sm text-red-400">(Overdue)</p>
                                </div>
                                <p class="font-semibold">₱{{ req.amount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end gap-2 mt-6 border-t border-gray-700 pt-4">
                    <button
                        @click="exportReceipt(selectedUser)"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm"
                    >
                        <Download class="w-4 h-4" /> Export
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
    </div>
</template>