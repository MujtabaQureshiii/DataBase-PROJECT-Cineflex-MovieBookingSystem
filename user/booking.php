<?php
session_start();
require_once("../includes/db.php");
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$show_id = isset($_GET['show_id']) ? intval($_GET['show_id']) : 0;
if (!$show_id) die("Invalid show!");

$show = mysqli_fetch_assoc(mysqli_query($conn, "SELECT s.*, m.title as movie_title, t.name as tname, t.city, s.show_time FROM shows s 
    LEFT JOIN movies m ON s.movie_id=m.id 
    LEFT JOIN theaters t ON s.theater_id=t.id 
    WHERE s.id=$show_id"));
if (!$show) die("Show not found!");

$booked_seats = [];
$res = mysqli_query($conn, "SELECT seats FROM bookings WHERE show_id=$show_id AND status='confirmed'");
while ($row = mysqli_fetch_assoc($res)) $booked_seats = array_merge($booked_seats, explode(',', $row['seats']));
$booked_seats = array_map('trim', $booked_seats);

$seat_prices = [
  'Gold'     => $show['price'],
  'Platinum' => $show['price'] + 200,
  'Box'      => $show['price'] + 500
];
$default_class = 'Gold';
$selected_class = isset($_POST['seat_class']) ? $_POST['seat_class'] : $default_class;
$seat_price = $seat_prices[$selected_class];
$kid_discount = 0.60;

$rows = ['A', 'B', 'C', 'D'];
$cols = range(1, 11);

$msg = $err = "";
$ticket_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $seats = isset($_POST['seats']) ? trim($_POST['seats']) : '';
  $seat_types = isset($_POST['seat_types']) ? $_POST['seat_types'] : [];
  $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) : '';
  $card = isset($_POST['card']) ? preg_replace('/\D/', '', $_POST['card']) : '';
  $exp = isset($_POST['exp']) ? trim($_POST['exp']) : '';
  $cvv = isset($_POST['cvv']) ? preg_replace('/\D/', '', $_POST['cvv']) : '';
  $class = isset($_POST['seat_class']) ? $_POST['seat_class'] : $default_class;
  $user_name = $_SESSION['user_name'] ?? 'CineFlex User';

  if (!$seats) $err = "Please select at least 1 seat!";
  elseif (!$email) $err = "Please enter a valid email!";
  elseif (strlen($card) != 16 || strlen($cvv) != 3 || !$exp) $err = "Enter valid card details!";
  else {
    $seatArr = explode(',', $seats);
    $already = false;
    foreach ($seatArr as $seat) {
      if (in_array($seat, $booked_seats)) {
        $already = true;
        break;
      }
    }
    if ($already) $err = "Some seats already booked! Please refresh.";
    else {
      $user_id = $_SESSION['user_id'];
      $seat_types_str = mysqli_real_escape_string($conn, json_encode($seat_types));
      $sql = "INSERT INTO bookings (user_id, show_id, seats, seat_class, seat_types, booking_date, status, payment_status)
        VALUES ($user_id, $show_id, '" . mysqli_real_escape_string($conn, $seats) . "',
        '".mysqli_real_escape_string($conn, $class)."', '$seat_types_str', NOW(), 'confirmed', 'paid')";
      if (mysqli_query($conn, $sql)) {
        $msg = "Booking & Payment Successful! Enjoy your movie.";
        $ticket_data = [
          'movie_name'  => $show['movie_title'],
          'theater'     => $show['tname'],
          'seat'        => $seats,
          'seat_class'  => $class,
          'date'        => date('Y/m/d', strtotime($show['show_time'])),
          'showtime'    => date('h:i A', strtotime($show['show_time'])),
          'barcode'     => rand(100000000,999999999),
          'user_name'   => $user_name,
        ];
      } else {
        $err = "Database Error: " . mysqli_error($conn);
      }
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>CineFlex | Book & Pay</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
  <!-- Ticket PDF libraries -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js"></script>
 <style>
  /* ======== Booking Form Card & Elements (Original Style) ======== */
body {
  background: #181818 !important;
  color: #fff;
  font-family: 'Roboto', Arial, sans-serif;
}
.main-card {
  background: #232323;
  border-radius: 1.25rem;
  box-shadow: 0 7px 32px #0008;
  padding: 2.2rem 1.5rem 1.7rem 1.5rem;
  max-width: 1150px;
  margin: 40px auto 0 auto;
}
.section-title {
  font-family: 'Montserrat', sans-serif;
  font-size: 1.25rem;
  font-weight: 900;
  color: #fff;
  margin-bottom: 1.18rem;
}
.kids-concession-note {
  color: #ffd96a;
  font-size: .99rem;
  margin-bottom: 1rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.screen-bar {
  width: 320px;
  height: 23px;
  background: linear-gradient(90deg, #e5091445 10%, #222 100%);
  border-radius: 60% 60% 0 0 / 100% 100% 0 0;
  margin: 0 auto 1.1rem auto;
  font-size: .94rem;
  color: #e50914;
  text-align: center;
  line-height: 22px;
  letter-spacing: 2px;
}
.seat-map-row {
  display: flex;
  gap: .65rem;
  justify-content: center;
}
.seat-btn {
  background: #fff;
  color: #222;
  border-radius: .5rem;
  border: 2px solid #e3e3e3;
  font-size: 1.04rem;
  font-family: monospace;
  font-weight: 700;
  min-width: 44px;
  min-height: 36px;
  margin: 0 2px;
  transition: .13s;
  outline: none;
  cursor: pointer;
  user-select: none;
  box-shadow: 0 1px 6px #0002;
  display: flex;
  align-items: center;
  justify-content: center;
}
.seat-btn.selected {
  background: #e50914 !important;
  color: #fff !important;
  border: 2px solid #e50914 !important;
}
.seat-btn.booked {
  background: #555 !important;
  color: #bbb !important;
  cursor: not-allowed;
  pointer-events: none;
  border-color: #333 !important;
}
/* Seat class highlights */
.seat-gold { background: #ffd96a !important; color: #181818 !important; border-color: #ffd96a !important; }
.seat-plat { background: #6ab0ff !important; color: #181818 !important; border-color: #6ab0ff !important; }
.seat-box  { background: #e56a6a !important; color: #fff !important; border-color: #e56a6a !important; }
/* Legends */
.legend-box {
  display: inline-block;
  width: 19px;
  height: 19px;
  border-radius: .35rem;
  vertical-align: middle;
  margin: 0 7px;
}
.legend-gold { background: #ffd96a; border: 2px solid #ffd96a; }
.legend-plat { background: #6ab0ff; border: 2px solid #6ab0ff; }
.legend-boxx { background: #e56a6a; border: 2px solid #e56a6a; }
.legend-selected { background: #e50914; border: 2px solid #e50914; box-shadow: 0 0 3px 2px #e5091460 inset; }
.legend-unavail { background: #555; border: 2px solid #333; }
.order-total {
  font-size: 1.2rem;
  font-weight: 900;
  color: #21cf5b;
}
.pay-label {
  font-weight: 700;
  font-size: 1.07rem;
}
.form-control, .form-select {
  background: #fff !important;
  border: 1.5px solid #e0e0e0;
  color: #232323;
  font-size: 1.06rem;
  transition: background .11s;
  border-radius: .4rem !important;
}
input.form-control,
select.form-select,
textarea.form-control {
  color: #232323 !important;
  caret-color: #e50914;
}
.form-control:focus {
  background: #fff;
  border-color: #e50914;
  box-shadow: 0 0 0 0.09rem #e5091477;
}
.form-control::placeholder {
  color: #9a9a9a;
  opacity: 1;
}
.btn-main {
  background: #e50914;
  color: #fff;
  font-weight: 700;
  font-size: 1.09rem;
  letter-spacing: .7px;
  border-radius: .7rem;
}
.btn-main:hover {
  background: #b0060f;
}
@media (max-width:700px) {
  .main-card { padding: 1rem .2rem; }
  .screen-bar { width: 98vw; }
}

/* =================== Ticket Preview Styling (Boarding Pass Look) =================== */
.ticket-preview-container {
  display:flex;justify-content:center;margin:30px 0 35px 0;
}
.ticket-card {
  width:650px; max-width:97vw; background:#fff; border-radius:18px;
  box-shadow:0 4px 32px #0002; color:#14304e;
  border:2.2px dashed #345;
  padding:0; margin:0; position:relative; font-family:sans-serif;
  overflow:hidden;
}
.ticket-card .ticket-header {
  background:#14213d; color:#fff; padding:13px 35px 11px 35px;
  display:flex; align-items:center; justify-content:space-between;
  font-family:'Montserrat',sans-serif;font-size:2.2rem;font-weight:900; letter-spacing:2px;
}
.ticket-card .ticket-header span {
  font-size:1.13rem;font-family:monospace;letter-spacing:1px;opacity:.82;
}
.ticket-body { display:flex; align-items:center; }
.ticket-left {
  flex:0 0 160px; text-align:center; padding:22px 16px 0 22px;
}
.ticket-left i {
  font-size:46px;color:#2d405e;margin-bottom:8px;margin-top:10px;opacity:.7;
}
.ticket-left .theater {font-size:1.12rem; font-weight:600; letter-spacing:1.3px;}
.ticket-left .seat {font-size:1.08rem;}
.ticket-left .date {margin-top:8px;color:#5c5c5c;font-size:1.01rem;}
.ticket-divider {
  border-left:2px dashed #2d405e30;height:110px;margin:0 8px 0 8px;align-self:center;
}
.ticket-main {
  flex:1;padding:22px 24px 0 12px;min-width:230px;display:flex;flex-direction:column;gap:7px;
}
.ticket-main .moviename {font-size:1.7rem;font-weight:800; color:#14213d;}
.ticket-main .username {font-size:1.01rem;font-weight:700;margin-bottom:5px;color:#c93836;}
.ticket-main .showtime {font-size:1.09rem;font-weight:600;color:#222;}
.ticket-main .detailrow {font-size:1.09rem; color:#1b2743;}
.ticket-main .barcode {margin-top:7px;}
.ticket-main .barcode canvas {display:block;margin-top:4px;}
@media (max-width:750px) {
  .ticket-card{width:99vw;padding:0;}
  .main-card {padding:.6rem .1rem;}
}

 </style>
</head>
<body>
  <?php include("header.php"); ?>

  <div class="container py-4">
    <div class="main-card">
      <h2 class="section-title mb-3">Book & Pay</h2>
      <div class="kids-concession-note mb-2"><i class="bi bi-balloon-heart-fill"></i> Special price for kids (aged 3–12): <span id="kids-price" style="font-weight:800;"></span> per seat!</div>
      <?php if ($err): ?><div class="alert alert-danger"><?= $err ?></div><?php endif; ?>

      <?php if ($msg): ?>
        <div class="alert alert-success"><?= $msg ?></div>
        <div class="ticket-preview-container">
          <div class="ticket-card" id="ticketCard">
            <div class="ticket-header">
              <div>CineFlex</div>
              <span>MOVIE TICKET</span>
            </div>
            <div class="ticket-body">
              <div class="ticket-left">
                <i class="bi bi-film"></i>
                <div class="theater">Theater: <b><?= htmlspecialchars($ticket_data['theater']) ?></b></div>
                <div class="seat">Seat: <b><?= htmlspecialchars($ticket_data['seat']) ?></b></div>
                <div class="date">Date: <?= htmlspecialchars($ticket_data['date']) ?></div>
              </div>
              <div class="ticket-divider"></div>
              <div class="ticket-main">
                <div class="moviename"><?= htmlspecialchars($ticket_data['movie_name']) ?></div>
                <div class="username">Name: <?= htmlspecialchars($ticket_data['user_name']) ?></div>
                <div class="showtime">Showtime: <?= htmlspecialchars($ticket_data['showtime']) ?></div>
                <div class="detailrow">Class: <b><?= htmlspecialchars($ticket_data['seat_class']) ?></b></div>
                <div class="barcode">
                  <canvas id="barcodeCanvas" width="180" height="34"></canvas>
                  <div style="font-size:.92rem;color:#878787;text-align:right;"><?= htmlspecialchars($ticket_data['barcode']) ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center my-4">
          <button id="downloadTicketBtn" class="btn btn-success btn-lg">
            <i class="bi bi-ticket-perforated"></i> Download Ticket
          </button>
        </div>
      <?php endif; ?>

      <?php if (!$msg): ?>
        <form method="POST" id="bookingForm" autocomplete="off">
          <!-- Seat Class Dropdown -->
          <div class="mb-3">
            <label class="pay-label mb-1">Choose Seat Class</label>
            <select class="form-select" name="seat_class" id="seatClass" required style="max-width:220px;">
              <?php foreach($seat_prices as $cls => $price): ?>
                <option value="<?= $cls ?>" <?= $cls == $selected_class ? "selected" : "" ?>>
                  <?= $cls ?> (Rs <?= $price ?> per seat)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="screen-bar mb-3">SCREEN</div>
          <div>
            <?php foreach ($rows as $r):
              $seat_class_type = ($r == 'A' || $r == 'B') ? 'gold' : (($r == 'C') ? 'plat' : 'box');
            ?>
              <div class="seat-map-row mb-1">
                <?php foreach ($cols as $c):
                  $seat = $r . $c;
                  $is_booked = in_array($seat, $booked_seats);
                  $seat_class = 'seat-btn seat-' . $seat_class_type . ($is_booked ? ' booked' : '');
                ?>
                  <button type="button"
                    class="<?= $seat_class ?>"
                    data-seat="<?= $seat ?>" <?= $is_booked ? 'disabled' : '' ?>>
                    <?= $seat ?>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
          </div>
          <input type="hidden" name="seats" id="selectedSeats" required>
          <div id="seatTypeRow" class="mt-3" style="display:none;"></div>
          <div class="d-flex gap-3 mt-3 mb-2 flex-wrap">
            <span><span class="legend-box legend-gold"></span> Gold</span>
            <span><span class="legend-box legend-plat"></span> Platinum</span>
            <span><span class="legend-box legend-boxx"></span> Box</span>
            <span><span class="legend-box legend-selected"></span> Selected</span>
            <span><span class="legend-box legend-unavail"></span> Unavailable</span>
          </div>
          <div style="font-size:1.11rem;margin-top:8px;">
            <b>Selected:</b>
            <span id="seat-list" style="color:#e50914;font-weight:600;">None</span>
            <span class="ms-3"><b>Total:</b> <span id="seat-total" style="color:#21cf5b;font-weight:900;">Rs 0</span></span>
          </div>
          <hr class="my-4" style="border-color:#333;">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="pay-label">Your Email Address</label>
              <input type="email" name="email" id="user_email" class="form-control" required placeholder="your@email.com">
            </div>
            <div class="col-md-6 mb-3">
              <label class="pay-label">Card Number</label>
              <input type="text" name="card" class="form-control" maxlength="16" pattern="\d{16}" required autocomplete="off" placeholder="1234 5678 9012 3456">
            </div>
            <div class="col-6 mb-3">
              <label class="pay-label">Expiry</label>
              <input type="month" name="exp" class="form-control" required>
            </div>
            <div class="col-6 mb-3">
              <label class="pay-label">CVV</label>
              <input type="text" name="cvv" class="form-control" maxlength="3" pattern="\d{3}" required autocomplete="off" placeholder="123">
            </div>
          </div>
          <input type="text" name="user_name" id="user_name" class="form-control d-none" value="<?= $_SESSION['user_name'] ?? 'CineFlex User' ?>" />
          <button type="submit" class="btn btn-main btn-lg w-100 mt-3"><i class="bi bi-credit-card-2-back me-2"></i>Pay & Book Now</button>
          <div class="text-center mt-3" style="color:#c6bfb2;font-size:.97rem;">
            <i class="bi bi-lock"></i> We do <b>not</b> store your card data.<br>
            For project demo only.
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
  <?php include("footer.php"); ?>

<script>
let selected = [];
const maxSeats = 5;
let seatPrices = <?php echo json_encode($seat_prices); ?>;
let seatPrice = seatPrices["<?= $selected_class ?>"];
let kidDiscount = <?= $kid_discount ?>;
let kidsPrice = Math.round(seatPrice * kidDiscount);

function updateKidsPrice() {
  seatPrice = seatPrices[document.getElementById('seatClass').value];
  kidsPrice = Math.round(seatPrice * kidDiscount);
  document.getElementById('kids-price').innerText = "Rs " + kidsPrice;
}
updateKidsPrice();

document.getElementById('seatClass').addEventListener('change', function() {
  updateKidsPrice();
  updateTotal();
});

function updateTotal() {
  let types = document.querySelectorAll('.seat-type-select');
  let kidCount = 0, adultCount = 0;
  types.forEach(sel => sel.value === "Kid" ? kidCount++ : adultCount++);
  let total = kidCount * kidsPrice + adultCount * seatPrice;
  document.getElementById('seat-total').innerText = 'Rs ' + total;
}

document.querySelectorAll('.seat-btn:not(.booked)').forEach(btn => {
  btn.addEventListener('click', function() {
    const seat = this.dataset.seat;
    if (selected.includes(seat)) {
      selected = selected.filter(s => s !== seat);
      this.classList.remove('selected');
    } else {
      if (selected.length >= maxSeats) return alert("Max " + maxSeats + " seats allowed.");
      selected.push(seat);
      this.classList.add('selected');
    }
    document.getElementById('selectedSeats').value = selected.join(',');
    document.getElementById('seat-list').innerText = selected.length ? selected.join(', ') : "None";

    let seatTypeRow = document.getElementById('seatTypeRow');
    seatTypeRow.innerHTML = '';
    if(selected.length){
      seatTypeRow.style.display = "block";
      selected.forEach(function(seat, idx){
        seatTypeRow.innerHTML += `
          <div class="mb-2 d-flex align-items-center gap-2">
            <input type="hidden" name="seat_types[${seat}][seat]" value="${seat}">
            <label style="min-width:54px;font-weight:600;">${seat}</label>
            <select class="form-select seat-type-select" style="width:130px;display:inline;" name="seat_types[${seat}][type]" onchange="updateTotal()" required>
              <option value="Adult">Adult</option>
              <option value="Kid">Kid (3–12 yrs)</option>
            </select>
          </div>
        `;
      });
    } else {
      seatTypeRow.style.display = "none";
    }
    updateTotal();
  });
});

document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
  if (selected.length === 0) {
    alert("Please select at least 1 seat!");
    e.preventDefault();
  }
});

updateTotal();
</script>

<?php
// ---- EmailJS send on success ----
if ($msg && isset($_POST['seat_types'])) {
  $kidCount = 0; $adultCount = 0;
  foreach($_POST['seat_types'] as $t) ($t['type']=="Kid") ? $kidCount++ : $adultCount++;
  echo "<script>
    window.bookingEmailData = " . json_encode([
      'email'      => $_POST['email'] ?? '',
      'name'       => $_SESSION['user_name'] ?? 'CineFlex User',
      'movie'      => $show['movie_title'],
      'seats'      => $_POST['seats'] ?? '',
      'showtime'   => date('d M, Y | h:i a', strtotime($show['show_time'])),
      'seat_class' => $_POST['seat_class'] ?? $default_class,
      'kid_count'  => $kidCount,
      'adult_count'=> $adultCount
    ]) . ";
    window.ticketData = " . json_encode($ticket_data) . ";
  </script>";
}
?>

<script>
emailjs.init("fJv_wLzROIoT69Odd");
function sendBookingEmail(email, name, movie, seats, showtime, seatClass, kidCount, adultCount) {
  emailjs.send("service_begmijg", "template_tlkd8vo", {
    email: email,
    name: name,
    movie: movie,
    seats: seats,
    showtime: showtime,
    seat_class: seatClass,
    kid_count: kidCount,
    adult_count: adultCount
  }).then(function(response) {
    alert("Booking confirmation email sent! Check your inbox.");
  }, function(error) {
    alert("Email sending failed: " + JSON.stringify(error));
  });
}
document.addEventListener("DOMContentLoaded", function() {
  // Send email after booking
  if (window.bookingEmailData) {
    sendBookingEmail(
      window.bookingEmailData.email,
      window.bookingEmailData.name,
      window.bookingEmailData.movie,
      window.bookingEmailData.seats,
      window.bookingEmailData.showtime,
      window.bookingEmailData.seat_class,
      window.bookingEmailData.kid_count,
      window.bookingEmailData.adult_count
    );
  }

  // Barcode on ticket HTML
  if (window.ticketData) {
    JsBarcode(document.getElementById("barcodeCanvas"), String(window.ticketData.barcode), {
      format:"CODE128", width:2, height:34, displayValue:false
    });

    document.getElementById('downloadTicketBtn').onclick = function() {
      const d = window.ticketData;
      const { jsPDF } = window.jspdf;
      var doc = new jsPDF({orientation:'landscape', unit:'px', format:[410,140]});
      doc.setFillColor(255,255,255); doc.rect(0,0,410,140,'F');
      doc.setDrawColor(34,56,90); doc.setLineWidth(1.2); doc.rect(6,6,398,128);

      // Header
      doc.setFillColor(20,33,61); doc.rect(0, 18, 410, 27, 'F');
      doc.setFont('helvetica','bold'); doc.setTextColor(255,255,255); doc.setFontSize(23);
      doc.text('CineFlex', 28, 38);
      doc.setFontSize(17); doc.text('MOVIE TICKET', 390, 38, {align:'right'});
      doc.setFontSize(13); doc.text('NO. 00000000', 25, 53);

      // Movie info
      doc.setFont('helvetica','bold'); doc.setTextColor(28,40,60); doc.setFontSize(22);
      doc.text(String(d.movie_name), 25, 78);
      doc.setFont('helvetica','normal'); doc.setFontSize(12);
      doc.text('User: ' + d.user_name, 25, 92);
      doc.setFontSize(13); doc.setTextColor(44,62,90);
      doc.text('THEATER:', 25, 112); doc.text(String(d.theater), 90, 112);
      doc.text('SEAT:', 200, 112); doc.text(String(d.seat), 245, 112);
      doc.text('CLASS:', 25, 128); doc.text(String(d.seat_class), 80, 128);
      doc.text('DATE:', 200, 128); doc.text(String(d.date), 245, 128);
      doc.text('SHOWTIME:', 300, 128); doc.text(String(d.showtime), 370, 128);

      doc.setFont('helvetica','bold'); doc.setTextColor(27,59,97); doc.setFontSize(26);
      doc.text("S:"+String(d.seat), 360, 72, {align:'right'});

      let canvas = document.createElement('canvas');
      JsBarcode(canvas, String(d.barcode), {format:"CODE128", width:1.9, height:26, displayValue:false});
      let imgData = canvas.toDataURL("image/png");
      doc.addImage(imgData, "PNG", 245, 90, 130, 28);

      doc.setFontSize(10); doc.setTextColor(88,88,88);
      doc.text(String(d.barcode), 340, 126, {align:'right'});

      doc.save('ticket-'+d.movie_name.replace(/\s/g,'_')+'.pdf');
    }
  }
});
</script>
</body>
</html>
