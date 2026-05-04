<!--Layouts que usamos como referencia, para el resto de vistas.-->
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="..."> <!--Para verse en moviles-->
  <title><?= $title ?? 'TimeTurner' ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/imagen/logo.svg') ?>">
  <!--Bootstrap-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!---->
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
  <!--CSS global-->
  <link rel="stylesheet" href="/assets/css/custom.css">
  <!--CSS específico de cada vista-->
  <?= $this->renderSection('styles') ?><!--Unificar proximamente-->
  <!--letras google-->
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <!--iconos-->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>

<body class="h-100">

  <main class="h-100">
    <?= $this->renderSection('vista') ?>
  </main>

  <!-- Footer -->
<!-- Footer -->
<footer class="border-top mt-auto">

  <!-- Aviso cookies -->
  <div id="ttCookieBanner" class="px-4 py-2 border-bottom" style="display: none;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <small class="text-muted">
        Esta aplicación usa cookies de sesión y almacenamiento local para funcionar correctamente y recordar tus preferencias. Al continuar usándola, aceptas su uso.
      </small>
      <button id="ttCookieAceptar" class="btn btn-primary btn-sm">Aceptar</button>
    </div>
  </div>

  <!-- Copyright -->
  <div class="px-4 py-2 text-center">
    <small class="text-muted">
      &copy; TimeTurner | Gestor de turnos | Antonio J. Marín Virues | Mar Sánchez Sevillano | <?= date('Y') ?>
      &nbsp;|&nbsp; Environment: <?= ENVIRONMENT ?>
    </small>
  </div>

</footer>

<script>
  (function () {
    const banner = document.getElementById('ttCookieBanner');
    const btn = document.getElementById('ttCookieAceptar');

    if (!localStorage.getItem('tt-cookies-aceptadas')) {
      banner.style.display = 'block';
    }

    btn.addEventListener('click', function () {
      localStorage.setItem('tt-cookies-aceptadas', '1');
      banner.style.display = 'none';
    });
  })();
</script>

  <!--Script-->
  <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src='/assets/main_calendar/calendar.js'></script>
  <script src='/assets/javascript/js.js'></script>
  <!-- Scripts específicos de cada vista -->
  <?= $this->renderSection('scripts') ?>

</body>

</html>