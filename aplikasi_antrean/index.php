<?php
require __DIR__ . '/config/koneksi.php';
$today = date('Y-m-d');
$produk = $pdo->query("SELECT id,nama,harga,stok FROM produk WHERE aktif=1 AND stok>0 ORDER BY nama")->fetchAll();
$q = $pdo->prepare("SELECT * FROM antrean WHERE tanggal=? ORDER BY nomor_antrean DESC LIMIT 10"); $q->execute([$today]); $recent = $q->fetchAll();
$q = $pdo->prepare("SELECT COUNT(*) FROM antrean WHERE tanggal=? AND status IN ('Menunggu','Diproses')"); $q->execute([$today]); $activeCount=(int)$q->fetchColumn();
$q = $pdo->prepare("SELECT nomor_antrean FROM antrean WHERE tanggal=? AND status='Diproses' ORDER BY id DESC LIMIT 1"); $q->execute([$today]); $beingMade=$q->fetchColumn();
$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $nama=trim($_POST['nama_pemesan']??''); $produkId=(int)($_POST['produk_id']??0); $jumlah=(int)($_POST['jumlah']??0); $catatan=trim($_POST['catatan']??'');
  if ($nama==='' || $produkId<1 || $jumlah<1 || $jumlah>20) $error='Lengkapi nama, menu, dan jumlah pesanan dengan benar.';
  else {
    $q=$pdo->prepare('SELECT id,nama,stok FROM produk WHERE id=? AND aktif=1'); $q->execute([$produkId]); $p=$q->fetch();
    if (!$p || $jumlah>(int)$p['stok']) $error='Menu tidak tersedia atau jumlah melebihi stok.';
    else {
      $q=$pdo->prepare('SELECT COALESCE(MAX(nomor_antrean),0)+1 FROM antrean WHERE tanggal=?'); $q->execute([$today]); $nomor=(int)$q->fetchColumn();
      $ins=$pdo->prepare("INSERT INTO antrean(nomor_antrean,nama_pemesan,produk_id,nama_produk,jumlah,catatan,tanggal) VALUES(?,?,?,?,?,?,?)");
      $ins->execute([$nomor,$nama,$produkId,$p['nama'],$jumlah,$catatan,$today]);
      header('Location: tiket.php?nomor='.$nomor); exit;
    }
  }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta http-equiv="refresh" content="30"><title>Antrean Kantin Sekolah</title><link rel="stylesheet" href="style.css"></head><body>
<header class="header"><div class="brand"><div class="brand-mark">K</div><div><b>KANTIN SEKOLAH</b><small>Layanan pemesanan & antrean</small></div></div><nav><a href="#pesan">Buat Pesanan</a><a href="#antrean">Lihat Antrean</a><a class="staff-link" href="petugas.php">Petugas</a></nav></header>
<main class="wrap"><section class="intro"><div><span class="kicker">PESAN DENGAN MUDAH</span><h1>Pesan makanan,<br><em>tanpa perlu berdesakan.</em></h1><p>Isi pesananmu, dapatkan nomor antrean, lalu pantau prosesnya sampai siap diambil.</p></div><div class="intro-art"><div class="plate">🍱</div><span class="art-tag">FRESH • FAST • EASY</span></div></section>
<?php if($error): ?><div class="alert"><?=h($error)?></div><?php endif; ?>
<?php if(isset($_GET['created'])): ?><div class="success">Pesanan berhasil dibuat. Nomor antrean kamu <?=h(nomorAntrean((int)$_GET['created']))?>.</div><?php endif; ?>
<section class="stats"><article><small>SEDANG DIPROSES</small><strong><?= $beingMade ? h(nomorAntrean($beingMade)) : '—' ?></strong><span>Nomor yang sedang disiapkan</span></article><article><small>ANTREAN AKTIF</small><strong><?=$activeCount?></strong><span>Pesanan menunggu dan diproses</span></article><article><small>MENU TERSEDIA</small><strong><?=count($produk)?></strong><span>Pilihan menu kantin hari ini</span></article></section>
<div class="content-grid"><section class="panel order-panel" id="pesan"><div class="panel-head"><div><span class="kicker">FORM PEMESANAN</span><h2>Ambil nomor antrean</h2><p>Masukkan detail pesananmu di bawah ini.</p></div><span class="step">01 / 03</span></div>
<form method="post" class="order-form"><label>Nama pemesan<input name="nama_pemesan" maxlength="100" placeholder="Contoh: Andi" required></label><label>Pilih menu<select name="produk_id" required><option value="">-- Pilih makanan / minuman --</option><?php foreach($produk as $p): ?><option value="<?=$p['id']?>"><?=h($p['nama'])?> — Rp <?=number_format($p['harga'],0,',','.')?> (stok <?=$p['stok']?>)</option><?php endforeach; ?></select></label><div class="two-fields"><label>Jumlah<input type="number" name="jumlah" min="1" max="20" value="1" required></label><label>Catatan <small>(opsional)</small><input name="catatan" maxlength="255" placeholder="Tidak pedas, dll."></label></div><button class="primary" type="submit">Buat Pesanan <span>→</span></button></form></section>
<section class="panel queue-panel" id="antrean"><div class="panel-head"><div><span class="kicker">PEMANTAUAN</span><h2>Antrean hari ini</h2><p>Daftar pesanan terbaru.</p></div><span class="live">● LIVE</span></div><div class="queue-table"><div class="table-head"><span>NOMOR</span><span>PEMESAN / MENU</span><span>STATUS</span></div><?php if(!$recent): ?><div class="empty">Belum ada pesanan hari ini.</div><?php else: foreach(array_reverse($recent) as $r): ?><div class="queue-row"><b><?=h(nomorAntrean($r['nomor_antrean']))?></b><div><strong><?=h($r['nama_pemesan'])?></strong><small><?=h($r['nama_produk'])?> × <?= (int)$r['jumlah']?></small></div><span class="status <?=strtolower($r['status'])?>"><?=h($r['status'])?></span></div><?php endforeach; endif; ?></div><p class="refresh-note">Halaman diperbarui otomatis setiap 30 detik.</p></section></div>
<section class="steps"><div><b>1</b><strong>Isi pesanan</strong><span>Pilih menu dan jumlah.</span></div><div><b>2</b><strong>Simpan nomor</strong><span>Nomor antrean dibuat otomatis.</span></div><div><b>3</b><strong>Pantau status</strong><span>Ambil saat pesanan selesai.</span></div></section></main><footer>ANTREAN KANTIN SEKOLAH · Pesan tertib, kantin nyaman.</footer></body></html>
