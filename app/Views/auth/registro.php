<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="/assets/css/login.css">
<?= $this->endSection() ?>

<?= $this->section('vista') ?>
<div class="login-page auth-page">
  <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100 py-3 rounded">
    <div class="row registro-wrapper mx-auto">

      <!--Lado izquierdo-->
      <div class="col-md-6 login-left d-flex  justify-content-center">
        <!--Título-->
        <div style="width: 100%; max-width: 400px;">
          <h1 class="fw-bold mb-4">TimeTurner</h1>
          <h2 class="mb-4">Registro</h2>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
              <?= esc(session()->getFlashdata('error')) ?>
            </div>
          <?php endif; ?>

          <!--Variable para validación de error-->
          <?php $errorCampo = session()->getFlashdata('errorCampo') ?? ''; ?>
          <form action="<?= base_url('registro') ?>" method="post">
            <!-- Seguridad -->
            <?= csrf_field() ?>

            <!-- Input del nombre -->
            <div class="mb-3 position-relative">
              <label class="form-label">Nombre <span class="text-danger">*</span></label>
              <span class="material-symbols-outlined input-icon">person</span>
              <input type="text" name="usu_nombre" 
                  class="form-control ps-5 
                  <?= in_array($errorCampo, ['nombre', 'obligatorios']) ? 'is-invalid' : '' ?>" 
                  placeholder="Mar" value="<?= old('usu_nombre') ?>" required>
              <div class="invalid-feedback"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>

            <!-- Input del apellido -->
            <div class="mb-3 position-relative">
              <label class="form-label">Apellidos <span class="text-danger">*</span></label>
              <span class="material-symbols-outlined input-icon">person</span>
              <input type="text" name="usu_apellidos" 
                  class="form-control ps-5 <?= in_array($errorCampo, ['nombre', 'obligatorios']) ? 'is-invalid' : '' ?>" 
                  placeholder="Sanchez" value="<?= old('usu_apellidos') ?>" required>
              <div class="invalid-feedback"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>

            <!--Input del email-->
            <div class="mb-3 position-relative">
              <label class="form-label">Email<span class="text-danger">*</span></label>
              <span class="material-symbols-outlined input-icon">mail</span>
              <input type="email" name="usu_email" 
                  class="form-control ps-5 <?= in_array($errorCampo, ['email', 'obligatorios']) ? 'is-invalid' : '' ?>" 
                  placeholder="mar@gmail.com" value="<?= old('usu_email') ?>" required>
              <div class="invalid-feedback"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>

            <!--Input de contraseña-->
            <div class="mb-3 position-relative">
              <label class="form-label">Contraseña <span class="text-danger">*</span></label>
              <span class="material-symbols-outlined input-icon">lock</span>
              <input type="password" name="usu_password" 
                  class="form-control ps-5 <?= in_array($errorCampo, ['password', 'obligatorios']) ? 'is-invalid' : '' ?>" 
                  placeholder="********" required>
              <div class="invalid-feedback"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>

            <div class="mb-3 position-relative">
              <label class="form-label">Confirma contraseña <span class="text-danger">*</span></label>
              <span class="material-symbols-outlined input-icon">lock</span>
              <input type="password" name="cpassword" 
                  class="form-control ps-5 <?= $errorCampo === 'password' ? 'is-invalid' : '' ?>" 
                  placeholder="********" required>
              <div class="invalid-feedback"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>

            <!-- Input nombre empresa -->
            <div class="mb-3 position-relative">
              <label class="form-label">Nombre de la empresa <span class="text-danger">*</span></label>
              <span class="material-symbols-outlined input-icon">business</span>
              <input type="text" name="emp_nombre" 
                  class="form-control ps-5 <?= $errorCampo === 'obligatorios' ? 'is-invalid' : '' ?>" 
                  placeholder="Mi empresa S.L." value="<?= old('emp_nombre') ?>" required>
              <div class="invalid-feedback"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>

            <!-- Input CIF -->
            <div class="mb-3 position-relative">
              <label class="form-label">CIF (opcional)</label>
              <span class="material-symbols-outlined input-icon">badge</span>
              <input type="text" name="emp_cif" 
                  class="form-control ps-5 <?= $errorCampo === 'cif' ? 'is-invalid' : '' ?>" 
                  placeholder="B12345678" value="<?= old('emp_cif') ?>">
              <div class="invalid-feedback"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>

            <!--botón-->
            <button type="submit" class="btn btn-primary w-100">Registrarse</button>

            <div class="text-center mt-3">
              <span>¿Ya tienes cuenta?</span>
              <a href="<?= base_url('login') ?>" class="ms-1">Inicia sesión</a>
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