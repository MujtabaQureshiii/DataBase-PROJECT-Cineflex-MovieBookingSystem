<style>
  .footer {
  background: #101010;
  color: #aaa;
  padding: 2.3rem 0 1.1rem 0;
  font-size: 1.13rem;
  border-top: 2.5px solid #232323;
  text-align: center;
  box-shadow: 0 -2px 16px #000c;
  margin-top: 3.2rem;
}

.footer a {
  color: #fff;
  text-decoration: none;
  margin: 0 .8rem;
  opacity: 0.92;
  transition: color .14s, opacity .14s;
  font-weight: 500;
  letter-spacing: .5px;
}

.footer a:hover {
  color: #e50914;
  opacity: 1;
  text-shadow: 0 2px 12px #e5091450;
}

.footer-icons {
  margin-top: 1rem;
}

.footer-icons a {
  font-size: 1.5rem;
  margin: 0 0.34rem;
  opacity: .79;
  vertical-align: middle;
  transition: color .16s, transform .14s;
}

.footer-icons a:hover {
  color: #e50914;
  opacity: 1;
  transform: scale(1.2) rotate(-8deg);
}

@media (max-width: 700px) {
  .footer {
    font-size: 1rem;
    padding: 1.1rem 0 .7rem 0;
  }
  .footer-icons a {
    font-size: 1.19rem;
  }
}

</style>
<div class="footer mt-5">
  <div>
    <a href="index.php">Home</a> | <a href="movies.php">Movies</a> | <a href="my-bookings.php">Bookings</a> | <a href="#">Contact</a>
  </div>
  <div class="footer-icons mt-2">
    <a href="#"><i class="bi bi-facebook"></i></a>
    <a href="#"><i class="bi bi-twitter-x"></i></a>
    <a href="#"><i class="bi bi-instagram"></i></a>
    <a href="#"><i class="bi bi-youtube"></i></a>
  </div>
  <div style="margin-top:.5rem;">
    CineFlex &copy; <?= date('Y') ?>. All Rights Reserved.
  </div>
</div>
