<?= $this->extend('layouts/main') ?>

<?= $this->section('vista') ?>

<!-- Shell principal: sidebar + contenido en fila -->
<div class="d-flex vh-100 overflow-hidden"> <!--cuerpo de la pagina-->


  <!--Menú-->
  <?= $this->include('menu/show/showMenu') ?>

  <!-- ========== ÁREA PRINCIPAL ========== -->
  <div class="d-flex flex-column flex-grow-1 overflow-hidden">

    <!-- Topbar -->
    <header class="d-flex align-items-center gap-3 px-4 bg-white border-bottom" style="height: 60px; min-height: 60px;">

      <!-- Título -->
      <div class="flex-grow-1">
        <span class="fw-bold tt-topbar-title">TimeTurner</span>
        <small class="text-muted d-none d-sm-inline ms-2 tt-topbar-sub">Gestor de turnos</small>
      </div>

      <!-- Usuario + Logout -->
      <div class="d-flex align-items-center gap-2">

        <span class="text-muted" style="font-size: 14px;">
          <?= esc(session('usu_nombre')) ?>
        </span>

        <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger">
          Cerrar sesión
        </a>

        <!-- Avatar -->
        <div class="tt-avatar rounded-circle d-flex align-items-center justify-content-center" title="Mi perfil">
          <span class="material-symbols-outlined">account_circle</span>
        </div>

      </div>

    </header>

    <!-- Contenido de la vista hija -->
    <div class="d-flex flex-column flex-grow-1 overflow-hidden">
      <?= $this->include($vista_contenido) ?>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
