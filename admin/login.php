<?php
// Tambahkan ini di bagian atas file PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if(isset($_SESSION["username"])){
    // cek hak akses
    header("Location:dashboard.php");
    exit;
  }

require '../functions.php';
if (isset($_POST['login'])) {
    global $conn;
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Debugging
    echo "Username: $username<br>";
    echo "Password: $password<br>";

    // Query untuk memeriksa user
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        // Ambil data user
        $user = mysqli_fetch_assoc($result);
        echo "Password dari database: " . $user['password'] . "<br>"; // Debugging

        // Cek password
        if ($user['password'] === $password) {
            $_SESSION['id'] = $user['id']; // Set session
            $_SESSION['username'] = $username; // Set session
            header("Location: dashboard.php"); // Redirect ke halaman dashboard
            exit;
        } else {
            $error = "Username atau password salah";
        }
    } else {
        echo "
        <script>
            alert('Username anda salah!');
        </script>
        ";
    }
}


?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="../js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card shaow-lg p-4" style="width: 24rem;">
            <h2 class="card-title text-center mb-4">Login</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" >
                <div class="mb-3">
                    <label for="username" class="form-label">username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </div>
    </div>
</body>
</html>
