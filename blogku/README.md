# 📝 Blog Ku — Aplikasi Blog Dinamis
> Dibuat dengan PHP & MySQL murni (tanpa framework)

---

## 🚀 Cara Install

### 1. Persyaratan
- XAMPP / WAMP / Laragon (PHP 7.4+ & MySQL 5.7+)
- Browser modern

### 2. Langkah Instalasi

**a) Copy folder ke htdocs**
```
Salin folder `blogku` ke:
C:/xampp/htdocs/blogku
```

**b) Import database**
- Buka phpMyAdmin: http://localhost/phpmyadmin
- Klik **Import** → pilih file `config/database.sql`
- Klik **Go**

**c) Sesuaikan konfigurasi** (jika perlu)
Edit file `config/db.php`:
```php
$host = 'localhost';
$db   = 'blogku';
$user = 'root';    // sesuaikan username MySQL
$pass = '';        // sesuaikan password MySQL
```

**d) Buka di browser**
- Blog publik: http://localhost/blogku/public/index.php
- Login admin: http://localhost/blogku/auth/login.php

---

## 🔑 Akun Default

| Role   | Username | Password  |
|--------|----------|-----------|
| Admin  | admin    | password  |
| Author | author1  | password  |

> ⚠️ Ganti password setelah login pertama!

---

## 📁 Struktur Folder

```
blogku/
├── admin/
│   ├── dashboard.php          ← Dashboard admin
│   ├── artikel/
│   │   ├── index.php          ← Daftar artikel
│   │   ├── tambah.php         ← Tambah artikel baru
│   │   ├── edit.php           ← Edit artikel
│   │   └── hapus.php          ← Hapus artikel
│   ├── kategori/
│   │   └── index.php          ← CRUD kategori
│   ├── tag/
│   │   └── index.php          ← CRUD tag
│   ├── komentar/
│   │   ├── index.php          ← Kelola komentar
│   │   └── aksi.php           ← Approve/hapus komentar
│   └── user/
│       └── index.php          ← Kelola pengguna
├── auth/
│   ├── login.php              ← Halaman login + CAPTCHA
│   └── logout.php             ← Proses logout
├── config/
│   ├── db.php                 ← Koneksi database
│   └── database.sql           ← Script SQL database
├── includes/
│   ├── auth_check.php         ← Cek session login
│   └── functions.php          ← Helper functions
├── public/
│   ├── index.php              ← Halaman utama blog
│   └── artikel.php            ← Detail artikel + komentar
└── assets/
    ├── css/
    │   └── style.css          ← Stylesheet utama
    ├── js/                    ← JavaScript (opsional)
    └── img/                   ← Upload gambar artikel
```

---

## ✨ Fitur Lengkap

### 🌐 Halaman Publik
- [x] Beranda dengan daftar artikel terbaru
- [x] Pencarian artikel
- [x] Filter by kategori & tag
- [x] Detail artikel dengan konten HTML
- [x] Komentar pengunjung (nama + email, tanpa login)
- [x] Artikel terkait
- [x] Sidebar: kategori, artikel populer, tag cloud
- [x] Pagination
- [x] View counter

### 🔐 Sistem Auth
- [x] Login Admin & Author
- [x] CAPTCHA matematika di form login
- [x] Session management
- [x] Logout

### 📝 Admin — Artikel
- [x] Daftar semua artikel
- [x] Tambah artikel baru (dengan upload gambar)
- [x] Edit artikel
- [x] Hapus artikel
- [x] Status draft / publish
- [x] Author hanya bisa kelola artikel sendiri

### 📂 Admin — Kategori & Tag
- [x] Tambah, edit, hapus kategori
- [x] Tambah, edit, hapus tag
- [x] Slug otomatis
- [x] Relasi artikel ↔ tag (many-to-many)

### 💬 Admin — Komentar
- [x] Moderasi komentar (pending → approved)
- [x] Hapus komentar
- [x] Filter by status

### 👥 Admin — Pengguna (Admin only)
- [x] Tambah pengguna baru
- [x] Edit nama, email, role, password
- [x] Hapus pengguna

---

## 🔒 Keamanan
- Password di-hash dengan `password_hash()` (bcrypt)
- PDO dengan prepared statements (anti SQL injection)
- Session untuk autentikasi
- CAPTCHA di halaman login
- Validasi file upload (hanya gambar)

---

## 📞 Dukungan
Jika ada pertanyaan atau masalah, silakan hubungi pengembang.

**Selamat ngeblog! 🎉**
