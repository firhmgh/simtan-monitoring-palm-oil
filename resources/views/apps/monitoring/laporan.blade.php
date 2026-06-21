<x-layout.default>
    {{-- Font Plus Jakarta Sans (standar tipografi SIMTAN) --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    {{-- html2canvas untuk ekspor dokumen dari tampilan pratinjau --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <div x-data="pusatLaporanDSS" class="relative font-['Plus_Jakarta_Sans'] antialiased">

        <!-- 1. Overlay Loading (Gunakan x-teleport agar selalu paling depan) -->
        <template x-teleport="body">
            <div x-show="isLoading" x-transition.opacity
                class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[99999] flex items-center justify-center pointer-events-auto">
                <div
                    class="panel p-10 rounded-[2.5rem] flex flex-col items-center gap-6 shadow-2xl bg-white dark:bg-[#0e1726]">
                    <span
                        class="animate-spin border-4 border-primary border-l-transparent rounded-full w-14 h-14 block"></span>
                    <p class="font-black text-xs uppercase tracking-[0.3em] text-primary text-center">Sinkronisasi Basis
                        Data &<br>Rendering Dokumen Digital...</p>
                </div>
            </div>
        </template>

        <!-- 2. Header Strategis -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-8 gap-4 pt-5 no-print">
            <div>
                <!-- Navigasi Breadcrumb Standar SIMTAN -->
                <ul class="flex space-x-2 text-xs mb-2 text-white-dark tracking-widest font-black uppercase">
                    <li><a href="{{ route('index') }}" class="text-primary hover:underline font-black">Monitoring</a></li>
                    <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2 font-black text-slate-400">Laporan</li>
                </ul>
                <h1
                    class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white uppercase italic tracking-tighter leading-none">
                    Pusat Generasi Laporan <span class="text-primary underline decoration-primary/20">Presisi TBM
                        III</span>
                </h1>
                <p class="text-xs font-bold italic text-slate-500 dark:text-slate-400 mt-2 border-l-2 border-primary pl-2 tracking-tight">
                    Sistem Integrasi Terpadu - PTPN IV Regional I
                </p>
            </div>

            <button type="button" @click="eksporPDF()" :disabled="!showPreview"
                class="btn btn-primary flex items-center gap-2 px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] shadow-xl transition-all hover:scale-105 active:scale-95 disabled:opacity-30">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5">
                    <path
                        d="M12 16V8M12 16L9 13M12 16L15 13M3 15V17C3 18.1046 3.89543 19 5 19H19C20.1046 19 21 18.1046 21 17V15"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Unduh Dokumen PDF
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- SIDEBAR KONFIGURASI -->
            <div class="lg:col-span-4 space-y-6 no-print">
                <div class="panel rounded-[2rem] border-none shadow-2xl bg-white dark:bg-[#0e1726] p-8">
                    <h5
                        class="font-black text-sm mb-8 text-gray-800 dark:text-white uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 pb-5 italic text-justify">
                        Konfigurasi Laporan</h5>

                    <div class="space-y-6">
                        <div>
                            <label
                                class="text-[10px] font-black uppercase text-gray-400 mb-2 block tracking-[0.2em]">Unit
                                Kerja</label>
                            <select x-model="form.kebun"
                                class="form-select border-2 border-gray-50 dark:border-gray-800 py-3.5 rounded-2xl font-bold text-sm bg-gray-50/50">
                                <option value="">Pilih Unit...</option>
                                <template x-for="unit in daftarKebun" :key="unit.kode">
                                    <option :value="unit.kode" x-text="`${unit.nama} (${unit.kode})`"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label
                                class="text-[10px] font-black uppercase text-gray-400 mb-2 block tracking-[0.2em]">Periode
                                Sensus</label>
                            <select x-model="form.periode"
                                class="form-select border-2 border-gray-50 dark:border-gray-800 py-3.5 rounded-2xl font-bold text-sm bg-gray-50/50">
                                <option value="">Pilih Periode...</option>
                                <template x-for="(info, slug) in listPeriode" :key="slug">
                                    <option :value="slug" x-text="info.label"></option>
                                </template>
                            </select>
                        </div>

                    {{-- Toggle opsional: sertakan atau kecualikan analisis AI dari laporan --}}
                        <div class="flex items-center justify-between bg-slate-50 dark:bg-black/40 p-4 rounded-2xl">
                            <span
                                class="text-xs font-black uppercase tracking-tighter text-gray-600 dark:text-white-dark">Sertakan
                                Analisis AI</span>
                            <input type="checkbox" x-model="form.includeAI" class="custom_switch" />
                        </div>

                        <button type="button" @click.prevent="inisialisasiLaporan()" :disabled="isLoading"
                            class="btn btn-primary w-full py-4 rounded-2xl font-black uppercase text-xs shadow-lg shadow-primary/20">
                            <span x-show="!isLoading">Tampilkan Preview Laporan</span>
                            <span x-show="isLoading"
                                class="animate-spin border-2 border-white border-l-transparent rounded-full w-4 h-4 mr-2"></span>
                            <span x-show="isLoading">Memproses...</span>
                        </button>
                    </div>
                </div>

                {{-- Kartu panel bagian laporan yang dapat dikustomisasi --}}
                <div class="panel rounded-[2rem] border-none shadow-sm bg-white dark:bg-[#0e1726] p-8">
                    <h5 class="font-black text-[10px] uppercase text-gray-400 mb-6 tracking-[0.2em]">Pilih Bagian
                        Laporan</h5>
                    <div class="space-y-4">
                        <template x-for="s in sections" :key="s.id">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <span
                                    class="text-[10px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-widest"
                                    x-text="s.label"></span>
                                <input type="checkbox" x-model="s.active"
                                    class="form-checkbox outline-primary rounded-md w-5 h-5 text-primary border-2" />
                            </label>
                        </template>
                    </div>
                </div>
            </div>

            <!-- AREA PRATINJAU (DIGITAL PAPER) -->
            <div class="lg:col-span-8 overflow-hidden">
                {{-- Area pratinjau kosong: tampil sebelum pengguna memilih parameter --}}
                <div x-show="!showPreview"
                    class="panel h-[700px] flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-[3rem]">
                    <h5 class="text-xl font-black text-gray-400 uppercase italic tracking-tighter text-center">Pilih Unit
                        & Periode<br>untuk Memulai Pratinjau Dokumen</h5>
                </div>

                <div x-show="showPreview" x-transition.opacity class="digital-paper-container shadow-inner">
                    <!-- Preview Container -->
                    <div class="digital-paper shadow-2xl bg-white text-black mx-auto overflow-auto"
                        x-html="htmlContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("alpine:init", () => {
            // MENGGUNAKAN Alpine.data UNTUK ISOLASI
            Alpine.data("pusatLaporanDSS", () => ({
                form: {
                    kebun: '',
                    periode: '',
                    includeAI: true
                },
                isLoading: false,
                isGenerating: false,
                showPreview: false,
                htmlContent: '',
                listPeriode: @json($listPeriode ?? []),
                daftarKebun: @json(collect($listKebun)->map(fn($nama, $kode) => ['kode' => $kode, 'nama' => $nama])->values()),

                sections: [{
                        id: 'summary',
                        label: 'Matriks Performa (I)',
                        active: true
                    },
                    {
                        id: 'recom',
                        label: 'Analisis AI (II)',
                        active: true
                    },
                    {
                        id: 'block',
                        label: 'Data Per Blok (III)',
                        active: true
                    },
                ],

                async inisialisasiLaporan() {
                    if (!this.form.kebun || !this.form.periode) {
                        return window.Swal.fire({
                            icon: 'warning',
                            title: 'Akses Ditolak',
                            text: 'Unit Kebun dan Periode wajib dipilih.'
                        });
                    }

                    this.isLoading = true;
                    this.showPreview = false;

                    try {
                        const activeIds = this.sections.filter(s => s.active).map(s => s.id);
                        const params = new URLSearchParams({
                            kebun: this.form.kebun,
                            periode: this.form.periode,
                            include_ai: this.form.includeAI,
                            active_sections: JSON.stringify(activeIds)
                        });

                        // PANGGIL URL SESUAI ROUTE DI web.php
                        const res = await fetch(
                            `/monitoring/laporan/preview-html?${params.toString()}`);

                        if (!res.ok) {
                            const errText = await res.text();
                            throw new Error(errText);
                        }

                        this.htmlContent = await res.text();
                        this.showPreview = true;
                    } catch (e) {
                        console.error(e);
                        window.Swal.fire({
                            icon: 'error',
                            title: 'Database Mismatch',
                            text: 'Variabel data tidak lengkap atau data rincian unit belum di-upload.'
                        });
                    } finally {
                        this.isLoading = false;
                    }
                },

                eksporPDF() {
                    const activeIds = this.sections.filter(s => s.active).map(s => s.id);
                    const params = new URLSearchParams({
                        kebun: this.form.kebun,
                        periode: this.form.periode,
                        include_ai: this.form.includeAI,
                        active_sections: JSON.stringify(activeIds)
                    });
                    window.location.href = `/monitoring/laporan/export-pdf?${params.toString()}`;
                }
            }));
        });
    </script>

    <style>
        /* Digital Paper Styling agar 1:1 dengan PDF */
        .digital-paper-container {
            background: #525659;
            padding: 50px 20px;
            border-radius: 2.5rem;
            overflow: auto;
            max-height: 850px;
        }

        .digital-paper {
            width: 210mm;
            /* A4 Width */
            min-height: 297mm;
            background: white !important;
            color: black !important;
            padding: 1.2cm;
            /* Margin yang sama dengan PDF */
            box-sizing: border-box;
            font-family: 'Helvetica', 'Arial', sans-serif !important;
        }

        /* Hilangkan override warna template di dalam area digital paper */
        .digital-paper * {
            color: black !important;
            border-color: black !important;
        }

        .digital-paper table {
            width: 100% !important;
            border-collapse: collapse !important;
            border: 1px solid black !important;
        }

        .digital-paper th,
        .digital-paper td {
            border: 1px solid black !important;
            padding: 8px !important;
        }

        .digital-paper .section-header {
            border-left: 5px solid #00a76f !important;
        }

        @media (max-width: 1024px) {
            .digital-paper {
                width: 100%;
                min-height: auto;
            }
        }
    </style>
</x-layout.default>
