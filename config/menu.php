<?php

use App\Models\Role;

/*
 * Módulos del sistema por rol. Alimenta el menú lateral y las tarjetas del dashboard.
 *
 *  - ruta:   nombre de la ruta cuando el caso de uso ya está implementado.
 *            null = se muestra como "Pronto" (sin enlace).
 *  - patron: patrón de routeIs() para marcar el ítem activo (por defecto, la propia ruta).
 *  - icono:  nombre de un icono de resources/views/components/ui/icon.blade.php.
 *
 * Para activar un módulo nuevo: cree sus rutas y reemplace el null por el nombre de la ruta.
 */
return [

    Role::ADMINISTRADOR => [
        ['grupo' => 'Administración', 'titulo' => 'Usuarios y roles', 'descripcion' => 'Cuentas del personal y su nivel de acceso.',
            'icono' => 'users', 'ruta' => 'admin.usuarios.index', 'patron' => 'admin.usuarios.*'],                    // CU03
        ['grupo' => 'Administración', 'titulo' => 'Médicos', 'descripcion' => 'Registro de médicos y sus especialidades.',
            'icono' => 'activity', 'ruta' => 'admin.medicos.index', 'patron' => 'admin.medicos.*'],                   // CU08–CU10
        ['grupo' => 'Administración', 'titulo' => 'Especialidades', 'descripcion' => 'Catálogo de especialidades médicas.',
            'icono' => 'tag', 'ruta' => 'admin.especialidades.index', 'patron' => 'admin.especialidades.*'],          // CU11
        ['grupo' => 'Administración', 'titulo' => 'Horarios de atención', 'descripcion' => 'Disponibilidad e intervalos de cada médico.',
            'icono' => 'clock', 'ruta' => 'admin.horarios.index', 'patron' => 'admin.horarios.*'],                    // CU12
        ['grupo' => 'Análisis', 'titulo' => 'Reportes', 'descripcion' => 'Citas por fecha, médico, especialidad y estado.',
            'icono' => 'bar-chart', 'ruta' => null],                                                                   // CU20
    ],

    Role::RECEPCIONISTA => [
        ['grupo' => 'Recepción', 'titulo' => 'Médicos', 'descripcion' => 'Consulte médicos activos por nombre o especialidad.',
            'icono' => 'activity', 'ruta' => 'recepcion.medicos.index', 'patron' => 'recepcion.medicos.*'],           // CU09
        ['grupo' => 'Recepción', 'titulo' => 'Pacientes', 'descripcion' => 'Registro, búsqueda y datos de los pacientes.',
            'icono' => 'user', 'ruta' => 'recepcion.pacientes.index', 'patron' => 'recepcion.pacientes.*'],           // CU04–CU07
        ['grupo' => 'Recepción', 'titulo' => 'Disponibilidad', 'descripcion' => 'Horarios libres por médico y fecha.',
            'icono' => 'clock', 'ruta' => 'recepcion.disponibilidad.index', 'patron' => 'recepcion.disponibilidad.*'], // CU13
        ['grupo' => 'Recepción', 'titulo' => 'Citas', 'descripcion' => 'Programar, reprogramar, cancelar y registrar asistencia.',
            'icono' => 'clipboard', 'ruta' => null],                                                                   // CU14–CU17
        ['grupo' => 'Recepción', 'titulo' => 'Historial de citas', 'descripcion' => 'Todas las citas de un paciente.',
            'icono' => 'file-text', 'ruta' => null],                                                                   // CU19
    ],

    Role::MEDICO => [
        ['grupo' => 'Consulta', 'titulo' => 'Mi agenda', 'descripcion' => 'Citas asignadas por día y semana.',
            'icono' => 'calendar', 'ruta' => null],                                                                    // CU18
        ['grupo' => 'Consulta', 'titulo' => 'Atención de citas', 'descripcion' => 'Marcar las citas atendidas.',
            'icono' => 'clipboard', 'ruta' => null],                                                                   // CU17
    ],

    Role::PACIENTE => [
        ['grupo' => 'Mi cuenta', 'titulo' => 'Mi perfil', 'descripcion' => 'Datos personales y de contacto.',
            'icono' => 'user', 'ruta' => null],                                                                        // CU22
        ['grupo' => 'Mi cuenta', 'titulo' => 'Mis citas', 'descripcion' => 'Reserve y siga el estado de sus citas.',
            'icono' => 'calendar', 'ruta' => null],                                                                    // CU14, CU23
        ['grupo' => 'Mi cuenta', 'titulo' => 'Historial médico', 'descripcion' => 'Diagnósticos y recetas asignadas.',
            'icono' => 'heart', 'ruta' => null],                                                                       // CU24
    ],

];
