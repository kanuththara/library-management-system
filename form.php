<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Step 1: Get values from form
  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];

  // Step 2: Basic Server-side Validation
  if (empty($name) || empty($email) || empty($phone)) {
    die("All fields are required!");
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format!");
  }

  if (!is_numeric($phone) || strlen($phone) != 10) {
    die("Phone number must be exactly 10 digits!");
  }

  // Step 3: Optional - Check for duplicate email
  $sql_check = "SELECT * FROM entries WHERE email='$email'";
  $result = $conn->query($sql_check);
  if ($result->num_rows > 0) {
    die("Email already exists!");
  }

  // Step 4: Insert data into table
  $sql = "INSERT INTO entries (name, email, phone) 
          VALUES ('$name', '$email', '$phone')";

  if ($conn->query($sql) === TRUE) {
    echo "<h3>✅ Entry added successfully!</h3>";
  } else {
    echo "❌ Error: " . $sql . "<br>" . $conn->error;
  }

  $conn->close();
}
?>
