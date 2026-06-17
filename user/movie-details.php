<?php
session_start();
require_once("../includes/db.php");

// Check movie ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) die("Movie not found!");
$id = intval($_GET['id']);
$movie = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM movies WHERE id=$id AND status='active' LIMIT 1"));
if(!$movie) die("Movie not found!");

// Fetch available shows for this movie
$shows = mysqli_query($conn, "
    SELECT s.*, t.name as tname, t.city 
    FROM shows s 
    LEFT JOIN theaters t ON s.theater_id = t.id 
    WHERE s.movie_id=$id AND s.show_time >= NOW() 
    ORDER BY s.show_time ASC
");

// Fetch dynamic cast from DB
$cast = [];
$cast_res = mysqli_query($conn, "SELECT * FROM movie_cast WHERE movie_id=$id AND status='active'");
while($row = mysqli_fetch_assoc($cast_res)) $cast[] = $row;

// Reviews
$reviews = mysqli_query($conn, "SELECT r.*, u.name as uname FROM reviews r LEFT JOIN users u ON r.user_id=u.id WHERE r.movie_id=$id AND r.status='active' ORDER BY r.created_at DESC LIMIT 8");

// Handle review submission
$review_msg = $review_err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
  if (!isset($_SESSION['user_id'])) {
    $review_err = "Please login to add your review.";
  } else {
    $rating = intval($_POST['rating'] ?? 0);
    $review = trim($_POST['review'] ?? '');
    $name = $_SESSION['user_name'] ?? 'User';
    $user_id = $_SESSION['user_id'];
    if (!$rating || $rating < 1 || $rating > 5) $review_err = "Give a rating!";
    elseif (!$review) $review_err = "Write your review!";
    else {
      $sql = "INSERT INTO reviews (movie_id, user_id, name, rating, review, created_at, status) VALUES ($id, $user_id, '".mysqli_real_escape_string($conn, $name)."', $rating, '".mysqli_real_escape_string($conn, $review)."', NOW(), 'active')";
      if(mysqli_query($conn, $sql)) {
        $review_msg = "Thank you for your review!";
      } else {
        $review_err = "DB Error: ".mysqli_error($conn);
      }
    }
  }
  // Redirect to show updated reviews
  header("Location: movie-details.php?id=$id");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($movie['title']) ?> - CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {background: #181818; color: #fff;}
    .bg-blur {
      background: linear-gradient(115deg, #191919 80%, #e5091445 100%);
      position: fixed; left:0; top:0; width:100vw; height:100vh; z-index:-2;
      filter: blur(14px) brightness(0.8);
    }
    .movie-detail-hero {
      max-width: 1120px;
      margin: 3.5rem auto 0 auto;
      padding: 2.5rem 2.5rem 2rem 2.5rem;
      background: rgba(24,24,24,0.97);
      border-radius: 1.7rem;
      box-shadow: 0 8px 42px #000c;
      display: flex; gap: 2.5rem; align-items: flex-start;
      position: relative;
    }
    .movie-poster {
      width: 310px; min-width: 170px; height: 420px;
      object-fit: cover; border-radius: .95rem; box-shadow:0 6px 32px #000b;
      border:4px solid #212121;
      background: #1e1e1e;
    }
    .movie-meta { flex:1; }
    .movie-title { font-family: 'Montserrat',sans-serif; font-size:2.5rem; font-weight:900; color:#fff; }
    .badges .badge {
      font-size:1.05rem; padding:.32rem 1.4rem; margin-right:.23rem; border-radius:2rem;
      background: #e50914; color: #fff; font-weight:700;
      text-shadow: 0 2px 7px #000a;
    }
    .badges .n { background:#0197f6; }
    .badges .t { background:#43a047; }
    .movie-info-chips {
      margin:1.1rem 0 .75rem 0;
      display: flex; flex-wrap:wrap; gap:.7rem;
    }
    .chip {
      background: #222; color: #ffd96a; border-radius: 1.2rem;
      font-size:1rem; font-weight:600; padding:.22rem .98rem; display:flex;align-items:center;gap:.33rem;
    }
    .rating-badge {
      background: #212121; color: #ffe600;
      padding: .18rem 1.1rem; border-radius: .9rem;
      font-weight:900; font-size:1.28rem; box-shadow:0 1px 7px #000c;
      margin-left: .5rem;
    }
    .movie-desc { font-size:1.16rem; color:#c7c7c7; margin:1.5rem 0 1.5rem 0; }
    .trailer-btn {background:#e50914; color:#fff; font-weight:700; border:none; padding:.54rem 1.8rem; border-radius:.6rem;}
    .trailer-btn:hover {background:#b0060f;}
    .show-table {margin-top:2.7rem;}
    .show-table th {background:#191919;color:#e50914;}
    .show-table td, .show-table th {text-align:center;}
    .book-btn {background:#e50914;color:#fff;border:none;padding:.47rem 1.6rem;border-radius:.5rem;font-weight:700;}
    .book-btn:hover {background:#b0060f;}
    .section-heading {font-family:'Montserrat',sans-serif;color:#e50914;font-weight:900;font-size:1.23rem;}
    /* Reviews */
    .review-card {
      background: #222; border-radius:.8rem; padding:1.15rem 1.5rem 1rem 1.5rem; margin-bottom:.85rem;
      box-shadow:0 4px 22px #0007;
      border-left:4px solid #e50914;
      position:relative;
    }
    .review-username {font-weight:700;font-size:1.11rem;color:#ffd96a;}
    .review-date {color:#aaa; font-size:.93rem;}
    .review-rating {color:#ffe600; font-size:1.11rem;}
    .add-review-form .form-control {background:#181818!important; color:#fff; border:1.5px solid #353535;}
    .add-review-form .form-control:focus {background:#232323; border-color:#e50914; box-shadow:0 0 0 .08rem #e509148a;}
    .add-review-form textarea {resize:none;}
    /* CAST SECTION */
    .cast-wrap {display: flex; gap:1.2rem; margin:1.5rem 0 1.2rem 0; flex-wrap:wrap;}
    .cast-card {
      background:#252525; padding:.82rem .95rem; border-radius:.7rem;
      display:flex;align-items:center;gap:.8rem;min-width:180px;
      box-shadow:0 2px 11px #0006;
      flex-direction:column;
      min-height:180px;
      max-width:140px;
    }
    .cast-img {
      width:65px; height:65px; object-fit:cover;
      border-radius:50%; border:2.5px solid #ffd96a; background:#1a1a1a;
    }
    .cast-name {font-size:1.07rem;font-weight:600;color:#ffd96a;}
    .cast-role {color:#b6b6b6; font-size:.96rem;}
    .modal-backdrop {background:rgba(24,24,24,0.88);}
    @media (max-width:1100px){.movie-detail-hero{flex-direction:column;align-items:center;max-width:99vw;padding:1.2rem .3rem;}.movie-poster{width:90vw;height:auto;max-width:370px;}}
    @media (max-width:600px){.movie-title{font-size:1.17rem;}.movie-info-chips{gap:.37rem;}.review-card{padding:.85rem;}}
  </style>
</head>
<body>
  <?php include("header.php"); ?>
  <div class="movie-detail-hero">
    <div>
      <?php if($movie['poster']): ?>
        <img src="../<?= htmlspecialchars($movie['poster']) ?>" class="movie-poster" alt="Movie Poster">
      <?php else: ?>
        <div class="movie-poster d-flex align-items-center justify-content-center bg-dark text-secondary">No Poster</div>
      <?php endif; ?>
      <?php if($movie['trailer']): ?>
        <div class="mt-3">
          <button class="trailer-btn w-100" data-bs-toggle="modal" data-bs-target="#trailerModal"><i class="bi bi-play-circle"></i> Watch Trailer</button>
        </div>
      <?php endif; ?>
      <!-- Social share/bookmark -->
      <div class="mt-4">
        <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>" target="_blank" class="btn btn-sm btn-primary me-2"><i class="bi bi-facebook"></i></a>
        <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>&text=Check+out+<?=urlencode($movie['title'])?>" target="_blank" class="btn btn-sm btn-info"><i class="bi bi-twitter"></i></a>
        <a href="#" onclick="alert('Added to Watchlist!')" class="btn btn-sm btn-dark ms-2"><i class="bi bi-bookmark-star"></i></a>
      </div>
    </div>
    <div class="movie-meta">
      <div class="movie-title"><?= htmlspecialchars($movie['title']) ?></div>
      <div class="badges mb-2">
        <?php if($movie['is_trending']): ?><span class="badge">Trending</span><?php endif; ?>
        <?php if($movie['is_nowshowing']): ?><span class="badge n">Now Showing</span><?php endif; ?>
        <?php if($movie['is_toprated']): ?><span class="badge t">Top Rated</span><?php endif; ?>
      </div>
      <div class="movie-info-chips">
        <span class="chip"><i class="bi bi-film"></i> <?= htmlspecialchars($movie['genre']) ?></span>
        <span class="chip"><i class="bi bi-clock"></i> <?= htmlspecialchars($movie['duration']) ?></span>
        <span class="chip"><i class="bi bi-calendar-event"></i>
          <?php
            if (!empty($movie['release_date'])) {
              echo date('M d, Y', strtotime($movie['release_date']));
            } else {
              echo 'Release date N/A';
            }
          ?>
        </span>
        <span class="chip rating-badge"><i class="bi bi-star-fill"></i> <?= number_format($movie['rating'],1) ?>/10</span>
      </div>
      <div class="movie-desc"><?= nl2br(htmlspecialchars($movie['description'])) ?></div>
      <!-- *** CAST SECTION (Dynamic) *** -->
      <div>
        <h5 class="section-heading mb-3"><i class="bi bi-person-badge"></i> Cast</h5>
        <div class="cast-wrap">
          <?php if(count($cast)): foreach($cast as $c): ?>
            <div class="cast-card">
              <?php if($c['image']): ?>
                <img src="../<?= htmlspecialchars($c['image']) ?>" class="cast-img" alt="<?= htmlspecialchars($c['name']) ?>">
              <?php else: ?>
                <div class="cast-img bg-secondary d-flex align-items-center justify-content-center" style="font-size:2.3rem;">?</div>
              <?php endif; ?>
              <div class="cast-name"><?= htmlspecialchars($c['name']) ?></div>
              <?php if($c['role']): ?><div class="cast-role"><?= htmlspecialchars($c['role']) ?></div><?php endif; ?>
            </div>
          <?php endforeach; else: ?>
            <div class="text-secondary ms-2 mb-2">No cast data yet.</div>
          <?php endif; ?>
        </div>
      </div>
      <!-- SHOWS -->
      <div class="show-table">
        <h5 class="section-heading mb-3">Available Shows</h5>
        <table class="table table-borderless table-hover align-middle shadow-sm">
          <thead>
            <tr>
              <th>Theater</th>
              <th>City</th>
              <th>Date/Time</th>
              <th>Seats</th>
              <th>Price</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if(mysqli_num_rows($shows)): while($s = mysqli_fetch_assoc($shows)): ?>
              <tr>
                <td><?= htmlspecialchars($s['tname']) ?></td>
                <td><?= htmlspecialchars($s['city']) ?></td>
                <td><?= date('d M Y, h:i A', strtotime($s['show_time'])) ?></td>
                <td><?= intval($s['seats']) ?></td>
                <td>Rs <?= number_format($s['price'],0) ?></td>
                <td>
                  <a href="booking.php?movie_id=<?= $movie['id'] ?>&show_id=<?= $s['id'] ?>" class="book-btn">Book Now</a>
                </td>
              </tr>
            <?php endwhile; else: ?>
              <tr>
                <td colspan="6" style="color:#bbb;">No shows available for this movie.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- REVIEWS -->
      <div class="mt-5">
        <h5 class="section-heading mb-2"><i class="bi bi-chat-left-text"></i> Reviews</h5>
        <?php if($review_msg): ?><div class="alert alert-success"><?= $review_msg ?></div><?php endif; ?>
        <?php if($review_err): ?><div class="alert alert-danger"><?= $review_err ?></div><?php endif; ?>
        <!-- Add Review Form -->
        <?php if(isset($_SESSION['user_id'])): ?>
          <form class="add-review-form mb-4" method="POST">
            <div class="row g-2">
              <div class="col-auto">
                <select name="rating" class="form-control" required>
                  <option value="">Rating</option>
                  <?php for($i=5;$i>=1;$i--): ?>
                  <option value="<?=$i?>"><?=str_repeat('★',$i)?></option>
                  <?php endfor; ?>
                </select>
              </div>
              <div class="col">
                <textarea name="review" class="form-control" placeholder="Write your review..." rows="2" maxlength="300" required></textarea>
              </div>
              <div class="col-auto">
                <button type="submit" name="add_review" class="btn btn-main px-4">Submit</button>
              </div>
            </div>
          </form>
        <?php else: ?>
          <div class="mb-3"><i class="bi bi-info-circle"></i> <a href="login.php" style="color:#e50914;">Login</a> to add a review.</div>
        <?php endif; ?>
        <!-- Show Reviews -->
        <?php if(mysqli_num_rows($reviews)): foreach($reviews as $r): ?>
        <div class="review-card">
          <div class="d-flex align-items-center mb-1">
            <div class="review-username"><i class="bi bi-person-circle"></i> <?=htmlspecialchars($r['uname'] ?? $r['name'])?></div>
            <div class="review-rating ms-3"><?=str_repeat('★',intval($r['rating']))?></div>
            <span class="review-date ms-auto"><?=date('d M Y',strtotime($r['created_at']))?></span>
          </div>
          <div><?=htmlspecialchars($r['review'])?></div>
        </div>
        <?php endforeach; else: ?>
        <div style="color:#aaa;">No reviews yet. Be the first!</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- Modal for Trailer -->
  <?php if($movie['trailer']): ?>
  <div class="modal fade" id="trailerModal" tabindex="-1" aria-labelledby="trailerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content" style="background:#181818;">
        <div class="modal-header border-0">
          <h5 class="modal-title" id="trailerModalLabel"><i class="bi bi-film"></i> Trailer</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <video controls autoplay style="width:100%;border-radius:1.1rem;">
            <source src="../<?= htmlspecialchars($movie['trailer']) ?>" type="video/mp4">
            Trailer not supported.
          </video>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <?php include("footer.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
