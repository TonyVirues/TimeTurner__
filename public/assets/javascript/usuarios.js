// ============================================================
// GESTIÓN DE USUARIOS — solo administrador
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  const btnNuevoEmpleado = document.getElementById('btnNuevoEmpleado');

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
        const rol = document.getElementById('sw_rol').value;

        if (!nombre || !apellidos || !email || !password) {
          Swal.showValidationMessage('Debes rellenar todos los campos.');
          return false;
        }

        if (password.length < 8) {
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
    const accion = activo ? 'desactivar' : 'activar';

    const confirmado = await Swal.fire({
      icon: 'question',
      title: `¿${activo ? 'Desactivar' : 'Activar'} empleado?`,
      text: `El empleado ${activo ? 'no podrá iniciar sesión' : 'podrá volver a iniciar sesión'}.`,
      showCancelButton: true,
      confirmButtonText: activo ? 'Desactivar' : 'Activar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true,
    });

    if (!confirmado.isConfirmed) return;

    const datos = new FormData();
    datos.append('usu_activo', activo ? '0' : '1');

    try {
      const res = await fetch(`/usuarios/actualizar/${id}`, { method: 'POST', body: datos });
      const data = await res.json();

      if (!res.ok || data.status !== 'success') {
        throw new Error(data.message || `No se pudo ${accion} el empleado.`);
      }

      await Swal.fire({
        icon: 'success',
        title: `Empleado ${activo ? 'desactivado' : 'activado'}`,
        text: `El empleado ha sido ${activo ? 'desactivado' : 'activado'} correctamente.`,
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

    const confirmado = await Swal.fire({
      icon: 'warning',
      title: '¿Eliminar empleado?',
      text: `Esta acción eliminará a ${nombre} permanentemente y no se puede deshacer.`,
      showCancelButton: true,
      confirmButtonText: 'Eliminar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true,
      confirmButtonColor: '#EF4444',
    });

    if (!confirmado.isConfirmed) return;

    try {
      const res = await fetch(`/usuarios/eliminar/${id}`, { method: 'POST' });
      const data = await res.json();

      if (!res.ok || data.status !== 'success') {
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