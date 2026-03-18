<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-5 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="mb-3">Forgot Password</h3>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
          <?php endif; ?>

          <form method="post" action="<?= site_url('/forgot-password') ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" value="<?= esc(old('username')) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="text" name="email" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Reset Password</button>
            <a class="btn btn-secondary w-100 mt-2" href="/">Back</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>