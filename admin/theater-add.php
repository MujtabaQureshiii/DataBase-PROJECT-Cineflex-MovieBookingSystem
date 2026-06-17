<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit;
}
require_once("../includes/db.php");
$msg = $err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    if(!$name || !$city || !$address) {
        $err = "Please fill all fields!";
    } else {
        $sql = "INSERT INTO theaters (name, city, address) VALUES ('$name', '$city', '$address')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Theater added successfully!";
        } else {
            $err = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Theater - Admin | CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .add-theater-panel {
      width:100%;max-width:450px;margin:2.5rem auto;background:#232323;
      border-radius:1.2rem;padding:2.2rem 2rem;box-shadow:0 5px 32px #0008;
    }
    .add-theater-panel h2 {font-family:'Montserrat',sans-serif;color:#e50914;font-weight:900;margin-bottom:2rem;text-align:center;}
    label {font-weight:700;}
    .msg, .error {margin-bottom:.9rem;}
    @media (max-width:700px){.add-theater-panel{max-width:99vw;padding:1.2rem .5rem 1rem .5rem;}}
  </style>
</head>
<body>
  <div class="sidebar d-none d-md-block">
    <div class="logo">CineFlex</div>
    <div class="nav flex-column">
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="movies.php" class="nav-link"><i class="bi bi-film"></i> View Movies</a>
            <a href="movies-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Movie</a>
            <a href="users.php" class="nav-link"><i class="bi bi-people"></i> Users</a>
            <a href="bookings.php" class="nav-link"><i class="bi bi-ticket-perforated"></i> Bookings</a>
            <a href="theaters.php" class="nav-link "><i class="bi bi-buildings"></i> Theaters</a>
            <a href="shows.php" class="nav-link"><i class="bi bi-clock"></i> Shows</a>
            <a href="movies-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Movie</a>
            <a href="theater-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Theater</a>
            <a href="show-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Show</a>
            <a href="../index.php" class="nav-link" target="_blank"><i class="bi bi-house"></i> View Website</a>
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a>
            
        </div>
  </div>
  <div class="main d-flex justify-content-center align-items-center" style="min-height:85vh;">
    <div class="add-theater-panel">
      <h2>Add Theater</h2>
      <?php if($msg): ?><div class="msg bg-success-subtle text-success-emphasis p-2 rounded mb-2"><?= $msg ?></div><?php endif; ?>
      <?php if($err): ?><div class="error bg-danger-subtle text-danger-emphasis p-2 rounded mb-2"><?= $err ?></div><?php endif; ?>
      <form method="POST" autocomplete="off">
        <div class="mb-3">
          <label>Theater Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>City</label>
          <input type="text" name="city" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Address</label>
          <textarea name="address" class="form-control" rows="2" required></textarea>
        </div>
        <div class="mb-2">
          <button type="submit" class="btn btn-main w-100 py-2"><i class="bi bi-plus-circle"></i> Add Theater</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
