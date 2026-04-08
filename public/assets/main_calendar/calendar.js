document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  const horarioSelect = document.getElementById("horarioSelect");

  const modalTurno = document.getElementById("modalTurno");
  const formTurno = document.getElementById("formTurno");
  const inputInicio = document.getElementById("tur_inicio");
  const inputFin = document.getElementById("tur_fin");
  const inputHorarioId = document.getElementById("tur_id_horario");
  const btnCerrarModal = document.getElementById("btnCerrarModal");

  const inputTurnoId = document.getElementById("tur_id_turno");
  const tituloModalTurno = document.getElementById("tituloModalTurno");
  const btnGuardarTurno = document.getElementById("btnGuardarTurno");
  const btnEliminarTurno = document.getElementById("btnEliminarTurno");

  let horariosDisponibles = [];
  let modoFormulario = "crear";

  if (!calendarEl) {
    console.error("No existe el elemento #calendar");
    return;
  }

  const nombreHorarioActual = document.getElementById("nombreHorarioActual");

  // Captura el nombre del horario donde se va crear/modificar turno
  if (nombreHorarioActual && horarioSelect) {
    nombreHorarioActual.textContent =
      horarioSelect.options[horarioSelect.selectedIndex].text;
  }

  /**
   * Vista del calendario
   */
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",

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
    firstDay: 1,
    selectable: true,
    editable: true,
    nowIndicator: true,
    allDaySlot: false,

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

    eventClassNames: function (arg) {
      const estado = arg.event.extendedProps.estado;

      if (estado === "asignado") {
        return ["turno-asignado"];
      }

      if (estado === "disponible") {
        return ["turno-disponible"];
      }

      if (estado === "pendiente_cambio") {
        return ["turno-pendiente"];
      }

      return [];
    },

    eventContent: function (arg) {
      const usuario = arg.event.extendedProps.usuario;

      return {
        html: `
          <div>
            <strong>${usuario ?? "Sin asignar"}</strong><br>
            ${arg.timeText}
          </div>
        `,
      };
    },

    select: function (info) {
      const horarioId = horarioSelect ? horarioSelect.value : "";

      if (!horarioId) {
        alert("Debes seleccionar un horario antes de crear un turno.");
        calendar.unselect();
        return;
      }

      const horarioSeleccionado = horariosDisponibles.find(function (h) {
        return String(h.hor_id_horario) === String(horarioId);
      });

      if (horarioSeleccionado) {
        const min = `${horarioSeleccionado.hor_fecha_inicio}T00:00`;
        const max = `${horarioSeleccionado.hor_fecha_fin}T23:59`;

        inputInicio.min = min;
        inputInicio.max = max;
        inputFin.min = min;
        inputFin.max = max;
      }

      modoFormulario = "crear";
      prepararModalCrear();
      formTurno.reset();

      inputInicio.value = formatearFechaParaDatetimeLocal(info.start);
      inputFin.value = formatearFechaParaDatetimeLocal(info.end);
      inputHorarioId.value = horarioId;

      if (nombreHorarioActual && horarioSelect) {
        nombreHorarioActual.textContent =
          horarioSelect.options[horarioSelect.selectedIndex].text;
      }

      limpiarErroresCampos();
      abrirModal(modalTurno);
      calendar.unselect();
    },

    eventClick: function (info) {
      const turnoId = info.event.id;

      if (!turnoId) {
        return;
      }

      fetch(`/turnos/mostrar/${encodeURIComponent(turnoId)}`)
        .then(function (response) {
          if (!response.ok) {
            throw new Error("No se pudo cargar el turno.");
          }

          return response.json();
        })
        .then(function (turno) {
          modoFormulario = "editar";
          prepararModalEditar();
          limpiarErroresCampos();

          inputTurnoId.value = turno.tur_id_turno;
          inputHorarioId.value = turno.tur_id_horario;
          inputInicio.value = formatearFechaParaInputDesdeIso(turno.tur_inicio);
          inputFin.value = formatearFechaParaInputDesdeIso(turno.tur_fin);

          document.getElementById("tur_estado").value =
            turno.tur_estado || "disponible";
          document.getElementById("tur_observaciones").value =
            turno.tur_observaciones || "";

          const horarioSeleccionado = horariosDisponibles.find(function (h) {
            return String(h.hor_id_horario) === String(turno.tur_id_horario);
          });

          if (horarioSeleccionado) {
            const min = `${horarioSeleccionado.hor_fecha_inicio}T00:00`;
            const max = `${horarioSeleccionado.hor_fecha_fin}T23:59`;

            inputInicio.min = min;
            inputInicio.max = max;
            inputFin.min = min;
            inputFin.max = max;
          }

          if (nombreHorarioActual && horarioSelect) {
            nombreHorarioActual.textContent =
              horarioSelect.options[horarioSelect.selectedIndex].text;
          }

          abrirModal(modalTurno);
        })
        .catch(function (error) {
          console.error("Error cargando turno:", error);
          alert(error.message);
        });
    },

    events: function (_fetchInfo, successCallback, failureCallback) {
      const horarioId = horarioSelect ? horarioSelect.value : "";

      if (!horarioId) {
        successCallback([]);
        return;
      }

      fetch(`/turnos/eventos?horario_id=${encodeURIComponent(horarioId)}`)
        .then(function (response) {
          if (!response.ok) {
            throw new Error("Error al cargar los eventos");
          }

          return response.json();
        })
        .then(function (data) {
          successCallback(data);
        })
        .catch(function (error) {
          console.error("Error cargando eventos:", error);
          failureCallback(error);
        });
    },
  });

  if (horarioSelect) {
    fetch("/horarios/listado")
      .then(function (response) {
        if (!response.ok) {
          throw new Error("Error al cargar los horarios");
        }

        return response.json();
      })
      .then(function (horarios) {
        horariosDisponibles = horarios;

        horarios.forEach(function (horario) {
          const option = document.createElement("option");
          option.value = horario.hor_id_horario;
          option.textContent = horario.hor_nombre;
          horarioSelect.appendChild(option);
        });

        if (horarios.length > 0) {
          horarioSelect.value = horarios[0].hor_id_horario;
          calendar.refetchEvents();
        }
      })
      .catch(function (error) {
        console.error("Error cargando horarios:", error);
      });

    horarioSelect.addEventListener("change", function () {
      if (nombreHorarioActual) {
        nombreHorarioActual.textContent =
          horarioSelect.options[horarioSelect.selectedIndex].text;
      }

      calendar.refetchEvents();
    });
  }

  if (btnCerrarModal) {
    btnCerrarModal.addEventListener("click", function () {
      cerrarModal(modalTurno);
    });
  }

  if (btnEliminarTurno) {
    btnEliminarTurno.addEventListener("click", function () {
      const turnoId = inputTurnoId.value;

      if (!turnoId) {
        return;
      }

      const confirmado = window.confirm(
        "¿Seguro que quieres eliminar este turno?",
      );

      if (!confirmado) {
        return;
      }

      fetch(`/turnos/eliminar/${encodeURIComponent(turnoId)}`, {
        method: "POST",
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return {
              ok: response.ok,
              data: data,
            };
          });
        })
        .then(function (resultado) {
          if (!resultado.ok || resultado.data.status !== "success") {
            throw new Error(
              resultado.data.message || "No se pudo eliminar el turno",
            );
          }

          cerrarModal(modalTurno);
          formTurno.reset();
          calendar.refetchEvents();
        })
        .catch(function (error) {
          console.error("Error al eliminar turno:", error);
          alert(error.message);
        });
    });
  }

  if (formTurno) {
    formTurno.addEventListener("submit", function (event) {
      event.preventDefault();

      limpiarErroresCampos();

      let hayErrores = false;

      if (!inputInicio.value) {
        mostrarErrorCampo("tur_inicio", "Debes indicar la fecha de inicio.");
        hayErrores = true;
      }

      if (!inputFin.value) {
        mostrarErrorCampo("tur_fin", "Debes indicar la fecha de fin.");
        hayErrores = true;
      }

      const inicioSql = convertirDatetimeLocalAFormatoSql(inputInicio.value);
      const finSql = convertirDatetimeLocalAFormatoSql(inputFin.value);

      if (inputInicio.value && inputFin.value && finSql <= inicioSql) {
        mostrarErrorCampo(
          "tur_fin",
          "Debe ser posterior a la fecha de inicio.",
        );
        hayErrores = true;
      }

      const horarioSeleccionado = horariosDisponibles.find(function (h) {
        return String(h.hor_id_horario) === String(inputHorarioId.value);
      });

      if (horarioSeleccionado && inputInicio.value && inputFin.value) {
        const inicioHorario = `${horarioSeleccionado.hor_fecha_inicio} 00:00:00`;
        const finHorario = `${horarioSeleccionado.hor_fecha_fin} 23:59:59`;

        if (inicioSql < inicioHorario) {
          mostrarErrorCampo("tur_inicio", "Fuera del rango del horario.");
          hayErrores = true;
        }

        if (finSql > finHorario) {
          mostrarErrorCampo("tur_fin", "Fuera del rango del horario.");
          hayErrores = true;
        }
      }

      if (hayErrores) {
        return;
      }

      const datos = new FormData();

      datos.append("tur_id_horario", inputHorarioId.value);
      datos.append("tur_inicio", inicioSql);
      datos.append("tur_fin", finSql);
      datos.append("tur_estado", document.getElementById("tur_estado").value);
      datos.append(
        "tur_observaciones",
        document.getElementById("tur_observaciones").value,
      );

      const url =
        modoFormulario === "editar"
          ? `/turnos/actualizar/${encodeURIComponent(inputTurnoId.value)}`
          : "/turnos/crear";

      fetch(url, {
        method: "POST",
        body: datos,
      })
        .then(function (response) {
          return response.json().then(function (data) {
            return {
              ok: response.ok,
              data: data,
            };
          });
        })
        .then(function (resultado) {
          if (!resultado.ok || resultado.data.status !== "success") {
            throw new Error(
              resultado.data.message || "No se pudo crear el turno",
            );
          }

          cerrarModal(modalTurno);
          formTurno.reset();
          calendar.refetchEvents();
        })
        .catch(function (error) {
          console.error("Error al crear turno:", error);
          alert(error.message);
        });
    });
  }

  calendar.render();
});

function abrirModal(modal) {
  if (!modal) {
    return;
  }

  modal.classList.remove("oculto");
}

function cerrarModal(modal) {
  if (!modal) {
    return;
  }

  modal.classList.add("oculto");
}

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
 * Prepara el modal para editar
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

function formatearFechaParaInputDesdeIso(fecha) {
  if (!fecha) {
    return "";
  }

  const date = new Date(fecha);
  return formatearFechaParaDatetimeLocal(date);
}

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
 * Limpia los errores de los campos
 */
function limpiarErroresCampos() {
  document.querySelectorAll(".error-campo").forEach(function (el) {
    el.textContent = "";
  });

  document.querySelectorAll(".input-error").forEach(function (el) {
    el.classList.remove("input-error");
  });
}

/**
 * Muestra los erroes en los campos
 * @param {*} idInput
 * @param {*} mensaje
 */
function mostrarErrorCampo(idInput, mensaje) {
  const input = document.getElementById(idInput);
  const error = document.getElementById("error_" + idInput);

  if (input) {
    input.classList.add("input-error");
  }

  if (error) {
    error.textContent = mensaje;
  }
}
