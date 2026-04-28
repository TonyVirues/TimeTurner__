document.addEventListener("DOMContentLoaded", function () {
  const contenedorSolicitudes = document.getElementById(
    "contenedorSolicitudes",
  );
  const estadoVacioSolicitudes = document.getElementById(
    "estadoVacioSolicitudes",
  );
  const filtroTipoSolicitud = document.getElementById("filtroTipoSolicitud");
  const filtroEstadoSolicitud = document.getElementById(
    "filtroEstadoSolicitud",
  );
  const btnRecargarSolicitudes = document.getElementById(
    "btnRecargarSolicitudes",
  );

  const totalEl = document.getElementById("ttSolicitudesTotal");
  const pendientesEl = document.getElementById("ttSolicitudesPendientes");
  const aceptadasEl = document.getElementById("ttSolicitudesAceptadas");
  const cerradasEl = document.getElementById("ttSolicitudesCerradas");

  let solicitudesOriginales = [];

  if (!contenedorSolicitudes) {
    console.error("No existe el contenedor de solicitudes.");
    return;
  }

  cargarSolicitudes();

  if (filtroTipoSolicitud) {
    filtroTipoSolicitud.addEventListener("change", aplicarFiltrosYRenderizar);
  }

  if (filtroEstadoSolicitud) {
    filtroEstadoSolicitud.addEventListener("change", aplicarFiltrosYRenderizar);
  }

  if (btnRecargarSolicitudes) {
    btnRecargarSolicitudes.addEventListener("click", function () {
      cargarSolicitudes();
    });
  }

  contenedorSolicitudes.addEventListener("click", async function (event) {
    const boton = event.target.closest("[data-accion][data-id]");

    if (!boton) {
      return;
    }

    const accion = boton.dataset.accion;
    const solicitudId = boton.dataset.id;

    if (!accion || !solicitudId) {
      return;
    }

    try {
      if (accion === "aceptar") {
        await aceptarSolicitud(solicitudId);
      }

      if (accion === "rechazar") {
        await rechazarSolicitud(solicitudId);
      }

      if (accion === "cancelar") {
        await cancelarSolicitud(solicitudId);
      }

      await cargarSolicitudes();
    } catch (error) {
      console.error("Error en acción de solicitud:", error);

      await Swal.fire({
        icon: "error",
        title: "Error",
        text: error.message || "Ha ocurrido un error.",
        confirmButtonText: "Aceptar",
      });
    }
  });

  async function cargarSolicitudes() {
    try {
      const estado = filtroEstadoSolicitud ? filtroEstadoSolicitud.value : "";
      let url = "/solicitudes/listado";

      if (estado) {
        url += `?sol_estado=${encodeURIComponent(estado)}`;
      }

      const resultado = await fetch(url).then(parsearRespuestaFetch);

      if (!resultado.ok || resultado.data.ok !== true) {
        throw new Error(
          resultado.data.mensaje || "No se pudieron cargar las solicitudes.",
        );
      }

      solicitudesOriginales = Array.isArray(resultado.data.data)
        ? resultado.data.data
        : [];

      aplicarFiltrosYRenderizar();
    } catch (error) {
      solicitudesOriginales = [];
      renderizarSolicitudes([]);
      actualizarResumen([]);

      await Swal.fire({
        icon: "error",
        title: "Error",
        text: error.message || "No se pudieron cargar las solicitudes.",
        confirmButtonText: "Aceptar",
      });
    }
  }

  function aplicarFiltrosYRenderizar() {
    const tipo = filtroTipoSolicitud ? filtroTipoSolicitud.value : "todas";
    const estado = filtroEstadoSolicitud ? filtroEstadoSolicitud.value : "";
    const idUsuarioActual = Number(window.ttUsuario?.id || 0);

    let solicitudesFiltradas = [...solicitudesOriginales];

    if (estado) {
      solicitudesFiltradas = solicitudesFiltradas.filter(function (solicitud) {
        return solicitud.sol_estado === estado;
      });
    }

    if (tipo === "recibidas") {
      solicitudesFiltradas = solicitudesFiltradas.filter(function (solicitud) {
        return (
          Number(solicitud.sol_id_usuario_destinatario) === idUsuarioActual
        );
      });
    }

    if (tipo === "enviadas") {
      solicitudesFiltradas = solicitudesFiltradas.filter(function (solicitud) {
        return Number(solicitud.sol_id_usuario_solicitante) === idUsuarioActual;
      });
    }

    actualizarResumen(solicitudesFiltradas);
    renderizarSolicitudes(solicitudesFiltradas);
  }

  function actualizarResumen(solicitudes) {
    const total = solicitudes.length;
    const pendientes = solicitudes.filter(function (solicitud) {
      return solicitud.sol_estado === "pendiente";
    }).length;

    const aceptadas = solicitudes.filter(function (solicitud) {
      return solicitud.sol_estado === "aceptada";
    }).length;

    const cerradas = solicitudes.filter(function (solicitud) {
      return (
        solicitud.sol_estado === "rechazada" ||
        solicitud.sol_estado === "cancelada"
      );
    }).length;

    if (totalEl) {
      totalEl.textContent = String(total);
    }

    if (pendientesEl) {
      pendientesEl.textContent = String(pendientes);
    }

    if (aceptadasEl) {
      aceptadasEl.textContent = String(aceptadas);
    }

    if (cerradasEl) {
      cerradasEl.textContent = String(cerradas);
    }
  }

  function renderizarSolicitudes(solicitudes) {
    contenedorSolicitudes.innerHTML = "";

    if (!solicitudes.length) {
      estadoVacioSolicitudes.classList.remove("d-none");
      return;
    }

    estadoVacioSolicitudes.classList.add("d-none");

    solicitudes.forEach(function (solicitud) {
      const card = document.createElement("div");
      card.className = "col-12 col-xl-6";

      card.innerHTML = `
        <div class="card border-0 shadow-sm h-100 tt-solicitud-card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
              <div>
                <h6 class="mb-1 fw-bold">
                  Solicitud #${escaparHtml(solicitud.sol_id_solicitud)}
                </h6>
                <small class="text-muted">
                  ${escaparHtml(formatearFecha(solicitud.sol_fecha_solicitud))}
                </small>
              </div>

              <span class="badge ${obtenerClaseBadgeEstado(solicitud.sol_estado)}">
                ${escaparHtml(capitalizarTexto(solicitud.sol_estado))}
              </span>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-12 col-md-6">
                <div class="tt-solicitud-bloque">
                  <small class="text-muted d-block mb-1">Solicitante</small>
                  <div class="fw-semibold">
                    ${escaparHtml(
                      construirNombreCompleto(
                        solicitud.solicitante_nombre,
                        solicitud.solicitante_apellidos,
                      ),
                    )}
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-6">
                <div class="tt-solicitud-bloque">
                  <small class="text-muted d-block mb-1">Destinatario</small>
                  <div class="fw-semibold">
                    ${escaparHtml(
                      construirNombreCompleto(
                        solicitud.destinatario_nombre,
                        solicitud.destinatario_apellidos,
                      ),
                    )}
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-6">
                <div class="tt-solicitud-bloque">
                  <small class="text-muted d-block mb-1">Turno original</small>
                  <div class="fw-semibold">
                    ${escaparHtml(
                      formatearRangoTurno(
                        solicitud.turno_original_inicio,
                        solicitud.turno_original_fin,
                      ),
                    )}
                  </div>
                  <small class="text-muted">
                    Estado: ${escaparHtml(capitalizarTexto(solicitud.turno_original_estado || ""))}
                  </small>
                </div>
              </div>

              <div class="col-12 col-md-6">
                <div class="tt-solicitud-bloque">
                  <small class="text-muted d-block mb-1">Turno propuesto</small>
                  <div class="fw-semibold">
                    ${escaparHtml(
                      formatearRangoTurno(
                        solicitud.turno_propuesto_inicio,
                        solicitud.turno_propuesto_fin,
                      ),
                    )}
                  </div>
                  <small class="text-muted">
                    Estado: ${escaparHtml(capitalizarTexto(solicitud.turno_propuesto_estado || ""))}
                  </small>
                </div>
              </div>
            </div>

            ${
              solicitud.sol_motivo
                ? `
                  <div class="mb-3">
                    <small class="text-muted d-block mb-1">Motivo</small>
                    <div class="tt-solicitud-texto">
                      ${escaparHtml(solicitud.sol_motivo)}
                    </div>
                  </div>
                `
                : ""
            }

            ${
              solicitud.sol_comentario_resolucion
                ? `
                  <div class="mb-3">
                    <small class="text-muted d-block mb-1">Comentario de resolución</small>
                    <div class="tt-solicitud-texto">
                      ${escaparHtml(solicitud.sol_comentario_resolucion)}
                    </div>
                  </div>
                `
                : ""
            }

            <div class="d-flex flex-wrap gap-2">
              ${construirBotonesAccion(solicitud)}
            </div>
          </div>
        </div>
      `;

      contenedorSolicitudes.appendChild(card);
    });
  }

  function construirBotonesAccion(solicitud) {
    const idUsuarioActual = Number(window.ttUsuario?.id || 0);
    const esDestinatario =
      Number(solicitud.sol_id_usuario_destinatario) === idUsuarioActual;
    const esSolicitante =
      Number(solicitud.sol_id_usuario_solicitante) === idUsuarioActual;

    if (solicitud.sol_estado !== "pendiente") {
      return `
        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
          Sin acciones disponibles
        </button>
      `;
    }

    let botones = "";

    if (esDestinatario) {
      botones += `
        <button type="button" class="btn btn-sm btn-success" data-accion="aceptar" data-id="${solicitud.sol_id_solicitud}">
          Aceptar
        </button>

        <button type="button" class="btn btn-sm btn-outline-danger" data-accion="rechazar" data-id="${solicitud.sol_id_solicitud}">
          Rechazar
        </button>
      `;
    }

    if (esSolicitante) {
      botones += `
        <button type="button" class="btn btn-sm btn-outline-secondary" data-accion="cancelar" data-id="${solicitud.sol_id_solicitud}">
          Cancelar
        </button>
      `;
    }

    if (!botones) {
      botones = `
        <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
          Sin acciones disponibles
        </button>
      `;
    }

    return botones;
  }

  async function aceptarSolicitud(solicitudId) {
    const confirmado = await Swal.fire({
      icon: "question",
      title: "Aceptar solicitud",
      text: "Se intercambiarán los usuarios asignados a ambos turnos.",
      showCancelButton: true,
      confirmButtonText: "Aceptar solicitud",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    });

    if (!confirmado.isConfirmed) {
      return;
    }

    const resultado = await fetch(
      `/solicitudes/aceptar/${encodeURIComponent(solicitudId)}`,
      {
        method: "POST",
      },
    ).then(parsearRespuestaFetch);

    if (!resultado.ok || resultado.data.ok !== true) {
      throw new Error(
        resultado.data.mensaje || "No se pudo aceptar la solicitud.",
      );
    }

    await Swal.fire({
      icon: "success",
      title: "Solicitud aceptada",
      text:
        resultado.data.mensaje || "La solicitud se ha aceptado correctamente.",
      confirmButtonText: "Aceptar",
    });
  }

  async function rechazarSolicitud(solicitudId) {
    const resultadoModal = await Swal.fire({
      title: "Rechazar solicitud",
      input: "textarea",
      inputLabel: "Comentario de resolución",
      inputPlaceholder: "Motivo del rechazo (opcional)",
      inputAttributes: {
        "aria-label": "Motivo del rechazo",
      },
      showCancelButton: true,
      confirmButtonText: "Rechazar",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    });

    if (!resultadoModal.isConfirmed) {
      return;
    }

    const datos = new FormData();
    datos.append("sol_comentario_resolucion", resultadoModal.value || "");

    const resultado = await fetch(
      `/solicitudes/rechazar/${encodeURIComponent(solicitudId)}`,
      {
        method: "POST",
        body: datos,
      },
    ).then(parsearRespuestaFetch);

    if (!resultado.ok || resultado.data.ok !== true) {
      throw new Error(
        resultado.data.mensaje || "No se pudo rechazar la solicitud.",
      );
    }

    await Swal.fire({
      icon: "success",
      title: "Solicitud rechazada",
      text:
        resultado.data.mensaje || "La solicitud se ha rechazado correctamente.",
      confirmButtonText: "Aceptar",
    });
  }

  async function cancelarSolicitud(solicitudId) {
    const resultadoModal = await Swal.fire({
      title: "Cancelar solicitud",
      input: "textarea",
      inputLabel: "Comentario de cancelación",
      inputPlaceholder: "Motivo de la cancelación (opcional)",
      inputAttributes: {
        "aria-label": "Motivo de la cancelación",
      },
      showCancelButton: true,
      confirmButtonText: "Cancelar solicitud",
      cancelButtonText: "Volver",
      reverseButtons: true,
    });

    if (!resultadoModal.isConfirmed) {
      return;
    }

    const datos = new FormData();
    datos.append("sol_comentario_resolucion", resultadoModal.value || "");

    const resultado = await fetch(
      `/solicitudes/cancelar/${encodeURIComponent(solicitudId)}`,
      {
        method: "POST",
        body: datos,
      },
    ).then(parsearRespuestaFetch);

    if (!resultado.ok || resultado.data.ok !== true) {
      throw new Error(
        resultado.data.mensaje || "No se pudo cancelar la solicitud.",
      );
    }

    await Swal.fire({
      icon: "success",
      title: "Solicitud cancelada",
      text:
        resultado.data.mensaje || "La solicitud se ha cancelado correctamente.",
      confirmButtonText: "Aceptar",
    });
  }
});

/**
 * Convierte la respuesta de fetch en { ok, data }
 * @param {*} response
 * @returns
 */
function parsearRespuestaFetch(response) {
  return response.json().then(function (data) {
    return { ok: response.ok, data: data };
  });
}

/**
 * Devuelve el nombre y apellidos en una sola cadena
 * @param {*} nombre
 * @param {*} apellidos
 * @returns
 */
function construirNombreCompleto(nombre, apellidos) {
  return `${nombre ?? ""} ${apellidos ?? ""}`.trim();
}

/**
 * Pone en mayúsculas la primera letra de un texto
 * @param {*} texto
 * @returns
 */
function capitalizarTexto(texto) {
  if (!texto) {
    return "";
  }

  return texto.charAt(0).toUpperCase() + texto.slice(1);
}

/**
 * Escapa texto para insertarlo con seguridad en HTML
 * @param {*} texto
 * @returns
 */
function escaparHtml(texto) {
  if (texto === null || texto === undefined) {
    return "";
  }

  return String(texto)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

/**
 * Formatea una fecha ISO o SQL a formato local
 * @param {*} fechaTexto
 * @returns
 */
function formatearFecha(fechaTexto) {
  if (!fechaTexto) {
    return "";
  }

  const fecha = new Date(fechaTexto.replace(" ", "T"));

  if (Number.isNaN(fecha.getTime())) {
    return fechaTexto;
  }

  return fecha.toLocaleString("es-ES", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  });
}

/**
 * Devuelve una franja inicio-fin legible
 * @param {*} inicio
 * @param {*} fin
 * @returns
 */
function formatearRangoTurno(inicio, fin) {
  if (!inicio || !fin) {
    return "";
  }

  return `${formatearFecha(inicio)} → ${formatearFecha(fin)}`;
}

/**
 * Devuelve la clase de badge según el estado de la solicitud
 * @param {*} estado
 * @returns
 */
function obtenerClaseBadgeEstado(estado) {
  const clases = {
    pendiente: "text-bg-warning",
    aceptada: "text-bg-success",
    rechazada: "text-bg-danger",
    cancelada: "text-bg-secondary",
  };

  return clases[estado] || "text-bg-light";
}
