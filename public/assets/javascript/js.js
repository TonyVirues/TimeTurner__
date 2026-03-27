document.addEventListener('DOMContentLoaded', function () {

    const contenedor = document.getElementById('offcanva__showMenu');

    if (contenedor) {
        contenedor.innerHTML = `
            <p>Esto es contenido cargado dinámicamente</p>
        `;
    }

});