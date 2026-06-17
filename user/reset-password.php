<?php
require_once("../includes/db.php");
$err = $msg = "";
$email = isset($_GET['email']) ? strtolower(trim($_GET['email'])) : "";
if (!$email) {
    header("Location: forgot.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newpass = $_POST['password'];
    $confirmpass = $_POST['confirm_password'];

    if (!$newpass || !$confirmpass) {
        $err = "Both fields are required!";
    } elseif ($newpass !== $confirmpass) {
        $err = "Passwords do not match!";
    } else {
        // Update password (plain text, as per your project)
        $res = mysqli_query($conn, "UPDATE users SET password='$newpass' WHERE email='$email'");
        if ($res) {
            $msg = "Password reset successful! <a href='login.php'>Sign In Now</a>";
        } else {
            $err = "Database error! Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Reset Password - CineFlex</title>
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
        <h2>Reset Password</h2>
        <?php if($err): ?><div class="error"><?= $err ?></div><?php endif; ?>
        <?php if($msg): ?><div class="success" style="background:#083b12;color:#bbffbf;padding:10px 0;margin-bottom:1rem;border-radius:.4rem;"><?php echo $msg; ?></div><?php endif; ?>
        <?php if(!$msg): ?>
        <label>New Password</label>
        <input type="password" name="password" placeholder="Enter new password" required>
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="Confirm new password" required>
        <button type="submit">Reset Password</button>
        <?php endif; ?>
        <div class="form-link">
          Back to <a href="login.php">Sign In</a>
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
