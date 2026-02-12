<?php
include 'db.php';
require 'vendor/autoload.php'; // Composer autoload for QR Code generation

use chillerlan\QRCode\QRCode;

// Pastikan ada data untuk item_id dan quantity
if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Ambil data item dari database
    $sql = "SELECT * FROM items WHERE id = $item_id";
    $result = $conn->query($sql);
    $item = $result->fetch_assoc();

    // Menghitung total pembelian
    $total_amount = $item['price'] * $quantity;

    // Generate QRIS Dinamis dengan harga total belanjaan
    $qris_data = "00020101021226570011ID.DANA.WWW011893600915302259148102090225914810303UMI51440014ID.CO.QRIS.WWW0215ID10200176114730303UMI5204" . sprintf("%02d", strlen($total_amount)) . $total_amount . "5802ID591750000000610559567630458C7";

    // Generate QR Code dari QRIS Data
    $qrcode = new QRCode();
    $file = 'uploads/transaction_qr.png';
    $qrcode->render($qris_data, $file);

    // Simpan transaksi ke database
    $sql = "INSERT INTO transactions (item_id, quantity, total_amount, qris_data) VALUES ($item_id, $quantity, $total_amount, '$qris_data')";
    if ($conn->query($sql) === TRUE) {
        echo "Transaksi berhasil dan QRIS QR Code telah dihasilkan.";
        echo "<img src='" . $file . "' alt='QRIS QR Code' />";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Data item_id atau quantity tidak lengkap.";
}
?>
