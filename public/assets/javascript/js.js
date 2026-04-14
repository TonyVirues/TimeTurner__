// ============================================================
  // TOGGLE SIDEBAR
  // ============================================================
 
  const sidebar   = document.getElementById("ttSidebar");
  const toggleBtn = document.getElementById("ttToggleBtn");
  const overlay   = document.getElementById("ttSidebarOverlay");
 
  if (toggleBtn && sidebar) {
 
    toggleBtn.addEventListener("click", function () {
      const esMobil = window.innerWidth <= 768;
 
      if (esMobil) {
        // Móvil: panel deslizante con overlay
        sidebar.classList.toggle("tt-open");
        if (overlay) overlay.classList.toggle("tt-visible");
      } else {
        // Escritorio: colapsar/expandir
        sidebar.classList.toggle("tt-collapsed");
        // Forzar redimensión del calendario al cambiar el ancho del sidebar
        setTimeout(function () {
          if (typeof calendar !== "undefined") calendar.updateSize();
        }, 280);
      }
    });
 
    // Cerrar sidebar al pulsar el overlay en móvil
    if (overlay) {
      overlay.addEventListener("click", function () {
        sidebar.classList.remove("tt-open");
        overlay.classList.remove("tt-visible");
      });
    }
 
    // Limpiar estados al redimensionar la ventana
    window.addEventListener("resize", function () {
      if (window.innerWidth > 768) {
        sidebar.classList.remove("tt-open");
        if (overlay) overlay.classList.remove("tt-visible");
      } else {
        sidebar.classList.remove("tt-collapsed");
      }
    });
  }