# SIMTAN — Sistem Informasi Monitoring Areal Tanaman
### PT. Perkebunan Nusantara IV (Regional I)
> **Enterprise WebGIS & Decision Support System (DSS) dengan Dual Neural Engine AI untuk Pemantauan Presisi Perkebunan Kelapa Sawit (TBM III).**

---

## 📌 Ringkasan Eksekutif
**SIMTAN** (Sistem Informasi Monitoring Areal Tanaman) adalah platform manajemen perkebunan terintegrasi yang dirancang untuk mengotomatisasi evaluasi agronomi pada fase **Tanaman Belum Menghasilkan (TBM III)** di PT. Perkebunan Nusantara IV Regional I. 

Aplikasi ini menggabungkan:
1. **Analisis Biometrik Allometrik:** Evaluasi indeks lingkar batang (`LB/KC`), indeks jumlah pelepah (`JP/KC`), dan panjang pelepah (`PP/KC`) terhadap tolok ukur standar **Pusat Penelitian Kelapa Sawit (PPKS)**.
2. **Sistem Informasi Spasial (WebGIS):** Visualisasi interaktif poligonal batas kebun dan blok per afdeling menggunakan Leaflet.js dengan klasifikasi kesehatan blok (Indeks Performa Kesehatan Individu / IPHI).
3. **Dual Neural Engine AI (L1 & L2):** Audit kausalitas (sebab-akibat) otomatis berbasis LLM dengan failover cerdas:
   - **L1 (Primary):** **Google Gemini (`gemini-3.8-flash`)** untuk analisis multimodal dan sintesis cepat.
   - **L2 (Backup/Failsafe):** **Groq Cloud (`qwen/qwen3.8-27b`)** berkecepatan tinggi yang siaga jika terjadi rate limit (OTPM) atau timeout tanpa menampilkan error kepada pengguna.

---

## 🚀 Akses Cepat Akun Demo (Recruiter & Reviewer)

Untuk memudahkan pengujian fungsionalitas tanpa registrasi baru, sistem telah dilengkapi dengan tombol **⚡ 1-Click Login** pada halaman login (`/login`) dengan kredensial berikut:

| Peran (Role) | Nama Pengguna | Alamat Email | Kata Sandi | Deskripsi Hak Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Superadmin** | `demo.superadmin` | `demo.superadmin@simtan.test` | `password123` | **System Owner:** Akses penuh seluruh modul, konfigurasi API AI, audit logs, dan impersonasi akun. |
| **Admin** | `demo.admin` | `demo.admin@simtan.test` | `password123` | **Data Controller:** Akses upload Excel sensus, manajemen data kebun, dan riwayat upload. |
| **User** | `demo.user` | `demo.user@simtan.test` | `password123` | **Decision Maker:** Akses visualisasi dashboard, peta GIS, dan ekspor laporan PDF eksekutif. |

---

## 🛠️ Tech Stack & Arsitektur Sistem

- **Framework Backend:** Laravel 10.x (PHP 8.1+)
- **Database Engine:** MySQL 8.0 / MariaDB
- **State Management & UI Reactivity:** Alpine.js, TailwindCSS, Blade Templating
- **WebGIS Engine:** Leaflet.js 1.9.4 dengan Orthophoto Tile Canvas Sharpener & GeoJSON layer management
- **Visualisasi Data:** ApexCharts.js
- **AI/LLM Architecture:**
  - Server-Side Proxying (Kunci API tidak pernah terekspos ke frontend browser)
  - Automatic Fallback Mechanism (Failover L1 -> L2 -> Deterministic Failsafe)
  - Token Budgeting (< 450 tokens untuk Groq agar selalu aman di bawah limit tier organisasi)

---

## 📦 Panduan Deployment Produksi (Dari Laragon ke Server Publik)

Aplikasi ini dapat di-deploy ke berbagai layanan hosting modern seperti **VPS (Ubuntu/Debian Nginx)**, **Cloud Platform (Railway / Fly.io / Render)**, maupun **cPanel Shared Hosting**.

### 1. Persyaratan Server (System Requirements)
- PHP >= 8.1 dengan ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml`
- Composer 2.x
- MySQL 8.0+ atau MariaDB 10.4+
- Web Server: Nginx atau Apache (dengan `mod_rewrite` aktif)
- Node.js & NPM (untuk kompilasi asset Vite pada saat build)

---

### 2. Langkah Deployment di VPS / Server Linux (Rekomendasi)

#### Langkah 1: Kloning Repositori
```bash
cd /var/www
git clone https://github.com/firhmgh/simtan-monitoring-palm-oil.git simtan
cd simtan
```

#### Langkah 2: Instalasi Dependensi PHP & JavaScript
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

#### Langkah 3: Konfigurasi File Lingkungan (`.env`)
Salin file template `.env.example` ke `.env`:
```bash
cp .env.example .env
nano .env
```
Sesuaikan konfigurasi kunci:
```env
APP_NAME="SIMTAN - PTPN IV Regional I"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://simtan.namadomainanda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_simtan
DB_USERNAME=simtan_dbuser
DB_PASSWORD=PasswordDatabaseKuatAnda

# Kredensial AI Neural Engine (Disimpan aman di server)
GEMINI_API_KEY=AIzaSy...
GEMINI_MODEL=gemini-3.8-flash

GROQ_API_KEY=gsk_...
GROQ_MODEL=qwen/qwen3.8-27b
GROQ_MAX_TOKENS=450
```

#### Langkah 4: Generate Application Key & Symlink Storage
```bash
php artisan key:generate
php artisan storage:link
```

#### Langkah 5: Migrasi Database & Seeding Akun Demo
```bash
php artisan migrate --force
php artisan db:seed --force
```

#### Langkah 6: Optimasi Cache Produksi
Jalankan perintah optimasi bawaan Laravel untuk performa maksimal:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

#### Langkah 7: Atur Izin Folder (Permissions)
```bash
sudo chown -R www-data:www-data /var/www/simtan
sudo chmod -R 775 /var/www/simtan/storage /var/www/simtan/bootstrap/cache
```

#### Langkah 8: Konfigurasi Nginx
Pastikan `root` diarahkan ke folder `/public`:
```nginx
server {
    listen 80;
    server_name simtan.namadomainanda.com;
    root /var/www/simtan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

### 3. Opsi Portofolio GitHub Pages (Halaman Case Study)

Karena Laravel membutuhkan runtime PHP & Database MySQL di server dinamis, GitHub Pages (yang hanya melayani file statis HTML/CSS/JS) digunakan sebagai **Halaman Portofolio / Case Study** yang menyediakan ringkasan arsitektur sistem dan tombol langsung menuju **Live Demo SIMTAN**.

1. Halaman statis case study telah disiapkan di folder [`/docs/index.html`](file:///c:/simtan-monitoring-palm-oil/docs/index.html).
2. Untuk mengaktifkannya di GitHub:
   - Buka repositori di GitHub -> Masuk ke tab **Settings** -> Pilih menu **Pages** di bilah sisi kiri.
   - Pada bagian **Build and deployment > Source**, pilih `Deploy from a branch`.
   - Pilih Branch: `main` (atau `master`) dan Folder: `/docs`.
   - Klik **Save**.
   - Halaman portofolio SIMTAN akan langsung online di `https://username.github.io/simtan-monitoring-palm-oil/`.
3. Di dalam file [`docs/index.html`](file:///c:/simtan-monitoring-palm-oil/docs/index.html), Anda cukup mengganti tautan pada tombol `Live Demo SIMTAN` ke URL hosting produksi Anda (misal Railway/VPS).

---

## 🔒 Kebijakan Keamanan & Manajemen Kredensial AI

1. **Proteksi Kunci API AI:** Seluruh permintaan AI ke Google Gemini dan Groq Cloud hanya dieksekusi secara privat oleh backend PHP ([`AIService.php`](file:///c:/simtan-monitoring-palm-oil/app/Services/AIService.php)). Tidak ada API key yang dikirim ke browser atau terekspos di kode JavaScript publik.
2. **Database-Driven Failover:** Administrator tingkat Superadmin dapat memperbarui kunci API dan batas threshold langsung melalui antarmuka **Settings** tanpa perlu me-restart server.
3. **Penyaringan Berkas Sensitif:** File `.env`, `.env.backup`, file dump database `.sql`, dan cache framework telah dimasukkan ke dalam [`.gitignore`](file:///c:/simtan-monitoring-palm-oil/.gitignore) untuk mencegah kebocoran data rahasia pada repositori publik.

---

## 👨‍💻 Kontributor & Lisensi
- **Dikembangkan oleh:** Tim Pengembang SIMTAN
- **Kerjasama Teknis:** PT. Perkebunan Nusantara IV (Regional I)
- **Lisensi:** Open-source untuk keperluan portofolio, evaluasi teknis, dan riset akademik.
