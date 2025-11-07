<?php
session_start();
include "koneksi.php";

$error = "";
$success = "";

// Proses login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    $data = mysqli_fetch_array($query);

    if ($data) {
        $_SESSION['username'] = $data['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}

// Proses register
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $cek_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
        if (mysqli_num_rows($cek_user) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $password_md5 = md5($password);
            $insert = mysqli_query($koneksi, "INSERT INTO user (username, password) VALUES ('$username', '$password_md5')");
            if ($insert) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan saat registrasi.";
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
<title>Login Program Faktur</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
  background: linear-gradient(135deg, #4e73df, #1cc88a);
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
  background-color: #4e73df;
  color: #fff;
  border-radius: 25px;
}
.btn-custom:hover {
  background-color: #2e59d9;
}
.toggle-link {
  color: #4e73df;
  cursor: pointer;
  text-decoration: underline;
}
</style>
</head>
<body>

<div class="card p-4" style="width: 23rem;">
  <h4 class="text-center mb-3 text-primary" id="formTitle">Login Program Faktur Apotek</h4>
  
  <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
  <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

  <!-- Form Login -->
  <form method="POST" id="loginForm">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" name="login" class="btn btn-custom w-100">Login</button>
    <p class="mt-3 text-center">Belum punya akun? <span class="toggle-link" onclick="toggleForm()">Daftar di sini</span></p>
  </form>

  <!-- Form Register -->
  <form method="POST" id="registerForm" style="display: none;">
    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Konfirmasi Password</label>
      <input type="password" name="confirm" class="form-control" required>
    </div>
    <button type="submit" name="register" class="btn btn-custom w-100">Register</button>
    <p class="mt-3 text-center">Sudah punya akun? <span class="toggle-link" onclick="toggleForm()">Login di sini</span></p>
  </form>
</div>

<script>
function toggleForm() {
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const title = document.getElementById('formTitle');
  if (loginForm.style.display === 'none') {
    loginForm.style.display = 'block';
    registerForm.style.display = 'none';
    title.innerText = 'Login Program Faktur Apotek';
  } else {
    loginForm.style.display = 'none';
    registerForm.style.display = 'block';
    title.innerText = 'Register Akun Baru';
  }
}
</script>

</body>
</html>
