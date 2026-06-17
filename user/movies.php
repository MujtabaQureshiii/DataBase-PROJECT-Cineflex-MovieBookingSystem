<?php
session_start();
require_once("../includes/db.php");

// Genre filter
$where = "status='active'";
$genre_filter = "";
if(isset($_GET['genre']) && $_GET['genre']) {
  $genre = mysqli_real_escape_string($conn, $_GET['genre']);
  $where .= " AND genre LIKE '%$genre%'";
  $genre_filter = $genre;
}
// Search filter
$search = "";
if(isset($_GET['search']) && $_GET['search']) {
  $search = mysqli_real_escape_string($conn, $_GET['search']);
  $where .= " AND (title LIKE '%$search%' OR genre LIKE '%$search%')";
}

$movies = mysqli_query($conn, "SELECT * FROM movies WHERE $where ORDER BY id DESC");

// For genre dropdown (distinct list from db)
$genres_res = mysqli_query($conn, "SELECT DISTINCT genre FROM movies WHERE status='active'");
$genres = [];
while($g = mysqli_fetch_assoc($genres_res)){
  $parts = array_map('trim', explode(',', $g['genre']));
  foreach($parts as $part){
    if($part && !in_array($part, $genres)) $genres[] = $part;
  }
}
sort($genres);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Movies - CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {background:#181818;color:#fff;}
    .navbar-brand {font-family:'Montserrat',sans-serif;color:#e50914!important;font-size:2.1rem;}
    .page-title {font-family:'Montserrat',sans-serif;font-weight:900;color:#e50914;font-size:2rem;margin-top:2.1rem;}
    .search-bar {border-radius:2.1rem;box-shadow:0 2px 14px #0005;border:none;padding:.7rem 1.6rem;font-size:1.1rem;width:100%;margin-bottom:1.2rem;}
    .genre-dropdown {background:#222;color:#fff;border-radius:1.2rem;padding:.5rem 1.5rem;margin-bottom:1.2rem;border:1.5px solid #e50914;display:inline-block;font-size:1rem;}
    .movie-grid {display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:2.2rem 1.3rem;}
    .movie-card {
      background:#232323;border-radius:1.1rem;box-shadow:0 3px 15px #0008;
      display:flex;flex-direction:column;align-items:center;padding:1.2rem .9rem 1.2rem .9rem;
      transition:.13s;position:relative;min-height:385px;
    }
    .movie-card:hover {box-shadow:0 6px 18px #e5091423;transform:translateY(-8px) scale(1.04);}
    .movie-poster {
      width:170px;height:250px;object-fit:cover;border-radius:.9rem;
      box-shadow:0 4px 18px #0007;margin-bottom:.95rem;background:#111;
    }
    .movie-title {font-family:'Montserrat',sans-serif;font-weight:900;color:#fff;font-size:1.17rem;margin-bottom:.15rem;text-align:center;}
    .movie-genre {color:#e50914;font-size:1.01rem;margin-bottom:.18rem;text-align:center;}
    .movie-rating {background:#191919;color:#ffe600;padding:.1rem .7rem;border-radius:.7rem;font-weight:700;font-size:.99rem;margin-bottom:.2rem;}
    .card-btn {background:#e50914;color:#fff;border:none;padding:.39rem 1.3rem;border-radius:.6rem;font-weight:700;margin-top:.25rem;font-size:.98rem;}
    .card-btn:hover {background:#b0060f;}
    .empty-msg {color:#bbb;padding:2.7rem 1rem 2rem 1rem;font-size:1.11rem;}
    @media (max-width:900px){.movie-card{min-width:75vw;max-width:99vw;}.movie-poster{width:99vw;max-width:170px;}}
    @media (max-width:600px){.movie-card{min-width:97vw;max-width:99vw;}.movie-title{font-size:1rem;}}
  </style>
</head>
<body>
  <?php include("header.php"); ?>

  <div class="container py-4">
    <div class="page-title">All Movies<?= $genre_filter ? " — <span style='color:#fff;font-size:1.2rem;'>".htmlspecialchars($genre_filter)."</span>" : "" ?></div>
    <!-- Filter/Dropdown/Search -->
    <form method="get" class="row mb-4">
      <div class="col-md-5 mb-2">
        <select name="genre" class="genre-dropdown" onchange="this.form.submit()">
          <option value="">All Genres</option>
          <?php foreach($genres as $gen): ?>
            <option value="<?= htmlspecialchars($gen) ?>" <?= $genre_filter==$gen?'selected':'' ?>><?= htmlspecialchars($gen) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-5 mb-2">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search movies..." class="search-bar">
      </div>
      <div class="col-md-2 mb-2">
        <button type="submit" class="btn btn-main w-100"><i class="bi bi-search"></i> Filter</button>
      </div>
    </form>

    <div class="movie-grid">
      <?php while($m = mysqli_fetch_assoc($movies)): ?>
      <div class="movie-card">
        <?php if($m['poster']): ?>
          <img src="../<?= htmlspecialchars($m['poster']) ?>" class="movie-poster" alt="Poster">
        <?php else: ?>
          <div class="movie-poster d-flex align-items-center justify-content-center bg-dark text-secondary">No Poster</div>
        <?php endif; ?>
        <div class="movie-title"><?= htmlspecialchars($m['title']) ?></div>
        <div class="movie-genre"><?= htmlspecialchars($m['genre']) ?></div>
        <div class="movie-rating"><i class="bi bi-star-fill"></i> <?= number_format($m['rating'],1) ?>/10</div>
        <a href="movie-details.php?id=<?= $m['id'] ?>" class="card-btn">View Details</a>
      </div>
      <?php endwhile; ?>
      <?php if(mysqli_num_rows($movies)==0): ?>
        <div class="empty-msg col-12">No movies found for this search/filter.</div>
      <?php endif; ?>
    </div>
  </div>
  <?php include("footer.php"); ?>

 