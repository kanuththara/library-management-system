<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Delete entry by ID
    $sql = "DELETE FROM entries WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view.php");
        exit;
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No ID provided for deletion.";
}
$conn->close();
?>
