<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit;
}
require_once("../includes/db.php");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if(!$id) die("Invalid Movie ID!");

$msg = $err = "";

// Fetch Movie
$movie = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM movies WHERE id=$id"));
if(!$movie) die("Movie not found!");

// Handle cast delete
if(isset($_GET['delcast']) && is_numeric($_GET['delcast'])) {
    $cast_id = intval($_GET['delcast']);
    mysqli_query($conn, "UPDATE movie_cast SET status='deleted' WHERE id=$cast_id AND movie_id=$id");
    header("Location: movies-edit.php?id=$id");
    exit;
}

// Fetch cast
$cast = [];
$cast_res = mysqli_query($conn, "SELECT * FROM movie_cast WHERE movie_id=$id AND status='active'");
while($row = mysqli_fetch_assoc($cast_res)) $cast[] = $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $desc = mysqli_real_escape_string($conn, trim($_POST['description']));
    $genre = mysqli_real_escape_string($conn, trim($_POST['genre']));
    $duration = mysqli_real_escape_string($conn, trim($_POST['duration']));
    $rating = floatval($_POST['rating']);
    $is_trending   = isset($_POST['is_trending'])   ? 1 : 0;
    $is_nowshowing = isset($_POST['is_nowshowing']) ? 1 : 0;
    $is_toprated   = isset($_POST['is_toprated'])   ? 1 : 0;

    // Poster Upload (optional)
    $poster_path = $movie['poster'];
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $poster_name = "poster_" . time() . "_" . rand(1000,9999) . "." . $ext;
        $poster_dest = "../uploads/posters/$poster_name";
        if (move_uploaded_file($_FILES['poster']['tmp_name'], $poster_dest)) {
            $poster_path = "uploads/posters/$poster_name";
        } else {
            $err .= "Poster upload failed! ";
        }
    }

    // Trailer Upload (optional)
    $trailer_path = $movie['trailer'];
    if (isset($_FILES['trailer']) && $_FILES['trailer']['error'] == 0) {
        $ext = pathinfo($_FILES['trailer']['name'], PATHINFO_EXTENSION);
        $trailer_name = "trailer_" . time() . "_" . rand(1000,9999) . "." . $ext;
        $trailer_dest = "../uploads/trailers/$trailer_name";
        if (move_uploaded_file($_FILES['trailer']['tmp_name'], $trailer_dest)) {
            $trailer_path = "uploads/trailers/$trailer_name";
        } else {
            $err .= "Trailer upload failed! ";
        }
    }

    // Update Movie
    if (!$err) {
        $sql = "UPDATE movies SET
                title='$title', description='$desc', genre='$genre', duration='$duration',
                poster='$poster_path', trailer='$trailer_path', rating='$rating',
                is_trending=$is_trending, is_nowshowing=$is_nowshowing, is_toprated=$is_toprated
                WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            // ---- Existing cast update ----
            if(isset($_POST['cast_existing'])) {
                foreach($_POST['cast_existing'] as $castid=>$castdata) {
                    $cname = mysqli_real_escape_string($conn, trim($castdata['name']));
                    $crole = mysqli_real_escape_string($conn, trim($castdata['role']));
                    $cimg_path = $castdata['image_old'];
                    // If new image uploaded
                    if(isset($_FILES['cast_image_'.$castid]) && $_FILES['cast_image_'.$castid]['error'] == 0) {
                        $ext = pathinfo($_FILES['cast_image_'.$castid]['name'], PATHINFO_EXTENSION);
                        $img_name = "cast_" . time() . "_$castid" . rand(1000,9999) . "." . $ext;
                        $img_dest = "../uploads/cast/$img_name";
                        if (move_uploaded_file($_FILES['cast_image_'.$castid]['tmp_name'], $img_dest)) {
                            $cimg_path = "uploads/cast/$img_name";
                        }
                    }
                    mysqli_query($conn, "UPDATE movie_cast SET name='$cname', role='$crole', image='$cimg_path' WHERE id=$castid");
                }
            }
            // ---- New cast add (up to 5) ----
            for ($i=1; $i<=5; $i++) {
                $cname = trim($_POST["cast_name_$i"] ?? '');
                $crole = trim($_POST["cast_role_$i"] ?? '');
                $cimg_path = "";
                if ($cname) {
                    if (isset($_FILES["cast_image_$i"]) && $_FILES["cast_image_$i"]['error'] == 0) {
                        $ext = pathinfo($_FILES["cast_image_$i"]['name'], PATHINFO_EXTENSION);
                        $img_name = "cast_" . time() . "_$i_" . rand(1000,9999) . "." . $ext;
                        $img_dest = "../uploads/cast/$img_name";
                        if (move_uploaded_file($_FILES["cast_image_$i"]['tmp_name'], $img_dest)) {
                            $cimg_path = "uploads/cast/$img_name";
                        }
                    }
                    mysqli_query($conn, "INSERT INTO movie_cast (movie_id, name, image, role) VALUES ($id, '".mysqli_real_escape_string($conn, $cname)."', '$cimg_path', '".mysqli_real_escape_string($conn, $crole)."')");
                }
            }

            $msg = "Movie and cast updated successfully!";
            // Reload data
            $movie = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM movies WHERE id=$id"));
            $cast = [];
            $cast_res = mysqli_query($conn, "SELECT * FROM movie_cast WHERE movie_id=$id AND status='active'");
            while($row = mysqli_fetch_assoc($cast_res)) $cast[] = $row;
        } else {
            $err = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Movie - Admin | CineFlex</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .edit-movie-panel {
      width: 100%;
      max-width: 800px;
      margin: 2.5rem auto;
      background: #232323;
      border-radius: 1.2rem;
      padding: 2.4rem 2.1rem 2rem 2.1rem;
      box-shadow: 0 5px 32px #0008;
    }
    .edit-movie-panel h2 {
      font-family: 'Montserrat',sans-serif;
      color: #e50914;
      font-weight: 900;
      margin-bottom: 2rem;
      text-align:center;
    }
    label {font-weight:700;}
    .msg, .error {margin-bottom:.9rem;}
    .cast-label {font-size:1.13rem;font-weight:700;color:#ffd96a;}
    .cast-row {background: #252525; border-radius:.7rem; padding: .85rem .8rem; margin-bottom: .55rem;}
    .cast-img-sm {width:52px;height:52px;object-fit:cover;border-radius:.5rem;border:2px solid #333;background:#181818;}
    .remove-cast-btn {color:#e50914;text-decoration:none;font-size:1.1rem;font-weight:700;}
    .remove-cast-btn:hover {color:#fff;}
    @media (max-width: 700px) {
      .edit-movie-panel {max-width:99vw; padding:1.2rem .5rem 1rem .5rem;}
      .edit-movie-panel h2 {font-size:1.3rem;}
    }
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
  <div class="main d-flex justify-content-center align-items-center" style="min-height:85vh;">
    <div class="edit-movie-panel">
      <h2>Edit Movie</h2>
      <?php if($msg): ?><div class="msg bg-success-subtle text-success-emphasis p-2 rounded mb-2"><?= $msg ?></div><?php endif; ?>
      <?php if($err): ?><div class="error bg-danger-subtle text-danger-emphasis p-2 rounded mb-2"><?= $err ?></div><?php endif; ?>
      <form method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="<?=htmlspecialchars($movie['title'])?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Genre</label>
            <input type="text" name="genre" class="form-control" value="<?=htmlspecialchars($movie['genre'])?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Duration</label>
            <input type="text" name="duration" class="form-control" value="<?=htmlspecialchars($movie['duration'])?>" required>
          </div>
          <div class="col-md-6 mb-3">
            <label>Rating (0-10)</label>
            <input type="number" name="rating" class="form-control" min="0" max="10" step="0.1" value="<?=htmlspecialchars($movie['rating'])?>" required>
          </div>
          <div class="col-12 mb-3">
            <label>Description</label>
            <textarea name="description" rows="3" class="form-control" required><?=htmlspecialchars($movie['description'])?></textarea>
          </div>
          <div class="col-md-6 mb-3">
            <label>Poster Image</label>
            <input type="file" name="poster" accept="image/*" class="form-control">
            <?php if($movie['poster']): ?>
              <img src="../<?=htmlspecialchars($movie['poster'])?>" alt="Poster" class="mt-2" style="width:90px;border-radius:.3rem;">
            <?php endif; ?>
            <div style="color:#bbb;font-size:.95rem;">Leave blank to keep old.</div>
          </div>
          <div class="col-md-6 mb-3">
            <label>Trailer Video</label>
            <input type="file" name="trailer" accept="video/*" class="form-control">
            <?php if($movie['trailer']): ?>
              <video src="../<?=htmlspecialchars($movie['trailer'])?>" class="mt-2" style="width:110px;max-height:80px;" controls></video>
            <?php endif; ?>
            <div style="color:#bbb;font-size:.95rem;">Leave blank to keep old.</div>
          </div>
          <div class="col-12 mb-3">
            <label style="font-weight:700;">Movie Flags:</label>
            <div class="form-check form-check-inline ms-2">
              <input class="form-check-input" type="checkbox" name="is_trending" value="1" id="flagTrending" <?=$movie['is_trending']?'checked':''?>>
              <label class="form-check-label" for="flagTrending">Trending</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="is_nowshowing" value="1" id="flagNow" <?=$movie['is_nowshowing']?'checked':''?>>
              <label class="form-check-label" for="flagNow">Now Showing</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="is_toprated" value="1" id="flagTop" <?=$movie['is_toprated']?'checked':''?>>
              <label class="form-check-label" for="flagTop">Top Rated</label>
            </div>
          </div>
          <!-- Existing Cast Edit -->
          <div class="col-12 mb-4">
            <label class="cast-label"><i class="bi bi-person-vcard"></i> Edit Cast Members</label>
            <?php if(count($cast)): foreach($cast as $c): ?>
            <div class="row cast-row align-items-center mb-2">
              <div class="col-md-3 mb-2 mb-md-0">
                <input type="text" name="cast_existing[<?=$c['id']?>][name]" class="form-control" value="<?=htmlspecialchars($c['name'])?>">
              </div>
              <div class="col-md-3 mb-2 mb-md-0">
                <input type="text" name="cast_existing[<?=$c['id']?>][role]" class="form-control" value="<?=htmlspecialchars($c['role'])?>">
              </div>
              <div class="col-md-3 mb-2 mb-md-0">
                <?php if($c['image']): ?>
                <img src="../<?=htmlspecialchars($c['image'])?>" class="cast-img-sm me-2 mb-1">
                <?php endif; ?>
                <input type="file" name="cast_image_<?=$c['id']?>" accept="image/*" class="form-control">
                <input type="hidden" name="cast_existing[<?=$c['id']?>][image_old]" value="<?=htmlspecialchars($c['image'])?>">
              </div>
              <div class="col-md-3 mb-2 mb-md-0 d-flex align-items-center gap-2">
                <a href="movies-edit.php?id=<?=$id?>&delcast=<?=$c['id']?>" class="remove-cast-btn" onclick="return confirm('Remove this cast member?')"><i class="bi bi-trash3"></i> Remove</a>
              </div>
            </div>
            <?php endforeach; else: ?>
              <div class="text-secondary ms-2 mb-2">No cast added yet.</div>
            <?php endif; ?>
          </div>
          <!-- New Cast Add -->
          <div class="col-12 mb-3">
            <label class="cast-label"><i class="bi bi-person-plus"></i> Add New Cast (max 5)</label>
            <?php for($i=1; $i<=5; $i++): ?>
            <div class="row cast-row align-items-center mb-2">
              <div class="col-md-4 mb-2 mb-md-0">
                <input type="text" name="cast_name_<?=$i?>" class="form-control" placeholder="Cast Name <?=$i?>">
              </div>
              <div class="col-md-4 mb-2 mb-md-0">
                <input type="text" name="cast_role_<?=$i?>" class="form-control" placeholder="Role (e.g. Lead)">
              </div>
              <div class="col-md-4">
                <input type="file" name="cast_image_<?=$i?>" accept="image/*" class="form-control">
              </div>
            </div>
            <?php endfor; ?>
          </div>
          <div class="col-12 mb-2">
            <button type="submit" class="btn btn-main w-100 py-2"><i class="bi bi-pencil-square"></i> Update Movie</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
