<?php
session_start();
require_once("../includes/db.php");

// Trending
$trending = mysqli_query($conn, "SELECT * FROM movies WHERE is_trending=1 AND status='active' ORDER BY rating DESC LIMIT 8");
$nowshowing = mysqli_query($conn, "SELECT * FROM movies WHERE is_nowshowing=1 AND status='active' ORDER BY created_at DESC LIMIT 8");
$toprated = mysqli_query($conn, "SELECT * FROM movies WHERE is_toprated=1 AND status='active' ORDER BY rating DESC LIMIT 8");

// Contact form processing
$contact_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = trim($_POST['contact_name']);
    $email = trim($_POST['contact_email']);
    $message = trim($_POST['contact_message']);

    if ($name && $email && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            $contact_msg = "<div class='alert alert-success mt-2'>Thank you for contacting us!</div>";
        } else {
            $contact_msg = "<div class='alert alert-danger mt-2'>Error, please try again.</div>";
        }
        $stmt->close();
    } else {
        $contact_msg = "<div class='alert alert-warning mt-2'>Please fill in all fields.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CineFlex – Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glider-js@1.7.8/glider.min.css">
  <style>
    body { background: #181818; color: #fff; font-family: 'Roboto', Arial, sans-serif; }
    .navbar { background: #111; padding: 1.2rem 2rem;}
    .navbar-brand { font-family: 'Montserrat', sans-serif; font-size: 2.5rem; color: #e50914 !important; letter-spacing: 2px; font-weight: 900;}
    .nav-link { color: #fff !important; margin-left: 1.2rem; font-weight: 500;}
    .nav-link:hover, .nav-link.active { color: #e50914 !important;}
    .btn-main { background: #e50914; color: #fff; border: none; padding: .7rem 2rem; font-weight: 700; border-radius: .45rem; margin-right: 1rem; box-shadow: 0 4px 16px #e509142a; transition: background 0.18s;}
    .btn-main:hover { background: #b0060f;}
    .hero-banner { background: linear-gradient(112deg,#000b,#232323c7), url('https://images.unsplash.com/photo-1467987506553-8f3916508521?auto=format&fit=crop&w=1500&q=80') no-repeat center center/cover; min-height:410px; border-radius: 2.5rem; box-shadow: 0 16px 62px #000c; display: flex; align-items: center; position: relative; margin-bottom: 3rem; overflow: hidden;}
    .hero-content { position: relative; z-index: 2; padding: 3.4rem 4.5rem 3.5rem; max-width: 600px;}
    .hero-title { font-family: 'Montserrat', sans-serif; font-size: 3.5rem; font-weight: 800; margin-bottom: 1.3rem; letter-spacing: 1px; line-height: 1.09;}
    .hero-desc { font-size: 1.26rem; color: #e0e0e0; margin-bottom: 2.1rem;}
    .search-section { margin: -72px auto 2.3rem; max-width: 99vw; z-index: 10; position: relative;}
    .search-bar { border-radius: 3rem; border: none; height: 52px; font-size: 1.14rem; padding-left: 2.1rem; box-shadow: 0 3px 24px #0007; margin-bottom: 1rem;}
    .category-chips { display: flex; gap: .8rem; margin-bottom: 1.3rem; flex-wrap: wrap;}
    .chip { background: #232323; border-radius: 1.5rem; color: #fff; padding: .48rem 1.2rem; font-size: 1.07rem; font-weight: 500; border: 1.5px solid #323232; cursor: pointer; transition: background .15s;}
    .chip:hover, .chip.active { background: #e50914; color: #fff; border: 1.5px solid #e50914;}
    .section-title { font-family: 'Montserrat', sans-serif; font-size: 1.7rem; margin: 2.2rem 0 1.3rem 2px; font-weight: 700; letter-spacing: 0.8px;}
    .glider-contain { position: relative; margin-bottom: 2.3rem; background: none;}
    .carousel-card {  background: #222;  border-radius: 1.25rem;  box-shadow: 0 5px 18px #000a;  overflow: hidden;  width: 220px;  min-width: 220px;  margin: 0 .7rem;  display: flex;  flex-direction: column;  align-items: stretch;  position: relative;  transition: box-shadow .15s, transform .12s;}
    .carousel-body {  position: absolute;  bottom: 0;  left: 0;  width: 100%;  padding: 1rem;  background: rgba(0, 0, 0, 0.85);  transform: translateY(100%);  opacity: 0;  transition: all 0.3s ease-in-out;}
    .carousel-card:hover .carousel-body {  transform: translateY(0);  opacity: 1;}
    .carousel-title {  font-size: 1rem;  font-weight: bold;  color: #fff;  margin-bottom: 0.3rem;}
    .carousel-info {  font-size: 0.85rem;  color: #ccc;  margin-bottom: 0.5rem;}
    .carousel-img {  height: 600px;  object-fit: cover;  display: block;  background: #111;  border-top-left-radius: 0.5rem;  border-top-right-radius: 0.5rem;}
    .carousel-body { padding: 1.08rem 1.09rem 1.13rem 1.12rem; background: linear-gradient(to top, rgba(34, 34, 34, 0.95), rgba(34, 34, 34, 0.8), rgba(34, 34, 34, 0.8), rgba(34, 34, 34, 0)); flex: 1 1 auto; display: flex; flex-direction: column; align-items: flex-start; }
    .carousel-title { font-family: 'Montserrat', sans-serif; font-size: 1.17rem; color: #fff; font-weight: 800; margin-bottom: .22rem;}
    .carousel-info { color: #e50914; font-size: 1rem; margin-bottom: .84rem;}
    .carousel-btn { margin-top: auto; background: #e50914; color: #fff; border: none; border-radius: .5rem; padding: .31rem 1.12rem; font-weight: 700; font-size: 1.07rem; letter-spacing: .8px; transition: background .17s;}
    .carousel-btn:hover { background: #b0060f;}
    .glider-prev-1, .glider-next-1, .glider-prev-2, .glider-next-2, .glider-prev-3, .glider-next-3 { background: #fff; color: #e50914; border: none; border-radius: 2rem; font-size: 1.5rem; width: 2.5rem; height: 2.5rem; position: absolute; top: 48%; transform: translateY(-50%); box-shadow: 0 2px 8px #0002; z-index: 3; cursor: pointer; opacity: 0.97; transition: background .13s, color .13s;}
    .glider-prev-1, .glider-prev-2, .glider-prev-3 { left: -1.8rem;}
    .glider-next-1, .glider-next-2, .glider-next-3 { right: -1.8rem;}
    .glider-prev-1:hover, .glider-next-1:hover, .glider-prev-2:hover, .glider-next-2:hover, .glider-prev-3:hover, .glider-next-3:hover { background: #e50914; color: #fff;}
    .movie-carousel-1, .movie-carousel-2, .movie-carousel-3 { scrollbar-width: none; -ms-overflow-style: none;}
    .movie-carousel-1::-webkit-scrollbar, .movie-carousel-2::-webkit-scrollbar, .movie-carousel-3::-webkit-scrollbar { display: none;}
    .why-cineflex { margin: 4rem 0 2.3rem 0; display: flex; justify-content: center; gap: 2.5rem; flex-wrap: wrap;}
    .why-item {background: #232323;border-radius: 1.5rem; padding: 1.33rem 1.85rem; min-width: 150px; text-align: center; box-shadow: 0 2px 14px #0004; margin-bottom: 1.4rem;}
    .why-icon { font-size: 2.25rem; color: #e50914; margin-bottom: .41rem;}
    .why-title {font-size: 1.09rem; font-weight: 700; color: #fff;}
    .testimonials {background: #222; border-radius: 1.5rem; margin-top: 2.7rem; padding: 2rem 1rem 1.1rem 1.3rem; box-shadow: 0 5px 26px #0009;}
    .testimonial-card {background: #282828; border-radius: 1.09rem; padding: 1.08rem 1.04rem; box-shadow: 0 1px 12px #0004; margin: 0 .7rem; min-width: 160px; max-width: 250px;}
    .testimonial-user {font-weight: 700; color: #e50914; margin-bottom: .2rem; font-size: 1.08rem;}
    .testimonial-stars {color: #ffc107; font-size: 1.08rem; margin-bottom: .23rem;}
    .cta-section {margin: 2.8rem auto 2.1rem; text-align: center;}
    .footer {background: #101010; color: #aaa; padding: 2rem 0 1.1rem 0; margin-top: 3.2rem; font-size: 1.11rem; border-top: 2px solid #232323;}
    .footer a {color: #fff;text-decoration: none;margin: 0 .8rem;opacity: .94;transition: color .12s;}
    .footer a:hover {color: #e50914;}
    .footer-icons a {font-size: 1.25rem;margin: 0 0.25rem;opacity: .82;}
    @media (max-width: 1100px) {.hero-title { font-size: 2.1rem; } .hero-banner { min-height:160px; } .carousel-card { width: 150px; min-width: 150px;} .carousel-img { height: 90px; }}
    @media (max-width: 700px) {.carousel-card { width: 83vw; min-width: 83vw;} .hero-banner { border-radius: 1.3rem;} .why-cineflex { gap: .9rem;} .footer {padding: 1rem 0 .5rem 0;}}
    @media (max-width: 1100px) {  .carousel-card {    width: 200px;    min-width: 200px;  }  .carousel-img {    height: 300px;  }}
    @media (max-width: 768px) {  .carousel-card {    width: 48vw;    min-width: 48vw;  }  .carousel-img {    height: 500px;    object-fit: cover;  }  .carousel-title {    font-size: 1.05rem;  }  .carousel-info {    font-size: 0.88rem;  }  .carousel-btn {    font-size: 0.9rem;    padding: 0.28rem 0.8rem;  }}
    @media (max-width: 480px) {  .carousel-card {    width: 86vw;    min-width: 86vw;  }  .carousel-img {    height: 500px;    object-fit: cover;  }}
    /* About & Contact custom */
    #about, #contact {scroll-margin-top:100px;}
    .contact-label {color:#fff; font-weight:600;}
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <a class="navbar-brand" href="index.php">CineFlex</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon" style="color:#fff;"></span>
    </button>
    <div class="collapse navbar-collapse show" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="movies.php">Movies</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="catDropdown" data-bs-toggle="dropdown">Categories</a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="movies.php?genre=Action">Action</a></li>
            <li><a class="dropdown-item" href="movies.php?genre=Comedy">Comedy</a></li>
            <li><a class="dropdown-item" href="movies.php?genre=Drama">Drama</a></li>
            <li><a class="dropdown-item" href="movies.php?genre=Thriller">Thriller</a></li>
            <li><a class="dropdown-item" href="movies.php?genre=Kids">Kids</a></li>
            <li><a class="dropdown-item" href="movies.php?genre=Family">Family</a></li>
          </ul>
        </li>
        <?php if(isset($_SESSION['user_name'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user_name']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item" href="my-bookings.php"><i class="bi bi-ticket-detailed me-1"></i> My Bookings</a></li>
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-lines-fill me-1"></i> Profile</a></li>
              <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li><a class="dropdown-item" href="../admin/dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Go to Dashboard</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link btn btn-main px-3 py-2 ms-2" href="register.php">Become a Member</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>

  <!-- Hero Banner -->
  <div class="container" style="max-width:1600px;">
    <div class="hero-banner mb-5 mt-3">
      <div class="hero-content">
        <div class="hero-title">Book Your Movie Tickets Instantly<br>on <span style="color:#e50914;">CineFlex</span></div>
        <div class="hero-desc">
          Watch the latest blockbusters, see trailers, reviews, and reserve your seat instantly!<br>
          <span style="color:#e50914;font-weight:700;">No queues. Just fun.</span>
        </div>
        <a href="movies.php" class="btn btn-main btn-lg me-2 px-4">Browse Movies</a>
        <a href="register.php" class="btn btn-light btn-lg px-4">Become a Member</a>
      </div>
    </div>

    <!-- Trending Now Carousel -->
    <div class="section-title">🔥 Trending Now</div>
    <div class="glider-contain mb-4">
      <button class="glider-prev-1" aria-label="Previous">&lt;</button>
      <div class="glider movie-carousel-1">
        <?php while($m = mysqli_fetch_assoc($trending)): ?>
        <div class="carousel-card">
          <img src="../<?= htmlspecialchars($m['poster']) ?>" class="carousel-img" alt="Poster">
          <div class="carousel-body">
            <div class="carousel-title"><?= htmlspecialchars($m['title']) ?></div>
            <div class="carousel-info"><?= htmlspecialchars($m['genre']) ?> • <?= htmlspecialchars($m['duration']) ?></div>
            <a href="movie-details.php?id=<?= $m['id'] ?>" class="carousel-btn">Details</a>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <button class="glider-next-1" aria-label="Next">&gt;</button>
      <div class="dots-1"></div>
    </div>

    <!-- Now Showing Carousel -->
    <div class="section-title">🎬 Now Showing</div>
    <div class="glider-contain mb-4">
      <button class="glider-prev-2" aria-label="Previous">&lt;</button>
      <div class="glider movie-carousel-2">
        <?php while($m = mysqli_fetch_assoc($nowshowing)): ?>
        <div class="carousel-card">
          <img src="../<?= htmlspecialchars($m['poster']) ?>" class="carousel-img" alt="Poster">
          <div class="carousel-body">
            <div class="carousel-title"><?= htmlspecialchars($m['title']) ?></div>
            <div class="carousel-info"><?= htmlspecialchars($m['genre']) ?> • <?= htmlspecialchars($m['duration']) ?></div>
            <a href="movie-details.php?id=<?= $m['id'] ?>" class="carousel-btn">Details</a>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <button class="glider-next-2" aria-label="Next">&gt;</button>
      <div class="dots-2"></div>
    </div>

    <!-- Top Rated Carousel -->
    <div class="section-title">⭐ Top Rated</div>
    <div class="glider-contain mb-5">
      <button class="glider-prev-3" aria-label="Previous">&lt;</button>
      <div class="glider movie-carousel-3">
        <?php while($m = mysqli_fetch_assoc($toprated)): ?>
        <div class="carousel-card">
          <img src="../<?= htmlspecialchars($m['poster']) ?>" class="carousel-img" alt="Poster">
          <div class="carousel-body">
            <div class="carousel-title"><?= htmlspecialchars($m['title']) ?></div>
            <div class="carousel-info"><?= htmlspecialchars($m['genre']) ?> • <?= htmlspecialchars($m['duration']) ?></div>
            <a href="movie-details.php?id=<?= $m['id'] ?>" class="carousel-btn">Details</a>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <button class="glider-next-3" aria-label="Next">&gt;</button>
      <div class="dots-3"></div>
    </div>

    <!-- Why CineFlex -->
    <div class="why-cineflex">
      <div class="why-item">
        <div class="why-icon"><i class="bi bi-shield-lock"></i></div>
        <div class="why-title">Secure Booking</div>
        <div>Book tickets safely and easily.</div>
      </div>
      <div class="why-item">
        <div class="why-icon"><i class="bi bi-film"></i></div>
        <div class="why-title">HD Trailers</div>
        <div>Watch HD trailers & posters.</div>
      </div>
      <div class="why-item">
        <div class="why-icon"><i class="bi bi-people"></i></div>
        <div class="why-title">Real Reviews</div>
        <div>Read only real user reviews.</div>
      </div>
      <div class="why-item">
        <div class="why-icon"><i class="bi bi-lightning"></i></div>
        <div class="why-title">Instant Tickets</div>
        <div>Get e-tickets in seconds.</div>
      </div>
    </div>
    <!-- Testimonials -->
    <div class="testimonials mt-5 mb-3">
      <div class="section-title" style="margin:0 0 1.2rem 0;">What Our Users Say</div>
      <div class="d-flex flex-wrap justify-content-center gap-4">
        <div class="testimonial-card">
          <div class="testimonial-user">Sara R.</div>
          <div class="testimonial-stars">★★★★★</div>
          <div>Booking tickets has never been easier. CineFlex is just awesome!</div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-user">Ali Khan</div>
          <div class="testimonial-stars">★★★★☆</div>
          <div>Loved the dark theme & instant booking. Looks premium!</div>
        </div>
        <div class="testimonial-card">
          <div class="testimonial-user">Hira M.</div>
          <div class="testimonial-stars">★★★★★</div>
          <div>HD trailers, real reviews, and a smooth booking experience.</div>
        </div>
      </div>
    </div>

    <!-- ====== ABOUT US SECTION ====== -->
    <div class="container" id="about" style="margin-top:4.5rem;max-width:950px;">
      <div class="section-title">About CineFlex</div>
      <div style="background:#232323;border-radius:1.2rem;padding:2.1rem 2.3rem;color:#ddd;box-shadow:0 3px 22px #0007;">
        <p style="font-size:1.14rem;">
          <b>CineFlex</b> is your ultimate movie booking destination. From the latest blockbusters to exclusive indie screenings, we bring you the most seamless ticket booking experience, stunning HD trailers, verified reviews, and instant e-tickets. Join thousands who trust CineFlex for fun, speed, and reliability!
        </p>
        <ul style="font-size:1.08rem; margin-top:1rem;">
          <li>🎬 Book your favorite movies instantly</li>
          <li>🌟 Read real user reviews</li>
          <li>🔒 100% Secure Payments</li>
          <li>🚀 Fast, easy & responsive experience</li>
        </ul>
      </div>
    </div>

    <!-- ====== CONTACT US SECTION ====== -->
    <div class="container" id="contact" style="margin-top:3.8rem;max-width:720px;">
      <div class="section-title">Contact Us</div>
      <div style="background:#232323;border-radius:1.2rem;padding:2rem 2.3rem;box-shadow:0 2px 16px #0008;">
        <form method="post" autocomplete="off">
          <div class="mb-3">
            <label for="contact_name" class="form-label contact-label">Your Name</label>
            <input type="text" class="form-control" id="contact_name" name="contact_name" required>
          </div>
          <div class="mb-3">
            <label for="contact_email" class="form-label contact-label">Email address</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" required>
          </div>
          <div class="mb-3">
            <label for="contact_message" class="form-label contact-label">Message</label>
            <textarea class="form-control" id="contact_message" name="contact_message" rows="4" required></textarea>
          </div>
          <button type="submit" name="contact_submit" class="btn btn-main px-5">Send</button>
          <?= $contact_msg ?? "" ?>
        </form>
      </div>
    </div>

    <!-- Call To Action -->
    <div class="cta-section">
      <h2 style="font-family:'Montserrat',sans-serif;font-size:2rem;">Join CineFlex Today!</h2>
      <div style="color:#ccc;font-size:1.17rem;">Become a member and experience the new way to watch and book movies.</div>
      <a href="register.php" class="btn btn-main mt-3 px-4 py-2" style="font-size:1.13rem;">Register Now</a>
    </div>
  </div>
  <!-- Footer -->
  <div class="footer text-center">
    <div>
      <a href="index.php">Home</a> |
      <a href="movies.php">Movies</a> |
      <a href="my-bookings.php">Bookings</a> |
      <a href="#about">About</a> |
      <a href="#contact">Contact</a>
    </div>
    <div class="footer-icons mt-2">
      <a href="#"><i class="bi bi-facebook"></i></a>
      <a href="#"><i class="bi bi-twitter-x"></i></a>
      <a href="#"><i class="bi bi-instagram"></i></a>
      <a href="#"><i class="bi bi-youtube"></i></a>
    </div>
    <div style="margin-top:.8rem;">
      CineFlex &copy; <?= date('Y') ?>. All Rights Reserved.
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/glider-js@1.7.8/glider.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      new Glider(document.querySelector('.movie-carousel-1'), {
        slidesToShow: 5,
        slidesToScroll: 1,
        draggable: true,
        dots: '.dots-1',
        arrows: { prev: '.glider-prev-1', next: '.glider-next-1' },
        responsive: [
          { breakpoint: 1400, settings: { slidesToShow: 4 } },
          { breakpoint: 1050, settings: { slidesToShow: 3 } },
          { breakpoint: 700, settings: { slidesToShow: 2 } },
          { breakpoint: 500, settings: { slidesToShow: 1 } }
        ]
      });
      new Glider(document.querySelector('.movie-carousel-2'), {
        slidesToShow: 5,
        slidesToScroll: 1,
        draggable: true,
        dots: '.dots-2',
        arrows: { prev: '.glider-prev-2', next: '.glider-next-2' },
        responsive: [
          { breakpoint: 1400, settings: { slidesToShow: 4 } },
          { breakpoint: 1050, settings: { slidesToShow: 3 } },
          { breakpoint: 700, settings: { slidesToShow: 2 } },
          { breakpoint: 500, settings: { slidesToShow: 1 } }
        ]
      });
      new Glider(document.querySelector('.movie-carousel-3'), {
        slidesToShow: 5,
        slidesToScroll: 1,
        draggable: true,
        dots: '.dots-3',
        arrows: { prev: '.glider-prev-3', next: '.glider-next-3' },
        responsive: [
          { breakpoint: 1400, settings: { slidesToShow: 4 } },
          { breakpoint: 1050, settings: { slidesToShow: 3 } },
          { breakpoint: 700, settings: { slidesToShow: 2 } },
          { breakpoint: 500, settings: { slidesToShow: 1 } }
        ]
      });
    });
  </script>
</body>
</html>
