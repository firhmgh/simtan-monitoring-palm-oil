/**
 * SIMTAN Enterprise AI Markdown & Output Renderer
 * Mengubah respon mentah LLM/DSS menjadi komponen UI HTML Enterprise yang rapi,
 * konsisten, bebas markdown mentah (`###`, `**`, `***`, bullet, table syntax),
 * responsif di mobile & desktop, dengan hierarki tipografi Plus Jakarta Sans.
 */
const formatAiOutput = function(text) {
    if (!text) return '<div class="flex items-center gap-3 text-slate-400 py-4"><span class="animate-spin border-2 border-emerald-500 border-t-transparent rounded-full w-4 h-4"></span><span class="text-xs font-semibold tracking-wide">Memproses inferensi narasi diagnostik...</span></div>';

    let raw = text.trim();

    // 1. Pre-process inline formatting
    function formatInline(str) {
        if (!str) return '';
        // Bold italic: ***text*** or ___text___
        str = str.replace(/\*\*\*(.*?)\*\*\*/g, '<strong class="font-extrabold text-slate-900 dark:text-white italic">$1</strong>');
        // Bold: **text** or __text__
        str = str.replace(/\*\*(.*?)\*\*/g, '<strong class="font-extrabold text-slate-900 dark:text-white">$1</strong>');
        str = str.replace(/__(.*?)__/g, '<strong class="font-extrabold text-slate-900 dark:text-white">$1</strong>');
        // Italic: *text* or _text_ (excluding bullet beginnings)
        str = str.replace(/(^|[^\*])\*([^\*]+?)\*([^\*]|$)/g, '$1<em class="italic text-slate-700 dark:text-slate-300">$2</em>$3');
        // Inline code: `code`
        str = str.replace(/`([^`]+)`/g, '<code class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 font-mono text-xs font-bold border border-emerald-500/20">$1</code>');
        
        // Highlight tag: [CONFIDENCE_SCORE]: 85% dsb
        str = str.replace(/\[CONFIDENCE_SCORE\]:\s*([^\n<]+)/gi, '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-black text-xs uppercase tracking-wider border border-emerald-500/30 mr-2"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Confidence: $1</span>');
        str = str.replace(/\[OBSERVASI\]:\s*/gi, '<span class="inline-block px-2.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 font-black text-xs uppercase tracking-wider border border-amber-500/30 mr-1">Observasi:</span> ');
        str = str.replace(/\[ANALISIS_KAUSAL\]:\s*/gi, '<span class="inline-block px-2.5 py-0.5 rounded-md bg-sky-100 dark:bg-sky-950/50 text-sky-800 dark:text-sky-300 font-black text-xs uppercase tracking-wider border border-sky-500/30 mr-1">Analisis Kausal:</span> ');
        str = str.replace(/\[REKOMENDASI_PRESKRIPTIF\]:\s*/gi, '<span class="inline-block px-2.5 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 font-black text-xs uppercase tracking-wider border border-emerald-500/30 mr-1">Rekomendasi Preskriptif:</span> ');
        
        return str;
    }

    let lines = raw.split(/\r?\n/);
    let html = '';
    let inTable = false;
    let tableRows = [];
    let inList = false;
    let listItems = [];

    function flushList() {
        if (!inList || listItems.length === 0) return;
        html += '<div class="space-y-2.5 my-4">';
        listItems.forEach(item => {
            html += `
                <div class="flex items-start gap-3 bg-slate-50/70 dark:bg-white/[0.03] hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20 p-3.5 rounded-xl border border-gray-100 dark:border-white/5 transition-all">
                    <span class="w-5 h-5 rounded-full bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </span>
                    <div class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed font-medium flex-1">${item}</div>
                </div>`;
        });
        html += '</div>';
        listItems = [];
        inList = false;
    }

    function flushTable() {
        if (!inTable || tableRows.length === 0) return;
        let validRows = tableRows.filter(r => !/^[\|\s\-:]+$/.test(r));
        if (validRows.length > 0) {
            html += '<div class="overflow-x-auto my-4 rounded-xl border border-gray-200/80 dark:border-white/10 shadow-sm">';
            html += '<table class="w-full text-left text-xs sm:text-sm border-collapse">';
            validRows.forEach((rowStr, idx) => {
                let cells = rowStr.split('|').map(c => c.trim()).filter((c, i, arr) => i > 0 && i < arr.length - 1);
                if (cells.length === 0) cells = rowStr.split('|').map(c => c.trim()).filter(c => c.length > 0);
                
                if (idx === 0) {
                    html += '<thead class="bg-gray-50 dark:bg-[#1b2e4b] border-b border-gray-200 dark:border-white/10"><tr>';
                    cells.forEach(c => {
                        html += `<th class="py-2.5 px-3.5 font-extrabold text-slate-800 dark:text-white uppercase tracking-wider text-[11px]">${formatInline(c)}</th>`;
                    });
                    html += '</tr></thead><tbody>';
                } else {
                    let bg = (idx % 2 === 0) ? 'bg-slate-50/40 dark:bg-white/[0.02]' : 'bg-white dark:bg-[#0e1726]';
                    html += `<tr class="${bg} border-b border-gray-100 dark:border-white/5 hover:bg-emerald-50/30 dark:hover:bg-emerald-950/20 transition-colors">`;
                    cells.forEach(c => {
                        html += `<td class="py-2.5 px-3.5 font-medium text-slate-700 dark:text-slate-300">${formatInline(c)}</td>`;
                    });
                    html += '</tr>';
                }
            });
            html += '</tbody></table></div>';
        }
        tableRows = [];
        inTable = false;
    }

    for (let i = 0; i < lines.length; i++) {
        let line = lines[i];
        let trimmed = line.trim();

        // 1. Deteksi Table Markdown (| ... |)
        if (trimmed.startsWith('|') && trimmed.endsWith('|')) {
            flushList();
            inTable = true;
            tableRows.push(trimmed);
            continue;
        } else if (inTable) {
            flushTable();
        }

        // 2. Baris Kosong
        if (!trimmed) {
            flushList();
            continue;
        }

        // 3. Horizontal Separator / Divider: ---, ***, ___
        if (/^(\-{3,}|\*{3,}|_{3,})$/.test(trimmed)) {
            flushList();
            html += '<hr class="my-5 border-t border-gray-200/80 dark:border-white/10" />';
            continue;
        }

        // 4. Heading Markdown (#, ##, ###, ####, #####, ######)
        let headingMatch = trimmed.match(/^(#{1,6})\s+(.*)$/);
        if (headingMatch) {
            flushList();
            let level = headingMatch[1].length;
            let title = formatInline(headingMatch[2]);
            if (level === 1 || level === 2) {
                html += `
                    <div class="mt-6 mb-3 pb-2 border-b border-gray-200/80 dark:border-white/10 flex items-center gap-2.5">
                        <span class="w-2.5 h-6 bg-emerald-500 rounded-full inline-block shrink-0"></span>
                        <h4 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight uppercase">${title}</h4>
                    </div>`;
            } else if (level === 3) {
                html += `
                    <div class="mt-5 mb-2.5 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <h5 class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">${title}</h5>
                    </div>`;
            } else {
                html += `
                    <h6 class="text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-4 mb-2 uppercase tracking-wider">${title}</h6>`;
            }
            continue;
        }

        // 5. Bullet List Item (*, -, +)
        let bulletMatch = trimmed.match(/^[\*\-\+]\s+(.*)$/);
        if (bulletMatch) {
            inList = true;
            listItems.push(formatInline(bulletMatch[1]));
            continue;
        }

        // 6. Numbered List Item (1., 2., 3., 1), 2), dst)
        let numMatch = trimmed.match(/^(\d+)[\.\)]\s+(.*)$/);
        if (numMatch) {
            flushList();
            let num = numMatch[1];
            let content = formatInline(numMatch[2]);
            html += `
                <div class="flex items-start gap-3 my-3 p-3.5 rounded-xl bg-slate-50/60 dark:bg-white/[0.02] border border-gray-100 dark:border-white/5 hover:border-emerald-500/20 transition-all">
                    <span class="w-6 h-6 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-sm mt-0.5">${num}</span>
                    <div class="text-xs sm:text-sm font-medium text-slate-800 dark:text-slate-200 leading-relaxed flex-1">${content}</div>
                </div>`;
            continue;
        }

        // 7. Label: Value atau Sub-bagian (misal "Catatan Kritis:", "Justifikasi Ilmiah:")
        if (/^[A-Z\s\(\)\/\-]{3,40}:$/i.test(trimmed)) {
            flushList();
            html += `<h6 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-slate-100 mt-4 mb-2 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> ${formatInline(trimmed.slice(0, -1))}</h6>`;
            continue;
        }

        // 8. Normal Paragraph
        flushList();
        html += `<p class="text-xs sm:text-sm text-slate-700 dark:text-slate-300 font-normal leading-[1.8] my-2.5 text-justify">${formatInline(trimmed)}</p>`;
    }

    flushList();
    flushTable();

    return html;
};

if (typeof window !== 'undefined') {
    window.formatAiOutput = formatAiOutput;
}
if (typeof globalThis !== 'undefined') {
    globalThis.formatAiOutput = formatAiOutput;
}
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { formatAiOutput };
}
