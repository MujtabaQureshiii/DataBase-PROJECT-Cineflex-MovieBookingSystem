<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
$booking_id = $_GET['booking_id'] ?? 0;
require_once("../includes/db.php");
$err = $msg = "";
if($_SERVER['REQUEST_METHOD']=='POST'){
  $card = preg_replace('/\D/', '', $_POST['card']); // Only digits
  $exp = trim($_POST['exp']);
  $cvv = preg_replace('/\D/', '', $_POST['cvv']);
  if(strlen($card)!=16 || strlen($cvv)!=3 || !$exp) $err="Invalid card details!";
  else {
    mysqli_query($conn,"UPDATE bookings SET payment_status='paid' WHERE id=$booking_id AND user_id=".$_SESSION['user_id']);
    $msg="Payment successful! Your booking is confirmed.";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Payment | CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body {background:#181818;font-family:'Roboto',Arial,sans-serif;}
    .pay-wrap {max-width:420px;background:#232323;margin:3.7rem auto 1rem auto;padding:2.8rem 2rem 2.3rem 2rem;border-radius:1.5rem;box-shadow:0 12px 48px #000b;}
    .payment-heading {color:#e50914;font-family:'Montserrat',sans-serif;font-weight:900;font-size:2.2rem;text-align:center;margin-bottom:1.8rem;}
    .card-icons {margin-bottom:1.2rem;text-align:center;}
    .card-icons i {font-size:2.2rem;color:#ececec;margin:0 .19rem;}
    label {font-weight:700;letter-spacing:.2px;color:#fff;}
    .form-control,.form-select {background:#181818!important;border:none;color:#fff;font-size:1.07rem;}
    .form-control:focus {background:#111;border-color:#e50914;box-shadow:0 0 0 0.1rem #e5091488;}
    .btn-main {background:#e50914;color:#fff;font-weight:700;font-size:1.09rem;letter-spacing:1px;border-radius:.7rem;box-shadow:0 4px 24px #e5091410;}
    .btn-main:hover {background:#b0060f;}
    .secure-badge {background:#333;color:#f2c51d;padding:.19rem .6rem;border-radius:1rem;font-size:.97rem;display:inline-flex;align-items:center;gap:.36rem;margin-bottom:.7rem;}
    .pay-success {text-align:center;margin:2.7rem auto 1rem;}
    .checkmark {display:inline-block;width:65px;height:65px;border-radius:50%;background:#21cf5b44;margin-bottom:1rem;}
    .checkmark:after {content:'\f633';font-family:"bootstrap-icons";color:#21cf5b;font-size:2.7rem;line-height:65px;display:block;}
    .pay-success .big {font-size:1.35rem;font-weight:700;color:#21cf5b;}
    .pay-success .sm {font-size:1.05rem;color:#bbb;}
    @media (max-width:600px){.pay-wrap{max-width:99vw;padding:1.7rem .3rem 1.2rem .3rem;}}
  </style>
</head>
<body>
<div class="pay-wrap">
  <div class="payment-heading">Payment</div>
  <div class="card-icons">
    <i class="bi bi-credit-card"></i>
    <i class="bi bi-cc-visa" style="color:#0156b7"></i>
    <i class="bi bi-cc-mastercard" style="color:#eb001b"></i>
    <i class="bi bi-cc-amex" style="color:#2e77bb"></i>
    <i class="bi bi-shield-lock" style="color:#e50914"></i>
  </div>
  <div class="secure-badge"><i class="bi bi-lock-fill"></i> Secure SSL 128-bit</div>
  <?php if($msg): ?>
    <div class="pay-success">
      <span class="checkmark"></span>
      <div class="big mb-1">Payment Successful!</div>
      <div class="sm mb-2">Your booking is confirmed.<br>Enjoy your movie!</div>
      <a href="my-bookings.php" class="btn btn-main px-4">Go to My Bookings</a>
    </div>
  <?php else: ?>
    <?php if($err): ?><div class="alert alert-danger"><?= $err ?></div><?php endif; ?>
    <form method="POST" autocomplete="off" class="mt-2">
      <div class="mb-3">
        <label>Card Number</label>
        <input type="text" name="card" class="form-control" maxlength="16" pattern="\d{16}" required autocomplete="off" placeholder="1234 5678 9012 3456">
      </div>
      <div class="mb-3 d-flex gap-2">
        <div class="flex-fill">
          <label>Expiry</label>
          <input type="month" name="exp" class="form-control" required>
        </div>
        <div style="width:90px;">
          <label>CVV</label>
          <input type="text" name="cvv" class="form-control" maxlength="3" pattern="\d{3}" required autocomplete="off" placeholder="123">
        </div>
      </div>
      <button class="btn btn-main w-100 py-2 mt-2" type="submit"><i class="bi bi-credit-card-2-back me-2"></i>Pay Now</button>
    </form>
    <div class="text-center mt-3" style="color:#888;font-size:.98rem;">
      <i class="bi bi-shield-lock"></i> Your payment is 100% secure. Demo only.
    </div>
  <?php endif; ?>
</div>
</body>
</html>
