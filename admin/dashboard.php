<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../user/login.php");
    exit;
}
require_once("../includes/db.php");

// Stats
$total_movies = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM movies"))['c'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings"))['c'];

// Movies by Genre (Pie Chart)
$genres = [];
$genreCounts = [];
$gq = mysqli_query($conn, "SELECT genre, COUNT(*) as c FROM movies GROUP BY genre ORDER BY c DESC");
while ($row = mysqli_fetch_assoc($gq)) {
    $genres[] = $row['genre'];
    $genreCounts[] = $row['c'];
}

// Users per month (last 6 months, Bar Chart)
$months = [];
$monthCounts = [];
for ($i = 5; $i >= 0; $i--) {
    $label = date('M Y', strtotime("-$i month"));
    $start = date('Y-m-01', strtotime("-$i month"));
    $end = date('Y-m-t', strtotime("-$i month"));
    $q = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE reg_date BETWEEN '$start 00:00:00' AND '$end 23:59:59'");
    $count = mysqli_fetch_assoc($q)['c'];
    $months[] = $label;
    $monthCounts[] = $count;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - CineFlex</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: #181818;
            color: #fff;
            font-family: 'Roboto', sans-serif;
        }

        .sidebar {
            width: 220px;
            background: #181818;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 5;
            box-shadow: 2px 0 22px #0003;
        }

        .sidebar .logo {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: #e50914;
            padding: 2.1rem 0 1.3rem 1.5rem;
        }

        .sidebar .nav {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            padding-left: 1.5rem;
        }

        .sidebar .nav-link {
            color: #fff;
            font-weight: 600;
            font-size: 1.07rem;
            padding: .6rem 0 .6rem .3rem;
            border-radius: .6rem;
            transition: .16s;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: #e50914;
            color: #fff;
        }

        .main {
            margin-left: 220px;
            padding: 2.3rem 2.7rem 1rem 2.7rem;
        }

        .top-bar {
            display: flex;
            justify-content: flex-end;
        }

        .logout-btn {
            background: #222;
            color: #e50914;
            font-weight: 600;
            padding: .45rem 1.4rem;
            border-radius: .8rem;
            border: none;
            transition: .15s;
        }

        .logout-btn:hover {
            background: #e50914;
            color: #fff;
        }

        .dashboard-cards {
            display: flex;
            gap: 2.1rem;
            flex-wrap: wrap;
            margin-top: 1.6rem;
        }

        .card-stat {
            background: #232323;
            color: #fff;
            border-radius: 1.2rem;
            padding: 2.2rem 2rem 2rem 2rem;
            box-shadow: 0 3px 18px #0007;
            min-width: 200px;
            flex: 1;
        }

        .card-stat .icon {
            font-size: 2.2rem;
            color: #e50914;
        }

        .card-stat .num {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.1rem;
            font-weight: 700;
            margin: .9rem 0;
        }

        .quick-links {
            margin-top: 3.3rem;
            display: flex;
            gap: 1.7rem;
            flex-wrap: wrap;
        }

        .qlink {
            background: #181818;
            border: 1.7px solid #e50914;
            border-radius: .9rem;
            padding: 2.1rem 2.3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 600;
            color: #e50914;
            box-shadow: 0 2px 14px #0004;
            transition: .14s;
        }

        .qlink:hover {
            background: #e50914;
            color: #fff;
            box-shadow: 0 4px 22px #e5091450;
        }

        .qlink i {
            font-size: 2.2rem;
            margin-bottom: .6rem;
        }

        

        /* Chart cards */
        .dashboard-charts {
            margin-top: 2.5rem;
        }

        .dashboard-charts .card-stat {
            padding: 1rem 1.5rem;
        }

        @media (max-width:1100px) {
            .main {
                padding: 1.1rem .8rem;
                margin-left: 0;
            }

            .sidebar {
                display: none;
            }

            .dashboard-cards {
                gap: .8rem;
            }

            .quick-links {
                gap: .6rem;
            }
        }

        @media (max-width:800px) {
            .dashboard-cards {
                flex-direction: column;
            }

            .quick-links {
                flex-direction: column;
            }
        }

        @media (max-width:600px) {
            .main {
                padding: .5rem .2rem;
            }

            .dashboard-charts {
                margin-top: 1rem;
            }
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
    <div class="main">
        <div class="top-bar">
            <form action="logout.php" method="post" style="display:inline;">
                <a href="logout.php"><button class="logout-btn" type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></a>
            </form>
        </div>
        <h1
            style="font-family:'Montserrat',sans-serif;font-weight:900;font-size:2.1rem;color:#e50914;margin-top:.6rem;">
            Admin Dashboard</h1>
        <div class="dashboard-cards">
            <div class="card-stat">
                <div class="icon"><i class="bi bi-film"></i></div>
                <div class="num"><?= $total_movies ?></div>
                <div>Total Movies</div>
            </div>
            <div class="card-stat">
                <div class="icon"><i class="bi bi-people"></i></div>
                <div class="num"><?= $total_users ?></div>
                <div>Total Users</div>
            </div>
            <div class="card-stat">
                <div class="icon"><i class="bi bi-ticket-perforated"></i></div>
                <div class="num"><?= $total_bookings ?></div>
                <div>Total Bookings</div>
            </div>
        </div>
        <div class="row dashboard-charts">
          <div class="col-md-6 mb-4">
            <div class="card-stat p-3" style="height: 370px;">
              <h5 style="color:#e50914; margin-bottom: 1.3rem;">Movies by Genre</h5>
              <div style="position:relative; width:100%; height:280px;">
                <canvas id="genreChart"></canvas>
              </div>
            </div>
          </div>

          <div class="col-md-6 mb-4">
            <div class="card-stat p-3" style="height: 370px;">
              <h5 style="color:#e50914; margin-bottom: 1.3rem;">User Registrations (Last 6 Months)</h5>
              <div style="position:relative; width:100%; height:280px;">
                <canvas id="userChart"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="quick-links">
            <a class="qlink" href="movies-add.php"><i class="bi bi-plus-circle"></i> Add Movie</a>
            <a class="qlink" href="theater-add.php"><i class="bi bi-plus-circle"></i> Add Theater</a>
            <a class="qlink" href="show-add.php"><i class="bi bi-plus-circle"></i> Add Show</a>
            <a class="qlink" href="movies.php"><i class="bi bi-film"></i> View Movies</a>
            <a class="qlink" href="theaters.php"><i class="bi bi-buildings"></i> View Theaters</a>
            <a class="qlink" href="shows.php"><i class="bi bi-clock"></i> View Shows</a>
            <a class="qlink" href="users.php"><i class="bi bi-people"></i> View Users</a>
            <a class="qlink" href="bookings.php"><i class="bi bi-ticket-perforated"></i> View Bookings</a>
        </div>


    </div>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Movies by Genre Pie Chart
        const genreChart = document.getElementById('genreChart').getContext('2d');
        new Chart(genreChart, {
            type: 'pie',
            data: {
                labels: <?= json_encode($genres) ?>,
                datasets: [{
                    data: <?= json_encode($genreCounts) ?>,
                    backgroundColor: [
                        '#bd151dff', '#1f1f1f', '#fff', '#cfb800', '#7b1fa2', '#009688', '#e64a19', '#0288d1', '#43a047', '#ffd600'
                    ]
                }]
            },
            options: {
                plugins: {
                    legend: { labels: { color: "#fff", font: { weight: "bold" } } }
                }
            }
        });

        // User Registrations Bar Chart
        const userChart = document.getElementById('userChart').getContext('2d');
        new Chart(userChart, {
            type: 'bar',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [{
                    label: 'Users',
                    data: <?= json_encode($monthCounts) ?>,
                    backgroundColor: '#e50914'
                }]
            },
            options: {
                scales: {
                    x: { ticks: { color: "#fff" } },
                    y: { ticks: { color: "#fff", beginAtZero: true } }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>
</body>

</html>