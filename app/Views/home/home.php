<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Shell principal: sidebar + contenido en fila -->
<div class="d-flex vh-100 overflow-hidden"> <!--cuerpo de la pagina-->


  <!--Menú-->
  <?= $this->include('menu/show/showMenu') ?>

  <!-- ========== ÁREA PRINCIPAL ========== -->
  <div class="d-flex flex-column flex-grow-1 overflow-hidden">

    <!-- Topbar -->
    <header class="d-flex align-items-center gap-3 px-4 bg-white border-bottom" style="height: 60px; min-height: 60px;">

      <!-- Toggle sidebar -->
      <button class="btn btn-light btn-sm tt-toggle-btn" id="ttToggleBtn" title="Menú">
        <span class="material-symbols-outlined">menu</span>
      </button>

      <!-- Título -->
      <div class="flex-grow-1">
        <span class="fw-bold tt-topbar-title">TimeTurner</span>
        <small class="text-muted d-none d-sm-inline ms-2 tt-topbar-sub">Gestor de turnos</small>
      </div>

      <!-- Notificaciones -->
      <div class="tt-topbar-notif" title="Notificaciones">
        <span class="material-symbols-outlined">notifications</span>
        <span class="tt-notif-dot"></span>
      </div>

    </header>

    <!-- Contenido de la vista hija -->
    <div class="d-flex flex-column flex-grow-1 overflow-hidden">
      <?= $this->renderSection('calendario') ?>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
