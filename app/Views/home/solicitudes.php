<?php
$rolUsuario = (string) session()->get('usu_rol');
$idUsuario = (int) session()->get('usu_id_usuario');
?>

<script>
  window.ttUsuario = {
    id: <?= json_encode($idUsuario) ?>,
    rol: <?= json_encode($rolUsuario) ?>,
  };
</script>

<div class="tt-calendario-wrapper">

  <!-- Cabecera -->
  <div class="d-flex align-items-center justify-content-between px-4 py-3 bg-white border-bottom flex-wrap gap-3">
    <div>
      <h5 class="mb-0 fw-bold">Solicitudes de cambio</h5>
      <small class="text-muted">Gestiona las solicitudes de intercambio de turnos</small>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <select id="filtroTipoSolicitud" class="form-select form-select-sm" style="min-width: 180px;">
        <option value="todas">Todas</option>
        <option value="recibidas">Recibidas</option>
        <option value="enviadas">Enviadas</option>
      </select>

      <select id="filtroEstadoSolicitud" class="form-select form-select-sm" style="min-width: 180px;">
        <option value="">Todos los estados</option>
        <option value="pendiente">Pendiente</option>
        <option value="aceptada">Aceptada</option>
        <option value="rechazada">Rechazada</option>
        <option value="cancelada">Cancelada</option>
      </select>

      <button type="button" id="btnRecargarSolicitudes" class="btn btn-sm btn-outline-secondary">
        Recargar
      </button>
    </div>
  </div>

  <!-- Resumen -->
  <div class="px-4 py-3 bg-light border-bottom">
    <div class="row g-3">
      <div class="col-12 col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <small class="text-muted d-block mb-1">Total visibles</small>
            <h4 class="mb-0 fw-bold" id="ttSolicitudesTotal">0</h4>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <small class="text-muted d-block mb-1">Pendientes</small>
            <h4 class="mb-0 fw-bold text-warning" id="ttSolicitudesPendientes">0</h4>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <small class="text-muted d-block mb-1">Aceptadas</small>
            <h4 class="mb-0 fw-bold text-success" id="ttSolicitudesAceptadas">0</h4>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <small class="text-muted d-block mb-1">Cerradas</small>
            <h4 class="mb-0 fw-bold text-secondary" id="ttSolicitudesCerradas">0</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenido -->
  <div class="p-4 overflow-auto flex-grow-1 bg-body-tertiary">
    <div id="contenedorSolicitudes" class="row g-3"></div>

    <div id="estadoVacioSolicitudes" class="d-none d-flex flex-column align-items-center justify-content-center h-100 text-center py-5">
      <span class="material-symbols-outlined mb-3" style="font-size: 48px; color: rgba(0,0,0,0.15);">swap_horiz</span>
      <h6 class="text-muted">No hay solicitudes para mostrar</h6>
      <small class="text-muted">Cuando existan solicitudes aparecerán aquí</small>
    </div>
  </div>

</div>

<?= $this->section('scripts') ?>
<script src="/javascript/solicitudes.js"></script>
<?= $this->endSection() ?>