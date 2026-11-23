CREATE DATABASE IF NOT EXISTS aplikasi_antrean CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aplikasi_antrean;

DROP TABLE IF EXISTS antrean;
DROP TABLE IF EXISTS produk;

CREATE TABLE produk (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(100) NOT NULL,
 harga DECIMAL(12,2) NOT NULL DEFAULT 0,
 stok INT NOT NULL DEFAULT 0,
 aktif TINYINT(1) NOT NULL DEFAULT 1,
 dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE antrean (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nomor_antrean INT NOT NULL,
 nama_pemesan VARCHAR(100) NOT NULL,
 produk_id INT NULL,
 nama_produk VARCHAR(100) NOT NULL,
 jumlah INT NOT NULL,
 catatan VARCHAR(255) DEFAULT NULL,
 status ENUM('Menunggu','Diproses','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu',
 tanggal DATE NOT NULL,
 dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_antrean_tanggal_status (tanggal,status),
 UNIQUE KEY uniq_nomor_tanggal (tanggal,nomor_antrean),
 CONSTRAINT fk_antrean_produk FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO produk (nama,harga,stok,aktif)
SELECT 'Nasi Goreng',12000,30,1 WHERE NOT EXISTS (SELECT 1 FROM produk WHERE nama='Nasi Goreng');
INSERT INTO produk (nama,harga,stok,aktif)
SELECT 'Mie Goreng',10000,30,1 WHERE NOT EXISTS (SELECT 1 FROM produk WHERE nama='Mie Goreng');
INSERT INTO produk (nama,harga,stok,aktif)
SELECT 'Roti Bakar',8000,25,1 WHERE NOT EXISTS (SELECT 1 FROM produk WHERE nama='Roti Bakar');
INSERT INTO produk (nama,harga,stok,aktif)
SELECT 'Es Teh',4000,50,1 WHERE NOT EXISTS (SELECT 1 FROM produk WHERE nama='Es Teh');
INSERT INTO produk (nama,harga,stok,aktif)
SELECT 'Air Mineral',3000,50,1 WHERE NOT EXISTS (SELECT 1 FROM produk WHERE nama='Air Mineral');
