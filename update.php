<?php
include 'config.php';

$row = null; // Initialize row to prevent "undefined variable" error

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Fetch existing data
    $sql = "SELECT * FROM entries WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        echo "<div class='container mt-5 text-danger'>Record not found.</div>";
        exit;
    }
} else {
    echo "<div class='container mt-5 text-danger'>No ID specified.</div>";
    exit;
}

if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Basic validation
    if (empty($name) || empty($email) || empty($phone)) {
        die("All fields are required!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        die("Invalid phone number.");
    }

    // Update record
    $sql = "UPDATE entries SET name = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $phone, $id);

    if ($stmt->execute()) {
        header("Location: view.php");
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Entry</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Edit Entry</h2>

  <?php if ($row): ?>
    <form method="POST">
      <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" class="form-control">
      </div>
      <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($row['phone']); ?>" class="form-control">
      </div>
      <button type="submit" name="update" class="btn btn-success">Update</button>
      <a href="view.php" class="btn btn-secondary">Cancel</a>
    </form>
  <?php endif; ?>

</div>
</body>
</html>
