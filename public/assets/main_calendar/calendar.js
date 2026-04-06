document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  const horarioSelect = document.getElementById("horarioSelect");

  if (!calendarEl) {
    console.error("No existe el elemento #calendar");
    return;
  }

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    locale: "es",
    selectable: true,
    editable: true,
    events: function (_fetchInfo, successCallback, failureCallback) {
      const horarioId = horarioSelect ? horarioSelect.value : "";

      if (!horarioId) {
        successCallback([]);
        return;
      }

      fetch(`/turnos/eventos?horario_id=${horarioId}`)
        .then((response) => response.json())
        .then((data) => successCallback(data))
        .catch((error) => failureCallback(error));
    },
  });

  if (horarioSelect) {
    fetch("/horarios/listado")
      .then((response) => response.json())
      .then((horarios) => {
        horarios.forEach((horario) => {
          const option = document.createElement("option");
          option.value = horario.hor_id_horario;
          option.textContent = horario.hor_nombre;
          horarioSelect.appendChild(option);
        });
      })
      .catch((error) => console.error("Error cargando horarios:", error));

    horarioSelect.addEventListener("change", function () {
      calendar.refetchEvents();
    });
  }

  calendar.render();
});
