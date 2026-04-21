<?php if (isset($component)) { $__componentOriginal9d5893b966d42bc9a39e2bb81c9df0c6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9d5893b966d42bc9a39e2bb81c9df0c6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout.default','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('layout.default'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div x-data="uploadHandler()">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white-light font-heading">Upload Data Monitoring</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Upload file Excel (.xlsx, .xls) untuk import data monitoring kebun TBM III - PTPN IV Regional I
            </p>
        </div>

        

        <div class="space-y-6">

            <!-- FORM START -->
            <!-- Uncomment action di bawah jika backend store sudah siap -->
            <form action="#" >
                

                <div class="space-y-6">
                    <!-- 1. PANEL METADATA DATA -->
                    <div class="panel">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center font-black">
                                1</div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Identifikasi Dataset</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 tracking-widest font-bold">
                                    Lengkapi informasi berkas sebelum mengunggah</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2">
                                <label
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Judul
                                    File</label>
                                <input type="text" name="judul_file"
                                    class="form-input rounded-xl py-3 font-bold border-gray-200 dark:border-gray-700 dark:bg-[#1b2e4b] focus:border-emerald-500"
                                    placeholder="Contoh: Monitoring Vegetatif Semester I - Kebun Sei Dadap" required>
                            </div>

                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Personel
                                    Penanggung Jawab</label>
                                <input type="text"
                                    class="form-input rounded-xl py-3 bg-gray-50 dark:bg-[#1b2e4b]/50 font-bold border-none cursor-not-allowed dark:text-white-dark"
                                    value="Maghfirah (Superadmin)"  readonly>
                            </div>

                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Jenis
                                    Dataset</label>
                                <select name="kategori_file"
                                    class="form-select rounded-xl py-3 font-bold border-gray-200 dark:border-gray-700 dark:bg-[#1b2e4b]"
                                    required>
                                    <option value="">Pilih Kategori...</option>
                                    <option value="Rekap TBM">Rekapitulasi TBM III (Populasi)</option>
                                    <option value="Korelasi Vegetatif">Korelasi Vegetatif (Biometrik)</option>
                                    <option value="Lokasi Kebun">Data Geospasial (Lokasi Peta)</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Periode
                                    Laporan</label>
                                <select name="periode_data"
                                    class="form-select rounded-xl py-3 font-bold border-gray-200 dark:border-gray-700 dark:bg-[#1b2e4b]"
                                    required>
                                    <option value="">Pilih Periode...</option>
                                    <option value="Januari 2026">Januari 2026</option>
                                    <option value="Februari 2026">Februari 2026</option>
                                    <option value="Maret 2026">Maret 2026</option>
                                </select>
                            </div>

                            <div class="hidden lg:block"></div>

                            <div class="md:col-span-2 lg:col-span-3">
                                <label
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Catatan
                                    Tambahan & Keterangan Khusus</label>
                                <textarea name="notes" rows="4"
                                    class="form-textarea rounded-2xl font-medium border-gray-200 dark:border-gray-700 dark:bg-[#1b2e4b] focus:border-emerald-500"
                                    placeholder="Gunakan kolom ini untuk menjelaskan kondisi anomali data atau informasi pendukung lainnya..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. MAIN UPLOAD AREA -->
                    <div class="panel">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 bg-primary/10 text-primary rounded-xl flex items-center justify-center font-black">
                                2</div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upload File</h3>
                        </div>

                        <div class="border-2 border-dashed rounded-3xl p-16 text-center transition-all cursor-pointer relative"
                            :class="isDragging ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/10' :
                                'border-gray-300 dark:border-gray-700 hover:border-emerald-400 bg-gray-50 dark:bg-black/20'"
                            @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)">
                            <input type="file" name="file_excel" id="file_input" class="hidden" accept=".xlsx,.xls"
                                @change="handleFileSelect($event)" required>

                            <div class="flex flex-col items-center">
                                <div
                                    class="w-24 h-24 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mb-6 shadow-inner text-emerald-600 dark:text-emerald-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="17 8 12 3 7 8" />
                                        <line x1="12" y1="3" x2="12" y2="15" />
                                    </svg>
                                </div>
                                <h4 class="text-2xl font-bold mb-2 text-gray-900 dark:text-white">Drag & Drop file Excel
                                    di sini</h4>
                                <button type="button" @click="document.getElementById('file_input').click()"
                                    class="btn btn-primary px-12 py-3.5 rounded-xl shadow-lg text-xs font-black tracking-widest">
                                    Pilih Berkas Lokal </button>
                            </div>
                        </div>

                        <!-- File Selected Info -->
                        <template x-if="uploadedFile">
                            <div
                                class="mt-8 p-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border-2 border-emerald-500 animate__animated animate__fadeInUp">
                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-16 h-16 bg-emerald-500 !text-white rounded-2xl flex items-center justify-center shadow-xl">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" class="!text-white">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <path d="M14 2v6h6" />
                                            <path d="M8 13h8" />
                                            <path d="M8 17h8" />
                                            <path d="M10 9H8" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-lg font-black text-emerald-900 dark:text-emerald-300"
                                            x-text="uploadedFile.name"></p>
                                        <p class="text-xs text-emerald-600 dark:text-emerald-500 font-bold tracking-widest"
                                            x-text="formatSize(uploadedFile.size)"></p>
                                    </div>
                                    <div class="flex gap-4">
                                        <button type="submit"
                                            class="btn btn-primary px-10 rounded-xl shadow-md text-xs font-black tracking-widest">
                                            Integrasikan Data </button>
                                        <button type="button" @click="resetUpload();"
                                            class="btn btn-outline-danger px-8 rounded-xl text-xs font-black tracking-widest">
                                            Batal </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </form>

            <!-- 3. PANEL RIWAYAT UPLOAD TERAKHIR -->
            <div class="panel">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Riwayat Upload Terakhir</h3>
                </div>
                <div class="table-responsive">
                    <table class="table-hover">
                        <thead>
                            <tr
                                class="text-[10px] font-black tracking-widest text-gray-400 dark:text-gray-500 border-b dark:border-gray-700">
                                <th class="py-4">ID Transaksi</th>
                                <th class="py-4">Dokumen Laporan</th>
                                <th class="py-4">Kategori Data</th>
                                <th class="py-4 text-center">Status</th>
                                <th class="py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm font-semibold">
                            

                            <!-- DATA DUMMY (Akan Muncul Selama DB Belum Diaktifkan) -->
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors border-b dark:border-gray-800">
                                <td class="py-4 text-primary font-black">KV-202603-0001</td>
                                <td class="py-4 font-bold text-gray-900 dark:text-white tracking-tight">
                                    monitoring_kebun_maret_2026.xlsx</td>
                                <td class="py-4"><span
                                        class="badge badge-outline-primary rounded-full px-4 text-[9px] font-black">Korelasi
                                        Vegetatif</span></td>
                                <td class="py-4 text-center">
                                    <span
                                        class="badge bg-success !text-white rounded-full px-5 text-[9px] font-black shadow-sm">Success</span>
                                </td>
                                <td class="py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            @click="triggerEdit(101, 'Korelasi Vegetatif', 'Maret 2026', 'Monitoring Semester I - Sei Dadap')"
                                            class="btn btn-sm btn-outline-primary rounded-lg text-[10px] font-black tracking-widest">Edit</button>
                                        <button @click="triggerDelete(101)"
                                            class="btn btn-sm btn-outline-danger rounded-lg text-[10px] font-black tracking-widest">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr
                                class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors border-b dark:border-gray-800">
                                <td class="py-4 text-primary font-black">RT-202602-0012</td>
                                <td class="py-4 font-bold text-gray-900 dark:text-white tracking-tight">
                                    rekap_tbm_februari_final.xlsx</td>
                                <td class="py-4"><span
                                        class="badge badge-outline-primary rounded-full px-4 text-[9px] font-black">Rekap
                                        TBM</span></td>
                                <td class="py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <span
                                            class="badge bg-danger !text-white rounded-full px-5 text-[9px] font-black">Failed</span>
                                        <button @click="viewErrorLog('Format tanggal tidak valid pada baris 42.')"
                                            class="text-[9px] text-danger font-bold hover:underline">Lihat Log
                                            Error</button>
                                    </div>
                                </td>
                                <td class="py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button
                                            @click="triggerEdit(102, 'Rekap TBM', 'Februari 2026', 'Rekap TBM Kebun Sei Dadap')"
                                            class="btn btn-sm btn-outline-primary rounded-lg text-[10px] font-black">Edit</button>
                                        <button @click="triggerDelete(102)"
                                            class="btn btn-sm btn-outline-danger rounded-lg text-[10px] font-black">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- MODAL LOG ERROR -->
        <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[rgba(0,0,0,0.6)]"
            :class="showErrorModal && '!block'">
            <div class="flex min-h-screen items-center justify-center px-4 animate__animated"
                :class="showErrorModal && 'animate__zoomIn'">
                <div
                    class="panel w-full max-w-[500px] overflow-hidden border-0 p-0 rounded-3xl shadow-2xl dark:bg-[#191e3a]">
                    <div class="flex items-center justify-between bg-red-600 px-8 py-5 !text-white">
                        <h5 class="text-lg font-bold tracking-widest !text-white">Detail Kegagalan Ingesti</h5>
                        <button type="button" @click="showErrorModal = false" class="!text-white hover:opacity-70">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="p-8 text-center">
                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-red-50 text-red-500 shadow-inner">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <h4 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Validasi Gagal</h4>
                        <div class="bg-gray-50 dark:bg-black/20 p-4 rounded-2xl border border-red-100 text-left mb-8">
                            <p class="text-sm font-semibold text-red-700 dark:text-red-400" x-text="errorLogContent">
                            </p>
                        </div>
                        <button type="button"
                            class="btn btn-outline-danger w-full rounded-xl py-3 text-xs font-black"
                            @click="showErrorModal = false">Tutup Laporan</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT METADATA -->
        <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[rgba(0,0,0,0.6)]"
            :class="showEditModal && '!block'">
            <div class="flex min-h-screen items-center justify-center px-4 animate__animated"
                :class="showEditModal && 'animate__zoomIn'">
                <div
                    class="panel w-full max-w-[600px] overflow-hidden border-0 p-0 rounded-3xl shadow-2xl dark:bg-[#191e3a]">
                    <div class="flex items-center justify-between bg-emerald-600 px-8 py-5 !text-white">
                        <h5 class="text-lg font-bold tracking-widest !text-white">Koreksi Metadata Ingesti
                        </h5>
                        <button type="button" @click="showEditModal = false" class="!text-white hover:opacity-70">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="px-8 py-8 text-left">
                        <form action="javascript:;" method="POST" class="space-y-5">
                            
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Nama
                                    Berkas Sumber (Read Only)</label>
                                <input type="text"
                                    class="form-input bg-gray-50 dark:bg-black/40 font-bold border-none dark:text-white-dark rounded-xl py-3"
                                    value="monitoring_kebun_maret_2026.xlsx" readonly>
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Judul
                                    Laporan / Berkas</label>
                                <input type="text" x-model="editForm.judul"
                                    class="form-input font-bold rounded-xl py-3 dark:bg-[#1b2e4b] dark:border-gray-700">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Jenis
                                        Dataset</label>
                                    <select x-model="editForm.kategori"
                                        class="form-select font-bold rounded-xl py-3 dark:bg-[#1b2e4b] dark:border-gray-700">
                                        <option value="Rekap TBM">Rekapitulasi TBM III (Populasi)</option>
                                        <option value="Korelasi Vegetatif">Korelasi Vegetatif (Biometrik)</option>
                                        <option value="Lokasi Kebun">Data Geospasial (Lokasi Peta)</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-black text-gray-400 dark:text-gray-500 mb-2 block tracking-widest">Periode
                                        Data</label>
                                    <select x-model="editForm.periode"
                                        class="form-select font-bold rounded-xl py-3 dark:bg-[#1b2e4b] dark:border-gray-700">
                                        <option>Januari 2026</option>
                                        <option>Februari 2026</option>
                                        <option>Maret 2026</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-10 flex items-center justify-end gap-3">
                                <button type="button"
                                    class="btn btn-outline-danger px-8 rounded-xl py-2.5 text-xs font-black"
                                    @click="showEditModal = false">Batal</button>
                                <button type="submit"
                                    class="btn btn-primary px-8 rounded-xl py-2.5 shadow-lg shadow-emerald-200 dark:shadow-none font-black text-xs">Simpan
                                    Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL HAPUS -->
        <div class="fixed inset-0 z-[999] hidden overflow-y-auto bg-[rgba(0,0,0,0.6)]"
            :class="showDeleteModal && '!block'">
            <div class="flex min-h-screen items-center justify-center px-4 animate__animated"
                :class="showDeleteModal && 'animate__zoomIn'">
                <div
                    class="panel w-full max-w-[450px] overflow-hidden border-0 p-0 rounded-3xl shadow-2xl text-center p-12 dark:bg-[#191e3a]">
                    <div
                        class="mx-auto mb-8 flex h-28 w-28 items-center justify-center rounded-full bg-red-50 text-red-500 shadow-inner">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                            </path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </div>
                    <h4 class="mb-3 text-2xl font-bold text-gray-900 dark:text-white">Konfirmasi Hapus</h4>
                    <p class="text-gray-500 text-sm">Data monitoring rill akan hilang dari sistem secara permanen.</p>
                    <div class="mt-12 flex items-center justify-center gap-4">
                        <button type="button" class="btn btn-outline-danger px-10 rounded-xl py-3 text-xs font-black"
                            @click="showDeleteModal = false">Batal</button>
                        <button type="button"
                            class="btn btn-primary px-10 rounded-xl py-3 shadow-lg text-xs font-black">Ya,
                            Hapus Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("uploadHandler", () => ({
                isDragging: false,
                uploadedFile: null,
                showDeleteModal: false,
                showEditModal: false,
                showErrorModal: false,
                errorLogContent: '',
                selectedItemId: null,
                editForm: {
                    kategori: '',
                    periode: '',
                    judul: ''
                },

                handleDrop(e) {
                    this.isDragging = false;
                    const files = e.dataTransfer.files;
                    if (files.length > 0) this.processFile(files[0]);
                },
                handleFileSelect(e) {
                    const files = e.target.files;
                    if (files.length > 0) this.processFile(files[0]);
                },
                processFile(file) {
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (ext !== 'xlsx' && ext !== 'xls') {
                        alert('Gunakan file Excel!');
                        return;
                    }
                    this.uploadedFile = file;
                },
                formatSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },
                resetUpload() {
                    this.uploadedFile = null;
                    document.getElementById('file_input').value = '';
                },
                triggerDelete(id) {
                    this.showDeleteModal = true;
                },
                triggerEdit(id, kat, per, jud) {
                    this.selectedItemId = id;
                    this.editForm.kategori = kat;
                    this.editForm.periode = per;
                    this.editForm.judul = jud;
                    this.showEditModal = true;
                },
                viewErrorLog(content) {
                    this.errorLogContent = content;
                    this.showErrorModal = true;
                }
            }));
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9d5893b966d42bc9a39e2bb81c9df0c6)): ?>
<?php $attributes = $__attributesOriginal9d5893b966d42bc9a39e2bb81c9df0c6; ?>
<?php unset($__attributesOriginal9d5893b966d42bc9a39e2bb81c9df0c6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9d5893b966d42bc9a39e2bb81c9df0c6)): ?>
<?php $component = $__componentOriginal9d5893b966d42bc9a39e2bb81c9df0c6; ?>
<?php unset($__componentOriginal9d5893b966d42bc9a39e2bb81c9df0c6); ?>
<?php endif; ?>
<?php /**PATH C:\simtan-monitoring-palm-oil\resources\views/apps/monitoring/import.blade.php ENDPATH**/ ?>