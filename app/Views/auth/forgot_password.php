<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

    .form-box {
      width: 100%;
      max-width: 380px;
    }

    .btn-primary {
      border-radius: 10px;
      background: #FFC91C;
      border: none;
    }

    .btn-primary:hover {
      background: #4f46e5;
    }

    .btn-secondary {
      border-radius: 10px;
    }

    .logo-img {
      width: 50px;
    }
  </style>
</head>

<body>

<div class="container-fluid">
  <div class="row g-0">

    <!-- LEFT SIDE -->
    <div class="col-md-6 d-none d-md-flex left-side">
      <img src="https://staging.joy-nostalg.com/_next/image?url=https%3A%2F%2Fjn-img.enclaves.ph%2FJNG%2520Website%2Flogo.png%3FupdatedAt%3D1758273146854%26tr%3Dc-at_max%2Cw-200&w=128&q=75" alt="Illustration">
      <h2 class="fw-bold">Forgot your password?</h2>
      <p class="opacity-75">No worries, we’ll help you recover your account.</p>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-12 col-md-6 right-side">
      <div class="form-box">

        <!-- LOGO -->
        <div class="mb-4 text-center">
          <img src="logo.png" class="logo-img"
               onerror="this.style.display='none'; document.getElementById('logoText').style.display='block';">
          <div id="logoText" class="fw-bold fs-1">Joy~Nostalg Group</div>
        </div>

        <h4 class="mb-1">Reset Password</h4>
        <p class="text-muted mb-4">Enter your details to continue</p>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?= esc(session()->getFlashdata('error')) ?>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('/forgot-password') ?>" onsubmit="showLoading()">
          <?= csrf_field() ?>

          <!-- Username -->
          <div class="form-floating mb-3">
            <input type="text" name="username" class="form-control" id="username"
              placeholder="Username" value="<?= esc(old('username')) ?>" required>
            <label for="username">Username</label>
          </div>

          <!-- Email -->
          <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="email"
              placeholder="Email Address" required>
            <label for="email">Email Address</label>
          </div>

          <!-- Button -->
          <button class="btn btn-primary w-100 py-2" id="resetBtn">
            <span id="btnText">Reset Password</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
          </button>

          <!-- Back -->
          <a class="btn btn-light w-100 mt-2" href="/">Back to Login</a>

        </form>

      </div>
    </div>

  </div>
</div>

<script>
  function showLoading() {
    document.getElementById('btnText').textContent = "Processing...";
    document.getElementById('btnSpinner').classList.remove('d-none');
  }
</script>

</body>
</html>