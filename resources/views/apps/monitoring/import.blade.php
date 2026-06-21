<x-layout.default>
    <!-- Wrapper Utama Proses Unggah Data SIMTAN -->
    <div x-data="uploadHandler()" class="relative text-slate-900 dark:text-white transition-colors duration-300" x-cloak>

        <!-- HEADER SECTION -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="text-left font-bold">
                <!-- Navigasi Breadcrumb Standar SIMTAN -->
                <ul class="flex space-x-2 text-xs mb-2 text-white-dark tracking-widest font-black uppercase font-plus-jakarta">
                    <li><a href="{{ route('index') }}" class="text-primary hover:underline font-black">Monitoring</a></li>
                    <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 font-black text-slate-400">Upload Data</li>
                </ul>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tighter leading-none font-plus-jakarta">
                    Pusat Proses Unggah Data
                </h1>
                <p class="text-xs font-bold italic text-gray-500 dark:text-gray-400 mt-1 border-l-2 border-primary pl-2 tracking-tight font-plus-jakarta">
                    Sistem Integrasi Terpadu - PTPN IV Regional I
                </p>
            </div>
        </div>

        <!-- FORM INPUT & UPLOAD SECTION -->
        <!-- Validasi data asinkronous untuk data sensus perkebunan kelapa sawit TBM III -->
        <form @submit.prevent="submitImport($event)" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-black font-plus-jakarta">
                
                <!-- PANEL 1: IDENTIFIKASI METADATA -->
                <div class="lg:col-span-1 space-y-6 text-left">
                    <div class="panel bg-white dark:bg-[#0f172a] border border-[#e0e6ed] dark:border-[#253b5c] shadow-sm rounded-2xl p-6 transition-all">
                        <div class="flex items-center gap-3 mb-6 border-b border-gray-100 dark:border-[#1e293b] pb-4">
                            <div class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center font-black italic shadow-md">
                                1
                            </div>
                            <h3 class="text-slate-900 dark:text-white font-black tracking-tight font-plus-jakarta">Identifikasi</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-[11px] text-slate-900 dark:text-white tracking-widest mb-1.5 block italic font-plus-jakarta uppercase">
                                    Judul Laporan Unggah Data
                                </label>
                                <input type="text" name="judul_file" value="{{ old('judul_file') }}" class="form-input" placeholder="INPUT JUDUL LAPORAN..." required>
                            </div>

                            <div>
                                <label class="text-[11px] text-slate-900 dark:text-white tracking-widest mb-1.5 block italic font-plus-jakarta uppercase">
                                    Penanggung Jawab Unggah Data
                                </label>
                                <input type="text" name="personel" value="{{ old('personel') }}" class="form-input" placeholder="NAMA PERSONEL PJ..." required>
                            </div>

                            <div>
                                <label class="text-[11px] text-slate-900 dark:text-white tracking-widest mb-1.5 block italic font-plus-jakarta uppercase">
                                    Kategori Dataset
                                </label>
                                <select x-model="kategori" name="kategori_file" class="form-select" required>
                                    <option value="">PILIH KATEGORI DATASET...</option>
                                    <option value="Rekap TBM">REKAPITULASI TBM III</option>
                                    <option value="Korelasi Vegetatif">KORELASI VEGETATIF</option>
                                    <option value="Lokasi Kebun">LOKASI PETA (GIS MASTER)</option>
                                </select>
                            </div>

                            <div x-show="kategori !== 'Lokasi Kebun' && kategori !== ''" x-transition x-cloak>
                                <label class="text-[11px] text-slate-900 dark:text-white tracking-widest mb-1.5 block italic font-plus-jakarta uppercase">
                                    Periode Laporan Sensus
                                </label>
                                <select name="periode_data" class="form-select" :required="kategori !== 'Lokasi Kebun' && kategori !== ''">
                                    <option value="">PILIH PERIODE LAPORAN...</option>
                                    @foreach ($listPeriode as $slug => $info)
                                        <option value="{{ $info['db_key'] }}">{{ strtoupper($info['label']) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-[11px] text-slate-900 dark:text-white tracking-widest mb-1.5 block italic font-plus-jakarta uppercase">
                                    Catatan Perubahan Parameter
                                </label>
                                <textarea name="notes" rows="3" class="form-textarea" placeholder="Catatan tambahan unggah data..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: DROPZONE AREA BERKAS -->
                <div class="lg:col-span-2">
                    <div class="panel bg-white dark:bg-[#0f172a] border border-[#e0e6ed] dark:border-[#253b5c] shadow-sm rounded-2xl p-6 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-6 font-black text-sm">
                            <div class="w-8 h-8 bg-success text-white rounded-lg flex items-center justify-center font-black italic shadow-md">
                                2
                            </div>
                            <h3 class="text-slate-900 dark:text-white leading-none font-black tracking-tight font-plus-jakarta">
                                Berkas Sumber (Excel)
                            </h3>
                        </div>

                        <div class="flex-1 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-[2rem] p-8 text-center transition-all relative hover:border-primary dark:hover:border-primary-light bg-slate-50/50 dark:bg-black/10 group overflow-hidden"
                            :class="isDragging ? 'border-primary bg-primary/5 ring-8 ring-primary/5' : ''"
                            @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)">

                            <input type="file" name="file_excel" x-ref="fileInput" class="hidden" accept=".xlsx,.xls" @change="handleFileSelect($event)">

                            <div class="flex flex-col items-center cursor-pointer h-full justify-center" @click="$refs.fileInput.click()">
                                <div class="w-24 h-24 bg-primary/10 text-primary dark:text-primary-light rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-all duration-500 shadow-inner">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <h4 class="text-xl font-black text-slate-900 dark:text-white tracking-tighter font-plus-jakarta">
                                    Drop Berkas Disini
                                </h4>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-2 font-bold tracking-[0.2em] leading-none font-plus-jakarta">
                                    Max 20MB (.xlsx / .xls)
                                </p>
                            </div>
                        </div>

                        <!-- PRATINJAU BERKAS (Glassmorphism & Responsif Adaptif) -->
                        <div x-show="uploadedFile" x-transition
                            class="mt-4 p-5 bg-white/10 dark:bg-black/30 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-2xl shadow-xl text-slate-900 dark:text-white font-black">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center w-full">
                                <div class="col-span-12 md:col-span-8 flex items-center gap-4 min-w-0">
                                    <div class="w-12 h-12 bg-primary/20 text-primary dark:text-primary-light rounded-lg flex items-center justify-center font-black text-xl shadow-inner italic shrink-0 font-plus-jakarta">
                                        XL
                                    </div>
                                    <div class="text-left leading-tight min-w-0 flex-1">
                                        <p class="text-sm font-black truncate text-slate-900 dark:text-white font-plus-jakarta"
                                            x-text="uploadedFile ? uploadedFile.name : ''" :title="uploadedFile?.name"></p>
                                        <p class="text-[9px] text-slate-500 dark:text-slate-400 font-bold tracking-widest italic mt-1 font-plus-jakarta"
                                            x-text="uploadedFile ? formatSize(uploadedFile.size) : ''"></p>
                                    </div>
                                </div>
                                <div class="col-span-12 md:col-span-4 flex gap-3 w-full md:justify-end">
                                    <button type="submit" :disabled="isProcessing"
                                        class="btn btn-primary flex-1 md:px-6 rounded-lg font-black border-none py-3 text-[11px] tracking-widest transition-all active:scale-95 disabled:opacity-50 font-plus-jakarta">
                                        <span x-show="!isProcessing">Sinkronisasi Berkas</span>
                                        <span x-show="isProcessing">Loading...</span>
                                    </button>
                                    <!-- Tombol Batal bergaya Ghost Button elegan demi kenyamanan visual HCI -->
                                    <button type="button" @click="resetUpload"
                                        class="border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 px-5 rounded-lg font-black py-3 text-[11px] tracking-widest transition-all active:scale-95 font-plus-jakarta">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- RIWAYAT TABEL UNGGAH DATA -->
        <div class="mt-12 text-left">
            <h3 class="text-xl font-black text-slate-900 dark:text-white mb-4 italic tracking-widest leading-none font-plus-jakarta">
                Riwayat Unggah Data
            </h3>
            <div class="table-responsive bg-white dark:bg-[#0f172a] rounded-2xl shadow-sm border border-[#e0e6ed] dark:border-[#253b5c] overflow-hidden ring-1 ring-black/5">
                <table class="w-full min-w-[1000px] border-collapse font-black font-plus-jakarta">
                    <thead>
                        <tr class="text-[10px] font-black tracking-widest text-slate-700 dark:text-slate-300 border-b border-gray-100 dark:border-[#1e293b] bg-gray-50 dark:bg-black/40 italic uppercase">
                            <th class="py-4 px-4 text-left">ID Transaksi</th>
                            <th class="py-4 px-4 text-left">Dokumen Laporan</th>
                            <th class="py-4 px-4 text-center">Jenis Dataset</th>
                            <th class="py-4 px-4 text-center">Status</th>
                            <th class="py-4 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-[12px] font-bold text-slate-900 dark:text-white">
                        @forelse($history as $log)
                            @php $item = $log->form; @endphp
                            <tr class="bg-white dark:bg-[#121e32] border-b border-gray-50 dark:border-[#1e293b] hover:bg-gray-50 dark:hover:bg-black/20 transition-all">
                                <td class="py-6 px-4 text-primary dark:text-primary-light font-black italic">
                                    #{{ $item->kode_upload ?? 'N/A' }}
                                </td>
                                <td class="px-4 text-left">
                                    <div class="text-sm font-black leading-tight font-plus-jakarta text-slate-900 dark:text-white">
                                        {{ $item->judul_file ?? 'Invalid Metadata' }}
                                    </div>
                                    <div class="text-[9px] text-slate-500 dark:text-slate-400 font-bold mt-1 tracking-tighter italic font-plus-jakarta">
                                        {{ $log->nama_file }} • {{ $log->created_at->format('d/m/Y H:i') }} •
                                        {{ number_format($log->rows_imported ?? 0) }} Baris
                                    </div>
                                </td>
                                <td class="text-center px-4">
                                    <span class="badge bg-primary/10 text-primary dark:text-primary-light text-[9px] px-3 py-1 rounded-full font-black tracking-widest border border-primary/20 font-plus-jakarta">
                                        {{ $log->jenis_dataset }}
                                    </span>
                                </td>
                                <td class="text-center px-4">
                                    @if ($log->status == 'Success')
                                        <span class="badge bg-success/10 text-success font-black text-[9px] px-4 py-1 rounded-full border border-success/20 tracking-widest font-plus-jakarta">SUCCESS</span>
                                    @else
                                        <div class="flex flex-col items-center gap-1 font-plus-jakarta">
                                            <span class="badge bg-danger/10 text-danger font-black text-[9px] px-4 py-1 rounded-full border border-danger/20 tracking-widest font-black">FAILED</span>
                                            <button type="button" @click="viewErrorLog($el.dataset.msg)" data-msg="{{ $log->message }}" class="text-[8px] text-rose-500 hover:underline italic font-black tracking-widest">
                                                Detail Log
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($item)
                                            <a href="{{ route('monitoring.import.download', $item->id) }}"
                                                class="p-2 bg-success/10 text-success rounded-lg hover:bg-success hover:text-white transition-all border border-success/20 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                            <button type="button"
                                                @click="triggerEdit('{{ $item->id }}', '{{ $item->kategori_file }}', '{{ $item->personel_pj }}', '{{ $item->periode_data }}', '{{ $item->judul_file }}', '{{ addslashes($item->notes) }}')"
                                                class="p-2 bg-primary/10 text-primary rounded-lg hover:bg-primary hover:text-white transition-all border border-primary/20 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>
                                            <button type="button" @click="triggerDelete('{{ $item->id }}')"
                                                class="p-2 bg-danger/10 text-danger rounded-lg hover:bg-danger hover:text-white transition-all border border-danger/20 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-20 text-center text-slate-400 font-black opacity-30 tracking-widest italic font-plus-jakarta">
                                    Belum ada riwayat unggah data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- LOADING INDICATOR OVERLAY (Transparan & Blur) -->
        <template x-teleport="body">
            <div x-show="isProcessing" class="fixed inset-0 z-[999999] flex flex-col items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-cloak>
                <div class="w-24 h-24 md:w-32 border-[12px] border-primary/10 border-t-primary rounded-full animate-spin mb-6 shadow-2xl shadow-primary/20"></div>
                <h2 class="text-xl md:text-3xl font-black text-white tracking-tighter animate-pulse leading-none font-plus-jakarta" x-text="processingText">
                    Sedang Mengintegrasikan Data Terpadu...
                </h2>
                <p class="text-emerald-400 font-black tracking-[0.5em] text-[11px] md:text-xs mt-4 italic font-plus-jakarta uppercase">
                    Sinkronisasi Berkas SIMTAN
                </p>
            </div>
        </template>

        <!-- ERROR DETAIL MODAL -->
        <template x-teleport="body">
            <div class="fixed inset-0 z-[99999] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 font-black font-plus-jakarta"
                x-show="showErrorModal" x-transition x-cloak @click.away="showErrorModal = false">
                <div class="relative w-full max-w-lg shadow-2xl transition-all my-auto">
                    <div class="border-0 p-8 rounded-[2.5rem] shadow-2xl bg-white dark:bg-[#1b2e4b] text-center border border-slate-200 dark:border-slate-800">
                        <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 shadow-inner italic text-5xl font-black leading-none rotate-3">
                            !
                        </div>
                        <h4 class="mb-4 text-2xl text-slate-900 dark:text-white tracking-tighter font-black font-plus-jakarta">
                            Integrasi Gagal
                        </h4>
                        <div class="bg-rose-50 dark:bg-black/40 p-6 rounded-2xl text-left mb-8 text-rose-700 dark:text-rose-400 font-mono text-[11px] leading-relaxed shadow-inner border border-rose-100 dark:border-rose-900/20 overflow-x-auto max-h-[300px]"
                            x-text="errorLogContent"></div>
                        <button type="button" class="btn btn-danger w-full rounded-2xl py-5 font-black text-xs tracking-widest shadow-xl italic font-plus-jakarta" @click="showErrorModal = false">
                            Tutup Laporan
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- KOREKSI METADATA UNGGAH DATA MODAL (GLASSMORPHISM, SYMMETRIC, GHOST BUTTONS & PLUS JAKARTA SANS) -->
        <template x-teleport="body">
            <div class="fixed inset-0 z-[99999] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 transition-all duration-300 overflow-hidden"
                x-show="showEditModal" x-transition x-cloak @click.self="showEditModal = false">
                <div class="relative w-full max-w-xl rounded-[2rem] overflow-hidden shadow-2xl border flex flex-col max-h-[90vh] my-auto scale-100 transform transition-transform"
                    :style="$store.app.theme === 'dark' || $store.app.isDarkMode ? 'background-color: #060818 !important; border-color: #1e293b !important;' : 'background-color: #ffffff !important; border-color: #cbd5e1 !important;'">
                    
                    <!-- Header Modal dengan Vibrant Gradient (Emerald PTPN / Dark Slate) -->
                    <div class="bg-[#0e1726] px-8 py-6 text-white flex justify-between items-center shadow-lg shrink-0" style="background-color: #0e1726 !important;">
                        <div class="text-left font-black font-plus-jakarta">
                            <h5 class="text-xl font-black tracking-tighter italic leading-none">
                                Koreksi Metadata Unggah Data
                            </h5>
                            <p class="text-[10px] opacity-75 font-bold tracking-[0.2em] mt-2 italic leading-none">
                                Sinkronisasi Parameter Dataset
                            </p>
                        </div>
                        <button @click="showEditModal = false" type="button" class="hover:rotate-90 transition-all duration-300 p-2 text-white hover:text-slate-200 dark:hover:text-emerald-400 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                <path d="M18 6L6 18M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Scrollable Form Body -->
                    <div class="p-8 overflow-y-auto custom-scrollbar flex-1 text-left font-black font-plus-jakarta">
                        <form @submit.prevent="submitUpdate($event)" class="space-y-6">
                            @csrf 
                            @method('PUT')
                            
                            <div class="space-y-4">
                                <div class="flex flex-col gap-1.5 text-left">
                                    <label class="text-[11px] font-black text-slate-900 dark:text-emerald-400 block italic tracking-widest font-plus-jakarta uppercase">
                                        Judul Laporan Baru
                                    </label>
                                    <input type="text" name="judul_file" x-model="editForm.judul" class="form-input shadow-inner py-3.5" required style="height: auto !important;">
                                </div>
                                
                                <div class="flex flex-col gap-1.5 text-left">
                                    <label class="text-[11px] font-black text-slate-900 dark:text-emerald-400 block italic tracking-widest font-plus-jakarta uppercase">
                                        Penanggung Jawab Baru
                                    </label>
                                    <input type="text" name="personel" x-model="editForm.personel" class="form-input shadow-inner py-3.5" required style="height: auto !important;">
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="flex flex-col gap-1.5 text-left">
                                        <label class="text-[11px] font-black text-slate-900 dark:text-emerald-400 block italic font-plus-jakarta uppercase">
                                            Kategori Dataset
                                        </label>
                                        <select name="kategori_file" x-model="editForm.kategori" class="form-select shadow-inner" required style="height: auto !important;">
                                            <option value="Rekap TBM">Rekap TBM III</option>
                                            <option value="Korelasi Vegetatif">Korelasi Vegetatif</option>
                                            <option value="Lokasi Kebun">Data GIS Master</option>
                                        </select>
                                    </div>
                                    
                                    <div x-show="editForm.kategori !== 'Lokasi Kebun'" class="flex flex-col gap-1.5 text-left" x-transition>
                                        <label class="text-[11px] font-black text-slate-900 dark:text-emerald-400 block italic font-plus-jakarta uppercase">
                                            Periode Laporan
                                        </label>
                                        <select name="periode_data" x-model="editForm.periode" class="form-select shadow-inner" :required="editForm.kategori !== 'Lokasi Kebun'" style="height: auto !important;">
                                            <option value="Tahun 2025">TAHUNAN 2025</option>
                                            <option value="JANFEBMARAPR2025REKAP">PERIODE 1</option>
                                            <option value="MEIJULJUNAGST2025REKAP">PERIODE 2</option>
                                            <option value="SEPOKTNOVDES2025REKAP">PERIODE 3</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-1.5 text-left">
                                    <label class="text-[11px] font-black text-slate-900 dark:text-emerald-400 block italic font-plus-jakarta uppercase">
                                        Catatan Penyesuaian
                                    </label>
                                    <textarea name="notes" x-model="editForm.notes" rows="3" class="form-textarea shadow-inner" style="height: auto !important;"></textarea>
                                </div>
                            </div>
                            
                            <!-- Aksi Interaksi Sinkronisasi -->
                            <div class="flex flex-col sm:flex-row gap-4 pt-4 shrink-0 font-plus-jakarta">
                                <!-- Tombol Batal menggunakan gaya Ghost Button elegan demi visibilitas optimal -->
                                <button type="button" 
                                        class="border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 flex-1 py-4 font-black rounded-xl tracking-widest transition-all italic shadow-sm leading-none focus:outline-none"
                                        @click="showEditModal = false">
                                    Batalkan Perubahan
                                </button>
                                <button type="submit" 
                                        class="btn btn-primary flex-1 py-4 font-black rounded-xl shadow-2xl tracking-[0.2em] transition-all hover:scale-[1.02] leading-none italic">
                                    Perbarui Parameter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- DELETE CONFIRMATION MODAL -->
        <template x-teleport="body">
            <div x-show="showDeleteModal" class="fixed inset-0 z-[99999] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 transition-all" x-transition x-cloak>
                <div class="bg-white dark:bg-[#1e293b] w-full max-w-sm p-10 rounded-[3rem] shadow-2xl text-center border border-gray-200 dark:border-[#334155] my-auto font-plus-jakarta">
                    <div class="mx-auto mb-6 h-16 w-16 bg-rose-50 dark:bg-rose-900/30 text-rose-600 flex items-center justify-center rounded-3xl text-4xl font-black shadow-inner animate-bounce ring-4 ring-rose-500/10 italic">
                        !
                    </div>
                    <h4 class="text-xl font-black tracking-tight text-slate-900 dark:text-white leading-none font-plus-jakarta">
                        Hapus Dataset?
                    </h4>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold tracking-widest mt-4 italic leading-tight font-plus-jakarta">
                        Dataset rill & berkas fisik akan hilang permanen dari server!
                    </p>
                    <div class="mt-8 flex gap-3">
                        <button type="button" class="border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 flex-1 py-4 rounded-xl font-black text-[10px] tracking-widest transition-all italic leading-none font-plus-jakarta" @click="showDeleteModal = false">
                            Batal
                        </button>
                        <form @submit.prevent="submitDelete($event)" class="flex-1">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-full py-4 rounded-xl font-black text-[10px] tracking-widest shadow-lg shadow-rose-500/20 italic transition-all hover:bg-rose-700 leading-none">
                                Eksekusi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- JAVASCRIPT LOGIC (ASYNCHRONOUS DATA INTEGRATION ENGINE) -->
    <!-- Alur proses validasi data dan manajemen state asinkron halaman unggah berkas -->
    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("uploadHandler", () => ({
                isDragging: false,
                uploadedFile: null,
                isProcessing: false,
                processingText: 'Sedang Memproses...',
                kategori: '',
                showDeleteModal: false,
                showEditModal: false,
                showErrorModal: false,
                errorLogContent: '',
                selectedItemId: null,
                editForm: {
                    kategori: '',
                    periode: '',
                    judul: '',
                    personel: '',
                    notes: ''
                },

                handleDrop(e) {
                    this.isDragging = false;
                    this.processFile(e.dataTransfer.files[0]);
                },
                handleFileSelect(e) {
                    this.processFile(e.target.files[0]);
                },
                processFile(file) {
                    if (!file) return;
                    if (file.size > 20 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Batas Ukuran',
                            text: 'Ukuran berkas melebihi batas maksimal 20MB.',
                            customClass: {
                                popup: 'font-plus-jakarta rounded-2xl shadow-xl'
                            }
                        });
                        return;
                    }
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!['xlsx', 'xls'].includes(ext)) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ekstensi Berkas',
                            text: 'Format berkas harus Microsoft Excel (.xlsx/.xls).',
                            customClass: {
                                popup: 'font-plus-jakarta rounded-2xl shadow-xl'
                            }
                        });
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
                    if (this.$refs.fileInput) this.$refs.fileInput.value = '';
                },
                triggerDelete(id) {
                    this.selectedItemId = id;
                    this.showDeleteModal = true;
                },
                triggerEdit(id, kat, pers, per, jud, not) {
                    this.selectedItemId = id;
                    this.editForm.kategori = kat;
                    this.editForm.periode = per;
                    this.editForm.judul = jud;
                    this.editForm.personel = pers;
                    this.editForm.notes = not;
                    this.showEditModal = true;
                },
                viewErrorLog(content) {
                    this.errorLogContent = content;
                    this.showErrorModal = true;
                },

                // ALUR UNGGAH DATA ASINKRON (Bab 3: Integrasi Data Terpadu)
                // Mengirimkan berkas sensus lapangan baru secara asinkron
                submitImport(e) {
                    if (!this.uploadedFile) return;
                    this.isProcessing = true;
                    this.processingText = 'Sedang Mengintegrasikan Data Terpadu...';

                    const formData = new FormData(e.target);
                    formData.set('file_excel', this.uploadedFile);

                    fetch("{{ route('monitoring.import.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Integrasi Berkas Gagal.'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.isProcessing = false;
                        Swal.fire({
                            icon: 'success',
                            title: 'Integrasi Berhasil',
                            text: data.message,
                            background: 'transparent',
                            customClass: {
                                popup: 'bg-white/95 dark:bg-[#0e1726]/95 backdrop-blur-xl border border-white/20 dark:border-white/10 rounded-[2rem] shadow-2xl text-slate-900 dark:text-white font-plus-jakarta',
                                confirmButton: 'btn btn-primary rounded-xl px-6 py-2.5 text-xs font-black'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(err => {
                        this.isProcessing = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Integrasi Berkas Gagal',
                            text: err.message,
                            background: 'transparent',
                            customClass: {
                                popup: 'bg-white/95 dark:bg-[#0e1726]/95 backdrop-blur-xl border border-white/20 dark:border-white/10 rounded-[2rem] shadow-2xl text-slate-900 dark:text-white font-plus-jakarta',
                                confirmButton: 'btn btn-danger rounded-xl px-6 py-2.5 text-xs font-black'
                            },
                            buttonsStyling: false
                        });
                    });
                },

                // ALUR ASYNCHRONOUS UPDATE (Bab 3: Koreksi Parameter Metadata)
                // Memperbarui informasi metadata audit trail ke database tanpa memicu refresh
                submitUpdate(e) {
                    this.isProcessing = true;
                    this.processingText = 'Sedang Menyelaraskan Parameter Metadata...';

                    const formData = new FormData(e.target);

                    fetch(`/monitoring/import/${this.selectedItemId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Koreksi Parameter Gagal.'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.isProcessing = false;
                        this.showEditModal = false;
                        Swal.fire({
                            icon: 'success',
                            title: 'Koreksi Parameter Berhasil',
                            text: data.message,
                            background: 'transparent',
                            customClass: {
                                popup: 'bg-white/95 dark:bg-[#0e1726]/95 backdrop-blur-xl border border-white/20 dark:border-white/10 rounded-[2rem] shadow-2xl text-slate-900 dark:text-white font-plus-jakarta',
                                confirmButton: 'btn btn-primary rounded-xl px-6 py-2.5 text-xs font-black'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(err => {
                        this.isProcessing = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Pembaruan Gagal',
                            text: err.message,
                            background: 'transparent',
                            customClass: {
                                popup: 'bg-white/95 dark:bg-[#0e1726]/95 backdrop-blur-xl border border-white/20 dark:border-white/10 rounded-[2rem] shadow-2xl text-slate-900 dark:text-white font-plus-jakarta',
                                confirmButton: 'btn btn-danger rounded-xl px-6 py-2.5 text-xs font-black'
                            },
                            buttonsStyling: false
                        });
                    });
                },

                // ALUR ASYNCHRONOUS DELETE (Bab 3: Pembersihan Berkas Sensus)
                // Menghapus database log dan file Excel sensus asinkron
                submitDelete(e) {
                    this.isProcessing = true;
                    this.processingText = 'Sedang Menghapus Berkas Sensus...';
                    this.showDeleteModal = false;

                    const formData = new FormData(e.target);

                    fetch(`/monitoring/import/${this.selectedItemId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Gagal menghapus berkas.'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        this.isProcessing = false;
                        Swal.fire({
                            icon: 'success',
                            title: 'Penghapusan Berhasil',
                            text: data.message,
                            background: 'transparent',
                            customClass: {
                                popup: 'bg-white/95 dark:bg-[#0e1726]/95 backdrop-blur-xl border border-white/20 dark:border-white/10 rounded-[2rem] shadow-2xl text-slate-900 dark:text-white font-plus-jakarta',
                                confirmButton: 'btn btn-primary rounded-xl px-6 py-2.5 text-xs font-black'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.reload();
                        });
                    })
                    .catch(err => {
                        this.isProcessing = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Penghapusan Gagal',
                            text: err.message,
                            background: 'transparent',
                            customClass: {
                                popup: 'bg-white/95 dark:bg-[#0e1726]/95 backdrop-blur-xl border border-white/20 dark:border-white/10 rounded-[2rem] shadow-2xl text-slate-900 dark:text-white font-plus-jakarta',
                                confirmButton: 'btn btn-danger rounded-xl px-6 py-2.5 text-xs font-black'
                            },
                            buttonsStyling: false
                        });
                    });
                }
            }));
        });
    </script>

    <!-- TRULY ADAPTIVE STYLES -->
    <style>
        [x-cloak] {
            display: none !important;
        }

        .font-plus-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* Unified Form Styling - High Contrast & No Forced Black */
        .form-input,
        .form-select,
        .form-textarea {
            @apply rounded-xl transition-all duration-300 font-black text-sm w-full py-4 px-5 focus:ring-4 focus:ring-primary/10 font-plus-jakarta;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
            background-image: unset;
            height: auto !important;
        }

        .dark .form-input,
        .dark .form-select,
        .dark .form-textarea {
            background-color: #0f172a !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
            height: auto !important;
        }

        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 0.75rem center !important;
            background-repeat: no-repeat !important;
            background-size: 1.5em 1.5rem !important;
            padding-right: 2.5rem !important;
        }

        .form-select option {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        .dark .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
        }

        .dark .form-select option {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }

        /* Force table row background colors in both light and dark mode */
        table tbody tr td {
            background-color: #ffffff !important;
        }
        .dark table tbody tr td {
            background-color: #121e32 !important;
        }
        table tbody tr:hover td {
            background-color: #f9fafb !important;
        }
        .dark table tbody tr:hover td {
            background-color: #0c1422 !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            @apply bg-slate-200 dark:bg-slate-800 rounded-full transition-colors;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            @apply bg-slate-300 dark:bg-slate-700;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            @apply bg-slate-200 dark:bg-slate-800 rounded-full shadow-inner;
        }
    </style>
</x-layout.default>
