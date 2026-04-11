<?= $this->extend('home/home') ?>



<?= $this->section('calendario') ?>
<section class="main">
    <div class="container">

      <div class="col-12">
        <label for="horarioSelect">Selecciona un horario:</label>
        <select id="horarioSelect">
          <option value="">Selecciona un horario</option>
        </select>
      </div>

      <div class="col-12">
        <div id="calendar" class="col-12"></div>
      </div>

    </div>
  </section>

  <div id="modalTurno" class="modal-turno oculto">
    <div class="modal-turno-contenido">
      <h2 id="tituloModalTurno">Crear turno</h2>
      <p><strong>Horario seleccionado:</strong> <span id="nombreHorarioActual"></span></p>

      <form id="formTurno">
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

        <!-- Botones modal -->
        <div class="acciones-modal">
          <button type="button" id="btnEliminarTurno" class="btn-eliminar oculto-boton">Eliminar turno</button>
          <button type="submit" id="btnGuardarTurno">Guardar turno</button>
          <button type="button" id="btnCerrarModal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  <?= $this->endSection() ?>