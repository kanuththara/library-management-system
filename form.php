<?php 
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Validate phone number (only digits and exactly 10 digits)
    if (!preg_match("/^\d{10}$/", $phone)) {
        header("Location: form.php?status=invalid_phone");
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: form.php?status=invalid_email");
        exit();
    }

    // Check if email already exists
    $checkEmail = "SELECT * FROM entries WHERE email = '$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        header("Location: form.php?status=email_exists");
    } else {
        // Insert new entry
        $sql = "INSERT INTO entries (name, email, phone) VALUES ('$name', '$email', '$phone')";
        if ($conn->query($sql) === TRUE) {
            header("Location: form.php?status=success");
        } else {
            header("Location: form.php?status=error");
        }
    }

    if (isset($_GET['success'])): ?>
  <div class="alert alert-success" role="alert">
    Entry submitted successfully!
  </div>
<?php endif; 


    $conn->close();
    exit();
}
?>

<!DOCTYPE html> 
<html lang="en"> 
<head> 
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <title>Form - Library System Management</title> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Apply ASH background to whole page */
    body {
      background-color: lab(21.92% -8.17 -15.43 / 0.926); 
    }

    .form-container {
      background-color: #e6f7ffd3; /* Light blue for the form */
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgb(0, 0, 0);
    }

    .form-control.bg-secondary {
      color: white;
    }
    .btn-secondary{
      background-color:#1a2e40;
      color: #000;
    }
  </style>
</head> 

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark"> 
  <div class="container-fluid"> 
    <a class="navbar-brand" href="#">Library System Management</a> 

    <!-- 🔁 Toggler for mobile screens -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- 🔁 Collapsible navigation links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto"> 
        <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li> 
        <li class="nav-item"><a class="nav-link" href="form.php">Form</a></li> 
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li> 
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li> 
      </ul> 
    </div>
  </div> 
</nav>


  <!-- Form Section -->
  
  <div class="container-fluid mt-5">
    <div class="row justify-content-center">
  <    <div class="col-md-6 col-lg-5 form-container">
  <h2 class="mb-4">Library Entry Form</h2> 
  <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
  <div class="alert alert-success" role="alert"> 
    Entry submitted successfully! 
  </div>
  <?php endif; ?>

  <form method="POST" action="form.php?status=success" onsubmit="return validateForm()"> 
  <div class="mb-3"> 
    <label class="form-label">Name</label> 
    <input type="text" id="name" name="name" class="form-control" placeholder="Enter name" oninput="this.value = 
    this.value.toUpperCase()"> 
  </div> 
  <div class="mb-3"> 
    <label class="form-label">Email</label> 
    <input type="email" id="email" name="email" class="form-control  text-black" placeholder="Enter Email"> 
  </div> 
  <div class="mb-3"> 
    <label class="form-label">Phone</label>
    <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter Phone" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
  </div> 
  <button type="submit" class="btn btn-secondary">Submit</button> 
  <button type="reset" class="btn btn-secondary">Clear Form</button> 
  </form> 
        </div> <!-- col -->
     </div> <!-- row -->
   </div> <!-- container-fluid -->

  
  <script >
    
    document.getElementById("name").addEventListener("input", function () {
    let name = this.value;
    if (name.length < 3) {
      this.style.borderColor = "red";
    } else {
      this.style.borderColor = "green";
    }
  });

  // Final validation on form submit
  function validateForm() {
    const name = document.getElementById("name").value;
    if (name.length < 3) {
      alert("Name must be at least 3 characters long.");
      return false; // prevent form submission
    }
    return true; // allow submission
  }

  function validateForm() {
    let name = document.getElementById("name").value; 
    let email = document.getElementById("email").value; 
    let phone = document.getElementById("phone").value; 
    
    if (name === "" || email === "" || phone === "") { 
      alert("All fields are required!"); 
      return false; 
    } 

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert("Invalid email format!");
      return false;
    }

    if (phone.length !== 10 || isNaN(phone)) { 
      alert("Phone number must be exactly 10 digits!"); 
      return false; 
    }

    return true; // allow form submission
  }

  // Alert based on PHP redirect status
  window.onload = function() {
    const params = new URLSearchParams(window.location.search);
    const status = params.get("status");

    if (status === "success") {
      alert("Entry submitted successfully! ");
    } else if (status === "email_exists") {
      alert("Error: This email is already used.");
    } else if (status === "invalid_phone") {
      alert("Error: Phone number must be exactly 10 digits.");
    } else if (status === "invalid_email") {
      alert("Error: Invalid email format.");
    } else if (status === "error") {
      alert("Something went wrong. Please try again.");
    }
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</html>