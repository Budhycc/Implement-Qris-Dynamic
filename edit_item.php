<?php
include 'db.php';
$id = $_GET['id'];
$sql = "SELECT * FROM items WHERE id = $id";
$result = $conn->query($sql);
$item = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Item — Kasir QRIS</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0f4c81;
            --primary-light: #1a6db5;
            --accent: #00b4d8;
            --bg: #f0f4f8;
            --text: #1a1a2e;
            --text-muted: #6b7280;
            --border: #e2e8f0;
            --shadow: 0 4px 24px rgba(15,76,129,0.10);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg); color: var(--text); min-height: 100vh;
        }
        .topbar {
            background: var(--primary); color: white;
            padding: 0 2rem;
            display: flex; align-items: center; justify-content: space-between;
            height: 64px; position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 12px rgba(15,76,129,0.25);
        }
        .topbar-brand { display: flex; align-items: center; gap: 12px; font-weight: 800; font-size: 1.2rem; }
        .logo-icon {
            width: 36px; height: 36px; background: var(--accent);
            border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;
        }
        .topbar-nav a {
            color: rgba(255,255,255,0.8); text-decoration: none; margin-left: 1.5rem;
            font-weight: 500; font-size: 0.9rem; transition: color 0.2s;
        }
        .topbar-nav a:hover { color: white; }

        .main-content {
            padding: 2rem; max-width: 560px; margin: 0 auto;
        }
        .page-header { margin-bottom: 1.75rem; }
        .page-header h1 { font-size: 1.7rem; font-weight: 800; color: var(--primary); margin: 0; }
        .page-header p { color: var(--text-muted); margin: 4px 0 0; font-size: 0.9rem; }

        .card {
            background: white; border: 1px solid var(--border);
            border-radius: 16px; box-shadow: var(--shadow); overflow: hidden;
        }
        .card-header-custom {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, #1a6db5 100%);
            color: white;
        }
        .card-header-custom h2 {
            font-size: 1.05rem; font-weight: 700; margin: 0;
            display: flex; align-items: center; gap: 8px;
        }
        .card-header-custom p { font-size: 0.82rem; opacity: 0.8; margin: 4px 0 0; }
        .card-body-custom { padding: 1.75rem 1.5rem; }

        .form-label-custom {
            font-weight: 600; font-size: 0.85rem; color: var(--text);
            margin-bottom: 6px; display: block;
        }
        .form-control-custom {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid var(--border); border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.9rem;
            color: var(--text); background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; outline: none;
        }
        .form-control-custom:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0,180,216,0.15);
            background: white;
        }
        textarea.form-control-custom { resize: vertical; min-height: 90px; }
        .mb-form { margin-bottom: 1.1rem; }

        .price-wrapper { position: relative; }
        .price-prefix {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            font-weight: 700; color: var(--text-muted); font-size: 0.9rem; pointer-events: none;
        }
        .price-wrapper .form-control-custom { padding-left: 42px; }

        .actions {
            display: flex; gap: 10px; margin-top: 1.5rem; flex-wrap: wrap;
        }
        .btn-save {
            flex: 1; background: var(--primary); color: white; border: none;
            border-radius: 10px; padding: 12px 20px; font-weight: 700; font-size: 0.9rem;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: background 0.2s, transform 0.1s; min-width: 140px;
        }
        .btn-save:hover { background: var(--primary-light); transform: translateY(-1px); }
        .btn-cancel {
            background: #f1f5f9; color: var(--text); border: 1.5px solid var(--border);
            border-radius: 10px; padding: 12px 20px; font-weight: 600; font-size: 0.9rem;
            text-decoration: none; display: flex; align-items: center; gap: 6px;
            transition: all 0.15s;
        }
        .btn-cancel:hover { background: #e2e8f0; color: var(--primary); border-color: var(--primary); }

        @media (max-width: 600px) {
            .main-content { padding: 1rem; }
            .topbar { padding: 0 1rem; }
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
        <a href="history.php"><i class="bi bi-clock-history"></i> Riwayat</a>
    </div>
</div>

<div class="main-content">

    <div class="page-header">
        <h1>Edit Item</h1>
        <p>Perbarui informasi item yang ada</p>
    </div>

    <div class="card">
        <div class="card-header-custom">
            <h2><i class="bi bi-pencil-square"></i> <?= htmlspecialchars($item['name']) ?></h2>
            <p>ID Item: #<?= $item['id'] ?></p>
        </div>
        <div class="card-body-custom">
            <form action="update_item.php" method="POST">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">

                <div class="mb-form">
                    <label class="form-label-custom" for="name">
                        <i class="bi bi-tag" style="color:var(--accent)"></i> Nama Item
                    </label>
                    <input type="text" id="name" name="name"
                           class="form-control-custom"
                           value="<?= htmlspecialchars($item['name']) ?>" required>
                </div>

                <div class="mb-form">
                    <label class="form-label-custom" for="price">
                        <i class="bi bi-cash" style="color:var(--accent)"></i> Harga
                    </label>
                    <div class="price-wrapper">
                        <span class="price-prefix">Rp</span>
                        <input type="number" id="price" name="price"
                               class="form-control-custom"
                               value="<?= htmlspecialchars($item['price']) ?>" required>
                    </div>
                </div>

                <div class="mb-form">
                    <label class="form-label-custom" for="description">
                        <i class="bi bi-card-text" style="color:var(--accent)"></i> Deskripsi
                    </label>
                    <textarea id="description" name="description"
                              class="form-control-custom"
                              placeholder="Deskripsi singkat item..."><?= htmlspecialchars($item['description']) ?></textarea>
                </div>

                <div class="actions">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                    <a href="index.php" class="btn-cancel">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>

</body>
</html>