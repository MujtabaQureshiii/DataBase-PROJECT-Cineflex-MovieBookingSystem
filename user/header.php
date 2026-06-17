<nav class="navbar navbar-expand-lg sticky-top" style="background:#111;">
  <div class="container">
    <a class="navbar-brand" href="index.php" style="font-family:'Montserrat',sans-serif;font-size:2.1rem;color:#e50914;letter-spacing:1.3px;font-weight:900;">CineFlex</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon" style="color:#fff;"></span>
    </button>
    <div class="collapse navbar-collapse show" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php" style="color:#fff;">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="movies.php" style="color:#fff;">Movies</a></li>
        <?php if(isset($_SESSION['user_name'])): ?>
          <li class="nav-item"><a class="nav-link" href="my-bookings.php" style="color:#fff;">My Bookings</a></li>
          <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link" href="../admin/dashboard.php" style="color:#e50914;font-weight:700;">
                <i class="bi bi-speedometer2 me-1"></i>Dashboard
              </a>
            </li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout.php" style="color:#fff;">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php" style="color:#fff;">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
