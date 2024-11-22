<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="../js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body>
    <?php
    include 'navbar.php';
    ?>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="text-center">
            <h1>Selamat datang, <?php echo $_SESSION['username']; ?>!</h1>
            <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
        </div>
    </div>
</body>

</html>