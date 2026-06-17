<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit;
}
require_once("../includes/db.php");

// Handle delete
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    // (Optional: Poster/Trailer delete logic here)
    mysqli_query($conn, "DELETE FROM movies WHERE id=$del_id");
    header("Location: movies.php?msg=deleted");
    exit;
}

// Search/filter (bonus)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = $search ? "WHERE title LIKE '%$search%' OR genre LIKE '%$search%'" : "";
$result = mysqli_query($conn, "SELECT * FROM movies $where ORDER BY id DESC");

$msg = isset($_GET['msg']) && $_GET['msg']=='deleted' ? "Movie deleted successfully!" : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Movies - Admin | CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {background:#181818;color:#fff;font-family:'Roboto',sans-serif;}
    .main {margin-left:220px;padding:2.3rem 2.7rem 1rem 2.7rem;}
    .table-wrap {background:#232323;border-radius:1.2rem;box-shadow:0 3px 18px #0007;padding:2.2rem 1.5rem;}
    .table {color:#fff;background:transparent;}
    .table th, .table td {vertical-align:middle;}
    .table th {background:#1f1f1f;color:#e50914;}
    .table tbody tr {background:rgba(255,255,255,0.01);}
    .poster-img {width:68px;height:90px;object-fit:cover;border-radius:.4rem;box-shadow:0 2px 8px #0006;}
    .flag {display:inline-block;background:#e50914;color:#fff;padding:.16rem .65rem;border-radius:.7rem;font-size:.93rem;margin-right:.2rem;}
    .flag.n {background:#0288d1;}
    .flag.t {background:#43a047;}
    .btn-act {padding:.3rem .7rem;border:none;border-radius:.4rem;font-size:1rem;}
    .btn-edit {background:#fff;color:#e50914;font-weight:700;}
    .btn-edit:hover {background:#e50914;color:#fff;}
    .btn-del {background:#e50914;color:#fff;font-weight:700;}
    .btn-del:hover {background:#b0060f;}
    .topbar {display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;}
    .topbar .btn-main {background:#e50914;color:#fff;border:none;border-radius:.4rem;padding:.53rem 1.6rem;font-weight:700;}
    .topbar .btn-main:hover {background:#b0060f;}
    .search-box {border-radius:2rem;padding:.5rem 1.2rem;border:none;outline:none;font-size:1.07rem;width:260px;}
    @media (max-width:1100px){.main{padding:1.1rem .5rem;margin-left:0;}}
    @media (max-width:700px){.table-wrap{padding:1rem .2rem;}.topbar{flex-direction:column;gap:1.2rem;}.search-box{width:100%;}}
    .msg {background:#1a4018;color:#bbffbf;padding:.8rem 0;border-radius:.5rem;margin-bottom:.7rem;text-align:center;}
  </style>
</head>
<body>
  <div class="sidebar d-none d-md-block" style="width:220px;background:#181818;min-height:100vh;position:fixed;left:0;top:0;z-index:5;box-shadow:2px 0 22px #0003;">
    <div class="logo" style="font-family:'Montserrat',sans-serif;font-size:2rem;font-weight:900;color:#e50914;padding:2.1rem 0 1.3rem 1.5rem;">CineFlex</div>
    <div class="nav flex-column">
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="movies.php" class="nav-link "><i class="bi bi-film"></i> View Movies</a>
            <a href="movies-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Movie</a>
            <a href="users.php" class="nav-link"><i class="bi bi-people"></i> Users</a>
            <a href="bookings.php" class="nav-link"><i class="bi bi-ticket-perforated"></i> Bookings</a>
            <a href="theaters.php" class="nav-link"><i class="bi bi-buildings"></i> Theaters</a>
            <a href="shows.php" class="nav-link"><i class="bi bi-clock"></i> Shows</a>
            <a href="movies-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Movie</a>
            <a href="theater-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Theater</a>
            <a href="show-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Show</a>
            <a href="../index.php" class="nav-link" target="_blank"><i class="bi bi-house"></i> View Website</a>
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a>
            
        </div>
  </div>
  <div class="main">
    <div class="topbar">
      <div>
        <a href="movies-add.php" class="btn-main"><i class="bi bi-plus-circle"></i> Add Movie</a>
      </div>
      <form method="get" class="d-flex">
        <input type="text" class="search-box me-2" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search movies/genre...">
        <button class="btn-main" type="submit"><i class="bi bi-search"></i></button>
      </form>
    </div>
    <?php if($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
    <div class="table-wrap">
      <div style="overflow-x:auto;">
      <table class="table table-borderless align-middle">
        <thead>
          <tr>
            <th>Poster</th>
            <th>Title</th>
            <th>Genre</th>
            <th>Flags</th>
            <th>Rating</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($result)): while($m = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td>
              <?php if($m['poster']): ?>
                <img src="../<?= htmlspecialchars($m['poster']) ?>" class="poster-img">
              <?php else: ?>
                <span style="color:#ccc;">No Poster</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($m['title']) ?></td>
            <td><?= htmlspecialchars($m['genre']) ?></td>
            <td>
              <?php if($m['is_trending']): ?><span class="flag">Trending</span><?php endif; ?>
              <?php if($m['is_nowshowing']): ?><span class="flag n">Now Showing</span><?php endif; ?>
              <?php if($m['is_toprated']): ?><span class="flag t">Top Rated</span><?php endif; ?>
            </td>
            <td><?= floatval($m['rating']) ?></td>
            <td>
              <a href="movies-edit.php?id=<?= $m['id'] ?>" class="btn-act btn-edit" title="Edit"><i class="bi bi-pencil"></i></a>
              <a href="movies.php?delete=<?= $m['id'] ?>" onclick="return confirm('Delete this movie?');" class="btn-act btn-del" title="Delete"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="6" style="color:#ccc;text-align:center;">No movies found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</body>
</html>

