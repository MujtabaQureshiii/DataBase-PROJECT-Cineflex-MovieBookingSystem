<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit;
}
require_once("../includes/db.php");

// Delete Show
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM shows WHERE id=$del_id");
    header("Location: shows.php?msg=deleted");
    exit;
}
$msg = isset($_GET['msg']) && $_GET['msg']=='deleted' ? "Show deleted successfully!" : "";

// Fetch all shows with movie/theater info
$query = "SELECT s.*, m.title as mtitle, t.name as tname, t.city 
          FROM shows s 
          LEFT JOIN movies m ON s.movie_id = m.id 
          LEFT JOIN theaters t ON s.theater_id = t.id 
          ORDER BY s.show_time DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shows - Admin | CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .main {margin-left:220px;padding:2.3rem 2.7rem 1rem 2.7rem;}
    .table-wrap {background:#232323;border-radius:1.2rem;box-shadow:0 3px 18px #0007;padding:2.2rem 1.5rem;margin:2rem auto;max-width:1100px;}
    .table th {background:#1f1f1f;color:#e50914;}
    .table tbody tr:hover {background:#22252b;}
    .btn-edit, .btn-del {
      padding:.33rem .7rem;border-radius:.4rem;font-size:1rem;
      border:none;box-shadow:0 2px 10px #e5091430;transition:.14s;margin-right:.2rem;
    }
    .btn-edit {background:#fff;color:#e50914;font-weight:700;}
    .btn-edit:hover {background:#e50914;color:#fff;}
    .btn-del {background:#e50914;color:#fff;}
    .btn-del:hover {background:#b0060f;}
    .msg {background:#1a4018;color:#bbffbf;padding:.8rem 0;border-radius:.5rem;margin-bottom:.7rem;text-align:center;}
    .topbar {display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;}
    .btn-main {background:#e50914;color:#fff;border:none;border-radius:.44rem;padding:.5rem 1.2rem;font-weight:800;}
    .btn-main:hover {background:#b0060f;}
    @media (max-width:1100px){.main{padding:1.1rem .5rem;margin-left:0;}.table-wrap{padding:1.1rem .2rem;}}
    @media (max-width:700px){.table-wrap{padding:1rem .1rem;}.topbar{flex-direction:column;gap:1.2rem;}}
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
            <a href="shows.php" class="nav-link "><i class="bi bi-clock"></i> Shows</a>
            <a href="movies-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Movie</a>
            <a href="theater-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Theater</a>
            <a href="show-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Show</a>
            <a href="../index.php" class="nav-link" target="_blank"><i class="bi bi-house"></i> View Website</a>
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a>
            
        </div>
  </div>
  <div class="main">
    <div class="topbar">
      <h3 style="font-family:'Montserrat',sans-serif;font-weight:800;color:#e50914;">Shows</h3>
      <a href="show-add.php" class="btn-main"><i class="bi bi-plus-circle"></i> Add Show</a>
    </div>
    <?php if($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
    <div class="table-wrap">
      <div style="overflow-x:auto;">
      <table class="table table-borderless align-middle">
        <thead>
          <tr>
            <th>Movie</th>
            <th>Theater</th>
            <th>City</th>
            <th>Date/Time</th>
            <th>Seats</th>
            <th>Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($result)): while($s = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= htmlspecialchars($s['mtitle']) ?></td>
            <td><?= htmlspecialchars($s['tname']) ?></td>
            <td><?= htmlspecialchars($s['city']) ?></td>
            <td><?= date('d M Y, h:i A', strtotime($s['show_time'])) ?></td>
            <td><?= htmlspecialchars($s['seats']) ?></td>
            <td><?= number_format($s['price'],0) ?></td>
            <td>
              <a href="show-edit.php?id=<?= $s['id'] ?>" class="btn-edit" title="Edit"><i class="bi bi-pencil"></i></a>
              <a href="shows.php?delete=<?= $s['id'] ?>" onclick="return confirm('Delete this show?');" class="btn-del" title="Delete"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="7" style="color:#ccc;text-align:center;">No shows found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</body>
</html>
