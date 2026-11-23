<?php
$host = 'localhost';
$db = 'aplikasi_antrean';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false];
try { $pdo = new PDO($dsn, $user, $pass, $options); }
catch (PDOException $e) { die('Koneksi database gagal. Pastikan MySQL aktif dan database aplikasi_antrean sudah diimpor. ' . htmlspecialchars($e->getMessage())); }
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function nomorAntrean($n): string { return 'A' . str_pad((string)$n, 3, '0', STR_PAD_LEFT); }
