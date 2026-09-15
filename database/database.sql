-- Database Schema untuk Praktikum 3 - Pemrograman Web
-- Nama Database: portfolio_db

CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

-- Tabel Pengguna (Users) untuk Autentikasi Login
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Proyek (Projects) untuk Menyimpan Portofolio Karya
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL DEFAULT 'images/project1.jpeg',
    link VARCHAR(255) NOT NULL DEFAULT '#',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Akun default untuk login:
-- Username : admin
-- Password : admin123 (dihash menggunakan password_hash() standar PHP)
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$dwC/i/uFq6trWcvMYU.15Oh/LbTnrYd05ToirY2Pm.2W212JIqa4K')
ON DUPLICATE KEY UPDATE username=username;

-- Data awal proyek (karya dari Praktikum 2 sebelumnya)
INSERT INTO projects (title, description, image, link) VALUES 
(
    'Fuenzer Research Website',
    'Asisten Riset Ilmiah Berbasis AI. Temukan referensi jurnal ilmiah dan dapatkan sintesis instan dengan Google Gemini.',
    'images/project1.jpeg',
    'https://research.fuenzer.web.id'
),
(
    'Fuenzer Sports Website',
    'Platform simulasi analitik olahraga interaktif berbasis AI. Platform ini berfungsi sebagai perpaduan antara antarmuka pencarian instan dan permainan manajemen olahraga.',
    'images/project2.jpeg',
    'https://sports.fuenzer.web.id'
);
