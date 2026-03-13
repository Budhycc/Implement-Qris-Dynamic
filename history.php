<?php
include 'db.php';
$sql = "SELECT * FROM transactions ORDER BY id DESC";
$result = $conn->query($sql);
$total = $conn->query("SELECT COUNT(*) as c, SUM(total_amount) as s FROM transactions")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi — Kasir QRIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0f4c81;
            --primary-light: #1a6db5;
            --accent: #00b4d8;
            --success: #06d6a0;
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
        .topbar {
            background: var(--primary);
            color: white;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(15,76,129,0.25);
        }
        .topbar-brand {
            display: flex; align-items: center; gap: 12px;
            font-weight: 800; font-size: 1.2rem;
        }
        .logo-icon {
            width: 36px; height: 36px;
            background: var(--accent); border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
        }
        .topbar-nav a {
            color: rgba(255,255,255,0.8);
            text-decoration: none; margin-left: 1.5rem;
            font-weight: 500; font-size: 0.9rem; transition: color 0.2s;
        }
        .topbar-nav a:hover, .topbar-nav a.active { color: white; }

        .main-content { padding: 2rem; max-width: 1100px; margin: 0 auto; }
        .page-header { margin-bottom: 1.75rem; }
        .page-header h1 { font-size: 1.7rem; font-weight: 800; color: var(--primary); margin: 0; }
        .page-header p { color: var(--text-muted); margin: 4px 0 0; font-size: 0.9rem; }

        .stats-bar {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;
        }
        .stat-card {
            background: white; border: 1px solid var(--border); border-radius: 14px;
            padding: 1rem 1.25rem; display: flex; align-items: center; gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }
        .stat-label { font-size: 0.78rem; color: var(--text-muted); font-weight: 500; }
        .stat-value { font-size: 1.35rem; font-weight: 800; color: var(--text); }

        .card {
            background: white; border: 1px solid var(--border);
            border-radius: 16px; box-shadow: var(--shadow); overflow: hidden;
        }
        .card-header-custom {
            padding: 1rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border);
        }
        .card-header-custom .title {
            font-weight: 700; font-size: 1rem;
            display: flex; align-items: center; gap: 8px;
        }

        .table-custom { width: 100%; border-collapse: collapse; }
        .table-custom thead th {
            background: #f8fafc;
            font-weight: 600; font-size: 0.78rem;
            text-transform: uppercase; letter-spacing: 0.5px;
            color: var(--text-muted); padding: 10px 16px;
            border-bottom: 1px solid var(--border);
        }
        .table-custom tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .table-custom tbody tr:last-child { border-bottom: none; }
        .table-custom tbody tr:hover { background: #f8fafc; }
        .table-custom td { padding: 12px 16px; font-size: 0.9rem; vertical-align: middle; }

        .trx-id {
            background: #f1f5f9; color: var(--text-muted);
            border-radius: 6px; padding: 2px 8px;
            font-size: 0.8rem; font-weight: 600; font-family: monospace;
        }
        .item-badge {
            background: #dbeafe; color: var(--primary);
            border-radius: 8px; padding: 4px 10px;
            font-size: 0.82rem; font-weight: 600;
        }
        .amount { font-weight: 700; color: #059669; }
        .qty-badge {
            background: #f0fdf4; color: #166534;
            border-radius: 6px; padding: 2px 8px;
            font-size: 0.82rem; font-weight: 600;
        }

        .qr-thumb {
            width: 56px; height: 56px; object-fit: contain;
            border-radius: 8px; border: 1px solid var(--border);
            cursor: pointer; transition: transform 0.2s;
        }
        .qr-thumb:hover { transform: scale(1.1); }

        .empty-state {
            text-align: center; padding: 3rem 1rem; color: var(--text-muted);
        }
        .empty-state i { font-size: 3rem; opacity: 0.25; display: block; margin-bottom: 0.75rem; }
        .empty-state a {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 1rem; background: var(--primary); color: white;
            border-radius: 10px; padding: 10px 20px; text-decoration: none;
            font-weight: 700; font-size: 0.9rem; transition: background 0.2s;
        }
        .empty-state a:hover { background: var(--primary-light); }

        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            background: white; border: 1.5px solid var(--border);
            color: var(--text); border-radius: 10px; padding: 8px 16px;
            text-decoration: none; font-weight: 600; font-size: 0.85rem;
            transition: all 0.15s;
        }
        .btn-back:hover { background: #f1f5f9; color: var(--primary); border-color: var(--primary); }

        /* Modal QR */
        .qr-modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.7); z-index: 999;
            align-items: center; justify-content: center;
        }
        .qr-modal-overlay.show { display: flex; }
        .qr-modal {
            background: white; border-radius: 20px; padding: 2rem;
            text-align: center; max-width: 340px; width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: pop 0.2s ease;
        }
        @keyframes pop { from { transform: scale(0.85); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .qr-modal img { width: 240px; height: 240px; object-fit: contain; border-radius: 12px; }
        .qr-modal h5 { font-weight: 700; margin-bottom: 4px; color: var(--primary); }
        .qr-modal p { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem; }
        .qr-modal-close {
            background: #f1f5f9; border: none; border-radius: 10px;
            padding: 8px 20px; font-weight: 600; cursor: pointer; margin-top: 1rem;
            color: var(--text); transition: background 0.15s;
        }
        .qr-modal-close:hover { background: #e2e8f0; }

        @media (max-width: 600px) {
            .main-content { padding: 1rem; }
            .topbar { padding: 0 1rem; }
            .stats-bar { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-brand">
        <div class="logo-icon"><i class="bi bi-qr-code-scan"></i></div>
        Kasir QRIS
    </div>
    <div class="topbar-nav">
        <a href="index.php"><i class="bi bi-grid-fill"></i> Dashboard</a>
        <a href="history.php" class="active"><i class="bi bi-clock-history"></i> Riwayat</a>
    </div>
</div>

<div class="main-content">

    <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem">
        <div>
            <h1>Riwayat Transaksi</h1>
            <p>Semua transaksi QRIS yang telah diproses</p>
        </div>
        <a href="index.php" class="btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon" style="background:#d1fae5;color:#059669"><i class="bi bi-receipt-cutoff"></i></div>
            <div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-value"><?= $total['c'] ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value" style="font-size:1.05rem">Rp <?= number_format($total['s'] ?? 0, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header-custom">
            <div class="title">
                <i class="bi bi-clock-history" style="color:var(--primary)"></i>
                Daftar Transaksi
            </div>
            <span style="font-size:0.8rem;color:var(--text-muted)"><?= $total['c'] ?> transaksi</span>
        </div>
        <div style="overflow-x:auto">
            <?php if ($result->num_rows === 0): ?>
            <div class="empty-state">
                <i class="bi bi-receipt"></i>
                <div>Belum ada transaksi yang tercatat.</div>
                <a href="index.php"><i class="bi bi-plus-circle"></i> Buat Transaksi</a>
            </div>
            <?php else: ?>
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th style="text-align:center">QR Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()):
                        $item_id = $row['item_id'];
                        $item_stmt = $conn->prepare("SELECT name FROM items WHERE id = ?");
                        $item_stmt->bind_param("i", $item_id);
                        $item_stmt->execute();
                        $item_result = $item_stmt->get_result();
                        $item = $item_result->fetch_assoc();
                    ?>
                    <tr>
                        <td><span class="trx-id">#<?= $row['id'] ?></span></td>
                        <td><span class="item-badge"><?= htmlspecialchars($item['name'] ?? '—') ?></span></td>
                        <td><span class="qty-badge">×<?= htmlspecialchars($row['quantity']) ?></span></td>
                        <td><span class="amount">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></span></td>
                        <td style="text-align:center">
                            <?php
                            $qr_file = "qrcodes/" . basename($row['qris_data']);
                            $qr_src = file_exists($qr_file) ? $qr_file : "uploads/" . basename($row['qris_data']);
                            ?>
                            <img class="qr-thumb"
                                 src="<?= htmlspecialchars($qr_src) ?>"
                                 alt="QR Code"
                                 onclick="showQR('<?= htmlspecialchars($qr_src) ?>', '<?= htmlspecialchars($item['name'] ?? '') ?>', 'Rp <?= number_format($row['total_amount'], 0, ',', '.') ?>')"
                                 title="Klik untuk perbesar">
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- QR Modal -->
<div class="qr-modal-overlay" id="qrModal" onclick="hideQR(event)">
    <div class="qr-modal">
        <h5 id="modalItemName">—</h5>
        <p id="modalAmount">—</p>
        <img id="modalQRImg" src="" alt="QR Code">
        <br>
        <button class="qr-modal-close" onclick="document.getElementById('qrModal').classList.remove('show')">
            Tutup
        </button>
    </div>
</div>

<script>
function showQR(src, name, amount) {
    document.getElementById('modalQRImg').src = src;
    document.getElementById('modalItemName').textContent = name;
    document.getElementById('modalAmount').textContent = amount;
    document.getElementById('qrModal').classList.add('show');
}
function hideQR(e) {
    if (e.target.id === 'qrModal') {
        document.getElementById('qrModal').classList.remove('show');
    }
}
</script>

</body>
</html>