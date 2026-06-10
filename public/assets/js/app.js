/**
 * SIMTAN Monitoring - Vristo Dashboard Core Application
 * High-performance Alpine.js initialization and global state management.
 * Integrated with Global Typography, Design System, and Forensic Fixes.
 */

document.addEventListener('alpine:init', () => {
    // 1. GLOBAL APP STORE - Pusat kontrol State Vristo
    if (!Alpine.store('app')) {
        Alpine.store('app', {
            sidebar: window.innerWidth > 1024,
            theme: localStorage.getItem('theme') || 'light',
            
            init() {
                this.refreshTheme();
            },

            toggleSidebar() {
                this.sidebar = !this.sidebar;
            },

            toggleTheme(val) {
                this.theme = val || (this.theme === 'light' ? 'dark' : 'light');
                localStorage.setItem('theme', this.theme);
                this.refreshTheme();
            },

            refreshTheme() {
                if (this.theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    }

    // 2. SCROLL TO TOP - Perbaikan Error "scrollToTop is not defined"
    Alpine.data('scrollToTop', () => ({
        showTopButton: false,
        init() {
            const handleScroll = () => {
                // Menggunakan window.scrollY untuk kompatibilitas browser modern
                this.showTopButton = window.scrollY > 200;
            };
            window.addEventListener('scroll', handleScroll);
            // Inisialisasi awal saat load
            handleScroll();
        },
        goToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        },
    }));

    // 3. SIDEBAR - Sinkronisasi dengan Global Store & Resize Handler
    Alpine.data('sidebar', () => ({
        init() {
            const handleResize = () => {
                if (window.innerWidth < 1024) {
                    this.$store.app.sidebar = false;
                }
            };
            window.addEventListener('resize', handleResize);
            handleResize();
        },
    }));

    // 4. CLOCK - Perbaikan Error "clock/date/time is not defined"
    Alpine.data('clock', () => ({
        date: '',
        time: '',
        init() {
            this.updateTime();
            // Sinkronisasi interval 1 detik untuk dashboard real-time
            setInterval(() => this.updateTime(), 1000);
        },
        updateTime() {
            const now = new Date();
            this.date = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
            // Format waktu Indonesia (HH.mm.ss) sesuai standar Enterprise
            this.time = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            }).replace(/:/g, '.');
        },
    }));

    // 5. HEADER - Notification & Profile Support
    Alpine.data('header', () => ({
        notifications: [
            { id: 1, message: 'Sistem SIMTAN siap menerima dataset', time: 'Baru saja' }
        ],
        removeNotification(id) {
            this.notifications = this.notifications.filter((n) => n.id !== id);
        },
    }));
});

/**
 * Defensive Patching & Initial Theme Setup
 * Eksekusi diluar event listener untuk menjamin konsistensi visual 
 * sebelum Alpine.js memanipulasi DOM.
 */
(function () {
    // A. Fix untuk kompatibilitas Vristo (Handling string kosong pada classList)
    const originalRemove = DOMTokenList.prototype.remove;
    DOMTokenList.prototype.remove = function (...tokens) {
        const filteredTokens = tokens.filter((token) => token !== '');
        if (filteredTokens.length > 0) {
            originalRemove.apply(this, filteredTokens);
        }
    };

    // B. Mencegah Flash of Unstyled Content (FOUC)
    // Langsung terapkan tema dari localStorage sebelum render halaman dimulai
    const savedTheme = localStorage.getItem('theme');
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
        document.documentElement.classList.add('dark');
        if (!savedTheme) localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        if (!savedTheme) localStorage.setItem('theme', 'light');
    }
})();