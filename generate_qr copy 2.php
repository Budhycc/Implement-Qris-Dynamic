<?php
include 'db.php'; // Database connection
include 'lib/phpqrcode/qrlib.php'; // QR Code library for image generation

// Composer autoload for QR Code generation, not used here anymore
// require 'vendor/autoload.php'; 

// Pastikan ada data untuk item_id dan quantity
if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Ambil data item dari database
    $sql = "SELECT * FROM items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    // Menghitung total pembelian
    $total_amount = $item['price'] * $quantity;

    // Generate QRIS Dinamis dengan harga total belanjaan
    $qris_data = "00020101021226570011ID.DANA.WWW011893600915302259148102090225914810303UMI51440014ID.CO.QRIS.WWW0215ID10200176114730303UMI5204" . sprintf("%02d", strlen($total_amount)) . $total_amount . "5802ID591750000000610559567630458C7";

    // Simpan transaksi ke database
    $sql = "INSERT INTO transactions (item_id, quantity, total_amount, qris_data) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $item_id, $quantity, $total_amount, $qris_data);

    if ($stmt->execute()) {
        // Generate the QR code image and save it
        $file = "qrcode.png";  // Set the output file path
        QRcode::png($qris_data, $file, QR_ECLEVEL_L, 5);

        // Tampilkan gambar
        echo "<h3>QR Code:</h3>";
        echo "<img src='$file' alt='QRIS QR Code'>";
        echo "<p>Transaksi berhasil dan QRIS QR Code telah dihasilkan.</p>";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Data item_id atau quantity tidak lengkap.";
}
?>
