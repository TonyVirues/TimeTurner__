
<?php $esAdministrador = session()->get('usu_rol') === 'administrador'; ?>

<script>
  window.ttUsuario = {
    rol: "<?= esc((string) session()->get('usu_rol')) ?>",
  };
</script>

<div class="tt-calendario-wrapper">

  <!-- Selector de horario -->
  <div class="tt-horario-selector d-flex align-items-center gap-2 px-3 py-2 border-bottom bg-white flex-wrap">
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
  <div class="tt-calendario-inner">
    <div id="calendar"></div>
  </div>

</div>

<!-- Modal turno — fuera del wrapper para no afectar al z-index -->
<div id="modalTurno" class="modal-turno oculto">
  <div class="modal-turno-contenido">
    <h2 id="tituloModalTurno">Crear turno</h2>
    <p><strong>Horario seleccionado:</strong> <span id="nombreHorarioActual"></span></p>

    <form id="formularioTurno">
      <input type="hidden" id="tur_id_horario" name="tur_id_horario">
      <input type="hidden" id="tur_id_turno" name="tur_id_turno">

      <div class="campo-formulario">
        <label for="tur_inicio">Inicio</label>
        <input type="datetime-local" id="tur_inicio" name="tur_inicio" required>
        <small class="error-campo" id="error_tur_inicio"></small>
      </div>

      <div class="campo-formulario">
        <label for="tur_fin">Fin</label>
        <input type="datetime-local" id="tur_fin" name="tur_fin" required>
        <small class="error-campo" id="error_tur_fin"></small>
      </div>

      <div class="campo-formulario">
        <label for="tur_estado">Estado</label>
        <select id="tur_estado" name="tur_estado" required>
          <option value="disponible">Disponible</option>
          <option value="asignado">Asignado</option>
          <option value="pendiente_cambio">Pendiente de cambio</option>
          <option value="cambiado">Cambiado</option>
          <option value="cancelado">Cancelado</option>
        </select>
      </div>

      <div class="campo-formulario">
        <label for="tur_observaciones">Observaciones</label>
        <textarea id="tur_observaciones" name="tur_observaciones" rows="4"></textarea>
      </div>

      <div class="acciones-modal">
        <button type="button" id="btnEliminarTurno" class="btn-eliminar oculto-boton">Eliminar turno</button>
        <button type="submit" id="btnGuardarTurno">Guardar turno</button>
        <button type="button" id="btnCerrarModal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

