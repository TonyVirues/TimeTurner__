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

      <!-- Notificaciones -->
      <div class="tt-topbar-notif" title="Notificaciones">
        <span class="material-symbols-outlined">notifications</span>
        <span class="tt-notif-dot"></span>
        
      </div>

    </header>
    
    <script>
        window.ttUsuario = {
          id: <?= (int) session()->get('usu_id_usuario') ?>,
          rol: "<?= esc((string) session()->get('usu_rol')) ?>"
        };
    </script>

    <!-- Contenido de la vista hija -->
    <div class="d-flex flex-column flex-grow-1 overflow-hidden">
      <?= $this->include($vista_contenido) ?>
    </div>

  </div>
</div>

<?= $this->endSection() ?>
