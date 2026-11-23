<?php
$host = 'localhost';
$db   = 'aplikasi_antrean';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Koneksi database gagal. Pastikan MySQL XAMPP aktif dan database aplikasi_antrean sudah diimpor. Detail: ' . htmlspecialchars($e->getMessage()));
}

function tanggalHariIni(): string {
    return date('Y-m-d');
}

function pastikanCounter(PDO $pdo): void {
    $stmt = $pdo->prepare('INSERT IGNORE INTO queue_counters (tanggal, nomor_terakhir) VALUES (?, 0)');
    $stmt->execute([tanggalHariIni()]);
}
