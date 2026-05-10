/**
 * SIMTAN Monitoring - Vristo Dashboard Core Application
 * High-performance Alpine.js initialization and global state management.
 */

document.addEventListener('alpine:init', () => {
    // 1. GLOBAL APP STORE initialization
    if (!Alpine.store('app')) {
        Alpine.store('app', {
            sidebar: window.innerWidth > 1024,
            theme: localStorage.getItem('theme') || 'light',
            toggleSidebar() {
                this.sidebar = !this.sidebar;
            },
            toggleTheme(val) {
                this.theme = val || (this.theme === 'light' ? 'dark' : 'light');
                localStorage.setItem('theme', this.theme);
            },
        });
    }

    // 2. SCROLL TO TOP component
    Alpine.data('scrollToTop', () => ({
        showTopButton: false,
        init() {
            const handleScroll = () => {
                this.showTopButton = window.scrollY > 300;
            };
            window.addEventListener('scroll', handleScroll);
        },
        goToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        },
    }));

    // 3. SIDEBAR component
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

    // 4. CLOCK component (Indonesian Format)
    Alpine.data('clock', () => ({
        date: '',
        time: '',
        init() {
            this.updateTime();
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
            this.time = now
                .toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false,
                })
                .replace(/:/g, '.');
        },
    }));

    // 5. HEADER component (Notification Support)
    Alpine.data('header', () => ({
        notifications: [{ id: 1, message: 'Sistem siap menerima dataset', time: 'Baru saja' }],
        removeNotification(id) {
            this.notifications = this.notifications.filter((n) => n.id !== id);
        },
    }));
});

/**
 * Defensive Patching for Vristo Template Compatibility
 * Prevents common DOM manipulation errors during theme transitions.
 */
(function () {
    const originalRemove = DOMTokenList.prototype.remove;
    DOMTokenList.prototype.remove = function (...tokens) {
        const filteredTokens = tokens.filter((token) => token !== '');
        if (filteredTokens.length > 0) {
            originalRemove.apply(this, filteredTokens);
        }
    };

    // Auto-detect theme based on system preference if not set
    if (!localStorage.getItem('theme')) {
        const darkQuery = window.matchMedia('(prefers-color-scheme: dark)');
        localStorage.setItem('theme', darkQuery.matches ? 'dark' : 'light');
    }
})();
