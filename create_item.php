<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    $sql = "INSERT INTO items (name, price, description) VALUES ('$name', '$price', '$description')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Item berhasil ditambahkan.";
        header("Location: index.php");  // Kembali ke halaman daftar item
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
