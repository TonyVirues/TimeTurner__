<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
  <header>
    <div>
      <h1>TimeTurner</h1>
    </div>
  </header>

    <?= $this->renderSection('calendario') ?>

  <footer>
    <div class="environment fixed-bottom">
      <p>&copy; TimeTurner | Gestor de turnos | Antonio J. Marín Virues | Mar Sánchez Sevillano | <?= date('Y') ?></p>
      <p>Environment: <?= ENVIRONMENT ?></p>
    </div>
  </footer>


</html><?= $this->endSection() ?>