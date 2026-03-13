<?php
include 'db.php';
$sql = "SELECT * FROM items";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Kasir QRIS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0f4c81;
            --primary-light: #1a6db5;
            --accent: #00b4d8;
            --success: #06d6a0;
            --danger: #ef233c;
            --warning: #f8961e;
            --bg: #f0f4f8;
            --card-bg: #ffffff;
            --text: #1a1a2e;
            --text-muted: #6b7280;
            --border: #e2e8f0;
            --shadow: 0 4px 24px rgba(15,76,129,0.10);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* Sidebar / Topbar */
        .topbar {
            background: var(--primary);
            color: white;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(15,76,129,0.25);
        }
        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 1.2rem;
            letter-spacing: -0.3px;
        }
        .topbar-brand .logo-icon {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .topbar-nav a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .topbar-nav a:hover, .topbar-nav a.active {
            color: white;
        }

        .main-content { padding: 2rem; max-width: 1200px; margin: 0 auto; }

        /* Page header */
        .page-header {
            margin-bottom: 1.75rem;
        }
        .page-header h1 {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
        }
        .page-header p {
            color: var(--text-muted);
            margin: 4px 0 0;
            font-size: 0.9rem;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .card-header-custom {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }
        .card-header-custom .title {
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-header-custom .title i {
            width: 30px; height: 30px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
        }
        .icon-blue { background: #dbeafe; color: var(--primary); }
        .icon-green { background: #d1fae5; color: #059669; }
        .icon-purple { background: #ede9fe; color: #7c3aed; }
        .card-body-custom { padding: 1.5rem; }

        /* Table */
        .table-custom { width: 100%; border-collapse: collapse; }
        .table-custom thead th {
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
        }
        .table-custom tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }
        .table-custom tbody tr:last-child { border-bottom: none; }
        .table-custom tbody tr:hover { background: #f8fafc; }
        .table-custom td { padding: 12px 16px; font-size: 0.9rem; vertical-align: middle; }
        .item-name { font-weight: 600; color: var(--text); }
        .item-price { font-weight: 700; color: var(--primary); }
        .item-desc { color: var(--text-muted); font-size: 0.85rem; }
        .badge-item {
            background: #dbeafe;
            color: var(--primary);
            border-radius: 6px;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Buttons */
        .btn-edit {
            background: #fef3c7; color: #92400e;
            border: 1px solid #fde68a;
            border-radius: 8px; padding: 5px 12px;
            font-size: 0.8rem; font-weight: 600;
            text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
            transition: all 0.15s;
        }
        .btn-edit:hover { background: #fde68a; color: #78350f; }
        .btn-delete {
            background: #fee2e2; color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 8px; padding: 5px 12px;
            font-size: 0.8rem; font-weight: 600;
            text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
            transition: all 0.15s;
        }
        .btn-delete:hover { background: #fecaca; color: #7f1d1d; }

        /* Form */
        .form-label-custom {
            font-weight: 600; font-size: 0.85rem;
            color: var(--text); margin-bottom: 6px; display: block;
        }
        .form-control-custom, .form-select-custom {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
            color: var(--text);
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0,180,216,0.15);
        }
        textarea.form-control-custom { resize: vertical; min-height: 80px; }
        .mb-form { margin-bottom: 1rem; }

        .btn-primary-custom {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-primary-custom:hover { background: var(--primary-light); transform: translateY(-1px); }

        .btn-success-custom {
            background: var(--success);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-success-custom:hover { background: #05b88a; transform: translateY(-1px); }

        /* Transaction card */
        .trx-card {
            background: linear-gradient(135deg, var(--primary) 0%, #1a6db5 100%);
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(15,76,129,0.25);
        }
        .trx-card .title {
            font-weight: 700; font-size: 1rem; margin-bottom: 1.25rem;
            display: flex; align-items: center; gap: 8px;
        }
        .trx-card select, .trx-card input[type="number"] {
            width: 100%; padding: 10px 14px;
            border: 1.5px solid rgba(255,255,255,0.3);
            border-radius: 10px;
            background: rgba(255,255,255,0.15);
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
            outline: none;
            backdrop-filter: blur(4px);
            transition: border-color 0.2s;
        }
        .trx-card select option { background: var(--primary); }
        .trx-card select:focus, .trx-card input:focus {
            border-color: var(--accent);
        }
        .trx-card label {
            font-weight: 600; font-size: 0.82rem;
            opacity: 0.85; margin-bottom: 6px; display: block;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-qris {
            width: 100%;
            background: var(--accent);
            color: var(--primary);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 800;
            font-size: 0.95rem;
            cursor: pointer;
            margin-top: 1rem;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s;
        }
        .btn-qris:hover { background: #00cfef; transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,180,216,0.4); }

        /* Layout grid */
        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1.5rem;
            align-items: start;
        }
        .layout-left { display: flex; flex-direction: column; gap: 1.5rem; }

        /* Stats bar */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex; align-items: center; gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .stat-label { font-size: 0.78rem; color: var(--text-muted); font-weight: 500; }
        .stat-value { font-size: 1.35rem; font-weight: 800; color: var(--text); }

        @media (max-width: 900px) {
            .layout-grid { grid-template-columns: 1fr; }
            .stats-bar { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .main-content { padding: 1rem; }
            .topbar { padding: 0 1rem; }
            .stats-bar { grid-template-columns: 1fr; }
        }

        .empty-state {
            text-align: center; padding: 2.5rem 1rem;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 2.5rem; opacity: 0.3; display: block; margin-bottom: 0.5rem; }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-brand">
        <div class="logo-icon"><i class="bi bi-qr-code-scan"></i></div>
        Kasir QRIS
    </div>
    <div class="topbar-nav">
        <a href="index.php" class="active"><i class="bi bi-grid-fill"></i> Dashboard</a>
        <a href="history.php"><i class="bi bi-clock-history"></i> Riwayat</a>
    </div>
</div>

<div class="main-content">

    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Kelola item dan proses transaksi QRIS dengan mudah</p>
    </div>

    <?php
    // Stats
    $total_items = $conn->query("SELECT COUNT(*) as c FROM items")->fetch_assoc()['c'];
    $total_trx = $conn->query("SELECT COUNT(*) as c FROM transactions")->fetch_assoc()['c'];
    $total_revenue = $conn->query("SELECT SUM(total_amount) as s FROM transactions")->fetch_assoc()['s'] ?? 0;
    ?>

    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;color:var(--primary)"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="stat-label">Total Item</div>
                <div class="stat-value"><?= $total_items ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-receipt"></i></div>
            <div>
                <div class="stat-label">Transaksi</div>
                <div class="stat-value"><?= $total_trx ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value" style="font-size:1.1rem">Rp <?= number_format($total_revenue, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <div class="layout-grid">
        <div class="layout-left">

            <!-- Daftar Item -->
            <div class="card">
                <div class="card-header-custom">
                    <div class="title">
                        <span class="icon-blue" style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-list-ul"></i></span>
                        Daftar Item
                    </div>
                    <span style="font-size:0.8rem;color:var(--text-muted)"><?= $total_items ?> item tersedia</span>
                </div>
                <div style="overflow-x:auto;">
                    <?php
                    $result = $conn->query("SELECT * FROM items");
                    if ($result->num_rows === 0):
                    ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        Belum ada item. Tambahkan item baru di bawah.
                    </div>
                    <?php else: ?>
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Nama Item</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
                                <th style="text-align:right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="item-name"><?= htmlspecialchars($row['name']) ?></span>
                                </td>
                                <td><span class="item-price">Rp <?= number_format($row['price'], 0, ',', '.') ?></span></td>
                                <td><span class="item-desc"><?= htmlspecialchars($row['description']) ?></span></td>
                                <td style="text-align:right">
                                    <div style="display:flex;gap:6px;justify-content:flex-end">
                                        <a href="edit_item.php?id=<?= $row['id'] ?>" class="btn-edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="delete_item.php?id=<?= $row['id'] ?>"
                                           class="btn-delete"
                                           onclick="return confirm('Yakin ingin menghapus item ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tambah Item -->
            <div class="card">
                <div class="card-header-custom">
                    <div class="title">
                        <span class="icon-green" style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-plus-lg"></i></span>
                        Tambah Item Baru
                    </div>
                </div>
                <div class="card-body-custom">
                    <form action="create_item.php" method="POST">
                        <div class="mb-form">
                            <label class="form-label-custom">Nama Item</label>
                            <input type="text" name="name" class="form-control-custom" placeholder="Contoh: Nasi Goreng" required>
                        </div>
                        <div class="mb-form">
                            <label class="form-label-custom">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control-custom" placeholder="Contoh: 25000" required>
                        </div>
                        <div class="mb-form">
                            <label class="form-label-custom">Deskripsi</label>
                            <textarea name="description" class="form-control-custom" placeholder="Deskripsi singkat item..."></textarea>
                        </div>
                        <button type="submit" class="btn-success-custom">
                            <i class="bi bi-plus-circle"></i> Tambah Item
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Sidebar: Transaksi -->
        <div>
            <div class="trx-card">
                <div class="title">
                    <i class="bi bi-qr-code-scan" style="font-size:1.2rem"></i>
                    Proses Transaksi QRIS
                </div>
                <form action="generate_qr.php" method="POST">
                    <div class="mb-form">
                        <label>Pilih Item</label>
                        <select name="item_id" required>
                            <?php
                            $items = $conn->query("SELECT * FROM items");
                            while ($item = $items->fetch_assoc()):
                            ?>
                            <option value="<?= $item['id'] ?>">
                                <?= htmlspecialchars($item['name']) ?> — Rp <?= number_format($item['price'], 0, ',', '.') ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-form">
                        <label>Jumlah</label>
                        <input type="number" name="quantity" min="1" value="1" required>
                    </div>
                    <button type="submit" class="btn-qris">
                        <i class="bi bi-qr-code"></i> Generate QR Code
                    </button>
                </form>
            </div>

            <div style="margin-top:1rem; background:white; border:1px solid var(--border); border-radius:14px; padding:1rem 1.25rem; text-align:center;">
                <a href="history.php" style="color:var(--primary);font-weight:600;text-decoration:none;font-size:0.9rem;display:flex;align-items:center;justify-content:center;gap:6px">
                    <i class="bi bi-clock-history"></i> Lihat Riwayat Transaksi
                </a>
            </div>
        </div>
    </div>

</div>

</body>
</html>