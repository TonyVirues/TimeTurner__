// ============================================================
// TOGGLE SIDEBAR
// ============================================================

// GATES
const sidebar  = document.getElementById("ttSidebar");
const overlay  = document.getElementById("ttSidebarOverlay");
const logoBtn  = document.getElementById("ttLogoBtn");

// Sidebar cerrado por defecto al cargar la página
if (sidebar) {
  sidebar.classList.add("tt-collapsed");
}

// Abrir/cerrar sidebar al hacer clic en el logo
if (logoBtn && sidebar) {
  logoBtn.addEventListener("click", function () {
    const esMobil = window.innerWidth <= 768;

    if (esMobil) {
      sidebar.classList.toggle("tt-open");
      if (overlay) overlay.classList.toggle("tt-visible");
    } else {
      sidebar.classList.toggle("tt-collapsed");
      setTimeout(function () {
        if (typeof calendar !== "undefined") calendar.updateSize();
      }, 280);
    }
  });
}

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