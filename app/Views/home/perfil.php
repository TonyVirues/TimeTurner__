<div class="tt-calendario-wrapper p-3">
  <div class="card h-100 border border-2 shadow rounded-4 overflow-hidden d-flex flex-column">

    <!-- Cabecera -->
    <div class="tt-horario-selector d-flex align-items-center gap-2 px-3 py-2 border-bottom flex-wrap">
      <span class="material-symbols-outlined">account_circle</span>
      <span class="fw-bold" style="font-size: 15px;">Mi perfil</span>
    </div>

    <!-- Cuerpo -->
    <div class="flex-grow-1 overflow-auto p-4 position-relative">

      <!-- Toggle tema -->
      <div style="position: absolute; top: 16px; right: 16px;">
        <span class="material-symbols-outlined" id="tt-toggle-tema" 
          style="cursor: pointer; font-size: 32px;" title="Cambiar tema">
          light_mode
        </span>
      </div>

      <div class="row justify-content-center">
        <div class="col-12 col-md-6">

          <!-- Avatar -->
          <div class="d-flex flex-column align-items-center mb-4">
            <div class="tt-usuario-avatar d-flex align-items-center justify-content-center rounded-circle mb-2"
              style="width: 72px; height: 72px; font-size: 28px;">
              <?= strtoupper(substr(session('usu_nombre') ?? 'U', 0, 1)) ?>
            </div>
            <span class="fw-bold"><?= (session('usu_nombre') ?? '') ?> <?= (session('usu_apellidos') ?? '') ?></span>
            <small class="text-muted"><?= (session('usu_rol') ?? '') ?></small>
            <small class="text-muted"><?= $empresa['emp_nombre'] ?? '' ?></small>          
          </div>

          <!-- Formulario -->
          <div id="tt-perfil-mensaje" class="mb-3" style="display:none;"></div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Nombre</label>
              <input type="text" id="usu_nombre" class="form-control"
                value="<?= $usuario['usu_nombre'] ?? '' ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Apellidos</label>
              <input type="text" id="usu_apellidos" class="form-control"
                value="<?= $usuario['usu_apellidos'] ?? '' ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Email</label>
              <input type="email" id="usu_email" class="form-control"
                value="<?= $usuario['usu_email'] ?? '' ?>">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Nueva contraseña</label>
              <input type="password" id="usu_password" class="form-control"
                placeholder="Dejar vacío para no cambiar">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Confirmar nueva contraseña</label>
              <input type="password" id="usu_password_confirm" class="form-control"
                placeholder="Repite la nueva contraseña">
            </div>

            <div class="d-flex justify-content-end mt-4">
              <button type="button" id="tt-btn-guardar-perfil" class="btn btn-primary px-4">
                Guardar cambios
              </button>
            </div>
          </div>
      </div>
    </div>

  </div>
</div>
