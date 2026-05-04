# Toko Online — CodeWithDiki

Aplikasi e-commerce berbasis **Laravel 13** yang dibangun dengan stack modern: **Filament v5** untuk panel admin, **Livewire v4** untuk UI frontend yang reaktif, **Midtrans** sebagai payment gateway, serta sistem manajemen produk, transaksi, dan pembayaran yang modular.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Prasyarat](#prasyarat)
- [Instalasi](#instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Default](#akun-default)
- [Struktur Modul](#struktur-modul)
- [Perintah Artisan Tambahan](#perintah-artisan-tambahan)

---

## Fitur Utama

- **Storefront publik** — homepage builder (mason blocks: banner, brand, produk unggulan, testimoni, dll.), halaman eksplorasi produk, detail produk.
- **Keranjang & Checkout** — cart management, checkout flow terintegrasi dengan transaksi.
- **Pembayaran via Midtrans** — mendukung berbagai metode pembayaran, webhook otomatis.
- **Dashboard pelanggan** — riwayat transaksi, riwayat pembayaran, pengaturan akun.
- **Panel Admin (Filament)** — manajemen produk, brand, kategori, varian, transaksi, pembayaran, role & permission, pengaturan situs.
- **Homepage Builder** — konfigurasi tampilan homepage langsung dari panel admin menggunakan Awcodes Mason.
- **SEO otomatis** — integrasi `ralphjsmit/laravel-seo` + sitemap generator.
- **Queue & Real-time** — Laravel Horizon untuk queue, Laravel Reverb untuk WebSocket.

---

## Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 13 (PHP ^8.3) |
| Frontend | Livewire v4, Tailwind CSS v4, Vite |
| Admin Panel | Filament v5 |
| Payment Gateway | Midtrans (via `codewithdiki/payment-module`) |
| Queue | Laravel Horizon |
| WebSocket | Laravel Reverb |
| Permission | Spatie Laravel Permission v7 |
| Settings | Spatie Laravel Settings v3 |
| SEO | ralphjsmit/laravel-seo |
| Sitemap | Spatie Laravel Sitemap v8 |
| Database (dev) | SQLite (default) / MySQL |

---

## Prasyarat

Pastikan environment lokal kamu sudah memiliki:

- **PHP** >= 8.3 (dengan ekstensi: `pdo`, `pdo_sqlite` atau `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `curl`)
- **Composer** >= 2
- **Node.js** >= 20 & **npm** >= 10
- **SQLite** (default) atau **MySQL** / **PostgreSQL**
- **Redis** (opsional, dipakai untuk cache & queue di production)

---

## Instalasi

### 1. Clone Repositori

```bash
git clone <url-repositori> toko-online
cd toko-online
```

### 2. Daftarkan Akun & Konfigurasi Private Package

Proyek ini menggunakan package privat dari **codewithdiki** (`payment-module`, `transaction-module`, `product-module`) yang di-host di repository privat `https://dikiakbarasyidiq.dev`. Ikuti langkah berikut sebelum menjalankan `composer install`:

#### a. Buat Akun

Daftar akun di [https://dikiakbarasyidiq.dev/auth/register](https://dikiakbarasyidiq.dev/auth/register).

Setelah berhasil mendaftar, buka halaman **Dashboard → Account** untuk melihat **license key** kamu.

#### b. Konfigurasi Bearer Token Composer

Salin license key kamu, lalu jalankan perintah berikut di terminal (ganti `<license_key>` dengan license key milikmu):

```bash
composer config bearer.dikiakbarasyidiq.dev <license_key>
```

Perintah ini menyimpan token autentikasi ke file `auth.json` lokal Composer agar bisa mengakses repository privat.

#### c. Pastikan Repository Sudah Ada di `composer.json`

Pastikan blok `repositories` berikut sudah ada di `composer.json` (jika belum, tambahkan):

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://dikiakbarasyidiq.dev"
        }
    ]
}
```

> Repository ini sudah tercantum secara default di file `composer.json` proyek ini.

### 3. Install Dependensi PHP

```bash
composer install
```

### 5. Install Dependensi Node.js

```bash
npm install
```

### 6. Salin File Environment

```bash
cp .env.example .env
```

### 7. Generate Application Key

```bash
php artisan key:generate
```

---

## Konfigurasi Environment

Buka file `.env` dan sesuaikan nilai berikut:

#### Aplikasi

```dotenv
APP_NAME="CodeWithDiki Toko Online"
APP_URL=http://localhost:8000
APP_ENV=local
APP_DEBUG=true
```

#### Database

Secara default aplikasi menggunakan **SQLite**. Tidak perlu konfigurasi tambahan.

```dotenv
DB_CONNECTION=sqlite
```

Jika ingin menggunakan **MySQL**, ubah menjadi:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toko_online
DB_USERNAME=root
DB_PASSWORD=
```

#### Midtrans (Payment Gateway)

Daftar akun di [https://midtrans.com](https://midtrans.com), lalu isi:

```dotenv
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=false
```

> Untuk pengujian lokal, gunakan credential **Sandbox** dari dashboard Midtrans.

#### Queue & WebSocket (Opsional untuk dev)

Default queue driver sudah menggunakan database, jadi tidak perlu Redis di lokal:

```dotenv
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=log
```

#### Pajak Transaksi

```dotenv
TAX_PERCENTAGE=0
```

---

## Menjalankan Aplikasi

### Langkah Sekali: Migrasi & Seed Database

```bash
# Jalankan migrasi
php artisan migrate

# Seed role (super_admin, partner, customer)
php artisan db:seed --class=RoleSeeder

# Seed data contoh (user test@example.com)
php artisan db:seed --class=DatabaseSeeder
```

### Buat Akun Super Admin Filament

```bash
php artisan make:filament-user
```

Ikuti prompt untuk memasukkan nama, email, dan password. Akun ini digunakan untuk login ke panel admin di `/admin`.

### Setup Filament Shield (Role & Permission)

Setelah membuat akun super admin, jalankan perintah berikut untuk mengatur permission berbasis role di Filament:

```bash
php artisan shield:setup
```

Perintah ini akan mendaftarkan semua policy dan permission yang dibutuhkan oleh Filament Shield. Jalankan sekali saat pertama kali setup.

### Jalankan Semua Service Sekaligus

Perintah berikut menjalankan development server, queue worker, log viewer, dan Vite dalam satu terminal:

```bash
composer dev
```

Aplikasi akan berjalan di: **http://localhost:8000**

> Perintah ini menggunakan `concurrently` untuk menjalankan `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, dan `npm run dev` secara bersamaan.

### Atau Jalankan Secara Terpisah

```bash
# Terminal 1 — PHP development server
php artisan serve

# Terminal 2 — Queue worker
php artisan queue:listen --tries=1 --timeout=0

# Terminal 3 — Vite (asset bundler)
npm run dev
```

---

## Akun Default

| Akun | Email | Password | Keterangan |
|---|---|---|---|
| Test User | `test@example.com` | *(diatur saat seed)* | User biasa dari DatabaseSeeder |
| Super Admin | *(diatur saat `make:filament-user`)* | *(diatur manual)* | Akses panel admin `/admin` |

---

## Struktur Modul

Aplikasi ini menggunakan arsitektur **modular** via package internal `codewithdiki/*`:

| Package | Fungsi |
|---|---|
| `codewithdiki/product-module` | Manajemen produk, brand, kategori, varian, warna, ukuran, gambar, review |
| `codewithdiki/transaction-module` | Manajemen transaksi, item transaksi, diskon, log status, customer |
| `codewithdiki/payment-module` | Manajemen pembayaran, metode pembayaran, integrasi Midtrans webhook |

### Role Pengguna

| Role | Deskripsi |
|---|---|
| `super_admin` | Akses penuh ke seluruh panel admin |
| `partner` | Akses terbatas (misalnya kelola produk) |
| `customer` | Pengguna frontend/toko |

---

## Perintah Artisan Tambahan

| Perintah | Fungsi |
|---|---|
| `php artisan app:generate-sitemap` | Generate ulang `public/sitemap.xml` |
| `php artisan shield:generate --all` | Generate permission untuk semua resource Filament |
| `php artisan horizon` | Jalankan Laravel Horizon (queue dashboard) |
| `php artisan reverb:start` | Jalankan Laravel Reverb (WebSocket server) |
| `php artisan pail` | Tail log aplikasi secara real-time |

---

## Setup Cepat (One-liner)

Jika ingin menjalankan semua langkah instalasi sekaligus, tersedia script `setup` di `composer.json`:

```bash
composer setup
```

Script ini akan otomatis menjalankan:
1. `composer install`
2. Menyalin `.env.example` ke `.env` (jika belum ada)
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install`
6. `npm run build`

Setelah selesai, tetap jalankan `php artisan db:seed` dan `php artisan make:filament-user` secara manual.

---

## Panel Admin

Panel admin tersedia di `/admin` dan dibangun dengan **Filament v5**. Fitur yang tersedia:

- **Dashboard** — statistik total penjualan, revenue, transaksi pending & failed.
- **Kelola Situs** — ubah nama situs, logo, deskripsi, info kontak, media sosial, SEO, dan builder homepage.
- **Produk** — brand, kategori, produk, varian, gambar, warna, ukuran, review.
- **Transaksi** — daftar transaksi, detail, update status, log perubahan.
- **Pembayaran** — metode pembayaran, grup metode, riwayat pembayaran.
- **Pengguna & Role** — manajemen user, role, dan permission (via Filament Shield).
