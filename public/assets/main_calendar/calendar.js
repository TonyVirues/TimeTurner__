document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  const horarioSelect = document.getElementById("horarioSelect");

  const btnNuevoHorario = document.getElementById("btnNuevoHorario");
  const btnEditarHorario = document.getElementById("btnEditarHorario");
  const btnEliminarHorario = document.getElementById("btnEliminarHorario");

  const inputEstado = document.getElementById("tur_estado");
  const inputObservaciones = document.getElementById("tur_observaciones");

  // Array que guarda los horarios cargados desde backend
  let horariosDisponibles = [];

  // Condición que corta el flujo si falla el calendario
  if (!calendarEl) {
    console.error("No existe el elemento calendar");
    return;
  }

  /**
   * Crea la instancia del calendario
   */
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",

    //Barra superior
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "timeGridWeek,dayGridMonth",
    },

    buttonText: {
      today: "Hoy",
      week: "Semana",
      month: "Mes",
    },

    locale: "es",
    firstDay: 1, //lunes
    selectable: true,
    editable: true,
    nowIndicator: true,
    allDaySlot: false,
    // Muestra hora principio/fin de turno
    displayEventTime: true,
    displayEventEnd: true,

    slotDuration: "00:30:00",
    slotLabelInterval: "01:00:00",
    slotMinTime: "06:00:00",
    slotMaxTime: "22:00:00",

    eventTimeFormat: {
      hour: "2-digit",
      minute: "2-digit",
      hour12: false,
    },

    /**
     * Añade un estilo al turno en función de su estado
     * @param {*} arg
     * @returns
     */
    eventClassNames: function (arg) {
      const clasesPorEstado = {
        asignado: "turno-asignado",
        disponible: "turno-disponible",
        pendiente_cambio: "turno-pendiente",
        cambiado: "turno-cambiado",
        cancelado: "turno-cancelado",
      };

      const estado = arg.event.extendedProps.estado;
      const clase = clasesPorEstado[estado];

      return clase ? [clase] : [];
    },

    /**
     * Inserta el nombre del usuario/sin asignar y la hora al evento turno del calendario
     * @param {*} arg
     * @returns
     */
    eventContent: function (arg) {
      // Si es el evento de fondo de horario, sale de la función
      if (arg.event.display === "background") {
        return {};
      }
      const usuario = arg.event.extendedProps.usuario ?? "Sin asignar";

      const container = document.createElement("div");

      const strong = document.createElement("strong");
      strong.textContent = usuario;

      const br = document.createElement("br");

      const time = document.createTextNode(arg.timeText);

      container.appendChild(strong);
      container.appendChild(br);
      container.appendChild(time);

      return { domNodes: [container] };
    },

    /**
     * Se ejecuta con click+arrastrar
     * En vista semana crea turnos
     * En vista mes crea horarios
     * @param {*} info
     * @returns
     */
    select: function (info) {
      const tipoVista = info.view.type;

      if (tipoVista === "timeGridWeek") {
        crearTurnoDesdeSeleccion(
          info,
          horarioSelect,
          horariosDisponibles,
          calendar,
        );
        return;
      }

      if (tipoVista === "dayGridMonth") {
        crearHorarioDesdeSeleccion(
          info,
          horarioSelect,
          calendar,
          function (horarios) {
            horariosDisponibles = horarios;
          },
        );
      }
    },

    /**
     * Se abre SweetAlert modo editar al hacer click en un turno
     * @param {*} info
     * @returns
     */
    eventClick: function (info) {
      const turnoId = info.event.id;

      if (!turnoId) {
        return;
      }

      editarTurnoDesdeEvento(turnoId, horariosDisponibles, calendar).catch(
        function (error) {
          console.error("Error cargando turno:", error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message,
            confirmButtonText: "Aceptar",
          });
        },
      );
    },

    /**
     * Carga los turnos del calendario desde el backend según el horario seleccionado
     * y los pasa a FullCalendar
     * @param {*} _fetchInfo
     * @param {*} successCallback
     * @param {*} failureCallback
     * @returns
     */
    events: function (_fetchInfo, successCallback, failureCallback) {
      const horarioId = horarioSelect ? horarioSelect.value : "";

      if (!horarioId) {
        successCallback([]);
        return;
      }

      const horarioSeleccionado = obtenerHorarioPorId(
        horariosDisponibles,
        horarioId,
      );

      fetch(`/turnos/eventos?horario_id=${encodeURIComponent(horarioId)}`)
        .then(function (response) {
          if (!response.ok) {
            throw new Error("Error al cargar los turnos");
          }

          return response.json();
        })
        .then(function (turnos) {
          const eventos = Array.isArray(turnos) ? [...turnos] : [];

          if (horarioSeleccionado) {
            eventos.push(crearEventoFondoHorario(horarioSeleccionado));
          }

          successCallback(eventos);
        })
        .catch(function (error) {
          console.error("Error cargando turnos:", error);
          failureCallback(error);
        });
    },
  });

  /**
   * Carga los horarios desde el backend, rellena el desplegable
   * y recarga los turnos del calendario cuando cambia el horario seleccionado
   */
  if (horarioSelect) {
    cargarHorariosEnSelector(horarioSelect)
      .then(function (resultado) {
        horariosDisponibles = resultado.horarios;

        if (resultado.horarios.length > 0) {
          horarioSelect.value = resultado.horarios[0].hor_id_horario;
          calendar.refetchEvents();
        }
      })
      .catch(function (error) {
        console.error("Error cargando horarios:", error);
      });

    // Se ejecuta cuando escucha cambios en el select
    horarioSelect.addEventListener("change", function () {
      calendar.refetchEvents();
    });
  }

  if (btnNuevoHorario) {
    btnNuevoHorario.addEventListener("click", function () {
      crearHorarioDesdeBoton(horarioSelect, calendar, function (horarios) {
        horariosDisponibles = horarios;
      });
    });
  }

  if (btnEditarHorario) {
    btnEditarHorario.addEventListener("click", function () {
      editarHorarioDesdeBoton(horarioSelect, calendar, function (horarios) {
        horariosDisponibles = horarios;
      });
    });
  }

  if (btnEliminarHorario) {
    btnEliminarHorario.addEventListener("click", function () {
      eliminarHorarioDesdeBoton(horarioSelect, calendar, function (horarios) {
        horariosDisponibles = horarios;
      });
    });
  }

  // Pinta el calendario
  calendar.render();
});

//==== Bloque de horarios ====//

/**
 * Carga los horarios desde backend y rellena el select
 * @param {*} horarioSelect
 * @returns
 */
function cargarHorariosEnSelector(horarioSelect) {
  return fetch("/horarios/listado")
    .then(function (response) {
      if (!response.ok) {
        throw new Error("Error al cargar los horarios");
      }

      return response.json();
    })
    .then(function (horarios) {
      renderizarOpcionesHorarios(horarioSelect, horarios);

      return {
        horarios: horarios,
      };
    });
}

/**
 * Rellena el desplegable de horarios
 * @param {*} horarioSelect
 * @param {*} horarios
 * @returns
 */
function renderizarOpcionesHorarios(horarioSelect, horarios) {
  if (!horarioSelect) {
    return;
  }

  const valorSeleccionadoActual = horarioSelect.value;

  horarioSelect.innerHTML = "";
  horarioSelect.appendChild(crearOpcionPorDefectoHorario());

  horarios.forEach(function (horario) {
    const option = document.createElement("option");
    option.value = horario.hor_id_horario;
    option.textContent = construirTextoOpcionHorario(horario);
    horarioSelect.appendChild(option);
  });

  if (valorSeleccionadoActual) {
    horarioSelect.value = valorSeleccionadoActual;
  }
}

/**
 * Crea la opción por defecto del select de horarios
 * @returns
 */
function crearOpcionPorDefectoHorario() {
  const option = document.createElement("option");
  option.value = "";
  option.textContent = "Selecciona un horario";
  return option;
}

/**
 * Construye el texto visible de cada horario en el selector
 * @param {*} horario
 * @returns
 */
function construirTextoOpcionHorario(horario) {
  return `${horario.hor_nombre} | ${horario.hor_fecha_inicio} → ${horario.hor_fecha_fin} | ${capitalizarTexto(horario.hor_estado)}`;
}

/**
 * Crea un horario desde el botón
 * @param {*} horarioSelect
 * @param {*} calendar
 * @param {*} setHorariosDisponibles
 * @returns
 */
async function crearHorarioDesdeBoton(
  horarioSelect,
  calendar,
  setHorariosDisponibles,
) {
  try {
    const datosHorario = await abrirSwalHorario("crear");

    if (!datosHorario) {
      return;
    }

    const horarioCreado = await guardarHorario(datosHorario);

    const resultado = await recargarHorariosYSelector(
      horarioSelect,
      calendar,
      setHorariosDisponibles,
      horarioCreado.hor_id_horario,
    );

    return resultado;
  } catch (error) {
    console.error("Error al crear horario:", error);

    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message,
      confirmButtonText: "Aceptar",
    });
  }
}

/**
 * Crea un horario desde la selección en vista mes
 * @param {*} info
 * @param {*} horarioSelect
 * @param {*} calendar
 * @param {*} setHorariosDisponibles
 * @returns
 */
async function crearHorarioDesdeSeleccion(
  info,
  horarioSelect,
  calendar,
  setHorariosDisponibles,
) {
  try {
    const rango = convertirSeleccionMesARangoHorario(info);

    const datosHorario = await abrirSwalHorario("crear", {
      hor_nombre: "",
      hor_fecha_inicio: rango.hor_fecha_inicio,
      hor_fecha_fin: rango.hor_fecha_fin,
      hor_estado: "borrador",
      hor_descripcion: "",
    });

    if (!datosHorario) {
      return;
    }

    const horarioCreado = await guardarHorario(datosHorario);

    await recargarHorariosYSelector(
      horarioSelect,
      calendar,
      setHorariosDisponibles,
      horarioCreado.hor_id_horario,
    );
  } catch (error) {
    console.error("Error al crear horario:", error);

    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message,
      confirmButtonText: "Aceptar",
    });
  } finally {
    calendar.unselect();
  }
}

/**
 * Edita el horario actualmente seleccionado
 * @param {*} horarioSelect
 * @param {*} calendar
 * @param {*} setHorariosDisponibles
 * @returns
 */
async function editarHorarioDesdeBoton(
  horarioSelect,
  calendar,
  setHorariosDisponibles,
) {
  const horarioId = horarioSelect ? horarioSelect.value : "";

  if (!horarioId) {
    Swal.fire({
      icon: "warning",
      title: "Falta un horario",
      text: "Debes seleccionar un horario para editarlo.",
      confirmButtonText: "Aceptar",
    });
    return;
  }

  try {
    const horario = await fetch(
      `/horarios/mostrar/${encodeURIComponent(horarioId)}`,
    )
      .then(parsearRespuestaFetch)
      .then(function (resultado) {
        if (!resultado.ok) {
          throw new Error(
            resultado.data.message || "No se pudo cargar el horario.",
          );
        }

        return resultado.data;
      });

    const datosHorario = await abrirSwalHorario("editar", horario);

    if (!datosHorario) {
      return;
    }

    const horarioActualizado = await actualizarHorario(horarioId, datosHorario);

    await recargarHorariosYSelector(
      horarioSelect,
      calendar,
      setHorariosDisponibles,
      horarioActualizado.hor_id_horario,
    );
  } catch (error) {
    console.error("Error al editar horario:", error);

    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message,
      confirmButtonText: "Aceptar",
    });
  }
}

/**
 * Elimina el horario actualmente seleccionado
 * @param {*} horarioSelect
 * @param {*} calendar
 * @param {*} setHorariosDisponibles
 * @returns
 */
async function eliminarHorarioDesdeBoton(
  horarioSelect,
  calendar,
  setHorariosDisponibles,
) {
  const horarioId = horarioSelect ? horarioSelect.value : "";

  if (!horarioId) {
    Swal.fire({
      icon: "warning",
      title: "Falta un horario",
      text: "Debes seleccionar un horario para eliminarlo.",
      confirmButtonText: "Aceptar",
    });
    return;
  }

  try {
    const confirmado = await confirmarEliminarHorario();

    if (!confirmado) {
      return;
    }

    const eliminado = await eliminarHorario(horarioId);

    if (!eliminado) {
      return;
    }

    const resultado = await recargarHorariosYSelector(
      horarioSelect,
      calendar,
      setHorariosDisponibles,
      "",
    );

    if (resultado.horarios.length > 0) {
      horarioSelect.value = resultado.horarios[0].hor_id_horario;
      calendar.refetchEvents();
    }
  } catch (error) {
    console.error("Error al eliminar horario:", error);

    Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message,
      confirmButtonText: "Aceptar",
    });
  }
}

/**
 * Abre el SweetAlert para crear o editar horarios
 * @param {*} modo
 * @param {*} horario
 * @returns
 */
async function abrirSwalHorario(modo, horario = null) {
  const esEdicion = modo === "editar";

  const resultado = await Swal.fire({
    title: esEdicion ? "Editar horario" : "Crear horario",
    html: `
      <div class="tt-swal-formulario">

        <label for="swal_hor_nombre" class="tt-swal-label">
          Nombre del horario:
        </label>
        <input
          id="swal_hor_nombre"
          class="swal2-input tt-swal-input"
          placeholder="Nombre del horario"
          value="${escaparHtml(horario?.hor_nombre || "")}"
        >

        <label class="tt-swal-label">
          Fecha:
        </label>

        <div class="tt-swal-fechas">
          <div class="tt-swal-fecha-item">
            <label for="swal_hor_fecha_inicio" class="tt-swal-sub-label">
              Inicio
            </label>
            <input
              id="swal_hor_fecha_inicio"
              type="date"
              class="swal2-input tt-swal-input"
              value="${horario?.hor_fecha_inicio || ""}"
            >
          </div>

          <div class="tt-swal-fecha-item">
            <label for="swal_hor_fecha_fin" class="tt-swal-sub-label">
              Fin
            </label>
            <input
              id="swal_hor_fecha_fin"
              type="date"
              class="swal2-input tt-swal-input"
              value="${horario?.hor_fecha_fin || ""}"
            >
          </div>
        </div>

        <label for="swal_hor_estado" class="tt-swal-label">
          Estado del calendario:
        </label>
        <select id="swal_hor_estado" class="swal2-input tt-swal-input">
          <option value="borrador" ${(horario?.hor_estado || "borrador") === "borrador" ? "selected" : ""}>Borrador</option>
          <option value="publicado" ${horario?.hor_estado === "publicado" ? "selected" : ""}>Publicado</option>
          <option value="cerrado" ${horario?.hor_estado === "cerrado" ? "selected" : ""}>Cerrado</option>
        </select>

        <label for="swal_hor_descripcion" class="tt-swal-label">
          Descripción:
        </label>
        <textarea
          id="swal_hor_descripcion"
          class="swal2-textarea tt-swal-textarea"
          placeholder="Descripción"
        >${escaparHtml(horario?.hor_descripcion || "")}</textarea>

      </div>
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: esEdicion ? "Guardar cambios" : "Crear horario",
    cancelButtonText: "Cancelar",
    preConfirm: function () {
      const nombre = document.getElementById("swal_hor_nombre").value.trim();
      const fechaInicio = document.getElementById(
        "swal_hor_fecha_inicio",
      ).value;
      const fechaFin = document.getElementById("swal_hor_fecha_fin").value;
      const estado = document.getElementById("swal_hor_estado").value;
      const descripcion = document
        .getElementById("swal_hor_descripcion")
        .value.trim();

      if (!nombre) {
        Swal.showValidationMessage("Debes indicar el nombre del horario.");
        return false;
      }

      if (nombre.length < 3) {
        Swal.showValidationMessage(
          "El nombre debe tener al menos 3 caracteres.",
        );
        return false;
      }

      if (!fechaInicio) {
        Swal.showValidationMessage("Debes indicar la fecha de inicio.");
        return false;
      }

      if (!fechaFin) {
        Swal.showValidationMessage("Debes indicar la fecha de fin.");
        return false;
      }

      if (fechaFin < fechaInicio) {
        Swal.showValidationMessage(
          "La fecha de fin no puede ser menor que la fecha de inicio.",
        );
        return false;
      }

      return {
        hor_nombre: nombre,
        hor_fecha_inicio: fechaInicio,
        hor_fecha_fin: fechaFin,
        hor_descripcion: descripcion,
        hor_estado: estado,
      };
    },
  });

  if (!resultado.isConfirmed) {
    return null;
  }

  return resultado.value;
}

/**
 * Guarda un horario nuevo
 * @param {*} datosHorario
 * @returns
 */
async function guardarHorario(datosHorario) {
  const resultado = await fetch("/horarios/crear", {
    method: "POST",
    body: construirFormData(datosHorario),
  }).then(parsearRespuestaFetch);

  if (!resultado.ok || resultado.data.status !== "success") {
    throw new Error(resultado.data.message || "No se pudo crear el horario.");
  }

  await Swal.fire({
    icon: "success",
    title: "Horario creado",
    text: "El horario se ha creado correctamente.",
    confirmButtonText: "Aceptar",
  });

  return resultado.data.data;
}

/**
 * Actualiza un horario
 * @param {*} horarioId
 * @param {*} datosHorario
 * @returns
 */
async function actualizarHorario(horarioId, datosHorario) {
  const resultado = await fetch(
    `/horarios/actualizar/${encodeURIComponent(horarioId)}`,
    {
      method: "POST",
      body: construirFormData(datosHorario),
    },
  ).then(parsearRespuestaFetch);

  if (!resultado.ok || resultado.data.status !== "success") {
    throw new Error(
      resultado.data.message || "No se pudo actualizar el horario.",
    );
  }

  await Swal.fire({
    icon: "success",
    title: "Horario actualizado",
    text: "Los cambios se han guardado correctamente.",
    confirmButtonText: "Aceptar",
  });

  return resultado.data.data;
}

/**
 * Confirma si se quiere eliminar el horario
 * @returns
 */
async function confirmarEliminarHorario() {
  const resultado = await Swal.fire({
    icon: "warning",
    title: "Eliminar horario",
    text: "Se eliminará el horario y también todos los turnos asociados.",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
    reverseButtons: true,
  });

  return resultado.isConfirmed;
}

/**
 * Elimina un horario
 * @param {*} horarioId
 * @returns
 */
async function eliminarHorario(horarioId) {
  const resultado = await fetch(
    `/horarios/eliminar/${encodeURIComponent(horarioId)}`,
    {
      method: "POST",
    },
  ).then(parsearRespuestaFetch);

  if (!resultado.ok || resultado.data.status !== "success") {
    throw new Error(
      resultado.data.message || "No se pudo eliminar el horario.",
    );
  }

  await Swal.fire({
    icon: "success",
    title: "Horario eliminado",
    text: "El horario se ha eliminado correctamente.",
    confirmButtonText: "Aceptar",
  });

  return true;
}

/**
 * Recarga los horarios, repinta el select y vuelve a cargar eventos
 * @param {*} horarioSelect
 * @param {*} calendar
 * @param {*} setHorariosDisponibles
 * @param {*} horarioIdSeleccionado
 * @returns
 */
async function recargarHorariosYSelector(
  horarioSelect,
  calendar,
  setHorariosDisponibles,
  horarioIdSeleccionado,
) {
  const resultado = await cargarHorariosEnSelector(horarioSelect);

  setHorariosDisponibles(resultado.horarios);

  if (horarioIdSeleccionado) {
    horarioSelect.value = String(horarioIdSeleccionado);
  }

  calendar.refetchEvents();

  return resultado;
}

//==== Bloque de turnos ====//

/**
 * Crea un turno al seleccionar en vista semana
 * @param {*} info
 * @param {*} horarioSelect
 * @param {*} horariosDisponibles
 * @param {*} calendar
 * @returns
 */
async function crearTurnoDesdeSeleccion(
  info,
  horarioSelect,
  horariosDisponibles,
  calendar,
) {
  const horarioId = horarioSelect ? horarioSelect.value : "";

  if (!horarioId) {
    await Swal.fire({
      icon: "warning",
      title: "Falta un horario",
      text: "Debes seleccionar un horario antes de crear un turno.",
      confirmButtonText: "Aceptar",
    });
    calendar.unselect();
    return;
  }

  try {
    // Llama a la función para obtener el horario por id
    const horarioSeleccionado = obtenerHorarioPorId(
      horariosDisponibles,
      horarioId,
    );

    if (!horarioSeleccionado) {
      throw new Error("No se ha encontrado el horario seleccionado.");
    }

    const datosTurno = await abrirSwalTurno("crear", {
      turno: null,
      horarioSeleccionado: horarioSeleccionado,
      fechaInicio: info.start,
      fechaFin: info.end,
    });

    if (!datosTurno) {
      return;
    }

    const resultado = await fetch("/turnos/crear", {
      method: "POST",
      body: construirFormData(datosTurno),
    }).then(parsearRespuestaFetch);

    if (!resultado.ok || resultado.data.status !== "success") {
      throw new Error(resultado.data.message || "No se pudo crear el turno");
    }

    await Swal.fire({
      icon: "success",
      title: "Turno creado",
      text: "El turno se ha creado correctamente.",
      confirmButtonText: "Aceptar",
    });

    calendar.refetchEvents();
  } catch (error) {
    console.error("Error al crear turno:", error);

    await Swal.fire({
      icon: "error",
      title: "Error",
      text: error.message,
      confirmButtonText: "Aceptar",
    });
  } finally {
    calendar.unselect();
  }
}

/**
 * Edita un turno al hacer click en un evento
 * @param {*} turnoId
 * @param {*} horariosDisponibles
 * @param {*} calendar
 * @returns
 */
async function editarTurnoDesdeEvento(turnoId, horariosDisponibles, calendar) {
  // Hace una petición con la id del turno al backend y este consulta a la db
  const turno = await fetch(`/turnos/mostrar/${encodeURIComponent(turnoId)}`)
    .then(parsearRespuestaFetch)
    .then(function (resultado) {
      if (!resultado.ok) {
        throw new Error(
          resultado.data.message || "No se pudo cargar el turno.",
        );
      }

      return resultado.data;
    });

  // Llama a la función para obtener el horario por id
  const horarioSeleccionado = obtenerHorarioPorId(
    horariosDisponibles,
    turno.tur_id_horario,
  );

  if (!horarioSeleccionado) {
    throw new Error("No se ha encontrado el horario asociado al turno.");
  }

  const accion = await abrirSwalAccionTurno();

  if (accion === "eliminar") {
    const confirmado = await confirmarEliminarTurno();

    if (!confirmado) {
      return;
    }

    const eliminado = await eliminarTurno(turnoId);

    if (eliminado) {
      calendar.refetchEvents();
    }

    return;
  }

  if (accion !== "editar") {
    return;
  }

  const datosTurno = await abrirSwalTurno("editar", {
    turno: turno,
    horarioSeleccionado: horarioSeleccionado,
    fechaInicio: null,
    fechaFin: null,
  });

  if (!datosTurno) {
    return;
  }

  const resultado = await fetch(
    `/turnos/actualizar/${encodeURIComponent(turnoId)}`,
    {
      method: "POST",
      body: construirFormData(datosTurno),
    },
  ).then(parsearRespuestaFetch);

  if (!resultado.ok || resultado.data.status !== "success") {
    throw new Error(resultado.data.message || "No se pudo actualizar el turno");
  }

  await Swal.fire({
    icon: "success",
    title: "Turno actualizado",
    text: "Los cambios se han guardado correctamente.",
    confirmButtonText: "Aceptar",
  });

  calendar.refetchEvents();
}

/**
 * Abre un SweetAlert para elegir si editar o eliminar un turno
 * @returns
 */
async function abrirSwalAccionTurno() {
  const resultado = await Swal.fire({
    title: "Turno",
    text: "¿Qué quieres hacer con este turno?",
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: "Editar",
    denyButtonText: "Eliminar",
    cancelButtonText: "Cancelar",
  });

  if (resultado.isConfirmed) {
    return "editar";
  }

  if (resultado.isDenied) {
    return "eliminar";
  }

  return null;
}

/**
 * Abre el SweetAlert para crear o editar turnos
 * @param {*} modo
 * @param {*} opciones
 * @returns
 */
async function abrirSwalTurno(modo, opciones) {
  const { turno, horarioSeleccionado, fechaInicio, fechaFin } = opciones;
  const esEdicion = modo === "editar";

  const valorInicio = turno
    ? formatearFechaParaInputDesdeIso(turno.tur_inicio)
    : formatearFechaParaDatetimeLocal(fechaInicio);

  const valorFin = turno
    ? formatearFechaParaInputDesdeIso(turno.tur_fin)
    : formatearFechaParaDatetimeLocal(fechaFin);

  const resultado = await Swal.fire({
    title: esEdicion ? "Editar turno" : "Crear turno",
    width: 550,
    html: `
      <div class="tt-swal-formulario">

        <div class="tt-swal-info">
          <span class="tt-swal-info-titulo">Horario seleccionado:</span>
          <span class="tt-swal-info-valor">${escaparHtml(horarioSeleccionado.hor_nombre)}</span>
        </div>

        <label class="tt-swal-label">
          Fecha y hora:
        </label>

        <div class="tt-swal-fechas">
          <div class="tt-swal-fecha-item">
            <label for="swal_tur_inicio" class="tt-swal-sub-label">
              Inicio
            </label>
            <input
              id="swal_tur_inicio"
              type="datetime-local"
              class="swal2-input tt-swal-input"
              value="${valorInicio}"
              min="${horarioSeleccionado.hor_fecha_inicio}T00:00"
              max="${horarioSeleccionado.hor_fecha_fin}T23:59"
            >
          </div>

          <div class="tt-swal-fecha-item">
            <label for="swal_tur_fin" class="tt-swal-sub-label">
              Fin
            </label>
            <input
              id="swal_tur_fin"
              type="datetime-local"
              class="swal2-input tt-swal-input"
              value="${valorFin}"
              min="${horarioSeleccionado.hor_fecha_inicio}T00:00"
              max="${horarioSeleccionado.hor_fecha_fin}T23:59"
            >
          </div>
        </div>

        <label for="swal_tur_estado" class="tt-swal-label">
          Estado del turno:
        </label>
        <select id="swal_tur_estado" class="swal2-input tt-swal-input">
          <option value="disponible" ${(turno?.tur_estado || "disponible") === "disponible" ? "selected" : ""}>Disponible</option>
          <option value="asignado" ${turno?.tur_estado === "asignado" ? "selected" : ""}>Asignado</option>
          <option value="pendiente_cambio" ${turno?.tur_estado === "pendiente_cambio" ? "selected" : ""}>Pendiente de cambio</option>
          <option value="cambiado" ${turno?.tur_estado === "cambiado" ? "selected" : ""}>Cambiado</option>
          <option value="cancelado" ${turno?.tur_estado === "cancelado" ? "selected" : ""}>Cancelado</option>
        </select>

        <label for="swal_tur_observaciones" class="tt-swal-label">
          Observaciones:
        </label>
        <textarea
          id="swal_tur_observaciones"
          class="swal2-textarea tt-swal-textarea"
          placeholder="Observaciones"
        >${escaparHtml(turno?.tur_observaciones || "")}</textarea>

      </div>
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: esEdicion ? "Guardar cambios" : "Guardar turno",
    cancelButtonText: "Cancelar",
    preConfirm: function () {
      const inicio = document.getElementById("swal_tur_inicio").value;
      const fin = document.getElementById("swal_tur_fin").value;
      const estado = document.getElementById("swal_tur_estado").value;
      const observaciones = document
        .getElementById("swal_tur_observaciones")
        .value.trim();

      if (!inicio) {
        Swal.showValidationMessage("Debes indicar la fecha de inicio.");
        return false;
      }

      if (!fin) {
        Swal.showValidationMessage("Debes indicar la fecha de fin.");
        return false;
      }

      const inicioSql = convertirDatetimeLocalAFormatoSql(inicio);
      const finSql = convertirDatetimeLocalAFormatoSql(fin);

      if (finSql <= inicioSql) {
        Swal.showValidationMessage("Debe ser posterior a la fecha de inicio.");
        return false;
      }

      const inicioHorario = `${horarioSeleccionado.hor_fecha_inicio} 00:00:00`;
      const finHorario = `${horarioSeleccionado.hor_fecha_fin} 23:59:59`;

      if (inicioSql < inicioHorario) {
        Swal.showValidationMessage(
          "La fecha de inicio está fuera del rango del horario.",
        );
        return false;
      }

      if (finSql > finHorario) {
        Swal.showValidationMessage(
          "La fecha de fin está fuera del rango del horario.",
        );
        return false;
      }

      return {
        tur_id_horario: horarioSeleccionado.hor_id_horario,
        tur_inicio: inicioSql,
        tur_fin: finSql,
        tur_estado: estado,
        tur_observaciones: observaciones,
      };
    },
  });

  if (!resultado.isConfirmed) {
    return null;
  }

  return resultado.value;
}

/**
 * Confirma si se quiere eliminar el turno
 * @returns
 */
async function confirmarEliminarTurno() {
  const resultado = await Swal.fire({
    icon: "warning",
    title: "Eliminar turno",
    text: "¿Seguro que quieres eliminar este turno?",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
    reverseButtons: true,
  });

  return resultado.isConfirmed;
}

/**
 * Elimina un turno
 * @param {*} turnoId
 * @returns
 */
async function eliminarTurno(turnoId) {
  // Llamamos al backend para eliminar el turno
  const resultado = await fetch(
    `/turnos/eliminar/${encodeURIComponent(turnoId)}`,
    {
      method: "POST",
    },
  ).then(parsearRespuestaFetch);

  if (!resultado.ok || resultado.data.status !== "success") {
    throw new Error(resultado.data.message || "No se pudo eliminar el turno");
  }

  await Swal.fire({
    icon: "success",
    title: "Turno eliminado",
    text: "El turno se ha eliminado correctamente.",
    confirmButtonText: "Aceptar",
  });

  return true;
}

//==== Funciones auxiliares ====//

/**
 * Convierte una fecha en formato ISO (string) a formato válido para input datetime-local
 * @param {*} fecha
 * @returns
 */
function formatearFechaParaInputDesdeIso(fecha) {
  if (!fecha) {
    return "";
  }

  const date = new Date(fecha);
  return formatearFechaParaDatetimeLocal(date);
}

/**
 * Convierte un objeto Date a formato YYYY-MM-DDTHH:mm (compatible con datetime-local)
 * @param {*} fecha
 * @returns
 */
function formatearFechaParaDatetimeLocal(fecha) {
  const year = fecha.getFullYear();
  const month = String(fecha.getMonth() + 1).padStart(2, "0");
  const day = String(fecha.getDate()).padStart(2, "0");
  const hours = String(fecha.getHours()).padStart(2, "0");
  const minutes = String(fecha.getMinutes()).padStart(2, "0");

  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

/**
 * Convierte el formato de fecha y hora que recibimos del modal en el adecuado para la DB
 * @param {*} valor
 * @returns
 */
function convertirDatetimeLocalAFormatoSql(valor) {
  if (!valor) {
    return "";
  }

  return `${valor.replace("T", " ")}:00`;
}

/**
 * Obtiene el horario por id
 * @param {*} horariosDisponibles
 * @param {*} horarioId
 * @returns
 */
function obtenerHorarioPorId(horariosDisponibles, horarioId) {
  return horariosDisponibles.find(function (h) {
    return String(h.hor_id_horario) === String(horarioId);
  });
}

/**
 * Crea un evento de fondo para sombrear el rango del horario seleccionado
 * @param {*} horario
 * @returns
 */
function crearEventoFondoHorario(horario) {
  const fechaFinExclusiva = sumarDiasAFechaTexto(horario.hor_fecha_fin, 1);

  return {
    id: `fondo-horario-${horario.hor_id_horario}`,
    start: horario.hor_fecha_inicio,
    end: fechaFinExclusiva,
    display: "background",
    allDay: true,
    className: "horario-rango-fondo",
  };
}

/**
 * Suma días a una fecha en formato YYYY-MM-DD
 * @param {*} fechaTexto
 * @param {*} dias
 * @returns
 */
function sumarDiasAFechaTexto(fechaTexto, dias) {
  const fecha = new Date(`${fechaTexto}T00:00:00`);
  fecha.setDate(fecha.getDate() + dias);
  return formatearFechaSoloDia(fecha);
}

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
 * Construye un FormData a partir de un objeto clave-valor
 * @param {*} objeto
 * @returns
 */
function construirFormData(objeto) {
  const datos = new FormData();

  Object.keys(objeto).forEach(function (clave) {
    datos.append(clave, objeto[clave] ?? "");
  });

  return datos;
}

/**
 * Capitaliza el primer carácter de un texto
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
 * Convierte la selección de la vista mes a un rango válido para horarios
 * @param {*} info
 * @returns
 */
function convertirSeleccionMesARangoHorario(info) {
  const fechaInicio = formatearFechaSoloDia(info.start);

  const fechaFinReal = new Date(info.end);
  fechaFinReal.setDate(fechaFinReal.getDate() - 1);

  const fechaFin = formatearFechaSoloDia(fechaFinReal);

  return {
    hor_fecha_inicio: fechaInicio,
    hor_fecha_fin: fechaFin,
  };
}

/**
 * Formatea una fecha Date a YYYY-MM-DD
 * @param {*} fecha
 * @returns
 */
function formatearFechaSoloDia(fecha) {
  const year = fecha.getFullYear();
  const month = String(fecha.getMonth() + 1).padStart(2, "0");
  const day = String(fecha.getDate()).padStart(2, "0");

  return `${year}-${month}-${day}`;
}

/**
 * Obtiene el horario por id
 * @param {*} horariosDisponibles
 * @param {*} horarioId
 * @returns
 */
function obtenerHorarioPorId(horariosDisponibles, horarioId) {
  return horariosDisponibles.find(function (h) {
    return String(h.hor_id_horario) === String(horarioId);
  });
}

/**
 * Establece los límites mínimo y máximo en los inputs de fecha
 * según el rango del horario seleccionado.
 * @param {*} horario
 * @param {*} inputInicio
 * @param {*} inputFin
 * @returns
 */
function aplicarRangoHorarioAInputs(horario, inputInicio, inputFin) {
  if (!horario || !inputInicio || !inputFin) {
    return;
  }

  const min = `${horario.hor_fecha_inicio}T00:00`;
  const max = `${horario.hor_fecha_fin}T23:59`;

  inputInicio.min = min;
  inputInicio.max = max;
  inputFin.min = min;
  inputFin.max = max;
}

/**
 * Actualiza el texto del modal con el nombre del horario seleccionado,
 * donde se va crear/modificar turno
 * @param {*} nombreHorarioActual
 * @param {*} horarioSelect
 * @returns
 */
function actualizarNombreHorario(nombreHorarioActual, horarioSelect) {
  if (!nombreHorarioActual || !horarioSelect) {
    return;
  }
  const selectedOption = horarioSelect.options[horarioSelect.selectedIndex];
  nombreHorarioActual.textContent = selectedOption ? selectedOption.text : "";
}

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

//==== Modal ====//

/**
 * Muestra el modal eliminando la clase que lo oculta
 * @param {*} modal
 * @returns
 */
function abrirModal(modal) {
  if (!modal) {
    return;
  }

  modal.classList.remove("oculto");
}

/**
 * Oculta el modal añadiendo la clase correspondiente
 * @param {*} modal
 * @returns
 */
function cerrarModal(modal) {
  if (!modal) {
    return;
  }

  modal.classList.add("oculto");
}

/**
 * Configura el modal en modo crear
 */
function prepararModalCrear() {
  const titulo = document.getElementById("tituloModalTurno");
  const btnGuardar = document.getElementById("btnGuardarTurno");
  const btnEliminar = document.getElementById("btnEliminarTurno");
  const inputTurnoId = document.getElementById("tur_id_turno");

  if (titulo) {
    titulo.textContent = "Crear turno";
  }

  if (btnGuardar) {
    btnGuardar.textContent = "Guardar turno";
  }

  if (btnEliminar) {
    btnEliminar.classList.add("oculto-boton");
  }

  if (inputTurnoId) {
    inputTurnoId.value = "";
  }
}

/**
 * Configura el modal para editar
 */
function prepararModalEditar() {
  const titulo = document.getElementById("tituloModalTurno");
  const btnGuardar = document.getElementById("btnGuardarTurno");
  const btnEliminar = document.getElementById("btnEliminarTurno");

  if (titulo) {
    titulo.textContent = "Editar turno";
  }

  if (btnGuardar) {
    btnGuardar.textContent = "Guardar cambios";
  }

  if (btnEliminar) {
    btnEliminar.classList.remove("oculto-boton");
  }
}
