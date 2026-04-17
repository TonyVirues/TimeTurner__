<!-- ===== SIDEBAR ===== -->
<nav class="tt-sidebar d-flex flex-column flex-shrink-0" id="ttSidebar">

  <!-- Logo -->
  <div class="d-flex align-items-center gap-2 px-3 tt-sidebar-border-bottom" style="height: 60px; min-height: 60px;">
    <div class="tt-logo-icon">
      <img src="/assets/imagen/logo.jpg" alt="Logo TimeTurner">
    </div>
    <div class="tt-logo-text" style="flex: 1; overflow: hidden;">
      <span class="d-block tt-logo-name">TimeTurner</span>
      <small class="tt-logo-sub">Gestor de turnos</small>
    </div>
  </div>

  <!--Menú de navegación-->
  <ul class="nav flex-column px-2 py-3 flex-grow-1">
    <li class="nav-item">
      <a href="/home" class="nav-link tt-nav-link active d-flex align-items-center gap-2 rounded-3">
        <span class="material-symbols-outlined">calendar_month</span>
        <span class="tt-nav-label">Calendario</span>
      </a>
    </li>
    <!-- Sección Compañeros -->
    <li class="nav-item mt-2">
      <small class="tt-nav-section px-2">Compañeros</small>
    </li>
    <li class="nav-item">
      <a href="#" class="nav-link tt-nav-link d-flex align-items-center gap-2 rounded-3">
        <span class="material-symbols-outlined">group</span>
        <span class="tt-nav-label">Compañeros</span>
      </a>
    </li>

    <!-- Sección Turnos -->
    <li class="nav-item mt-2">
      <small class="tt-nav-section px-2">Turnos</small>
    </li>

    <!--Solicitudes-->
    <li class="nav-item">
      <a href="#" class="nav-link tt-nav-link d-flex align-items-center gap-2 rounded-3">
        <span class="material-symbols-outlined">swap_horiz</span>
        <span class="tt-nav-label">Solicitudes</span>
        <span class="tt-nav-badge ms-auto">3</span>
      </a>
    </li>

    <!--Mis turnos-->
    <li class="nav-item">
      <a href="#" class="nav-link tt-nav-link d-flex align-items-center gap-2 rounded-3">
        <span class="material-symbols-outlined">badge</span>
        <span class="tt-nav-label">Mis turnos</span>
        <span class="tt-nav-badge-blue ms-auto">12</span>
      </a>
    </li>

    <!--Horarios-->
    <li class="nav-item">
      <a href="#" class="nav-link tt-nav-link d-flex align-items-center gap-2 rounded-3">
        <span class="material-symbols-outlined">table_view</span>
        <span class="tt-nav-label">Horarios</span>
      </a>
    </li>

  </ul>
  </ul>

<!-- Pie del sidebar — usuario -->
  <div class="px-2 pb-3 tt-sidebar-footer tt-sidebar-border-top">
    <div class="d-flex align-items-center gap-2 px-2 py-2 rounded-3 tt-user-block">
      <div class="tt-user-avatar d-flex align-items-center justify-content-center rounded-circle">
        <?= strtoupper(substr(session()->get('nombre') ?? 'U', 0, 1)) ?>
      </div>
      <div style="overflow: hidden;">
        <span class="d-block tt-user-name text-truncate">
          <?= session()->get('nombre') ?? 'Usuario' ?>
        </span>
        <small class="tt-user-role">
          <?= session()->get('rol') ?? 'Operario' ?>
        </small>
      </div>
    </div>
  </div>

</nav>

<!-- Overlay para móvil -->
<div class="tt-sidebar-overlay" id="ttSidebarOverlay"></div>