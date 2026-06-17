<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit;
}
require_once("../includes/db.php");
$msg = $err = "";

// Movies dropdown
$movies = mysqli_query($conn, "SELECT id, title FROM movies ORDER BY title");
// Theaters dropdown
$theaters = mysqli_query($conn, "SELECT id, name, city FROM theaters ORDER BY city, name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DEBUG: print submitted data
    // echo '<pre>'; print_r($_POST); echo '</pre>';

    $movie_id = isset($_POST['movie_id']) ? intval($_POST['movie_id']) : 0;
    $theater_id = isset($_POST['theater_id']) ? intval($_POST['theater_id']) : 0;
    $show_time = isset($_POST['show_time']) ? $_POST['show_time'] : '';
    $seats = isset($_POST['seats']) ? intval($_POST['seats']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

    // Datetime-local format conversion
    if ($show_time) {
        $show_time = date('Y-m-d H:i:s', strtotime($show_time));
    }

    if (!$movie_id || !$theater_id || !$show_time || !$seats || !$price) {
        $err = "Please fill all fields!";
    } else {
        $sql = "INSERT INTO shows (movie_id, theater_id, show_time, seats, price)
                VALUES ($movie_id, $theater_id, '$show_time', $seats, $price)";
        if (mysqli_query($conn, $sql)) {
            $msg = "Show added successfully!";
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
  <title>Add Show - Admin | CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
 >
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .add-show-panel {
      width:100%;max-width:470px;
      margin:2.5rem auto;background:#232323;
      border-radius:1.2rem;padding:2.2rem 2rem;
      box-shadow:0 5px 32px #0008;
    }
    .add-show-panel h2 {
      font-family:'Montserrat',sans-serif;
      color:#e50914;font-weight:900;
      margin-bottom:2rem;text-align:center;
    }
    label {font-weight:700;}
    .msg, .error {margin-bottom:.9rem;}
    @media (max-width:700px){.add-show-panel{max-width:99vw;padding:1.2rem .5rem 1rem .5rem;}}
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
            <a href="theaters.php" class="nav-link"><i class="bi bi-buildings"></i> Theaters</a>
            <a href="shows.php" class="nav-link"><i class="bi bi-clock"></i> Shows</a>
            <a href="movies-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Movie</a>
            <a href="theater-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Theater</a>
            <a href="show-add.php" class="nav-link "><i class="bi bi-plus-circle"></i> Add Show</a>
            <a href="../index.php" class="nav-link" target="_blank"><i class="bi bi-house"></i> View Website</a>
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a>
            
        </div>
  </div>
  <div class="main d-flex justify-content-center align-items-center" style="min-height:85vh;">
    <div class="add-show-panel">
      <h2>Add Show</h2>
      <?php if($msg): ?><div class="msg bg-success-subtle text-success-emphasis p-2 rounded mb-2"><?= $msg ?></div><?php endif; ?>
      <?php if($err): ?><div class="error bg-danger-subtle text-danger-emphasis p-2 rounded mb-2"><?= $err ?></div><?php endif; ?>
      <form method="POST" autocomplete="off">
        <div class="mb-3">
          <label>Select Movie</label>
          <select name="movie_id" class="form-select" required>
            <option value="">-- Select Movie --</option>
            <?php
            mysqli_data_seek($movies, 0);
            while($m = mysqli_fetch_assoc($movies)): ?>
              <option value="<?= $m['id'] ?>" <?= (isset($_POST['movie_id']) && $_POST['movie_id'] == $m['id']) ? "selected" : "" ?>>
                <?= htmlspecialchars($m['title']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Select Theater</label>
          <select name="theater_id" class="form-select" required>
            <option value="">-- Select Theater --</option>
            <?php
            mysqli_data_seek($theaters, 0);
            while($t = mysqli_fetch_assoc($theaters)): ?>
              <option value="<?= $t['id'] ?>" <?= (isset($_POST['theater_id']) && $_POST['theater_id'] == $t['id']) ? "selected" : "" ?>>
                <?= htmlspecialchars($t['name']) ?>, <?= htmlspecialchars($t['city']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Date & Time</label>
          <input type="datetime-local" name="show_time" class="form-control"
            value="<?= isset($_POST['show_time']) ? htmlspecialchars($_POST['show_time']) : '' ?>" required>
        </div>
        <div class="mb-3">
          <label>Total Seats</label>
          <input type="number" name="seats" class="form-control" min="1" max="500"
            value="<?= isset($_POST['seats']) ? htmlspecialchars($_POST['seats']) : '' ?>" required>
        </div>
        <div class="mb-3">
          <label>Ticket Price</label>
          <input type="number" name="price" class="form-control" min="0" step="1"
            value="<?= isset($_POST['price']) ? htmlspecialchars($_POST['price']) : '' ?>" required>
        </div>
        <div class="mb-2">
          <button type="submit" class="btn btn-main w-100 py-2"><i class="bi bi-plus-circle"></i> Add Show</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
