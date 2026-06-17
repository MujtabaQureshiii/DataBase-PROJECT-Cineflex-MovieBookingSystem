<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once("../includes/db.php");
$user_id = $_SESSION['user_id'];

// Fetch all bookings for this user (with show, movie, theater info)
$res = mysqli_query($conn, "
  SELECT 
    b.*, 
    s.show_time, 
    m.title AS movie_title, 
    t.name AS theater_name, 
    t.city 
  FROM 
    bookings b
    LEFT JOIN shows s ON b.show_id = s.id
    LEFT JOIN movies m ON s.movie_id = m.id
    LEFT JOIN theaters t ON s.theater_id = t.id
  WHERE b.user_id = $user_id
  ORDER BY b.booking_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings - CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {background:#181818;}
    .mybook-wrap {max-width:1000px;margin:2.2rem auto;padding:2.2rem 1.7rem 1.2rem 1.7rem;background:#232323;border-radius:1.2rem;box-shadow:0 5px 32px #0008;}
    h2 {font-family:'Montserrat',sans-serif;font-size:2rem;font-weight:900;color:#e50914;margin-bottom:2.2rem;}
    .table th {background:#1a1a1a;color:#e50914;}
    .table tbody tr:hover {background:#202428;}
    .status-badge {border-radius:.5rem;padding:.22rem 1rem;font-weight:700;}
    .status-badge.confirmed {background:#1a4018;color:#8dff93;}
    .status-badge.cancelled {background:#2a1918;color:#ffb5b5;}
    .empty-msg {color:#bbb;text-align:center;padding:3rem 1rem;font-size:1.3rem;}
    @media (max-width:900px){.mybook-wrap{max-width:99vw;padding:1.1rem .3rem 1rem .3rem;}}
  </style>
</head>
<body>
  <nav class="navbar navbar-dark" style="background:#181818;">
    <div class="container">
      <a class="navbar-brand fw-bold" style="font-family:'Montserrat',sans-serif;color:#e50914;font-size:2rem;" href="index.php">CineFlex</a>
      <a class="btn btn-outline-light" href="index.php"><i class="bi bi-house"></i> Home</a>
    </div>
  </nav>
  <div class="mybook-wrap">
    <h2>My Bookings</h2>
    <div style="overflow-x:auto;">
    <table class="table table-borderless align-middle">
      <thead>
        <tr>
          <th>Movie</th>
          <th>Theater</th>
          <th>Date/Time</th>
          <th>Seats</th>
          <th>Status</th>
          <th>Booked On</th>
        </tr>
      </thead>
      <tbody>
      <?php if(mysqli_num_rows($res)): while($b = mysqli_fetch_assoc($res)): ?>
        <tr>
          <td><?= $b['movie_title'] ? htmlspecialchars($b['movie_title']) : "<span style='color:#bbb;'>Invalid movie</span>" ?></td>
          <td><?= $b['theater_name'] ? htmlspecialchars($b['theater_name']) : "<span style='color:#bbb;'>Invalid theater</span>" ?><?= $b['city'] ? " (".htmlspecialchars($b['city']).")" : "" ?></td>
          <td><?= $b['show_time'] ? date('d M Y, h:i A', strtotime($b['show_time'])) : "<span style='color:#bbb;'>Invalid show</span>" ?></td>
          <td><?= htmlspecialchars($b['seats']) ?></td>
          <td>
            <span class="status-badge <?= $b['status'] == 'confirmed' ? 'confirmed' : 'cancelled' ?>">
              <?= htmlspecialchars(ucfirst($b['status'])) ?>
            </span>
          </td>
          <td><?= date('d M Y', strtotime($b['booking_date'])) ?></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="6" class="empty-msg">You have not booked any tickets yet.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
    </div>
  </div>
</body>
</html>

