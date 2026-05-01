<?= $this->extend('layouts/main') ?>

<?= $this->section('vista') ?>

<!-- Shell principal: sidebar + contenido en fila -->
<div class="d-flex vh-100 overflow-hidden"> <!--cuerpo de la pagina-->


  <!--Menú-->
  <?= $this->include('menu/show/showMenu') ?>

  <!-- ========== ÁREA PRINCIPAL ========== -->
  <div class="d-flex flex-column flex-grow-1 overflow-hidden">

    <!-- Topbar -->
    <header class="d-flex justify-content-end px-3 py-2">

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
