<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>TimeTurner</title>
  <meta name="description" content="The small framework with powerful features">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/custom.css">
  <link rel="icon" type="image/svg+xml" href="/assets/imagen/logo.svg">
</head>

<body>

  <header>
    <div>
      <h1>TimeTurner</h1>
    </div>
  </header>

  <!-- Selector de horarios @TODO @mar @tony la selección de horarios hay que ponerla bonita en el lateral -->
  <section class="main">
    <div class="container">

      <div class="col-12">
        <label for="horarioSelect">Selecciona un horario:</label>
        <select id="horarioSelect">
          <option value="">Selecciona un horario</option>
        </select>
      </div>

      <!-- Contenedor donde FullCalendar dibuja el calendario -->
      <div class="col-12">
        <div id="calendar" class="col-12"></div>
      </div>

    </div>
  </section>

  <!-- Modal turnos -->
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

        <!-- Botones modal -->
        <div class="acciones-modal">
          <button type="button" id="btnEliminarTurno" class="btn-eliminar oculto-boton">Eliminar turno</button>
          <button type="submit" id="btnGuardarTurno">Guardar turno</button>
          <button type="button" id="btnCerrarModal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    <div class="environment fixed-bottom">
      <p>&copy; TimeTurner | Gestor de turnos | Antonio J. Marín Virues | Mar Sánchez Sevillano | <?= date('Y') ?></p>
      <p>Environment: <?= ENVIRONMENT ?></p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="/assets/main_calendar/calendar.js"></script>
  <script src="/assets/javascript/js.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>