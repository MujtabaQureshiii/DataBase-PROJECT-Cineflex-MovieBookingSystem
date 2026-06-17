<?php
require_once("../includes/db.php");
$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
    if (mysqli_num_rows($res)) {
        // In real app, yahan email pe link bhejte. Hum seedha reset pe bhej rahe hain
        header("Location: reset-password.php?email=" . urlencode($email));
        exit;
    } else {
        $err = "No user found with this email!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Forgot Password - CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
  <div class="bg-image"></div>
  <div class="overlay"></div>
  <div class="form-main">
    <div style="width:100%; text-align:center;">
      <div class="cineflex-title">CineFlex</div>
      <form class="form-card" method="POST" autocomplete="off" style="display:inline-block; margin-top:1rem;">
        <h2>Forgot Password</h2>
        <?php if($err): ?><div class="error"><?= $err ?></div><?php endif; ?>
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Continue</button>
        <div class="form-link">
          Remember your password? <a href="login.php">Sign In</a>
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
