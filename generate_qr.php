    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
include 'db.php'; // Database connection
include 'lib/phpqrcode/qrlib.php'; // QR Code library for image generation

function ConvertCRC16($str) {
    function charCodeAt($str, $i) {
        return ord(substr($str, $i, 1));
    }
    $crc = 0xFFFF;
    $strlen = strlen($str);
    for($c = 0; $c < $strlen; $c++) {
        $crc ^= charCodeAt($str, $c) << 8;
        for($i = 0; $i < 8; $i++) {
            if($crc & 0x8000) {
                $crc = ($crc << 1) ^ 0x1021;
            } else {
                $crc = $crc << 1;
            }
        }
    }
    $hex = $crc & 0xFFFF;
    $hex = strtoupper(dechex($hex));
    if (strlen($hex) == 3) $hex = "0".$hex;
    return $hex;
}

// Static QRIS data (your new QRIS string)
$qris_data = "00020101021226690021ID.CO.BANKMANDIRI.WWW01189360000801612980420211716129804220303UKE51440014ID.CO.QRIS.WWW0215ID10243355395720303UKE520427415303360540115802ID5915Rindu Alam Mart6014Kendari (Kota)61059311162070703A0163046E01";
$qris = $qris_data;

// Check if form data is submitted
if (isset($_POST['item_id']) && isset($_POST['quantity'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Validate input
    if (!is_numeric($item_id) || !is_numeric($quantity) || $quantity <= 0) {
        echo "Invalid input: item_id and quantity must be positive numbers.";
        exit;
    }

    // Retrieve item details from the database
    $sql = "SELECT * FROM items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Error: Item with ID $item_id not found in the database.";
        exit;
    }

    $item = $result->fetch_assoc();

    // Calculate total amount (price * quantity)
    $total_amount = $item['price'] * $quantity;
    $qty = $total_amount;

    // Now, modify the QRIS string correctly without altering the initial structure
    $qris = substr($qris, 0, -4);  // Remove last part for modification

    // Modify the QRIS string to include the correct total amount
    $step1 = str_replace("010211", "010212", $qris);  // Modify version if necessary
    $step2 = explode("5802ID", $step1);  // Split the string

    // Prepare the total amount (ensure it's in the correct format)
    $uang = "54" . sprintf("%02d", strlen($qty)) . $qty;

    // Append tax if exists (optional)
    if (empty($tax)) {
        $uang .= "5802ID";
    } else {
        $uang .= $tax . "5802ID";
    }

    // Final QRIS data preparation
    $final_qris = trim($step2[0]) . $uang . trim($step2[1]);
    $final_qris .= ConvertCRC16($final_qris);  // Add CRC16 checksum to the string

    // Insert transaction into the database
    $sql = "INSERT INTO transactions (item_id, quantity, total_amount, qris_data) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $item_id, $quantity, $total_amount, $final_qris);

    if ($stmt->execute()) {
        // Set dynamic file path for the QR code
        $file = "qrcodes/qrcode_" . $item_id . "_" . $quantity . ".png";  // Save QR code with unique name
        $directory = "qrcodes";  // Directory for storing QR codes

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);  // Create directory if it doesn't exist
        }

        // Generate the QR code image and save it to the file
        QRcode::png($final_qris, $file, QR_ECLEVEL_L, 5);

        // Display the generated QR code
        echo "<h2 style='text-align: center;'>          QR Code:</h2>";

        echo "<img style='  width: 400px; 
                            display: block;
                            margin-left: auto;
                            margin-right: auto;' 
                src='$file' alt='QRIS QR Code'>";

        echo "<p style='text-align: center;'>Silahkan Di Scan QR Code Untuk Pembayarannya.</p>";

        echo "<h2 style='text-align: center;'><b>Terima Kasih.</h2>";

        // echo $final_qris;  // Output the final QRIS data string
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Data item_id atau quantity tidak lengkap.";
}
?>

<a href="index.php" class="btn btn-secondary" style="display:block; width:150px; margin:20px auto; text-align:center;">Kembali</a>