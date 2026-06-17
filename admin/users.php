<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit;
}
require_once("../includes/db.php");

// Handle delete user (optional, safety: do not allow self delete or admin delete)
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$del_id"));
    if($user && $user['role'] != 'admin' && $del_id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id=$del_id");
        header("Location: users.php?msg=deleted");
        exit;
    }
}

// Search/filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = $search ? "WHERE name LIKE '%$search%' OR email LIKE '%$search%'" : "";
$result = mysqli_query($conn, "SELECT * FROM users $where ORDER BY reg_date DESC");

$msg = isset($_GET['msg']) && $_GET['msg']=='deleted' ? "User deleted successfully!" : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Users - Admin | CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .main {margin-left:220px;padding:2.3rem 2.7rem 1rem 2.7rem;}
    .table-wrap {
      background:#232323;border-radius:1.2rem;box-shadow:0 3px 18px #0007;padding:2.2rem 1.5rem;
      margin:2rem auto;max-width:1100px;
    }
    .table th {background:#1f1f1f;color:#e50914;}
    .table tbody tr:hover {background:#22252b;}
    .role-tag {
      display:inline-block;
      padding:.12rem .9rem;
      border-radius:.8rem;
      font-weight:700;
      background:#e50914;color:#fff;
      font-size:.98rem;
      letter-spacing:.3px;
    }
    .role-tag.admin {background:#2d6a4f;}
    .role-tag.user {background:#e50914;}
    .btn-del {
      padding:.3rem .7rem;
      border:none;
      border-radius:.4rem;
      font-size:1rem;
      background:#e50914;
      color:#fff;
      font-weight:700;
      box-shadow:0 2px 10px #e5091430;
      transition:.14s;
    }
    .btn-del:hover {background:#b0060f;}
    .search-box {border-radius:2rem;padding:.5rem 1.2rem;border:none;outline:none;font-size:1.07rem;width:260px;}
    .topbar {display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;}
    @media (max-width:1100px){.main{padding:1.1rem .5rem;margin-left:0;}.table-wrap{padding:1.1rem .2rem;}}
    @media (max-width:700px){.table-wrap{padding:1rem .1rem;}.topbar{flex-direction:column;gap:1.2rem;}.search-box{width:100%;}}
    .msg {background:#1a4018;color:#bbffbf;padding:.8rem 0;border-radius:.5rem;margin-bottom:.7rem;text-align:center;}
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
            <a href="show-add.php" class="nav-link"><i class="bi bi-plus-circle"></i> Add Show</a>
            <a href="../index.php" class="nav-link" target="_blank"><i class="bi bi-house"></i> View Website</a>
            <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a>
            
        </div>
  </div>
  <div class="main">
    <div class="topbar">
      <div>
        <h3 style="font-family:'Montserrat',sans-serif;font-weight:800;color:#e50914;">Registered Users</h3>
      </div>
      <form method="get" class="d-flex">
        <input type="text" class="search-box me-2" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search user/email...">
        <button class="btn-main" type="submit"><i class="bi bi-search"></i></button>
      </form>
    </div>
    <?php if($msg): ?><div class="msg"><?= $msg ?></div><?php endif; ?>
    <div class="table-wrap">
      <div style="overflow-x:auto;">
        <table class="table table-borderless align-middle">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Registered</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($result)): while($u = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= htmlspecialchars($u['phone']) ?></td>
              <td>
                <span class="role-tag <?= $u['role'] == 'admin' ? 'admin' : 'user' ?>">
                  <?= ucfirst($u['role']) ?>
                </span>
              </td>
              <td><?= date('d M Y', strtotime($u['reg_date'])) ?></td>
              <td>
                <?php if($u['role'] != 'admin' && $u['id'] != $_SESSION['user_id']): ?>
                <a href="users.php?delete=<?= $u['id'] ?>" onclick="return confirm('Delete this user?');" class="btn-del" title="Delete"><i class="bi bi-trash"></i></a>
                <?php else: ?>
                  <span style="color:#999;">--</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
              <td colspan="6" style="color:#ccc;text-align:center;">No users found.</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>

