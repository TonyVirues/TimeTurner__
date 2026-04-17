<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="/assets/css/login.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="login-page">
  <div class="container-fluid  d-flex justify-content-center align-items-center vh-100">
    <div class="row w-100 login-wrapper mx-auto">

      <!--Lado izquierdo-->
      <div class="col-md-6 login-left d-flex align-items-center justify-content-center">
        <!--Título-->
        <div style="width: 100%; max-width: 400px;">
          <h1 class="fw-bold mb-4">TimeTurner</h1>
          <h2 class="mb-4">Inicia sesión</h2>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
              <?= esc(session()->getFlashdata('error')) ?>
            </div>
          <?php endif; ?>

          <form action="<?= base_url('login') ?>" method="post">
            <!-- Seguridad -->
            <?= csrf_field() ?>

            <!-- Input del ususario -->
            <div class="mb-3 position-relative">
              <label class="form-label">Usuario / Email</label>
              <span class="material-symbols-outlined input-icon">person</span>
              <input type="text" name="email" class="form-control ps-5" placeholder="mar@outlook.com" value="<?= old('email') ?>" required>
            </div>

            <!-- Input de contraseña -->
            <div class="mb-3 position-relative">
              <label class="form-label">Password</label>
              <span class="material-symbols-outlined input-icon">lock</span>
              <input type="password" name="password" class="form-control ps-5" placeholder="********" required>
            </div>

            <!--Recordarme-->
            <div class="d-flex justify-content-between mb-3">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" name="remember">
                <label class="form-check-label">Recuerdame</label>
              </div>

              <a href="#">¿Olvidaste la contraseña?</a>
            </div>

            <!--botón-->
            <button type="submit" class="btn btn-primary w-100">Continuar</button>

            <div class="text-center mt-3">
              <span class="text-muted">¿No tienes cuenta?</span>
              <a href="<?= base_url('registro') ?>" class="ms-1">Crear cuenta</a>
            </div>
          </form>
        </div>
      </div>

      <!--Lado derecho-->
      <div class="col-md-6 login-right d-none d-md-flex align-items-center justify-content-center">
        <div class="text-center px-5">
          <!--Logo-->
          <div class="mb-4">
            <img src="/assets/imagen/logo.svg" class="img-fluid  rounded" style="max-width: 200px;" alt="Logo Timeturner">
          </div>

          <!--Título-->
          <h2 class="fw-bold mb-3">
            TimeTurner
          </h2>

          <!--Texto-->
          <p>
            La mejor forma de gestionar tus turnos de trabajo.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>