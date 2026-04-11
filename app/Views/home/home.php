<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Shell principal: sidebar + contenido en fila -->
<div class="d-flex" style="height: 100vh; overflow: hidden;">

  <!-- ========== SIDEBAR ========== -->
  <nav class="tt-sidebar d-flex flex-column flex-shrink-0 bg-white border-end" id="ttSidebar">

    <!-- Logo -->
    <div class="d-flex align-items-center gap-2 px-3 py-3 border-bottom">
      <div class="tt-logo-icon rounded-3 d-flex align-items-center justify-content-center">
        <span class="material-symbols-outlined text-white">schedule</span>
      </div>
      <div class="tt-logo-text overflow-hidden">
        <span class="d-block fw-bold tt-logo-name">TimeTurner</span>
        <small class="text-muted tt-logo-sub">Gestor de turnos</small>
      </div>
    </div>

    <!-- Navegación -->
    <ul class="nav flex-column px-2 py-3 flex-grow-1">
      <li class="nav-item">
        <a href="/home" class="nav-link tt-nav-link active d-flex align-items-center gap-2 rounded-3">
          <span class="material-symbols-outlined">calendar_month</span>
          <span class="tt-nav-label">Calendario</span>
        </a>
      </li>
      <!-- Aquí irán las próximas secciones -->
    </ul>

    <!-- Pie del sidebar -->
    <div class="px-2 pb-3">
      <div class="bg-light rounded-3 p-3 text-center">
        <small class="text-muted">Más secciones próximamente</small>
      </div>
    </div>

  </nav>

  <!-- Overlay para móvil -->
  <div class="tt-sidebar-overlay" id="ttSidebarOverlay"></div>

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

      <!-- Avatar -->
      <div class="tt-avatar rounded-circle d-flex align-items-center justify-content-center" title="Mi perfil">
        <span class="material-symbols-outlined">account_circle</span>
      </div>

    </header>

    <!-- Contenido de la vista hija -->
<div class="container">
        <div class="col-12"> 
            <div class="row">
            <?= $this->renderSection('calendario') ?>
            </div>

        </div>
</div>




    <!-- Footer -->
    <footer class="border-top px-4 py-2 bg-white mt-auto">
      <small class="text-muted">
        &copy; TimeTurner | Gestor de turnos | Antonio J. Marín Virues | Mar Sánchez Sevillano | <?= date('Y') ?>
        &nbsp;|&nbsp; Environment: <?= ENVIRONMENT ?>
      </small>
    </footer>

  </div>
</div>

<?= $this->endSection() ?>