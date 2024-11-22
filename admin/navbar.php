<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<style>
    /* Applying Poppins font to navbar and footer */
    body, .navbar-brand, footer p {
        font-family: 'Poppins', sans-serif;
    }

    /* Navbar Customization */
    .curved-navbar {
        background-color: #222;
        border-radius: 0 0 25px 25px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .navbar-brand {
        font-size: 1.7rem;
        color: #fff;
        font-weight: 600;
    }
    .navbar-nav .nav-link {
        color: #fff;
    }

    /* Table Design */
    .movie-table {
        background: rgba(255, 255, 255, 0.1); /* Semi-transparent background */
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* Card shadow */
    }

    .table thead {
        background: #444;
        color: white;
    }

    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }

    .table tr {
        transition: background-color 0.3s ease;
    }

    .table tr:hover {
        background-color: rgba(255, 255, 255, 0.15); /* Subtle hover effect */
    }

    /* Footer */
    footer {
        background-color: #222;
        color: white;
        text-align: center;
        padding: 20px 0;
        margin-top: 40px;
        font-weight: 600;
    }

    /* Button styling */
    .btn {
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn:hover {
        background-color: #fff;
        color: #222;
    }

    /* Responsive table padding */
    @media (max-width: 768px) {
        .movie-table {
            padding: 15px;
        }
    }

</style>

<nav class="navbar navbar-expand-lg curved-navbar fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">StreamX</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="list.php">Daftar Film</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="music.php">Daftar Musik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="comic.php">Daftar Komik</a>
                </li>
            </ul>
            <span class="navbar-text">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><?= $_SESSION['username']?></a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="logout.php">Log Out</a></li>
                    </ul>
                </div>
            </span>
        </div>
    </div>
</nav>