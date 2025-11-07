<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "koneksi.php";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $cek = $koneksi->prepare("SELECT * FROM user WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $result = $cek->get_result();

        if ($result->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $hash = md5($password);
            $stmt = $koneksi->prepare("INSERT INTO user (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            
            if ($stmt->execute()) {
                header("Location: login.php?success=1");
                exit;
            } else {
                $error = "Gagal menyimpan data pengguna.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Akun</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #36b9cc, #1cc88a);
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: "Poppins", sans-serif;
}
.card {
  border: none;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.btn-custom {
  background-color: #1cc88a;
  color: #fff;
  border-radius: 25px;
}
.btn-custom:hover {
  background-color: #17a673;
}
</style>
</head>
<body>

<div class="card p-4" style="width: 22rem;">
  <h4 class="text-center mb-3 text-success">Register Akun Baru</h4>
  <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Konfirmasi Password</label>
      <input type="password" name="confirm" class="form-control" required>
    </div>
    <button type="submit" name="register" class="btn btn-custom w-100">Simpan</button>
    <div class="text-center mt-3">
      <a href="login.php" class="text-decoration-none text-primary">Sudah punya akun? Login</a>
    </div>
  </form>
</div>

</body>
</html>
