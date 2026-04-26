<?php $esAdministrador = session()->get('usu_rol') === 'administrador'; ?>

<script>
  window.ttUsuario = {
    rol: "<?= esc((string) session()->get('usu_rol')) ?>",
  };
</script>

<div class="tt-calendario-wrapper p-3">
  <div class="card h-100  border-2 shadow rounded-4 overflow-hidden d-flex flex-column">
    
    <!-- Selector de horario -->
    <div class="tt-horario-selector d-flex align-items-center gap-2 px-3 py-2 border-bottom  flex-wrap">
      <label for="horarioSelect" class="mb-0 text-muted" style="font-size: 13px; white-space: nowrap;">Selecciona un horario:</label>

      <select id="horarioSelect" class="form-select form-select-sm" style="max-width: 420px;">
        <option value="">Selecciona un horario</option>
      </select>

      <?php if ($esAdministrador): ?>
        <button type="button" id="btnNuevoHorario" class="btn btn-sm btn-primary">
          Nuevo horario
        </button>

        <button type="button" id="btnEditarHorario" class="btn btn-sm btn-outline-secondary">
          Editar horario
        </button>

        <button type="button" id="btnEliminarHorario" class="btn btn-sm btn-outline-danger">
          Eliminar horario
        </button>
      <?php endif; ?>
    </div>

    <!-- Calendario -->
    <div class="tt-calendario-inner flex-grow-1">
      <div id="calendar"></div>
    </div>

  </div>
</div>