/*
 * Menú lateral colapsable.
 *  - Escritorio: alterna entre completo y solo iconos; recuerda la elección (localStorage).
 *  - Móvil: el botón abre/cierra el menú como panel; se cierra con el fondo oscuro o con Escape.
 */
(function () {
    var root = document.documentElement;
    var body = document.body;
    var KEY = 'clinica.sidebar';
    var movil = window.matchMedia('(max-width: 63.99rem)');

    function actualizarBoton() {
        var boton = document.querySelector('[data-sidebar-toggle]');
        if (!boton) return;

        var visible = movil.matches
            ? body.classList.contains('sidebar-open')
            : !root.classList.contains('sidebar-collapsed');

        boton.setAttribute('aria-expanded', visible ? 'true' : 'false');
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('[data-sidebar-toggle]')) {
            if (movil.matches) {
                body.classList.toggle('sidebar-open');
            } else {
                var colapsado = root.classList.toggle('sidebar-collapsed');
                try { localStorage.setItem(KEY, colapsado ? '1' : '0'); } catch (err) { /* sin almacenamiento */ }
            }
            actualizarBoton();
        } else if (e.target.closest('[data-sidebar-close]')) {
            body.classList.remove('sidebar-open');
            actualizarBoton();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && body.classList.contains('sidebar-open')) {
            body.classList.remove('sidebar-open');
            actualizarBoton();
        }
    });

    movil.addEventListener('change', function () {
        body.classList.remove('sidebar-open');
        actualizarBoton();
    });

    actualizarBoton();
})();
