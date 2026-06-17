<?php
require_once("../includes/db.php");
$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $pass = $_POST['password'];
    $phone = trim($_POST['phone']);

    if (!$name || !$email || !$pass) {
        $err = "All fields are required!";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($check)) {
            $err = "Email already registered!";
        } else {
            $sql = "INSERT INTO users (name, email, password, phone) VALUES ('$name','$email','$pass','$phone')";
            if (!mysqli_query($conn, $sql)) {
                $err = "MySQL Error: " . mysqli_error($conn);
            } else {
                header("Location: login.php?register=success");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Sign Up - CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="assets/css/style.css" rel="stylesheet">                         
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
    <style>
    .text-decoration-none {
      text-decoration: none !important;
    }
    .text-red {
    color: red !important;
    }
  </style>
</head>
<body>
  <div class="bg-image"></div>
  <div class="overlay"></div>
  <div class="form-main">
    <div style="width:100%; text-align:center;">
      <div class="cineflex-title"><a href="index.php" class="text-red text-decoration-none">CineFlex</a></div>
      <form class="form-card" method="POST" autocomplete="off" style="display:inline-block; margin-top:1rem;">
        <h2>Sign Up</h2>
        <?php if($err): ?><div class="error"><?= $err ?></div><?php endif; ?>
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Full Name" required>
        <label>Email</label>
        <input type="email" name="email" placeholder="Email" required>
        <label>Phone</label>
        <input type="text" name="phone" placeholder="Phone">
        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign Up</button>
        <div class="form-link">
          Already have an account? <a href="login.php">Sign In</a>
        </div>
      </form>
    </div>
  </div>
  <div class="footer">
    <div class="footer-links">
      <a href="#">FAQ</a>
      <a href="#">Help Center</a>
      <a href="#">Terms of Use</a>
      <a href="#">Privacy</a>
      <a href="#">Contact</a>
      <a href="#">Cookie Preferences</a>
    </div>
    <div style="text-align:center;">
      CineFlex &copy; 2025. All Rights Reserved.
    </div>
  </div>
</body>
</html>
