<x-layout.default>
    <!-- Wrapper Utama: Menggunakan skema warna adaptive standar SaaS -->
    <div x-data="uploadHandler()" class="relative text-gray-900 dark:text-gray-200 transition-colors duration-300" x-cloak>

        <!-- 1. HEADER SECTION -->
        <div
            class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="text-left font-bold">
                <ul class="flex space-x-2 text-xs mb-2 text-gray-500 dark:text-gray-400 tracking-widest uppercase">
                    <li><a href="javascript:;" class="text-primary hover:underline">Monitoring</a></li>
                    <li
                        class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 text-gray-900 dark:text-white font-black">
                        Upload Data</li>
                </ul>
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white tracking-tighter leading-none">
                    Pusat Ingesti Data</h1>
                <p
                    class="text-xs font-bold italic text-gray-500 dark:text-gray-400 mt-1 border-l-2 border-primary pl-2 uppercase tracking-tight">
                    Sistem Integrasi Multimoda - PTPN IV Regional I</p>
            </div>
        </div>

        <!-- 2. GLOBAL TOASTS -->
        <template x-teleport="body">
            <div class="fixed top-5 right-5 z-[99999] isolate space-y-3 w-full max-w-[350px] pointer-events-none">
                @if (session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
                        class="pointer-events-auto p-4 bg-white dark:bg-[#1e293b] rounded-xl shadow-2xl border-l-[6px] border-success flex items-center gap-3 ring-1 ring-black/5 dark:ring-white/10">
                        <div
                            class="w-8 h-8 bg-success/10 text-success rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="3">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-[9px] text-gray-500 dark:text-gray-400 font-black tracking-widest uppercase">
                                Success</p>
                            <p class="text-xs text-gray-900 dark:text-white font-bold leading-tight">
                                {{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" x-transition
                        class="pointer-events-auto p-4 bg-white dark:bg-[#1e293b] rounded-xl shadow-2xl border-l-[6px] border-danger flex items-center gap-3 ring-1 ring-black/5 dark:ring-white/10">
                        <div
                            class="w-8 h-8 bg-danger/10 text-danger rounded-full flex items-center justify-center shrink-0">
                            <span class="font-black">!</span>
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-[9px] text-gray-500 dark:text-gray-400 font-black tracking-widest uppercase">
                                Error Ingesti</p>
                            <p class="text-xs text-gray-900 dark:text-white font-bold leading-tight">
                                {{ $errors->first() }}</p>
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
                        class="panel bg-white dark:bg-[#0f172a] border border-gray-200 dark:border-[#334155] shadow-sm rounded-2xl p-6 transition-all">
                        <div class="flex items-center gap-3 mb-6 border-b border-gray-100 dark:border-[#1e293b] pb-4">
                            <div
                                class="w-8 h-8 bg-primary text-white rounded-lg flex items-center justify-center font-black italic shadow-md">
                                1</div>
                            <h3 class="text-gray-900 dark:text-white font-black uppercase tracking-tight">Identifikasi
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label
                                    class="text-[11px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic uppercase">Judul
                                    Laporan</label>
                                <input type="text" name="judul_file" value="{{ old('judul_file') }}"
                                    class="form-input" placeholder="INPUT JUDUL..." required>
                            </div>

                            <div>
                                <label
                                    class="text-[11px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic uppercase">Penanggung
                                    Jawab</label>
                                <input type="text" name="personel" value="{{ old('personel') }}" class="form-input"
                                    placeholder="NAMA PERSONEL..." required>
                            </div>

                            <div>
                                <label
                                    class="text-[11px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic uppercase">Kategori
                                    Dataset</label>
                                <select x-model="kategori" name="kategori_file" class="form-select" required>
                                    <option value="">PILIH KATEGORI...</option>
                                    <option value="Rekap TBM">REKAPITULASI TBM III</option>
                                    <option value="Korelasi Vegetatif">KORELASI VEGETATIF</option>
                                    <option value="Lokasi Kebun">LOKASI PETA (GIS MASTER)</option>
                                </select>
                            </div>

                            <div x-show="kategori !== 'Lokasi Kebun' && kategori !== ''" x-transition x-cloak>
                                <label class="...">Periode Laporan</label>
                                {{-- NAME HARUS 'periode_data' SESUAI DB --}}
                                <select name="periode_data" class="form-select"
                                    :required="kategori !== 'Lokasi Kebun' && kategori !== ''">
                                    <option value="">PILIH PERIODE...</option>
                                    @foreach ($listPeriode as $slug => $info)
                                        <option value="{{ $info['db_key'] }}">{{ strtoupper($info['label']) }}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div>
                                <label
                                    class="text-[11px] text-gray-500 dark:text-gray-400 tracking-widest mb-1 block italic uppercase">Keterangan
                                    Khusus</label>
                                <textarea name="notes" rows="3" class="form-textarea" placeholder="Catatan tambahan..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: UPLOAD AREA -->
                <div class="lg:col-span-2">
                    <div
                        class="panel bg-white dark:bg-[#0f172a] border border-gray-200 dark:border-[#334155] shadow-sm rounded-2xl p-6 h-full flex flex-col">
                        <div class="flex items-center gap-3 mb-6 font-black text-sm">
                            <div
                                class="w-8 h-8 bg-success text-white rounded-lg flex items-center justify-center font-black italic shadow-md">
                                2</div>
                            <h3 class="text-gray-900 dark:text-white uppercase leading-none font-black tracking-tight">
                                Berkas Sumber (Excel)</h3>
                        </div>

                        <div class="flex-1 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-[2rem] p-8 text-center transition-all relative hover:border-primary dark:hover:border-primary-light bg-slate-50/50 dark:bg-black/10 group overflow-hidden"
                            :class="isDragging ? 'border-primary bg-primary/5 ring-8 ring-primary/5' : ''"
                            @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop($event)">

                            <input type="file" name="file_excel" x-ref="fileInput" class="hidden" accept=".xlsx,.xls"
                                @change="handleFileSelect($event)" required>

                            <div class="flex flex-col items-center cursor-pointer h-full justify-center"
                                @click="$refs.fileInput.click()">
                                <div
                                    class="w-24 h-24 bg-primary/10 text-primary dark:text-primary-light rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-all duration-500 shadow-inner">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="1.5">
                                        <path
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                </div>
                                <h4
                                    class="text-xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">
                                    Drop Berkas Disini</h4>
                                <p
                                    class="text-xs text-slate-400 dark:text-slate-500 mt-2 font-bold tracking-[0.2em] uppercase leading-none">
                                    Max 20MB (.xlsx / .xls)</p>
                            </div>
                        </div>

                        <!-- FEEDBACK PREVIEW -->
                        <div x-show="uploadedFile" x-transition
                            class="mt-4 p-5 bg-primary rounded-xl shadow-lg text-white font-black ring-4 ring-primary/10">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-white rounded-lg flex items-center justify-center font-black text-primary text-xl shadow-inner italic">
                                        XL</div>
                                    <div class="text-left leading-none">
                                        <p class="text-sm font-black truncate max-w-[200px]"
                                            x-text="uploadedFile ? uploadedFile.name : ''"></p>
                                        <p class="text-[9px] opacity-80 tracking-widest uppercase italic mt-1"
                                            x-text="uploadedFile ? formatSize(uploadedFile.size) : ''"></p>
                                    </div>
                                </div>
                                <div class="flex gap-2 w-full md:w-auto">
                                    <button type="submit" :disabled="isProcessing"
                                        class="btn bg-white text-primary hover:bg-gray-100 flex-1 md:px-8 rounded-lg font-black border-none py-3 text-[11px] uppercase tracking-widest transition-all active:scale-95 disabled:opacity-50">
                                        <span x-show="!isProcessing">Integrasikan</span>
                                        <span x-show="isProcessing">Loading...</span>
                                    </button>
                                    <button type="button" @click="resetUpload"
                                        class="btn bg-rose-500 text-white hover:bg-rose-600 px-6 rounded-lg font-black border-none py-3 text-[11px] uppercase tracking-widest transition-all active:scale-95">Batal</button>
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
                class="text-xl font-black text-gray-900 dark:text-white mb-4 italic tracking-widest uppercase leading-none">
                Riwayat Ingesti</h3>
            <div
                class="table-responsive bg-white dark:bg-[#0f172a] rounded-2xl shadow-sm border border-gray-200 dark:border-[#334155] overflow-hidden ring-1 ring-black/5">
                <table class="w-full min-w-[1000px] border-collapse font-black">
                    <thead>
                        <tr
                            class="text-[10px] font-black tracking-widest text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-[#1e293b] bg-gray-50 dark:bg-black/40 uppercase italic">
                            <th class="py-4 px-4 text-left">ID Transaksi</th>
                            <th class="py-4 px-4 text-left">Dokumen Laporan</th>
                            <th class="py-4 px-4 text-center">Jenis Dataset</th>
                            <th class="py-4 px-4 text-center">Status</th>
                            <th class="py-4 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-[12px] font-bold text-gray-900 dark:text-white">
                        @forelse($history as $log)
                            @php $item = $log->form; @endphp
                            <tr
                                class="border-b border-gray-50 dark:border-[#1e293b] hover:bg-gray-50 dark:hover:bg-black/20 transition-all">
                                <td class="py-6 px-4 text-primary dark:text-primary-light font-black italic">
                                    #{{ $item->kode_upload ?? 'N/A' }}</td>
                                <td class="px-4 text-left">
                                    <div class="text-sm font-black leading-tight">
                                        {{ $item->judul_file ?? 'Invalid Metadata' }}</div>
                                    <div
                                        class="text-[9px] text-gray-500 dark:text-gray-400 font-bold mt-1 uppercase tracking-tighter italic">
                                        {{ $log->nama_file }} • {{ $log->created_at->format('d/m/Y H:i') }} •
                                        {{ number_format($log->rows_imported ?? 0) }} Baris
                                    </div>
                                </td>
                                <td class="text-center px-4">
                                    <span
                                        class="badge bg-primary/10 text-primary dark:text-primary-light text-[9px] px-3 py-1 rounded-full font-black uppercase tracking-widest border border-primary/20">
                                        {{ $log->jenis_dataset }}
                                    </span>
                                </td>
                                <td class="text-center px-4">
                                    @if ($log->status == 'Success')
                                        <span
                                            class="badge bg-success/10 text-success font-black text-[9px] px-4 py-1 rounded-full border border-success/20 uppercase tracking-widest">SUCCESS</span>
                                    @else
                                        <div class="flex flex-col items-center gap-1">
                                            <span
                                                class="badge bg-danger/10 text-danger font-black text-[9px] px-4 py-1 rounded-full border border-danger/20 uppercase tracking-widest font-black">FAILED</span>
                                            <button type="button" @click="viewErrorLog($el.dataset.msg)"
                                                data-msg="{{ $log->message }}"
                                                class="text-[8px] text-rose-500 hover:underline italic font-black uppercase tracking-widest">Detail
                                                Log</button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($item)
                                            <a href="{{ route('monitoring.import.download', $item->id) }}"
                                                class="p-2 bg-gray-100 dark:bg-black/40 text-gray-700 dark:text-gray-300 rounded-lg hover:text-success border border-gray-200 dark:border-[#334155] transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path
                                                        d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                            <button type="button"
                                                @click="triggerEdit('{{ $item->id }}', '{{ $item->kategori_file }}', '{{ $item->personel_pj }}', '{{ $item->periode_data }}', '{{ $item->judul_file }}', '{{ addslashes($item->notes) }}')"
                                                class="p-2 bg-primary/10 text-primary rounded-lg hover:bg-primary hover:text-white transition-all border border-primary/20 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path
                                                        d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>
                                            <button type="button" @click="triggerDelete('{{ $item->id }}')"
                                                class="p-2 bg-danger/10 text-danger rounded-lg hover:bg-danger hover:text-white transition-all border border-danger/20 shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path
                                                        d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="py-20 text-center text-gray-400 font-black opacity-30 tracking-widest italic uppercase font-black uppercase">
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
                class="fixed inset-0 z-[999999] flex flex-col items-center justify-center bg-white/90 dark:bg-black/90 backdrop-blur-md"
                x-cloak>
                <div
                    class="w-24 h-24 md:w-32 border-[12px] border-primary/10 border-t-primary rounded-full animate-spin mb-6 shadow-2xl shadow-primary/20">
                </div>
                <h2
                    class="text-xl md:text-3xl font-black uppercase text-gray-900 dark:text-white tracking-tighter animate-pulse leading-none">
                    Sedang Memproses...</h2>
                <p
                    class="text-gray-500 dark:text-gray-400 font-black tracking-[0.5em] text-[11px] md:text-xs mt-4 italic uppercase">
                    Integrasi Multimoda Berjalan...</p>
            </div>
        </template>

        <!-- 6. MODALS SECTION -->
        <!-- Error Detail Modal -->
        <template x-teleport="body">
            <div class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/80 backdrop-blur-[4px] p-4 font-black"
                x-show="showErrorModal" x-transition x-cloak @click.away="showErrorModal = false">
                <div class="relative w-full max-w-lg shadow-2xl transition-all my-auto">
                    <div
                        class="panel border-0 p-8 rounded-[2.5rem] shadow-2xl bg-white dark:bg-[#1b2e4b] text-center border border-slate-200 dark:border-slate-800">
                        <div
                            class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-600 shadow-inner italic text-5xl font-black leading-none rotate-3">
                            !</div>
                        <h4 class="mb-4 text-2xl text-gray-900 dark:text-white tracking-tighter uppercase font-black">
                            Integrasi Gagal</h4>
                        <div class="bg-rose-50 dark:bg-black/40 p-6 rounded-2xl text-left mb-8 text-rose-700 dark:text-rose-400 font-mono text-[11px] leading-relaxed shadow-inner border border-rose-100 dark:border-rose-900/20 overflow-x-auto max-h-[300px]"
                            x-text="errorLogContent"></div>
                        <button type="button"
                            class="btn btn-danger w-full rounded-2xl py-5 font-black text-xs uppercase tracking-widest shadow-xl italic"
                            @click="showErrorModal = false">Tutup Laporan</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Edit Metadata Modal (FIXED SCALING & RESPONSIVE) -->
        <template x-teleport="body">
            <div class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/85 backdrop-blur-[8px] p-4 transition-all duration-300 overflow-hidden"
                x-show="showEditModal" x-transition x-cloak @click.self="showEditModal = false">
                <div
                    class="relative bg-white dark:bg-[#0f172a] w-full max-w-xl rounded-[2.5rem] overflow-hidden shadow-2xl border border-slate-200 dark:border-[#334155] flex flex-col max-h-[92vh] my-auto scale-100 transform transition-transform">
                    <!-- Fixed Header -->
                    <div class="bg-primary px-8 py-8 text-white flex justify-between items-center shadow-lg shrink-0">
                        <div class="text-left font-black">
                            <h5 class="text-2xl font-black uppercase tracking-tighter italic leading-none">Koreksi
                                Metadata</h5>
                            <p
                                class="text-[10px] opacity-70 font-bold tracking-[0.2em] mt-2 uppercase italic leading-none">
                                Audit Trail & Adjustments</p>
                        </div>
                        <button @click="showEditModal = false"
                            class="hover:rotate-90 transition-all duration-300 p-2"><svg class="w-8 h-8"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="4">
                                <path d="M18 6L6 18M6 6l12 12" />
                            </svg></button>
                    </div>

                    <!-- Scrollable Form Body -->
                    <div class="p-8 overflow-y-auto custom-scrollbar flex-1 text-left font-black">
                        <form :action="`/monitoring/import/${selectedItemId}`" method="POST" class="space-y-6"
                            @submit="isProcessing = true">
                            @csrf @method('PUT')
                            <div class="space-y-6">
                                <div>
                                    <label
                                        class="text-[11px] font-black text-gray-500 dark:text-gray-400 mb-1.5 block italic tracking-widest uppercase leading-none">Judul
                                        Laporan Baru</label>
                                    <input type="text" name="judul_file" x-model="editForm.judul"
                                        class="form-input shadow-inner py-4" required>
                                </div>
                                <div>
                                    <label
                                        class="text-[11px] font-black text-gray-500 dark:text-gray-400 mb-1.5 block italic tracking-widest uppercase leading-none">Penanggung
                                        Jawab Baru</label>
                                    <input type="text" name="personel" x-model="editForm.personel"
                                        class="form-input shadow-inner py-4" required>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="text-[11px] font-black text-gray-500 dark:text-gray-400 mb-1.5 block italic uppercase leading-none">Kategori</label>
                                        <select name="kategori_file" x-model="editForm.kategori"
                                            class="form-select shadow-inner" required>
                                            <option value="Rekap TBM">Rekap TBM III</option>
                                            <option value="Korelasi Vegetatif">Korelasi Vegetatif</option>
                                            <option value="Lokasi Kebun">Data GIS Master</option>
                                        </select>
                                    </div>
                                    <div x-show="editForm.kategori !== 'Lokasi Kebun'">
                                        <label
                                            class="text-[11px] font-black text-gray-500 dark:text-gray-400 mb-1.5 block italic uppercase leading-none">Periode
                                            Baru</label>
                                        <select name="periode_data" x-model="editForm.periode"
                                            class="form-select shadow-inner" required>
                                            <option value="Tahun 2025">TAHUNAN 2025</option>
                                            <option value="JANFEBMARAPR2025REKAP">PERIODE 1</option>
                                            <option value="MEIJULJUNAGST2025REKAP">PERIODE 2</option>
                                            <option value="SEPOKTNOVDES2025REKAP">PERIODE 3</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="text-[11px] font-black text-gray-500 dark:text-gray-400 mb-1.5 block italic uppercase leading-none">Catatan
                                        Perubahan</label>
                                    <textarea name="notes" x-model="editForm.notes" rows="3" class="form-textarea shadow-inner"></textarea>
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 pt-4 shrink-0">
                                <button type="button"
                                    class="btn bg-gray-100 dark:bg-black/30 text-gray-600 dark:text-gray-300 flex-1 py-5 font-black rounded-2xl uppercase tracking-widest border-none transition-all italic shadow-sm leading-none"
                                    @click="showEditModal = false">Batalkan</button>
                                <button type="submit"
                                    class="btn btn-primary flex-1 py-5 font-black rounded-2xl uppercase shadow-2xl tracking-[0.2em] transition-all hover:scale-[1.02] leading-none italic">Update
                                    Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>

        <!-- Delete Modal -->
        <template x-teleport="body">
            <div x-show="showDeleteModal"
                class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/90 backdrop-blur-[4px] p-4 transition-all"
                x-transition x-cloak>
                <div
                    class="bg-white dark:bg-[#1e293b] w-full max-w-sm p-10 rounded-[3rem] shadow-2xl text-center border border-gray-200 dark:border-[#334155] my-auto">
                    <div
                        class="mx-auto mb-6 h-16 w-16 bg-rose-50 dark:bg-rose-900/30 text-rose-600 flex items-center justify-center rounded-3xl text-4xl font-black shadow-inner animate-bounce ring-4 ring-rose-500/10 italic font-black">
                        !</div>
                    <h4 class="text-xl font-black uppercase tracking-tight text-gray-900 dark:text-white leading-none">
                        Hapus Dataset?</h4>
                    <p
                        class="text-[10px] text-gray-500 dark:text-gray-400 font-bold tracking-widest mt-4 uppercase italic leading-tight">
                        Dataset rill & berkas fisik akan hilang permanen dari server!</p>
                    <div class="mt-8 flex gap-3">
                        <button type="button"
                            class="btn btn-outline-danger flex-1 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all italic leading-none"
                            @click="showDeleteModal = false">Batal</button>
                        <form :action="`/monitoring/import/${selectedItemId}`" method="POST" class="flex-1"
                            @submit="isProcessing = true">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="btn btn-danger w-full py-4 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-rose-500/20 italic transition-all hover:bg-rose-700 leading-none font-black uppercase">Eksekusi</button>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- SCRIPTS -->
    <script>
        document.addEventListener("alpine:init", () => {
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
                        alert('Maksimal file 20MB');
                        return;
                    }
                    const ext = file.name.split('.').pop().toLowerCase();
                    if (!['xlsx', 'xls'].includes(ext)) {
                        alert('Format harus Excel (.xlsx/.xls)');
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
                }
            }));
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Unified Form Styling - Truly Adaptive & No Forced Black */
        .form-input,
        .form-select,
        .form-textarea {
            @apply rounded-xl border-slate-200 bg-slate-50/50 text-slate-900 transition-all duration-300 font-black text-sm w-full py-4 px-5 focus:border-primary focus:ring-4 focus:ring-primary/10 dark:border-[#1e293b] dark:bg-black/20 dark:text-white dark:focus:border-primary-light;
            color: unset !important;
            background-image: unset;
        }

        .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5rem;
            padding-right: 2.5rem;
        }

        .dark .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        }

        .panel {
            @apply transition-colors duration-300 !important;
            background: unset !important;
        }

        /* Scrollbar Design */
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
