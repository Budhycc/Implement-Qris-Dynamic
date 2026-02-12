<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $sql = "UPDATE items SET name='$name', price='$price', description='$description' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Item berhasil diperbarui.";
        header("Location: index.php");  // Kembali ke halaman daftar item
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
