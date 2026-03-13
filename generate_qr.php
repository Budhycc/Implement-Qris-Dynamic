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
        --text: #1a1a2e;
        --text-muted: #6b7280;
        --border: #e2e8f0;
        --shadow: 0 4px 24px rgba(15,76,129,0.10);
    }
    * { box-sizing: border-box; }
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--bg); color: var(--text); min-height: 100vh; margin: 0;
    }
    .topbar {
        background: var(--primary); color: white;
        padding: 0 2rem; display: flex; align-items: center; justify-content: space-between;
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
    .main-content { padding: 2rem; max-width: 560px; margin: 0 auto; }
</style>

<?php
include 'db.php';
include 'lib/phpqrcode/qrlib.php';

function ConvertCRC16($str) {
    function charCodeAt($str, $i) { return ord(substr($str, $i, 1)); }
    $crc = 0xFFFF;
    $strlen = strlen($str);
    for($c = 0; $c < $strlen; $c++) {
        $crc ^= charCodeAt($str, $c) << 8;
        for($i = 0; $i < 8; $i++) {
            if($crc & 0x8000) $crc = ($crc << 1) ^ 0x1021;
            else $crc = $crc << 1;
        }
    }
    $hex = $crc & 0xFFFF;
    $hex = strtoupper(dechex($hex));
    if (strlen($hex) == 3) $hex = "0".$hex;
    return $hex;
}

$qris_data = "00020101021126570011ID.DANA.WWW011893600915300082210502090008221050303UMI51440014ID.CO.QRIS.WWW0215ID10264852474270303UMI5204737253033605802ID5907Budhycc6012Kendari City6105931176304041A";
$qris = $qris_data;
?>

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

<?php
if (isset($_POST['item_id']) && isset($_POST['quantity'])):
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    if (!is_numeric($item_id) || !is_numeric($quantity) || $quantity <= 0) {
        echo '<div style="text-align:center;padding:2rem;color:#ef233c;font-weight:600">Input tidak valid.</div>';
        exit;
    }

    $sql = "SELECT * FROM items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo '<div style="text-align:center;padding:2rem;color:#ef233c;font-weight:600">Item tidak ditemukan.</div>';
        exit;
    }

    $item = $result->fetch_assoc();
    $total_amount = $item['price'] * $quantity;
    $qty = $total_amount;

    $qris = substr($qris, 0, -4);
    $step1 = str_replace("010211", "010212", $qris);
    $step2 = explode("5802ID", $step1);
    $uang = "54" . sprintf("%02d", strlen($qty)) . $qty;
    if (empty($tax)) $uang .= "5802ID";
    else $uang .= $tax . "5802ID";
    $final_qris = trim($step2[0]) . $uang . trim($step2[1]);
    $final_qris .= ConvertCRC16($final_qris);

    $sql = "INSERT INTO transactions (item_id, quantity, total_amount, qris_data) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $item_id, $quantity, $total_amount, $final_qris);

    if ($stmt->execute()):
        $file = "qrcodes/qrcode_" . $item_id . "_" . time() . ".png";
        $directory = "qrcodes";
        if (!file_exists($directory)) mkdir($directory, 0777, true);
        QRcode::png($final_qris, $file, QR_ECLEVEL_L, 5);
?>

<style>
.result-page { padding: 1.5rem 0; }
.success-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #d1fae5; color: #065f46;
    border-radius: 20px; padding: 6px 14px;
    font-weight: 700; font-size: 0.82rem; margin-bottom: 1.5rem;
}
.result-card {
    background: white; border: 1px solid var(--border);
    border-radius: 20px; box-shadow: var(--shadow); overflow: hidden;
}
.qr-container {
    background: linear-gradient(135deg, var(--primary) 0%, #1a6db5 100%);
    padding: 2rem; text-align: center;
}
.qr-frame {
    display: inline-block;
    background: white; border-radius: 16px;
    padding: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.25);
}
.qr-frame img { width: 220px; height: 220px; display: block; }
.qr-label {
    color: rgba(255,255,255,0.8); font-size: 0.85rem; margin-top: 1rem; font-weight: 500;
}
.info-section { padding: 1.5rem; }
.info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 0; border-bottom: 1px solid #f1f5f9;
}
.info-row:last-child { border-bottom: none; }
.info-label { font-size: 0.82rem; color: var(--text-muted); font-weight: 500; }
.info-value { font-weight: 700; color: var(--text); font-size: 0.92rem; }
.info-value.highlight { color: var(--primary); font-size: 1.05rem; }
.divider {
    height: 1px; background: var(--border); margin: 0 1.5rem;
}
.action-section {
    padding: 1.25rem 1.5rem; display: flex; gap: 10px; flex-wrap: wrap;
}
.btn-home {
    flex: 1; background: var(--primary); color: white; border: none;
    border-radius: 10px; padding: 12px 20px; font-weight: 700; font-size: 0.9rem;
    text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: background 0.2s; min-width: 140px;
}
.btn-home:hover { background: var(--primary-light); color: white; }
.btn-history {
    background: #f1f5f9; color: var(--text); border: 1.5px solid var(--border);
    border-radius: 10px; padding: 12px 20px; font-weight: 600; font-size: 0.9rem;
    text-decoration: none; display: flex; align-items: center; gap: 6px;
    transition: all 0.15s;
}
.btn-history:hover { background: #e2e8f0; color: var(--primary); border-color: var(--primary); }
.scan-hint {
    background: #fffbeb; border: 1px solid #fde68a;
    border-radius: 12px; padding: 12px 16px; margin: 0 1.5rem 1.25rem;
    font-size: 0.82rem; color: #92400e; font-weight: 500;
    display: flex; align-items: center; gap: 8px;
}
</style>

<div class="result-page">
    <div class="success-badge">
        <i class="bi bi-check-circle-fill"></i> Transaksi Berhasil
    </div>

    <div class="result-card">
        <div class="qr-container">
            <div class="qr-frame">
                <img src="<?= $file ?>" alt="QRIS QR Code">
            </div>
            <div class="qr-label"><i class="bi bi-qr-code"></i> QRIS Dinamis</div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Nama Item</span>
                <span class="info-value"><?= htmlspecialchars($item['name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Harga Satuan</span>
                <span class="info-value">Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Jumlah</span>
                <span class="info-value">×<?= htmlspecialchars($quantity) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Pembayaran</span>
                <span class="info-value highlight">Rp <?= number_format($total_amount, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="scan-hint">
            <i class="bi bi-phone" style="font-size:1.1rem"></i>
            Silakan scan QR Code di atas menggunakan aplikasi e-wallet atau mobile banking Anda.
        </div>

        <div class="action-section">
            <a href="index.php" class="btn-home">
                <i class="bi bi-house"></i> Kembali
            </a>
            <a href="history.php" class="btn-history">
                <i class="bi bi-clock-history"></i> Riwayat
            </a>
        </div>
    </div>
</div>

<?php
    else:
        echo '<div style="text-align:center;padding:2rem;color:#ef233c;font-weight:600"><i class="bi bi-exclamation-triangle"></i> Gagal menyimpan transaksi: ' . $conn->error . '</div>';
    endif;
else:
    echo '<div style="text-align:center;padding:2rem;color:#ef233c;font-weight:600"><i class="bi bi-exclamation-triangle"></i> Data item_id atau quantity tidak lengkap.</div>';
endif;
?>

</div>