<x-layout.default>
    <!-- Alpine.js Component Wrapper -->
    <div x-data="userManagement()" class="space-y-6">

        <!-- Page Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Manajemen Pengguna</h1>
                <p class="text-gray-500 text-sm mt-1 dark:text-gray-400">Kelola pengguna sistem dan kontrol hak akses
                    secara menyeluruh</p>
            </div>
            <button @click="openDialog()"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary/30 transition-all hover:bg-primary/90 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <line x1="19" x2="19" y1="8" y2="14" />
                    <line x1="22" x2="16" y1="11" y2="11" />
                </svg>
                Tambah Pengguna
            </button>
        </div>

        <!-- Premium Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Total Users Card -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-primary p-6 text-white shadow-lg shadow-primary/20 transition-all duration-300 hover:-translate-y-1">
                <div
                    class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl transition-all group-hover:bg-white/20">
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white/80 tracking-wider">Total Pengguna</p>
                        <p class="mt-2 text-4xl font-extrabold" x-text="roleStats.total"></p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-black/20 backdrop-blur-sm border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="text-white">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                            <circle cx="14" cy="7" r="4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Superadmin Card -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-secondary p-6 text-white shadow-lg shadow-secondary/20 transition-all duration-300 hover:-translate-y-1">
                <div
                    class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl transition-all group-hover:bg-white/20">
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white/80 tracking-wider">Superadmin</p>
                        <p class="mt-2 text-4xl font-extrabold" x-text="roleStats.superadmin"></p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-black/20 backdrop-blur-sm border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="text-white">
                            <path
                                d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Admin Card -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-info p-6 text-white shadow-lg shadow-info/20 transition-all duration-300 hover:-translate-y-1">
                <div
                    class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl transition-all group-hover:bg-white/20">
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white/80 tracking-wider">Admin</p>
                        <p class="mt-2 text-4xl font-extrabold" x-text="roleStats.admin"></p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-black/20 backdrop-blur-sm border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="text-white">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <polyline points="16 11 18 13 22 9" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div
                class="group relative overflow-hidden rounded-2xl bg-success p-6 text-white shadow-lg shadow-success/20 transition-all duration-300 hover:-translate-y-1">
                <div
                    class="absolute -right-8 -top-8 h-32 w-32 rounded-full bg-white/10 blur-2xl transition-all group-hover:bg-white/20">
                </div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-white/80 tracking-wider">User Aktif</p>
                        <p class="mt-2 text-4xl font-extrabold" x-text="roleStats.active"></p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-black/20 backdrop-blur-sm border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                            stroke-linejoin="round" class="text-white">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Area -->
        <div class="panel h-full flex-1 p-5">
            <div class="flex flex-col md:flex-row gap-4">

                <!-- Search: Area Pencarian -->
                <div class="relative flex-1">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama atau email pengguna..."
                        class="form-input ps-10 placeholder:text-white-dark" />
                    <span class="absolute start-4 top-1/2 -translate-y-1/2 text-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="m-auto">
                            <circle cx="11.5" cy="11.5" r="9.5" stroke="currentColor" stroke-width="1.5"
                                opacity="0.5"></circle>
                            <path d="M18.5 18.5L22 22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"></path>
                        </svg>
                    </span>
                </div>

                <!-- Filter Role -->
                <div class="relative min-w-[200px]">
                    <select x-model="filterRole" class="form-select ps-10">
                        <option value="all">Semua Role / Akses</option>
                        <option value="superadmin">Superadmin</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <span class="absolute start-4 top-1/2 -translate-y-1/2 text-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="6" r="4" stroke="currentColor" stroke-width="1.5">
                            </circle>
                            <path
                                d="M20 17.5C20 19.1569 16.4183 20.5 12 20.5C7.58172 20.5 4 19.1569 4 17.5C4 15.8431 7.58172 14.5 12 14.5C16.4183 14.5 20 15.8431 20 17.5Z"
                                stroke="currentColor" stroke-width="1.5"></path>
                        </svg>
                    </span>
                </div>

                <!-- Filter Status -->
                <div class="relative min-w-[180px]">
                    <select x-model="filterStatus" class="form-select ps-10">
                        <option value="all">Semua Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <span class="absolute start-4 top-1/2 -translate-y-1/2 text-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2Z"
                                stroke="currentColor" stroke-width="1.5"></path>
                            <path d="M8.5 12.5L10.5 14.5L15.5 9.5" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                </div>

                <!-- Reset Button -->
                <button type="button" @click="resetFilters()" class="btn btn-outline-primary p-2"
                    title="Reset Filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                        <path d="M3 3v5h5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="panel p-0 border-none shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table-hover w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="py-4 px-6 font-bold">Nama Pengguna</th>
                            <th class="py-4 px-6 font-bold">Username</th>
                            <th class="py-4 px-6 font-bold">Email</th>
                            <th class="py-4 px-6 font-bold">Role</th>
                            <th class="py-4 px-6 font-bold">Status</th>
                            <th class="py-4 px-6 font-bold">Last Login</th>
                            <th class="py-4 px-6 font-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="user in filteredUsers" :key="user.id">
                            <tr class="group">
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white" x-text="user.name"></td>
                                <td class="px-6 py-4 text-white-dark font-mono text-sm" x-text="user.username"></td>
                                <td class="px-6 py-4 text-white-dark" x-text="user.email"></td>
                                <td class="px-6 py-4">
                                    <span class="badge flex items-center gap-1 w-fit"
                                        :class="{
                                            'badge-outline-secondary': user.role === 'superadmin',
                                            'badge-outline-primary': user.role === 'admin',
                                            'badge-outline-info': user.role === 'user'
                                        }">
                                        <span x-text="user.role.t()"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="badge rounded-full px-3"
                                        :class="user.status === 'active' ? 'badge-outline-success' : 'badge-outline-danger'"
                                        x-text="user.status === 'active' ? 'Active' : 'Inactive'">
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-white-dark text-sm" x-text="user.lastLogin"></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="openDialog(user)"
                                            class="text-primary hover:scale-110 transition">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.5"
                                                    d="M7 21H17C19.357 21 20.5355 21 21.2678 20.2678C22 19.5355 22 18.357 22 16V14M2 10V16C2 18.357 2 19.5355 2.73223 20.2678C3.46447 21 4.64298 21 7 21"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                                </path>
                                                <path
                                                    d="M12.4246 3.51465C13.2917 2.64761 14.6987 2.64761 15.5658 3.51465L16.4853 4.43415C17.3524 5.30119 17.3524 6.70823 16.4853 7.57527L10.3756 13.685C9.69976 14.3608 9.36183 14.6987 8.94911 14.9085C8.5364 15.1182 8.08389 15.1878 7.17887 15.327L6 15.5083L6.18134 14.3295C6.32053 13.4245 6.39012 12.972 6.59987 12.5592C6.80961 12.1465 7.14755 11.8086 7.82343 11.1327L12.4246 3.51465Z"
                                                    stroke="currentColor" stroke-width="1.5"></path>
                                                <path opacity="0.5" d="M11.5 4.5L15.5 8.5" stroke="currentColor"
                                                    stroke-width="1.5"></path>
                                            </svg>
                                        </button>
                                        <button @click="confirmDelete(user)"
                                            class="text-danger hover:scale-110 transition">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.5001 6H3.5" stroke="currentColor" stroke-width="1.5"
                                                    stroke-linecap="round"></path>
                                                <path
                                                    d="M18.8334 8.5L18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80373 18.054 5.62669 15.3991L5.16675 8.5"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                                </path>
                                                <path opacity="0.5" d="M9.5 11L10 16" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"></path>
                                                <path opacity="0.5" d="M14.5 11L14 16" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"></path>
                                                <path opacity="0.5"
                                                    d="M6.5 6C7.35523 6 8.04832 5.30315 8.09325 4.44845L8.14081 3.54359C8.19741 2.46616 8.22571 1.92744 8.60412 1.58872C8.98254 1.25 9.52226 1.25 10.6017 1.25H13.3983C14.4778 1.25 15.0175 1.25 15.3959 1.58872C15.7743 1.92744 15.8026 2.46616 15.8592 3.54359L15.9068 4.44845C15.9517 5.30315 16.6448 6 17.5 6"
                                                    stroke="currentColor" stroke-width="1.5"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredUsers.length === 0">
                            <td colspan="7" class="py-10 text-center text-white-dark">
                                <p>Tidak ada data pengguna yang ditemukan.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit User Modal (LAPISAN PALING LUAR - z-[1000]) -->
        <div x-show="isDialogOpen" class="fixed inset-0 z-[1000] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Overlay Backdrop -->
                <div x-show="isDialogOpen" x-transition.opacity @click="closeDialog()"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="isDialogOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="inline-block align-middle bg-white dark:bg-[#0e1726] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-800">

                    <div class="flex items-center justify-between px-6 py-4 bg-gray-50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white"
                            x-text="editingUser ? 'Edit Detail Akun' : 'Buat Akun Baru'"></h3>
                        <button @click="closeDialog()" class="text-white-dark hover:text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold mb-1.5 block">Nama Lengkap</label>
                                <input type="text" x-model="formData.name" placeholder="Budi Santoso"
                                    class="form-input" />
                            </div>
                            <div>
                                <label class="text-sm font-semibold mb-1.5 block">Username</label>
                                <input type="text" x-model="formData.username" placeholder="budi01"
                                    class="form-input" />
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-semibold mb-1.5 block">Alamat Email</label>
                            <input type="email" x-model="formData.email" placeholder="budi@plantation.com"
                                class="form-input" />
                        </div>

                        <div>
                            <label class="text-sm font-semibold mb-1.5 block">Password</label>
                            <input type="password" x-model="formData.password" placeholder="••••••••"
                                class="form-input" />
                            <p class="text-[11px] text-white-dark mt-1" x-show="editingUser">* Kosongkan jika tidak
                                ingin mengubah password</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-semibold mb-1.5 block">Hak Akses / Role</label>
                                <select x-model="formData.role" class="form-select">
                                    <option value="superadmin">Superadmin</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-semibold mb-1.5 block">Status Akun</label>
                                <select x-model="formData.status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end px-6 py-4 gap-3 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="closeDialog()" class="btn btn-outline-danger">Batal</button>
                        <button type="button" @click="saveUser()" class="btn btn-primary"
                            x-text="editingUser ? 'Simpan Perubahan' : 'Buat Pengguna'"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal (LAPISAN PALING LUAR - z-[1001]) -->
        <div x-show="isDeleteDialogOpen" class="fixed inset-0 z-[1001] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div x-show="isDeleteDialogOpen" x-transition.opacity @click="isDeleteDialogOpen = false"
                    class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>

                <div x-show="isDeleteDialogOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="relative bg-white dark:bg-[#0e1726] rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl">

                    <div
                        class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-danger/10 text-danger mb-6">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 9V11M12 15H12.01M5.07183 19H18.9282C20.4678 19 21.4301 17.3333 20.6603 16L13.7321 4C12.9623 2.66667 11.0378 2.66667 10.268 4L3.33978 16C2.56998 17.3333 3.53223 19 5.07183 19Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Konfirmasi Hapus</h3>
                    <p class="text-white-dark">
                        Apakah Anda yakin ingin menghapus <span class="font-bold text-primary"
                            x-text="userToDelete?.name"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <button @click="isDeleteDialogOpen = false"
                            class="btn btn-outline-secondary w-full py-3">Batal</button>
                        <button @click="deleteUser()"
                            class="btn btn-danger w-full py-3 shadow-lg shadow-danger/30">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Data Alpine.js -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('userManagement', () => ({
                searchQuery: '',
                filterRole: 'all',
                filterStatus: 'all',
                isDialogOpen: false,
                isDeleteDialogOpen: false,
                editingUser: null,
                userToDelete: null,

                formData: {
                    name: '',
                    username: '',
                    email: '',
                    password: '',
                    role: 'user',
                    status: 'active'
                },

                usersData: [{
                        id: 1,
                        name: 'Maghfirah',
                        username: 'gitaddpir',
                        email: 'maghfirah@plantation.com',
                        role: 'superadmin',
                        status: 'active',
                        lastLogin: '2026-03-12 10:30'
                    },
                    {
                        id: 2,
                        name: 'Asisten Investasi dan Pemetaan',
                        username: 'admin01',
                        email: 'admin01@gmail.com',
                        role: 'admin',
                        status: 'active',
                        lastLogin: '2026-03-11 09:15'
                    },
                    {
                        id: 3,
                        name: 'Kasub Investasi',
                        username: 'user01',
                        email: 'user@gmail.com',
                        role: 'user',
                        status: 'active',
                        lastLogin: '2026-03-07 16:45'
                    },
                ],

                get filteredUsers() {
                    return this.usersData.filter((user) => {
                        const search = this.searchQuery.toLowerCase();
                        const matchesSearch =
                            user.name.toLowerCase().includes(search) ||
                            user.email.toLowerCase().includes(search) ||
                            user.username.toLowerCase().includes(search);

                        const matchesRole = this.filterRole === 'all' || user.role === this
                            .filterRole;
                        const matchesStatus = this.filterStatus === 'all' || user.status ===
                            this.filterStatus;

                        return matchesSearch && matchesRole && matchesStatus;
                    });
                },

                get roleStats() {
                    return {
                        total: this.usersData.length,
                        superadmin: this.usersData.filter((u) => u.role === 'superadmin').length,
                        admin: this.usersData.filter((u) => u.role === 'admin').length,
                        active: this.usersData.filter((u) => u.status === 'active').length,
                    };
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.filterRole = 'all';
                    this.filterStatus = 'all';
                },

                openDialog(user = null) {
                    if (user) {
                        this.editingUser = user;
                        this.formData = {
                            name: user.name,
                            username: user.username,
                            email: user.email,
                            password: '', // Password tidak dimunculkan demi keamanan
                            role: user.role,
                            status: user.status
                        };
                    } else {
                        this.editingUser = null;
                        this.formData = {
                            name: '',
                            username: '',
                            email: '',
                            password: '',
                            role: 'user',
                            status: 'active'
                        };
                    }
                    this.isDialogOpen = true;
                },

                closeDialog() {
                    this.isDialogOpen = false;
                },

                saveUser() {
                    if (!this.formData.name || !this.formData.username || !this.formData.email) {
                        alert('Silakan lengkapi field yang wajib diisi!');
                        return;
                    }

                    if (this.editingUser) {
                        const index = this.usersData.findIndex(u => u.id === this.editingUser.id);
                        if (index !== -1) {
                            this.usersData[index] = {
                                ...this.usersData[index],
                                name: this.formData.name,
                                username: this.formData.username,
                                email: this.formData.email,
                                role: this.formData.role,
                                status: this.formData.status
                            };
                        }
                    } else {
                        this.usersData.unshift({
                            id: Date.now(),
                            ...this.formData,
                            lastLogin: '-'
                        });
                    }
                    this.closeDialog();
                },

                confirmDelete(user) {
                    this.userToDelete = user;
                    this.isDeleteDialogOpen = true;
                },

                deleteUser() {
                    if (this.userToDelete) {
                        this.usersData = this.usersData.filter(u => u.id !== this.userToDelete.id);
                        this.isDeleteDialogOpen = false;
                        this.userToDelete = null;
                    }
                }
            }));
        });
    </script>
</x-layout.default>
