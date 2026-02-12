<?php
include 'db.php';

$id = $_GET['id'];
$sql = "SELECT * FROM items WHERE id = $id";
$result = $conn->query($sql);
$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        input {
            width: 100%;
        }
        textarea {
            width: 100%;
        
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-4">
        <div class="card mb-4">
            <div class="card-header bg-success text-center text-white">
                EDIT Item
            </div>
            <div class="card-body">
                <form action="update_item.php" method="POST">
                        <input class="form-control" type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama Item:</label><br>
                        <input type="text" name="name" value="<?php echo $item['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="price">Harga:</label><br>
                        <input type="number" name="price" value="<?php echo $item['price']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label><br>
                        <textarea name="description"><?php echo $item['description']; ?></textarea>
                    </div>


                    <button class="btn btn-primary" type="submit">Update Item</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>