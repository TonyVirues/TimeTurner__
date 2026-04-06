<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>TimeTurner</title>
  <meta name="description" content="The small framework with powerful features">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/custom.css">
  <link rel="shortcut icon" type="image/png" href="/favicon.ico">
</head>

<body>

  <header>
    <div>
      <h1>TimeTurner</h1>
    </div>
  </header>

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