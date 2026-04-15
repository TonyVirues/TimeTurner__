<!--Layouts que usamos como referencia, para el resto de vistas.-->
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="..."> <!--Para verse en moviles-->
  <title><?= $title ?? 'TimeTurner' ?></title>

  <!--Bootstrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!---->
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
  <!--CSS global-->
  <link rel="stylesheet" href="/assets/css/custom.css">
  <!--CSS específico de cada vista-->
  <?= $this->renderSection('styles') ?>
  <!--letras google-->
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <!--iconos-->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>

<body class="">

  <main>
    <?= $this->renderSection('content') ?>
  </main>

  <!--Script-->
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src='/assets/main_calendar/calendar.js'></script>
  <script src='/assets/javascript/js.js'></script>

</body>

</html>