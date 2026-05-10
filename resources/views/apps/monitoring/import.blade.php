<x-layout.default>
    <!-- Wrapper Utama: Alpine Handler & x-cloak -->
    <div x-data="uploadHandler()" class="relative text-gray-900 dark:text-white-light" x-cloak>

        <!-- 1. HEADER SECTION -->
        <div
            class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="text-left font-bold">
                <ul class="flex space-x-2 text-xs mb-2 text-gray-500 dark:text-gray-400 tracking-widest">
                    <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                    <li
                        class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 text-gray-900 dark:text-white font-black">
                        Upload Data</li>
                </ul>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-none">
                    Pusat Ingesti Data</h1>
                <p class="text-xs font-bold italic text-gray-500 dark:text-gray-400 mt-1 border-l-2 border-primary pl-2">
                    Sistem Integrasi Multimoda - PTPN IV Regional I</p>
            </div>
        </div>

        <!-- 2. GLOBAL PROFESSIONAL TOASTS -->
        <template x-teleport="body">
            <div
                class="fixed top-5 right-5 z-[99999] isolate pointer-events-none space-y-3 w-full max-w-[350px] pointer-events-none">
                @if (session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="pointer-events-auto p-4 bg-white dark:bg-[#0e1726] rounded-xl shadow-2xl border-l-[6px] border-success flex items-center gap-3 ring-1 ring-black/5 dark:ring-white/10"
                        x-cloak>
                        <div
                            class="w-8 h-8 bg-success/10 text-success rounded-full flex items-center justify-center shrink-0 shadow-inner">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="3">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-[9px] text-gray-500 dark:text-gray-400 font-black tracking-widest">
                                Success</p>
                            <p class="text-xs text-gray-900 dark:text-white font-bold leading-tight">
                                {{ session('success') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </template>

        <!-- 3. FORM INPUT & UPLOAD SECTION -->
        <form action="{{ route('monitoring.import.store') }}" method="POST" enctype="multipart/form-data"
            @submit="isProcessing = true">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-black">
                <!-- PANEL 1: IDENTIFIKASI -->
                <div class="lg:col-span-1 space-y-6 text-left">
                    <div
                        class="panel bg-white dark:bg-[#1b2e4b] border border-gray-200 dark:border-transparent shadow-sm rounded-2xl p-6">
                        <div
                            class="flex items-center gap-3 mb-6 border-b border-gray-100 dark:border-gray-700 pb-4 text-sm">
                            <div
                                class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center font-black italic shadow-md">
                                1</div>
                            <h3 class="text-gray-900 dark:text-white font-black">Identifikasi</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label
                                    class="text-[12px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic leading-none">Judul
                                    Laporan</label>
                                <input type="text" name="judul_file" value="{{ old('judul_file') }}"
                                    class="form-input rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-black/30 text-gray-900 dark:text-white focus:border-primary py-3 text-[12px] font-black shadow-inner"
                                    placeholder="INPUT JUDUL..." required>
                            </div>

                            <div>
                                <label
                                    class="text-[12px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic leading-none text-left">Penanggung
                                    Jawab</label>
                                <input type="text" name="personel" value="{{ old('personel') }}"
                                    class="form-input rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-black/30 text-gray-900 dark:text-white focus:border-primary py-3 text-[12px] font-black shadow-inner"
                                    placeholder="NAMA PERSONEL..." required>
                            </div>

                            <div>
                                <label
                                    class="text-[12px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic leading-none text-left">Kategori
                                    Dataset</label>
                                <select x-model="kategori" name="kategori_file"
                                    class="form-select rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-black/30 text-gray-900 dark:text-white focus:border-primary text-[12px] font-black py-3"
                                    required>
                                    <option value="">PILIH KATEGORI...</option>
                                    <option value="Rekap TBM">REKAPITULASI TBM III</option>
                                    <option value="Korelasi Vegetatif">KORELASI VEGETATIF</option>
                                    <option value="Lokasi Kebun">LOKASI PETA (GIS MASTER)</option>
                                </select>
                            </div>

                            <div x-show="kategori !== 'Lokasi Kebun'" x-transition x-cloak>
                                <label
                                    class="text-[12px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic leading-none text-left">Periode
                                    Laporan</label>
                                <select name="periode_data"
                                    class="form-select rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-black/30 text-gray-900 dark:text-white focus:border-primary text-[12px] font-black py-3">
                                    <option value="JANFEBMAR 2025">JANFEBMAR 2025</option>
                                    <option value="APRMEIJUN 2025">APRMEIJUN 2025</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="text-[12px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic leading-none text-left">Keterangan
                                    Khusus</label>
                                <textarea name="notes" rows="3"
                                    class="form-textarea rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-black/30 text-gray-900 dark:text-white focus:border-primary text-[12px] leading-relaxed shadow-inner"
                                    placeholder="Anomali data lapangan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: UPLOAD AREA -->
                <div class="lg:col-span-2">
                    <div
                        class="panel bg-white dark:bg-[#1b2e4b] border border-gray-200 dark:border-transparent shadow-sm rounded-2xl p-6 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-6 font-black text-sm text-left">
                            <div
                                class="w-8 h-8 bg-success text-white rounded-lg flex items-center justify-center font-black italic shadow-md">
                                2</div>
                            <h3 class="text-gray-900 dark:text-white leading-none font-black">Berkas Sumber
                                (Excel)</h3>
                        </div>

                        <div class="flex-1 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center transition-all relative hover:border-primary dark:hover:border-primary bg-gray-50/50 dark:bg-black/10 group shadow-inner"
                            :class="isDragging ? 'border-primary bg-primary/5' : ''"
                            @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)">

                            <input type="file" name="file_excel" x-ref="fileInput" class="hidden" accept=".xlsx,.xls"
                                @change="handleFileSelect($event)" required>

                            <div class="flex flex-col items-center cursor-pointer h-full justify-center font-black"
                                @click="$refs.fileInput.click()">
                                <div
                                    class="w-16 h-16 bg-primary/5 text-primary rounded-full flex items-center justify-center mb-4 group-hover:scale-105 transition-transform shadow-sm text-primary">
                                    <svg class="w-8 h-8 text-gray-700 dark:text-gray-300" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <polyline points="17 8 12 3 7 8" />
                                        <line x1="12" y1="3" x2="12" y2="15" />
                                    </svg>
                                </div>
                                <h4 class="text-lg font-black text-gray-900 dark:text-white tracking-tighter">
                                    DRAG & DROP BERKAS</h4>
                                <p class="text-[12px] text-gray-400 mt-1 italic tracking-widest leading-none">
                                    Max 10MB (.xlsx / .xls)</p>
                            </div>
                        </div>

                        <!-- FEEDBACK PREVIEW -->
                        <div x-show="uploadedFile" x-transition
                            class="mt-4 p-5 bg-primary rounded-xl shadow-lg text-white font-black ring-4 ring-white/5">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-left">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-white rounded-lg flex items-center justify-center font-black text-primary text-xl shadow-inner italic">
                                        XL</div>
                                    <div>
                                        <p class="text-sm tracking-tight leading-none mb-1 font-black"
                                            x-text="uploadedFile ? uploadedFile.name : ''"></p>
                                        <p class="text-[9px] opacity-80 tracking-widest font-black leading-none italic"
                                            x-text="uploadedFile ? formatSize(uploadedFile.size) : ''"></p>
                                    </div>
                                </div>
                                <div class="flex gap-2 w-full md:w-auto">
                                    <button type="submit" :disabled="isProcessing"
                                        class="btn bg-white text-primary hover:bg-gray-100 flex-1 md:px-8 rounded-lg font-black border-none shadow-md py-3 text-[12px] disabled:opacity-50 transition-all active:scale-95">
                                        <span x-show="!isProcessing">Integrasikan</span>
                                        <span x-show="isProcessing">Loading...</span>
                                    </button>
                                    <button type="button" @click="resetUpload"
                                        class="btn bg-rose-500 text-white hover:bg-rose-600 px-6 rounded-lg font-black border-none shadow-lg py-3 text-[12px] transition-all active:scale-95">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- 4. RIWAYAT TABEL SECTION -->
        <div class="mt-12 text-left">
            <h3
                class="text-xl font-black text-gray-900 dark:text-white mb-4 italic tracking-widest text-left font-black">
                Riwayat Ingesti</h3>
            <div
                class="table-responsive bg-white dark:bg-[#1b2e4b] rounded-2xl shadow-sm border border-gray-200 dark:border-transparent ring-1 ring-black/5 dark:ring-white/5 overflow-hidden">
                <table class="w-full min-w-[1000px] table-hover border-collapse font-black text-left font-black">
                    <thead>
                        <tr
                            class="text-[12px] font-black tracking-widest text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700 italic bg-gray-50 dark:bg-black/20 text-left font-black">
                            <th class="py-4 px-4 text-left">ID Transaksi</th>
                            <th class="py-4 px-4 text-left">Dokumen Laporan</th>
                            <th class="py-4 px-4 text-center">Jenis Dataset</th>
                            <th class="py-4 px-4 text-center">Status</th>
                            <th class="py-4 px-4 text-center font-black">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-[12px] font-bold text-gray-900 dark:text-white font-black">
                        @forelse($history as $log)
                            @php $item = $log->form; @endphp
                            <tr
                                class="border-b border-gray-50 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-black/10 transition-all">
                                <td class="py-6 px-4 text-primary font-black italic">
                                    #{{ $item->kode_upload ?? 'UNREGISTERED' }}
                                </td>
                                <td class="px-4 text-left">
                                    <div class="text-sm font-black leading-tight text-gray-900 dark:text-white">
                                        {{ $item->judul_file ?? 'Invalid Metadata' }}
                                    </div>
                                    <div class="text-[9px] text-gray-500 font-bold mt-1">
                                        {{ $log->nama_file }} • {{ $log->created_at->format('d/m/Y H:i') }} •
                                        {{ number_format($log->rows_imported ?? 0) }} Baris
                                    </div>
                                </td>
                                <td class="text-center px-4">
                                    @php
                                        $catBadge = match ($log->jenis_dataset) {
                                            'Rekap TBM' => 'badge badge-outline-primary',
                                            'Korelasi Vegetatif' => 'badge badge-outline-info',
                                            'Lokasi Kebun' => 'badge badge-outline-secondary',
                                            default => 'badge badge-outline-dark',
                                        };
                                    @endphp

                                    <span
                                        class="{{ $catBadge }} text-[9px] px-3 py-1 rounded-full font-black whitespace-nowrap inline-flex items-center justify-center">
                                        {{ $log->jenis_dataset }}
                                    </span>
                                </td>
                                <td class="text-center px-4">
                                    @if ($log->status == 'Success')
                                        <span
                                            class="badge badge-outline-success font-black text-[9px] px-4 py-1 rounded-full">SUCCESS</span>
                                    @else
                                        <div class="flex flex-col items-center">
                                            <span
                                                class="badge badge-outline-danger font-black text-[9px] px-4 py-1 rounded-full">FAILED</span>
                                            <button type="button" @click="viewErrorLog($el.dataset.msg)"
                                                data-msg="{{ $log->message }}"
                                                class="text-[8px] text-rose-500 hover:underline mt-1 italic font-black">Lihat
                                                Log</button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($item)
                                            <a href="{{ route('monitoring.import.download', $item->id) }}"
                                                class="p-2 bg-gray-100 dark:bg-black/20 text-gray-700 dark:text-white rounded-lg hover:text-success shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path
                                                        d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                            <button type="button"
                                                @click="triggerEdit('{{ $item->id }}', '{{ $item->kategori_file }}', '{{ $item->personel_pj }}', '{{ $item->periode_data }}', '{{ $item->judul_file }}', '{{ addslashes($item->notes) }}')"
                                                class="btn btn-sm btn-outline-primary rounded-lg px-3 py-1.5 font-black text-[9px] italic">Edit</button>
                                            <button type="button" @click="triggerDelete('{{ $item->id }}')"
                                                class="btn btn-sm btn-outline-danger rounded-lg px-3 py-1.5 font-black text-[9px] italic">Hapus</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="py-20 text-center text-gray-400 font-black opacity-30 tracking-widest italic">
                                    Belum ada riwayat ingesti</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. NEURAL PROCESSING OVERLAY -->
        <template x-teleport="body">
            <div x-show="isProcessing"
                class="fixed inset-0 z-[999999] flex flex-col items-center justify-center isolate font-black font-black"
                x-cloak>
                <div
                    class="absolute inset-0 bg-white/80 dark:bg-black/85 backdrop-blur-md transition-all duration-300 font-black">
                </div>
                <div
                    class="relative z-10 flex flex-col items-center p-6 text-center animate__animated animate__fadeIn font-black font-black font-black">
                    <div
                        class="w-24 h-24 md:w-32 md:h-32 border-[12px] md:border-[12px] border-primary/10 border-t-primary rounded-full animate-spin mb-6 shadow-[0_0_80px_rgba(67,97,238,0.4)] font-black">
                    </div>
                    <h2
                        class="text-xl md:text-3xl font-black tracking-tighter animate-pulse text-gray-900 dark:text-white leading-none font-black">
                        Sedang Memproses...</h2>
                    <p
                        class="text-gray-500 dark:text-gray-400 font-black tracking-[0.4em] text-[12px] md:text-xs mt-3 italic leading-none font-black font-black font-black font-black font-black">
                        Mohon tunggu sebentar...</p>
                </div>
            </div>
        </template>

        <!-- 6. MODALS SECTION -->
        <template x-teleport="body">
            <div class="fixed inset-0 z-[99999] overflow-y-auto bg-black/80 backdrop-blur-[4px] p-4 flex items-center justify-center font-black transition-all duration-300 font-black"
                x-show="showErrorModal" x-transition x-cloak>
                <div class="relative w-full max-w-lg transform transition-all my-auto shadow-2xl font-black font-black"
                    @click.away="showErrorModal = false">
                    <div
                        class="panel border-0 p-8 rounded-2xl shadow-2xl bg-white dark:bg-[#1b2e4b] text-center font-black font-black">
                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 shadow-inner italic text-4xl font-black leading-none font-black">
                            !</div>
                        <h4
                            class="mb-4 text-2xl text-gray-900 dark:text-white tracking-tighter text-center font-black leading-none font-black">
                            Integrasi Gagal</h4>
                        <div class="bg-rose-50 dark:bg-black/40 p-6 rounded-xl text-left mb-8 text-rose-700 dark:text-rose-400 italic font-mono font-bold text-xs border border-rose-100 dark:border-rose-900/50 leading-relaxed shadow-inner overflow-x-auto font-black"
                            x-text="errorLogContent"></div>
                        <button type="button"
                            class="btn btn-danger w-full rounded-xl py-4 font-black text-xs tracking-widest shadow-lg italic font-black"
                            @click="showErrorModal = false">Tutup Laporan</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Edit Metadata Modal (FIXED RESPONSIVE & HIGH CONTRAST) -->
        <template x-teleport="body">
            <div class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/80 backdrop-blur-[6px] p-4 overflow-y-auto transition-all duration-300 font-black"
                x-show="showEditModal" x-transition x-cloak @click.self="showEditModal = false">
                <div class="relative w-full max-w-xl transform transition-all my-auto shadow-2xl">
                    <div
                        class="panel border-0 p-0 rounded-[3rem] bg-white dark:bg-[#191e3a] overflow-hidden ring-1 ring-white/10 text-left transition-all duration-300 font-black">
                        <!-- HEADER MODAL EDIT: FIX VISIBILITY -->
                        <div
                            class="flex items-center justify-between bg-emerald-100 dark:bg-emerald-600 px-10 py-10 text-emerald-800 dark:text-white font-black italic tracking-[0.1em] shadow-lg font-black font-black">
                            <h5 class="text-2xl tracking-tighter font-black">Koreksi Metadata Ingesti</h5>
                            <button type="button" @click="showEditModal = false"
                                class="text-emerald-800 dark:text-white hover:opacity-70 transition-opacity font-black font-black"><svg
                                    class="w-10 h-10 font-black" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" stroke-width="4">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg></button>
                        </div>
                        <div class="p-8 space-y-8 font-black text-left">
                            <form :action="`/monitoring/import/${selectedItemId}`" method="POST"
                                class="space-y-6 font-black" @submit="handleSubmit($event)">
                                @csrf @method('PUT')
                                <div class="space-y-6 font-black">
                                    <div>
                                        <label
                                            class="text-[12px] font-black text-gray-500 dark:text-gray-400 mb-2 block italic tracking-widest font-black text-left">Judul
                                            Laporan Baru</label>
                                        <input type="text" name="judul_file" x-model="editForm.judul"
                                            class="form-input rounded-xl py-5 font-black border-slate-300 dark:border-slate-700 bg-gray-50 dark:bg-black/30 text-gray-900 dark:text-white focus:border-primary text-base shadow-inner italic font-black opacity-100">
                                    </div>
                                    <div>
                                        <label
                                            class="text-[12px] font-black text-gray-500 dark:text-gray-400 mb-2 block italic tracking-widest font-black text-left font-black">Personel
                                            Penanggung Jawab Baru</label>
                                        <input type="text" name="personel" x-model="editForm.personel"
                                            class="form-input rounded-xl py-5 font-black border-slate-300 dark:border-slate-700 bg-gray-50 dark:bg-black/30 text-gray-900 dark:text-white focus:border-primary text-base shadow-inner italic font-black opacity-100">
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-10 font-black text-left leading-none font-black font-black">
                                        <div><label
                                                class="text-[12px] font-black text-gray-500 dark:text-gray-400 mb-2 block italic font-black text-left font-black font-black">Jenis
                                                Dataset</label>
                                            <select name="kategori_file" x-model="editForm.kategori"
                                                class="form-select rounded-2xl py-6 font-black bg-gray-50 dark:bg-black/30 border-slate-300 dark:border-slate-700 text-gray-900 dark:text-white text-base shadow-inner italic font-black text-left">
                                                <option value="Rekap TBM">Rekapitulasi TBM III</option>
                                                <option value="Korelasi Vegetatif">Korelasi Vegetatif</option>
                                                <option value="Lokasi Kebun">Data Geospasial</option>
                                            </select>
                                        </div>
                                        <div x-show="editForm.kategori !== 'Lokasi Kebun'"><label
                                                class="text-[12px] font-black text-gray-500 dark:text-gray-400 mb-2 block italic font-black text-left font-black">Periode
                                                Data</label>
                                            <select name="periode_data" x-model="editForm.periode"
                                                class="form-select rounded-xl py-6 font-black bg-gray-50 dark:bg-black/30 border-slate-300 dark:border-slate-700 text-gray-900 dark:text-white text-base shadow-inner italic font-black text-left font-black font-black font-black">
                                                <option value="JANFEBMAR 2025">JANFEBMAR 2025</option>
                                                <option value="APRMEIJUN 2025">APRMEIJUN 2025</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div><label
                                            class="text-[12px] font-black text-gray-500 dark:text-gray-400 mb-2 block italic font-black text-left font-black font-black font-black">Catatan
                                            Internal Baru</label>
                                        <textarea name="notes" x-model="editForm.notes" rows="3"
                                            class="form-textarea rounded-3xl py-6 font-bold border-gray-200 dark:border-gray-700 dark:bg-black/30 text-gray-900 dark:text-white text-base leading-relaxed shadow-inner italic opacity-100 text-left font-black"></textarea>
                                    </div>
                                </div>
                                <div class="mt-8 flex gap-4 font-black">
                                    <button type="button"
                                        class="btn btn-outline-danger flex-1 rounded-[2rem] py-6 font-black tracking-widest shadow-sm italic text-sm font-black"
                                        @click="showEditModal = false">Batalkan</button>
                                    <button type="submit"
                                        class="btn btn-primary flex-1 rounded-[2.5rem] py-6 font-black shadow-2xl tracking-widest transition-all hover:scale-[1.02] italic text-sm font-black">Simpan
                                        Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-teleport="body">
            <div x-show="showDeleteModal"
                class="fixed inset-0 z-[99999] overflow-y-auto bg-black/85 backdrop-blur-[4px] p-4 flex items-center justify-center font-black transition-all duration-300 font-black font-black font-black font-black"
                x-transition x-cloak>
                <div class="relative w-full max-w-sm transform transition-all my-auto shadow-2xl font-black">
                    <div
                        class="panel border-0 p-10 text-center bg-white dark:bg-slate-900 rounded-2xl shadow-2xl ring-1 ring-white/10 font-black font-black font-black font-black">
                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 shadow-inner italic font-black text-4xl leading-none font-black animate-pulse font-black font-black">
                            !</div>
                        <h4
                            class="mb-3 text-lg font-black text-gray-900 dark:text-white tracking-tighter text-center leading-none font-black font-black font-black font-black">
                            Konfirmasi Hapus</h4>
                        <p
                            class="text-gray-500 dark:text-gray-400 text-[9px] font-black tracking-[0.1em] italic leading-relaxed text-center leading-none font-black font-black font-black font-black">
                            Dataset rill & berkas fisik akan hilang permanen!</p>
                        <div
                            class="mt-6 flex gap-3 font-black leading-none text-xs font-black font-black font-black font-black">
                            <button type="button"
                                class="btn btn-outline-danger flex-1 rounded-xl py-4 font-black tracking-widest text-[9px] border-rose-600 text-rose-600 shadow-sm italic leading-none font-black font-black font-black font-black font-black font-black"
                                @click="showDeleteModal = false">Batal</button>
                            <form :action="`/monitoring/import/${selectedItemId}`" method="POST"
                                class="flex-1 font-black leading-none font-black font-black font-black font-black font-black"
                                @submit="isProcessing = true">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    @click="isProcessing = true font-black font-black font-black font-black font-black"
                                    class="btn btn-danger w-full rounded-xl py-4 font-black shadow-lg text-[9px] tracking-[0.2em] shadow-rose-300 dark:shadow-none transition-all leading-none italic font-black font-black font-black font-black font-black font-black font-black font-black">Eksekusi</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener("alpine:init", () => {
            // Main uploadHandler Logic
            Alpine.data("uploadHandler", () => ({
                isDragging: false,
                uploadedFile: null,
                isProcessing: false,
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
                    if (file.size > 10 * 1024 * 1024) {
                        alert('Maksimal file 10MB');
                        return;
                    }
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!['xlsx', 'xls'].includes(ext)) {
                        alert('Format harus Excel (.xlsx atau .xls)');
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
                handleSubmitForm(e) {
                    this.isProcessing = true;
                }
            }));
        });

        // Patch global DOMTokenList
        const originalRemove = DOMTokenList.prototype.remove;
        DOMTokenList.prototype.remove = function(...tokens) {
            const filteredTokens = tokens.filter(token => token !== "");
            if (filteredTokens.length > 0) {
                originalRemove.apply(this, filteredTokens);
            }
        };

        // Reset state on pageshow
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) window.location.reload();
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .panel {
            border: 1px solid #cbd5e1;
            background: #fff !important;
            transition: all 0.3s ease;
        }

        .dark .panel {
            border: 1px solid #1e293b;
            background: #0f172a !important;
        }

        .form-input,
        .form-select,
        .form-textarea {
            border-radius: 0.75rem;
            border-color: #cbd5e1;
            color: #000000 !important;
            font-weight: 800 !important;
            opacity: 1 !important;
            min-height: 42px;
            text-align: left !important;
            font-size: 0.875rem;
        }

        .dark .form-input,
        .dark .form-select,
        .dark .form-textarea {
            border-color: #334155;
            background-color: rgba(15, 23, 42, 0.8) !important;
            color: #ffffff !important;
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }

        .dark .table-responsive::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>
</x-layout.default>
