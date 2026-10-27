<?php
// Layout atas: head, navbar, header halaman, dan flash message.
// Variabel yang dipakai: $judul, $subjudul, $menu, $tamu.
$judul = $judul ?? 'Inventaris Barang';
$subjudul = $subjudul ?? '';
$menu = $menu ?? '';
$tamu = $tamu ?? false;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?> - Inventaris Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-md app-navbar" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand" href="<?= $tamu ? 'login.php' : 'dashboard.php' ?>">Inventaris Barang</a>
        <?php if (!$tamu): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menuUtama" aria-controls="menuUtama"
                    aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menuUtama">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link<?= $menu === 'dashboard' ? ' active' : '' ?>" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link<?= $menu === 'barang' ? ' active' : '' ?>" href="barang.php">Data Barang</a></li>
                    <li class="nav-item"><a class="nav-link<?= $menu === 'laporan' ? ' active' : '' ?>" href="laporan.php">Laporan</a></li>
                </ul>
                <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 py-2 py-md-0">
                    <span class="navbar-text py-0"><?= e($_SESSION['user_nama'] ?? '') ?></span>
                    <form action="logout.php" method="post" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-light btn-sm">Keluar</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>

<header class="page-header">
    <div class="container">
        <h1><?= e($judul) ?></h1>
        <?php if ($subjudul !== ''): ?><p><?= e($subjudul) ?></p><?php endif; ?>
    </div>
</header>

<main class="container py-4 flex-grow-1">
    <?php foreach (flash_ambil() as $f): ?>
        <div class="alert alert-<?= e($f['tipe']) ?> alert-dismissible fade show" role="alert">
            <?= e($f['pesan']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    <?php endforeach; ?>
