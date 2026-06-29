# Aplikasi Manajemen Surat

Aplikasi Manajemen Surat adalah sistem informasi berbasis web yang dirancang untuk mendigitalkan, mengelola, melacak, dan menyetujui proses surat-menyurat secara terpusat di dalam sebuah instansi atau perusahaan.

## 🚀 Fitur Utama

- **Manajemen Hak Akses (Multi-Role)**: Dilengkapi tingkatan akses untuk *Super Admin*, *Admin Sistem*, *Admin Divisi*, dan *Staff* dengan wewenangnya masing-masing.
- **Alur Persetujuan Surat (Approval Workflow)**: Surat yang diajukan dapat melewati proses review persetujuan (revisi, disetujui, atau ditolak) dengan alur yang menyesuaikan jenis kategori surat.
- **Audit Trail & Log Aktivitas**: Setiap pergerakan surat (perubahan status) dan aktivitas seluruh pengguna dicatat secara detail untuk keamanan dan transparansi.
- **Manajemen Kategori & Divisi**: Pengaturan surat dan staff secara spesifik berdasarkan divisi operasionalnya.
- **Manajemen Template Surat**: Fasilitas untuk menyimpan dan mengunduh format standar surat perusahaan.
- **Notifikasi**: Sistem pemberitahuan agar pengguna tahu jika ada surat yang harus diperiksa atau status surat yang berubah.

## 💻 Persyaratan Sistem (Requirements)

Pastikan lingkungan server atau komputer Anda memenuhi kriteria berikut:
- PHP >= 8.2
- Composer
- Node.js & NPM
- Database (MySQL / SQLite / PostgreSQL)

## 🛠 Panduan Instalasi (Local Development)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di komputer lokal Anda:

1. **Install Dependensi PHP & Frontend**
   Buka terminal di direktori project, lalu jalankan perintah:
   ```bash
   composer install
   npm install
   ```

2. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   *Atur koneksi database Anda di file `.env` yang baru dibuat (sesuaikan nama database, username, dan password).*

3. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

4. **Migrasi Database & Seeding Data Awal**
   Perintah ini akan membangun seluruh struktur tabel database beserta data *dummy*/awal yang wajib ada:
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server Lokal**
   Anda perlu menjalankan dua terminal secara bersamaan untuk PHP dan Frontend (Vite):
   - Terminal 1 (PHP Server):
     ```bash
     php artisan serve
     ```
   - Terminal 2 (Vite / Tailwind CSS):
     ```bash
     npm run dev
     ```

   Aplikasi Anda sekarang dapat diakses melalui browser di alamat: `http://localhost:8000`

## 🔑 Akun Default (Login)

Setelah perintah *seeding* berhasil dijalankan, Anda dapat langsung mencoba masuk ke dalam sistem menggunakan akun akses tertinggi:

- **Role:** Super Admin
- **Email:** `superadmin@kantor.com`
- **Password:** `password`

---
*Dikembangkan dengan menggunakan Framework Laravel 11, TailwindCSS, dan Vite.*
