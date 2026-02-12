<?php
include 'db.php';

$id = $_GET['id'];  // Get the item ID from the URL

// Start by checking if the ID is numeric and valid
if (!is_numeric($id) || $id <= 0) {
    echo "Invalid item ID.";
    exit;
}

// First, delete related transactions
$sql_delete_transactions = "DELETE FROM transactions WHERE item_id = $id";
if ($conn->query($sql_delete_transactions) === TRUE) {
    // Now proceed to delete the item
    $sql = "DELETE FROM items WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        echo "Item berhasil dihapus.";
        header("Location: index.php");  // Redirect to the items list page
    } else {
        echo "Error deleting item: " . $conn->error;
    }
} else {
    echo "Error deleting transactions: " . $conn->error;
}
?>
