<div class="tt-calendario-wrapper">

  <!-- Cabecera -->
  <div class="d-flex align-items-center justify-content-between px-4 py-3 bg-white border-bottom">
    <div>
      <h5 class="mb-0 fw-bold">Compañeros</h5>
      <small class="text-muted">Usuarios registrados en el sistema</small>
    </div>
    <!-- Buscador preparado para backend -->
    <div class="d-flex gap-2">
      <input
        type="text"
        id="buscadorUsuarios"
        class="form-control form-control-sm"
        placeholder="Buscar por nombre..."
        style="max-width: 220px;"
      >
    </div>
  </div>

  <!-- Contenido -->
  <div class="p-4 overflow-auto flex-grow-1">

    <?php if (!empty($usuarios)) : ?>

      <!-- Grid de tarjetas -->
      <div class="row g-3">
        <?php foreach ($usuarios as $usuario) : ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 border tt-usuario-card">
              <div class="card-body d-flex flex-column align-items-center text-center gap-2 py-4">

                <!-- Avatar iniciales -->
                <div class="tt-usuario-avatar d-flex align-items-center justify-content-center rounded-circle mb-2">
                  <?= strtoupper(substr($usuario['usu_nombre'], 0, 1)) ?>
                  <?= strtoupper(substr($usuario['usu_apellidos'], 0, 1)) ?>
                </div>

                <!-- Nombre -->
                <h6 class="mb-0 fw-bold">
                  <?= esc($usuario['usu_nombre']) ?> <?= esc($usuario['usu_apellidos']) ?>
                </h6>

                <!-- Email -->
                <small class="text-muted text-truncate w-100">
                  <?= esc($usuario['usu_email']) ?>
                </small>

                <!-- Badge estado -->
                <?php if ($usuario['usu_activo']) : ?>
                  <span class="badge tt-badge-activo">Activo</span>
                <?php else : ?>
                  <span class="badge tt-badge-inactivo">Inactivo</span>
                <?php endif; ?>

                <!-- Botón solicitar cambio — solo si no es el propio usuario -->
                <?php if ($usuario['usu_id_usuario'] !== session()->get('usu_id_usuario')) : ?>
                  <button
                    type="button"
                    class="btn btn-sm btn-outline-primary mt-2 tt-btn-solicitar"
                    data-id="<?= esc($usuario['usu_id_usuario']) ?>"
                    data-nombre="<?= esc($usuario['usu_nombre'] . ' ' . $usuario['usu_apellidos']) ?>">
                    Solicitar cambio
                  </button>
                <?php endif; ?>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    <?php else : ?>

      <!-- Estado vacío -->
      <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center py-5">
        <span class="material-symbols-outlined mb-3" style="font-size: 48px; color: rgba(0,0,0,0.15);">group</span>
        <h6 class="text-muted">No hay compañeros registrados</h6>
        <small class="text-muted">Cuando se registren usuarios aparecerán aquí</small>
      </div>

    <?php endif; ?>

  </div>

</div>
