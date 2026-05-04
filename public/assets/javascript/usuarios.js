// ============================================================
// GESTIÓN DE USUARIOS — solo administrador
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  const btnNuevoEmpleado = document.getElementById('btnNuevoEmpleado');

  // ============================================================
  // BUSCADOR DE USUARIOS
  // ============================================================

  const buscador = document.getElementById('buscadorUsuarios');

  if (buscador) {
    buscador.addEventListener('input', function () {
      const termino = this.value.toLowerCase().trim();
      const tarjetas = document.querySelectorAll('.tt-usuario-card');

      tarjetas.forEach(function (tarjeta) {
        const nombre = tarjeta.querySelector('h6')?.textContent.toLowerCase() ?? '';
        const email = tarjeta.querySelector('small')?.textContent.toLowerCase() ?? '';

        const coincide = nombre.includes(termino) || email.includes(termino);
        tarjeta.closest('.col-12').style.display = coincide ? '' : 'none';
      });
    });
  }


  // Solo ejecutamos si el botón existe (administrador)
  if (!btnNuevoEmpleado) return;

  // ============================================================
  // CREAR EMPLEADO
  // ============================================================

  btnNuevoEmpleado.addEventListener('click', async function () {
    const resultado = await Swal.fire({
      title: 'Nuevo empleado',
      width: 550,
      html: `
        <div style="text-align:left">
          <label class="tt-swal-label">Nombre</label>
          <input id="sw_nombre" class="swal2-input tt-swal-input" placeholder="Nombre">
          <label class="tt-swal-label">Apellidos</label>
          <input id="sw_apellidos" class="swal2-input tt-swal-input" placeholder="Apellidos">
          <label class="tt-swal-label">Email</label>
          <input id="sw_email" type="email" class="swal2-input tt-swal-input" placeholder="email@empresa.com">
          <label class="tt-swal-label">Contraseña</label>
          <input id="sw_password" type="password" class="swal2-input tt-swal-input" placeholder="Mínimo 8 caracteres">
          <label class="tt-swal-label">Confirmar contraseña</label>
<input id="sw_password_confirm" type="password" class="swal2-input tt-swal-input" placeholder="Confirmar contraseña">
          <label class="tt-swal-label">Rol</label>
          <select id="sw_rol" class="swal2-input tt-swal-input">
            <option value="empleado">Empleado</option>
            <option value="administrador">Administrador</option>
          </select>
        </div>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Crear empleado',
      cancelButtonText: 'Cancelar',
      preConfirm: function () {
  const nombre = document.getElementById('sw_nombre').value.trim();
  const apellidos = document.getElementById('sw_apellidos').value.trim();
  const email = document.getElementById('sw_email').value.trim();
  const password = document.getElementById('sw_password').value;
  const passwordConfirm = document.getElementById('sw_password_confirm').value;
  const rol = document.getElementById('sw_rol').value;

  if (!nombre || !apellidos || !email || !password || !passwordConfirm) {
    Swal.showValidationMessage('Debes rellenar todos los campos.');
    return false;
  }

  if (password.length < 8) {
    Swal.showValidationMessage('La contraseña debe tener al menos 8 caracteres.');
    return false;
  }

  if (password !== passwordConfirm) {
    Swal.showValidationMessage('Las contraseñas no coinciden.');
    return false;
  }

  return { nombre, apellidos, email, password, rol };
}
    });

    if (!resultado.isConfirmed) return;

    const datos = new FormData();
    datos.append('usu_nombre', resultado.value.nombre);
    datos.append('usu_apellidos', resultado.value.apellidos);
    datos.append('usu_email', resultado.value.email);
    datos.append('usu_password', resultado.value.password);
    datos.append('usu_rol', resultado.value.rol);

    try {
      const res = await fetch('/usuarios/crear', { method: 'POST', body: datos });
      const data = await res.json();

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || 'No se pudo crear el empleado.');
      }

      await Swal.fire({
        icon: 'success',
        title: 'Empleado creado',
        text: 'El empleado se ha creado correctamente.',
        confirmButtonText: 'Aceptar'
      });

      window.location.reload();

    } catch (error) {
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message,
        confirmButtonText: 'Aceptar'
      });
    }
  });

  // ============================================================
  // EDITAR EMPLEADO
  // ============================================================

  document.addEventListener('click', async function (e) {
    const boton = e.target.closest('.tt-btn-editar-usuario');
    if (!boton) return;

    const id = boton.dataset.id;
    const nombre = boton.dataset.nombre;
    const apellidos = boton.dataset.apellidos;
    const email = boton.dataset.email;
    const rol = boton.dataset.rol;

    const resultado = await Swal.fire({
      title: 'Editar empleado',
      width: 550,
      html: `
        <div style="text-align:left">
          <label class="tt-swal-label">Nombre</label>
          <input id="sw_nombre" class="swal2-input tt-swal-input" value="${nombre}">
          <label class="tt-swal-label">Apellidos</label>
          <input id="sw_apellidos" class="swal2-input tt-swal-input" value="${apellidos}">
          <label class="tt-swal-label">Email</label>
          <input id="sw_email" type="email" class="swal2-input tt-swal-input" value="${email}">
          <label class="tt-swal-label">Nueva contraseña (opcional)</label>
          <input id="sw_password" type="password" class="swal2-input tt-swal-input" placeholder="Dejar vacío para no cambiar">
          <label class="tt-swal-label">Rol</label>
          <select id="sw_rol" class="swal2-input tt-swal-input">
            <option value="empleado" ${rol === 'empleado' ? 'selected' : ''}>Empleado</option>
            <option value="administrador" ${rol === 'administrador' ? 'selected' : ''}>Administrador</option>
          </select>
        </div>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Guardar cambios',
      cancelButtonText: 'Cancelar',
      preConfirm: function () {
        const nombre = document.getElementById('sw_nombre').value.trim();
        const apellidos = document.getElementById('sw_apellidos').value.trim();
        const email = document.getElementById('sw_email').value.trim();
        const password = document.getElementById('sw_password').value;
        const rol = document.getElementById('sw_rol').value;

        if (!nombre || !apellidos || !email) {
          Swal.showValidationMessage('Nombre, apellidos y email son obligatorios.');
          return false;
        }

        if (password && password.length < 8) {
          Swal.showValidationMessage('La contraseña debe tener al menos 8 caracteres.');
          return false;
        }

        return { nombre, apellidos, email, password, rol };
      }
    });

    if (!resultado.isConfirmed) return;

    const datos = new FormData();
    datos.append('usu_nombre', resultado.value.nombre);
    datos.append('usu_apellidos', resultado.value.apellidos);
    datos.append('usu_email', resultado.value.email);
    datos.append('usu_rol', resultado.value.rol);
    if (resultado.value.password) {
      datos.append('usu_password', resultado.value.password);
    }

    try {
      const res = await fetch(`/usuarios/actualizar/${id}`, { method: 'POST', body: datos });
      const data = await res.json();

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || 'No se pudo actualizar el empleado.');
      }

      await Swal.fire({
        icon: 'success',
        title: 'Empleado actualizado',
        text: 'Los datos se han guardado correctamente.',
        confirmButtonText: 'Aceptar'
      });

      window.location.reload();

    } catch (error) {
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message,
        confirmButtonText: 'Aceptar'
      });
    }
  });


  // ============================================================
  // ACTIVAR / DESACTIVAR EMPLEADO
  // ============================================================

  document.addEventListener('click', async function (e) {
    const boton = e.target.closest('.tt-btn-toggle-activo');
    if (!boton) return;

    const id = boton.dataset.id;
    const activo = boton.dataset.activo === '1';

    // ACTIVAR — flujo simple
    if (!activo) {
      const confirmado = await Swal.fire({
        icon: 'question',
        title: '¿Activar empleado?',
        text: 'El empleado podrá volver a iniciar sesión.',
        showCancelButton: true,
        confirmButtonText: 'Activar',
        cancelButtonText: 'Cancelar',
      });

      if (!confirmado.isConfirmed) return;

      const datos = new FormData();
      datos.append('usu_activo', '1');

      try {
        const res = await fetch(`/usuarios/actualizar/${id}`, { method: 'POST', body: datos });
        const data = await res.json();

        if (!res.ok || data.status !== 'success') {
          throw new Error(data.message || 'No se pudo activar el empleado.');
        }

        await Swal.fire({
          icon: 'success',
          title: 'Empleado activado',
          text: 'El empleado puede volver a iniciar sesión.',
          confirmButtonText: 'Aceptar'
        });

        window.location.reload();

      } catch (error) {
        await Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message,
          confirmButtonText: 'Aceptar'
        });
      }

      return;
    }

    // DESACTIVAR — flujo con selección de horarios
    try {
      // Paso 1 — Cargamos los horarios del empleado
      const res = await fetch(`/usuarios/horarios-de-empleado/${id}`);
      const data = await res.json();

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || 'No se pudieron cargar los horarios.');
      }

      const horarios = data.data;

      // Paso 2 — Construimos la tabla de horarios
      let tablaHtml = '';

      if (horarios.length === 0) {
        tablaHtml = `<p class="text-muted">Este empleado no tiene turnos asignados en ningún horario.</p>`;
      } else {
        tablaHtml = `
          <p class="mb-2" style="text-align:left">Selecciona los horarios cuyos turnos quieres liberar:</p>
          <table class="table table-sm table-bordered" style="font-size:0.85rem">
            <thead>
              <tr>
                <th style="width:40px"></th>
                <th>Horario</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Turnos</th>
              </tr>
            </thead>
            <tbody>
              ${horarios.map(h => `
                <tr style="cursor:pointer" onclick="this.querySelector('input').click()">
                  <td class="text-center">
                    <input type="checkbox" class="tt-horario-check" value="${h.hor_id_horario}">
                  </td>
                  <td>${h.hor_nombre}</td>
                  <td>${h.hor_fecha_inicio ?? '—'}</td>
                  <td>${h.hor_fecha_fin ?? '—'}</td>
                  <td>${h.total_turnos}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
      }

      // Paso 3 — Mostramos el modal
const resultado = await Swal.fire({
  title: 'Desactivar empleado',
  width: 800,
  html: `
<p class="mb-2" style="text-align:left">Selecciona los horarios cuyos turnos quieres liberar:</p>
  <table class="table table-sm table-bordered" style="font-size:0.85rem">
    <thead>
      <tr>
        <th style="width:40px"></th>
        <th>Horario</th>
        <th>Inicio</th>
        <th>Fin</th>
        <th>Turnos</th>
      </tr>
    </thead>
    <tbody>
      ${horarios.map(h => `
        <tr class="tt-horario-fila" style="cursor:pointer">
          <td class="text-center">
            <input type="checkbox" class="tt-horario-check" value="${h.hor_id_horario}" onclick="event.stopPropagation()">
          </td>
          <td>${h.hor_nombre}</td>
          <td>${h.hor_fecha_inicio ?? '—'}</td>
          <td>${h.hor_fecha_fin ?? '—'}</td>
          <td>${h.total_turnos}</td>
        </tr>
      `).join('')}
    </tbody>
  </table>
  `,
  showCancelButton: true,
  confirmButtonText: 'Desactivar',
  cancelButtonText: 'Cancelar',
  didOpen: () => {
    const filas = document.querySelectorAll('.tt-horario-fila');
    filas.forEach(fila => {
      fila.addEventListener('click', function () {
        const checkbox = this.querySelector('.tt-horario-check');
        checkbox.checked = !checkbox.checked;
      });
    });
  }
});

      if (!resultado.isConfirmed) return;

      // Paso 4 — Recogemos los horarios seleccionados
      const seleccionados = [...document.querySelectorAll('.tt-horario-check:checked')]
        .map(cb => cb.value);

      // Paso 5 — Enviamos
      const datos = new FormData();
      seleccionados.forEach(idHorario => datos.append('horarios[]', idHorario));

      const res2 = await fetch(`/usuarios/desactivar-con-liberar/${id}`, { method: 'POST', body: datos });
      const data2 = await res2.json();

      if (!res2.ok || data2.status !== 'success') {
        throw new Error(data2.message || 'No se pudo desactivar el empleado.');
      }

      await Swal.fire({
        icon: 'success',
        title: 'Empleado desactivado',
        text: data2.message,
        confirmButtonText: 'Aceptar'
      });

      window.location.reload();

    } catch (error) {
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message,
        confirmButtonText: 'Aceptar'
      });
    }
  });

  // ============================================================
  // ELIMINAR EMPLEADO
  // ============================================================

  document.addEventListener('click', async function (e) {
    const boton = e.target.closest('.tt-btn-eliminar-usuario');
    if (!boton) return;

    const id = boton.dataset.id;
    const nombre = boton.dataset.nombre;

    // Paso 1 — Confirmación previa siempre
    const confirmacionPrevia = await Swal.fire({
      icon: 'warning',
      title: '¿Eliminar empleado?',
      text: `¿Estás seguro de que quieres eliminar a ${nombre}? Esta acción no se puede deshacer.`,
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#EF4444',
    });

    if (!confirmacionPrevia.isConfirmed) return;

    // Paso 2 — Intentamos eliminar
    try {
      const res = await fetch(`/usuarios/eliminar/${id}`, { method: 'POST' });
      const data = await res.json();

      // Paso 3 — Si tiene turnos asignados mostramos aviso
      if (data.status === 'tiene_turnos') {
        const confirmado = await Swal.fire({
          icon: 'warning',
          title: 'Este empleado tiene turnos asignados',
          html: `<p>${nombre} tiene turnos asignados. Debes liberarlos antes de eliminar al empleado.</p>
                <p class="text-muted" style="font-size:0.85rem">Los turnos quedarán disponibles para ser reasignados.</p>`,
          showCancelButton: true,
          confirmButtonText: 'Liberar turnos y eliminar',
          cancelButtonText: 'Cancelar',
          confirmButtonColor: '#EF4444',
        });

        if (!confirmado.isConfirmed) return;

        // Paso 4 — Liberamos turnos y eliminamos
        const res2 = await fetch(`/usuarios/liberar-y-eliminar/${id}`, { method: 'POST' });
        const data2 = await res2.json();

        if (!res2.ok || data2.status !== 'success') {
          throw new Error(data2.message || 'No se pudo eliminar el empleado.');
        }

        await Swal.fire({
          icon: 'success',
          title: 'Empleado eliminado',
          text: data2.message,
          confirmButtonText: 'Aceptar'
        });

        window.location.reload();
        return;
      }

      // Paso 5 — Sin turnos, éxito directo
      if (data.status !== 'success') {
        throw new Error(data.message || 'No se pudo eliminar el empleado.');
      }

      await Swal.fire({
        icon: 'success',
        title: 'Empleado eliminado',
        text: 'El empleado ha sido eliminado correctamente.',
        confirmButtonText: 'Aceptar'
      });

      window.location.reload();

    } catch (error) {
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message,
        confirmButtonText: 'Aceptar'
      });
    }
  });


});