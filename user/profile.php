<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once("../includes/db.php");
$user_id = $_SESSION['user_id'];

$msg = $err = "";
// Get user info
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    if(!$name) {
        $err = "Name cannot be empty!";
    } else {
        if($password) {
            $sql = "UPDATE users SET name='".mysqli_real_escape_string($conn, $name)."', password='".mysqli_real_escape_string($conn, $password)."' WHERE id=$user_id";
        } else {
            $sql = "UPDATE users SET name='".mysqli_real_escape_string($conn, $name)."' WHERE id=$user_id";
        }
        if(mysqli_query($conn, $sql)) {
            $_SESSION['user_name'] = $name;
            $msg = "Profile updated!";
        } else {
            $err = "Error updating profile!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile - CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {background:#181818;}
    .profile-panel {
      width:100%;max-width:430px;margin:2.8rem auto;background:#232323;
      border-radius:1.2rem;padding:2.2rem 2rem;box-shadow:0 5px 32px #0008;
    }
    .profile-panel h2 {
      font-family:'Montserrat',sans-serif;color:#e50914;font-weight:900;margin-bottom:1.2rem;text-align:center;
    }
    label {font-weight:700;}
    .msg, .error {margin-bottom:.9rem;}
    @media (max-width:700px){.profile-panel{max-width:99vw;padding:1.2rem .5rem 1rem .5rem;}}
  </style>
</head>
<body>
  <nav class="navbar navbar-dark" style="background:#181818;">
    <div class="container">
      <a class="navbar-brand fw-bold" style="font-family:'Montserrat',sans-serif;color:#e50914;font-size:2rem;" href="index.php">CineFlex</a>
      <a class="btn btn-outline-light" href="index.php"><i class="bi bi-house"></i> Home</a>
    </div>
  </nav>
  <div class="profile-panel">
    <h2>My Profile</h2>
    <?php if($msg): ?><div class="msg bg-success-subtle text-success-emphasis p-2 rounded mb-2"><?= $msg ?></div><?php endif; ?>
    <?php if($err): ?><div class="error bg-danger-subtle text-danger-emphasis p-2 rounded mb-2"><?= $err ?></div><?php endif; ?>
    <form method="POST" autocomplete="off">
      <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($user['name']) ?>">
      </div>
      <div class="mb-3">
        <label>Email (cannot change)</label>
        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
      </div>
      <div class="mb-3">
        <label>Change Password <span class="text-secondary">(leave blank to keep current)</span></label>
        <input type="password" name="password" class="form-control" placeholder="New password">
      </div>
      <div class="mb-2">
        <button type="submit" class="btn btn-main w-100 py-2"><i class="bi bi-person-lines-fill"></i> Update Profile</button>
      </div>
    </form>
  </div>
</body>
</html>
