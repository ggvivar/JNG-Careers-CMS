<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      height: 100vh;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f8fafc;
    }

    .left-side {
      background: #0A0147 ;
      color: white;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
      padding: 40px;
    }

    .left-side img {
      max-width: 80%;
      border-radius: 12px;
      margin-bottom: 20px;
    }

    .right-side {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-box {
      width: 100%;
      max-width: 380px;
    }

    .form-control {
      border-radius: 10px;
    }

    .btn-primary {
      border-radius: 10px;
      background: #FFC91C;
      border: none;
    }

    .btn-primary:hover {
      background: #4f46e5;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b7280;
    }

    .password-wrapper {
      position: relative;
    }

    .logo-img {
      width: 70px;
    }
  </style>
</head>

<body>

<div class="container-fluid">
  <div class="row g-0">

    <!-- LEFT SIDE (IMAGE / BRANDING) -->
    <div class="col-md-6 d-none d-md-flex left-side">
      <img src="https://staging.joy-nostalg.com/_next/image?url=https%3A%2F%2Fjn-img.enclaves.ph%2FJNG%2520Website%2Flogo.png%3FupdatedAt%3D1758273146854%26tr%3Dc-at_max%2Cw-200&w=128&q=75" alt="Illustration">
      <h2 class="fw-bold">Welcome Back!</h2>
      <p class="opacity-75"></p>
    </div>

    <!-- RIGHT SIDE (LOGIN) -->
    <div class="col-12 col-md-6 right-side">
      <div class="login-box">

        <!-- LOGO -->
        <div class="mb-4 text-center">
          <img src="logo.png" class="logo-img"
               onerror="this.style.display='none'; document.getElementById('logoText').style.display='block';">
          <div id="logoText" class="fw-bold fs-1">Joy~Nostalg Group</div>
        </div>

        <h4 class="mb-1">Sign in</h4>
        <p class="text-muted mb-4">Please login to your account</p>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('/login') ?>" onsubmit="showLoading()">
          <?= csrf_field() ?>

          <!-- Username -->
          <div class="form-floating mb-3">
            <input type="text" name="username" class="form-control" id="username"
              placeholder="Username" value="<?= esc(old('username')) ?>" required>
            <label for="username">Username</label>
          </div>

          <!-- Password -->
          <div class="form-floating mb-2 password-wrapper">
            <input type="password" name="password" id="password"
              class="form-control" placeholder="Password" required>
            <label for="password">Password</label>
            <i class="bi bi-eye toggle-password" onclick="togglePassword()" id="eyeIcon"></i>
          </div>

          <!-- Options -->
          <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">
                Remember me
              </label>
            </div>
            <a href="admin/forgot-password" class="text-decoration-none">Forgot Password?</a>
          </div>

          <!-- Button -->
          <button class="btn btn-primary w-100 py-2 fw-semibold" id="loginBtn">
            <span id="btnText">Login</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
          </button>
        </form>

      </div>
    </div>

  </div>
</div>

<script>
  function togglePassword() {
    const password = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');

    if (password.type === 'password') {
      password.type = 'text';
      icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
      password.type = 'password';
      icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
  }

  function showLoading() {
    document.getElementById('btnText').textContent = "Logging in...";
    document.getElementById('btnSpinner').classList.remove('d-none');
  }
</script>

</body>
</html>