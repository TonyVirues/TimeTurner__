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


//funcion que actualiza las notificaciones en el menu para solicitudes

// ============================================================
// BADGE SOLICITUDES PENDIENTES
// ============================================================

function actualizarBadgeSolicitudes() {
  const badge = document.querySelector('.tt-nav-badge');

  if (!badge) return;

  fetch('/solicitudes/listado?sol_estado=pendiente')
    .then(function (response) {
      if (!response.ok) return;
      return response.json();
    })
    .then(function (data) {
      if (!data || !data.ok) return;

      const total = data.data.length;

      if (total === 0) {
        badge.style.display = 'none';
      } else {
        badge.style.display = '';
        badge.textContent = total;
      }
    })
    .catch(function () {
      badge.style.display = 'none';
    });
}

// ============================================================
// SOLICITAR CAMBIO DE TURNO DESDE VISTA COMPAÑEROS
// ============================================================

document.addEventListener('click', function (e) {
  const boton = e.target.closest('.tt-btn-solicitar');
  if (!boton) return;

  const idDestinatario = boton.dataset.id;
  const nombreDestinatario = boton.dataset.nombre;

  abrirSwalSolicitudCambio(idDestinatario, nombreDestinatario);
});

async function abrirSwalSolicitudCambio(idDestinatario, nombreDestinatario) {

  // Paso 1 — Cargamos los turnos del usuario logueado
  let misTurnos = [];

  try {
    const res = await fetch('/turnos/mis-turnos');
    const data = await res.json();
    if (data.status === 'success' && Array.isArray(data.data)) {
      misTurnos = data.data;
    }
  } catch (e) {
    await Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudieron cargar tus turnos.',
      confirmButtonText: 'Aceptar'
    });
    return;
  }

  if (misTurnos.length === 0) {
    await Swal.fire({
      icon: 'warning',
      title: 'Sin turnos',
      text: 'No tienes turnos asignados disponibles para solicitar un cambio.',
      confirmButtonText: 'Aceptar'
    });
    return;
  }

  // Paso 2 — Cargamos los turnos del compañero
  let turnosCompanero = [];

  try {
    const res = await fetch(`/turnos/mis-turnos-de/${encodeURIComponent(idDestinatario)}`);
    const data = await res.json();
    if (data.status === 'success' && Array.isArray(data.data)) {
      turnosCompanero = data.data;
    }
  } catch (e) {
    await Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudieron cargar los turnos del compañero.',
      confirmButtonText: 'Aceptar'
    });
    return;
  }

  if (turnosCompanero.length === 0) {
    await Swal.fire({
      icon: 'warning',
      title: 'Sin turnos',
      text: `${nombreDestinatario} no tiene turnos disponibles para intercambiar.`,
      confirmButtonText: 'Aceptar'
    });
    return;
  }

  // Paso 3 — Mostramos el formulario
  const opcionesMisTurnos = misTurnos.map(t =>
    `<option value="${t.tur_id_turno}">${t.tur_inicio} → ${t.tur_fin}</option>`
  ).join('');

  const opcionesCompanero = turnosCompanero.map(t =>
    `<option value="${t.tur_id_turno}">${t.tur_inicio} → ${t.tur_fin}</option>`
  ).join('');

  const resultado = await Swal.fire({
    title: `Solicitar cambio con ${nombreDestinatario}`,
    width: 550,
    html: `
      <div style="text-align:left">
        <label style="display:block;margin-bottom:6px;font-weight:600">Tu turno (original):</label>
        <select id="swal_turno_original" class="swal2-input" style="width:100%;margin:0 0 16px 0">
          ${opcionesMisTurnos}
        </select>
        <label style="display:block;margin-bottom:6px;font-weight:600">Turno de ${nombreDestinatario} (propuesto):</label>
        <select id="swal_turno_propuesto" class="swal2-input" style="width:100%;margin:0 0 16px 0">
          ${opcionesCompanero}
        </select>
        <label style="display:block;margin-bottom:6px;font-weight:600">Motivo (opcional):</label>
        <textarea id="swal_motivo" class="swal2-textarea" style="width:100%;margin:0" placeholder="Explica el motivo del cambio..."></textarea>
      </div>
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Enviar solicitud',
    cancelButtonText: 'Cancelar',
    preConfirm: function () {
      const turnoOriginal = document.getElementById('swal_turno_original').value;
      const turnoPropuesto = document.getElementById('swal_turno_propuesto').value;
      const motivo = document.getElementById('swal_motivo').value.trim();

      if (!turnoOriginal) {
        Swal.showValidationMessage('Debes seleccionar tu turno.');
        return false;
      }

      if (!turnoPropuesto) {
        Swal.showValidationMessage('Debes seleccionar el turno del compañero.');
        return false;
      }

      return { turnoOriginal, turnoPropuesto, motivo };
    }
  });

  if (!resultado.isConfirmed) return;

  // Paso 4 — Enviamos la solicitud
  const idUsuarioLogueado = window.ttUsuario?.id;

  const datos = new FormData();
  datos.append('sol_id_usuario_solicitante', idUsuarioLogueado);
  datos.append('sol_id_usuario_destinatario', idDestinatario);
  datos.append('sol_id_turno_original', resultado.value.turnoOriginal);
  datos.append('sol_id_turno_propuesto', resultado.value.turnoPropuesto);
  datos.append('sol_motivo', resultado.value.motivo);

  try {
    const res = await fetch('/solicitudes/crear', {
      method: 'POST',
      body: datos
    });
    const data = await res.json();

    if (!res.ok || !data.ok) {
      throw new Error(data.mensaje || 'No se pudo crear la solicitud.');
    }

    await Swal.fire({
      icon: 'success',
      title: 'Solicitud enviada',
      text: 'La solicitud de cambio se ha enviado correctamente.',
      confirmButtonText: 'Aceptar'
    });

  } catch (error) {
    await Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.message,
      confirmButtonText: 'Aceptar'
    });
  }
}


//Llamada de la función para actualizar las notificaciones de solicitudes
actualizarBadgeSolicitudes();