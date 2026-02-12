<?php
include 'db.php';

// Ambil semua transaksi dari database
$sql = "SELECT * FROM transactions";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
</head>
<body>
    <h2>Riwayat Transaksi</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>QRIS</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <?php
                    // Ambil nama item dari transaksi
                    $item_id = $row['item_id'];
                    $item_sql = "SELECT name FROM items WHERE id = $item_id";
                    $item_result = $conn->query($item_sql);
                    $item = $item_result->fetch_assoc();
                    echo $item['name'];
                    ?>
                </td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['total_amount']; ?></td>
                <td><img src="uploads/<?php echo basename($row['qris_data']); ?>" alt="QRIS"></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
