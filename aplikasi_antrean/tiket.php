<?php
require __DIR__ . '/config/koneksi.php';
$nomor=(int)($_GET['nomor']??0); $q=$pdo->prepare('SELECT * FROM antrean WHERE tanggal=? AND nomor_antrean=? LIMIT 1'); $q->execute([date('Y-m-d'),$nomor]); $r=$q->fetch();
if(!$r){http_response_code(404);die('Pesanan tidak ditemukan.');}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Tiket <?=h(nomorAntrean($nomor))?></title><link rel="stylesheet" href="style.css"></head><body class="ticket-page"><main class="ticket"><div class="brand-mark">K</div><span class="kicker">TIKET ANTREAN KANTIN</span><h1>Pesanan berhasil!</h1><p>Simpan nomor ini untuk memantau pesananmu.</p><div class="ticket-number"><?=h(nomorAntrean($nomor))?></div><div class="ticket-details"><span>Nama</span><b><?=h($r['nama_pemesan'])?></b><span>Pesanan</span><b><?=h($r['nama_produk'])?> × <?= (int)$r['jumlah']?></b><span>Status</span><b><?=h($r['status'])?></b></div><button class="primary" onclick="window.print()">Cetak Tiket</button><a class="back" href="index.php">← Kembali ke antrean</a></main></body></html>
