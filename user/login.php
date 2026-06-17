<?php
require_once("../includes/db.php");
session_start();
$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $pass = $_POST['password'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
    $user = mysqli_fetch_assoc($res);
    if ($user && $user['password'] === $pass) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        if($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $err = "Invalid email or password!";
    }
}
?>

  

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Sign In - CineFlex</title>
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
        <h2>Sign In</h2>
        <?php if($err): ?><div class="error"><?= $err ?></div><?php endif; ?>
        <label>Email</label>
        <input type="email" name="email" placeholder="Email or mobile number" required>
        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
        <div class="forgot-link"><a href="forgot.php">Forgot password?</a></div>
        <div class="form-link">
          New to CineFlex? <a href="register.php">Sign up now.</a>
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
