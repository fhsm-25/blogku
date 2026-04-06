-- =============================================
-- DATABASE: blogku
-- =============================================
CREATE DATABASE IF NOT EXISTS blogku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blogku;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    avatar VARCHAR(255) DEFAULT 'default.png',
    role ENUM('admin','author') DEFAULT 'author',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel kategori
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    deskripsi TEXT
);

-- Tabel tag
CREATE TABLE IF NOT EXISTS tag (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL
);

-- Tabel artikel
CREATE TABLE IF NOT EXISTS artikel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    konten LONGTEXT,
    ringkasan TEXT,
    gambar VARCHAR(255),
    id_kategori INT,
    id_user INT,
    status ENUM('draft','publish') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel artikel_tag
CREATE TABLE IF NOT EXISTS artikel_tag (
    id_artikel INT,
    id_tag INT,
    PRIMARY KEY (id_artikel, id_tag),
    FOREIGN KEY (id_artikel) REFERENCES artikel(id) ON DELETE CASCADE,
    FOREIGN KEY (id_tag) REFERENCES tag(id) ON DELETE CASCADE
);

-- Tabel komentar
CREATE TABLE IF NOT EXISTS komentar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_artikel INT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    isi TEXT NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_artikel) REFERENCES artikel(id) ON DELETE CASCADE
);

-- =============================================
-- DATA AWAL
-- =============================================

-- Admin default (password: admin123)
INSERT INTO users (nama, username, password, email, role) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@blogku.com', 'admin'),
('Author Satu', 'author1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'author@blogku.com', 'author');

-- Kategori awal
INSERT INTO kategori (nama, slug, deskripsi) VALUES
('Teknologi', 'teknologi', 'Artikel seputar dunia teknologi terkini'),
('Lifestyle', 'lifestyle', 'Gaya hidup modern dan inspiratif'),
('Tutorial', 'tutorial', 'Panduan dan tutorial praktis'),
('Berita', 'berita', 'Berita terkini dan terpercaya');

-- Tag awal
INSERT INTO tag (nama, slug) VALUES
('PHP', 'php'), ('MySQL', 'mysql'), ('Web', 'web'),
('Programming', 'programming'), ('Design', 'design'), ('Tips', 'tips');

-- Artikel contoh
INSERT INTO artikel (judul, slug, konten, ringkasan, id_kategori, id_user, status, views) VALUES
('Selamat Datang di Blog Ku', 'selamat-datang-di-blog-ku',
'<p>Selamat datang di <strong>Blog Ku</strong>! Blog ini dibuat dengan PHP dan MySQL murni tanpa framework.</p><p>Di sini kamu akan menemukan berbagai artikel menarik seputar teknologi, lifestyle, dan tutorial yang bermanfaat.</p><p>Nikmati membaca dan jangan lupa tinggalkan komentar ya!</p>',
'Selamat datang di Blog Ku, platform blog yang dibuat dengan PHP dan MySQL.', 1, 1, 'publish', 120),
('Tips Belajar Programming untuk Pemula', 'tips-belajar-programming-pemula',
'<p>Belajar programming memang tidak mudah, tapi bukan berarti mustahil. Berikut tips-tips yang bisa membantu kamu:</p><ul><li>Mulai dari dasar-dasar logika</li><li>Konsisten berlatih setiap hari</li><li>Buat proyek kecil untuk praktek</li><li>Bergabung dengan komunitas developer</li></ul><p>Semangat terus dan jangan menyerah!</p>',
'Kumpulan tips bermanfaat untuk pemula yang ingin belajar programming dari nol.', 3, 1, 'publish', 85);

INSERT INTO artikel_tag VALUES (1,3),(1,1),(2,4),(2,6);

INSERT INTO komentar (id_artikel, nama, email, isi, status) VALUES
(1, 'Budi Santoso', 'budi@email.com', 'Artikel yang sangat bermanfaat! Terima kasih sudah berbagi.', 'approved'),
(1, 'Siti Rahayu', 'siti@email.com', 'Blog yang keren, semoga terus update ya!', 'approved'),
(2, 'Andi Wijaya', 'andi@email.com', 'Tips yang sangat membantu untuk saya yang baru belajar!', 'approved');
