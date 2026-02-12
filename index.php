<?php
include 'db.php';
$sql = "SELECT * FROM items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kasir - Daftar Item</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <h2 class="mb-4 text-center">Sistem Transaksi QR</h2>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Daftar Item
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Deskripsi</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['name']; ?></td>
                            <td>Rp <?= number_format($row['price']); ?></td>
                            <td><?= $row['description']; ?></td>
                            <td>
                                <a href="edit_item.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_item.php?id=<?= $row['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Yakin ingin menghapus item ini?')">
                                   Hapus
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            Tambah Item Baru
        </div>
        <div class="card-body">
            <form action="create_item.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Item</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-success"> Tambah Item</button>
            </form>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header bg-dark text-white">
            Mulai Transaksi
        </div>
        <div class="card-body">
            <form action="generate_qr.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Pilih Item</label>
                    <select name="item_id" class="form-select" required>
                        <?php
                        $items = $conn->query("SELECT * FROM items");
                        while ($item = $items->fetch_assoc()) {
                            echo "<option value='{$item['id']}'>
                                    {$item['name']} - Rp " . number_format($item['price']) . "
                                  </option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">💳 Proses Transaksi</button>
            </form>
        </div>
    </div>

</div>

</body>
</html>
