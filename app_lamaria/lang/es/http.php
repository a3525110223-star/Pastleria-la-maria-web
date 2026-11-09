<?php

return [
    'status' => [
        '400' => 'No pudimos procesar tu solicitud. Revisa los datos e inténtalo de nuevo.',
        '401' => 'Tu sesión no está activa. Inicia sesión para continuar.',
        '403' => 'No tienes permiso para acceder a esta sección.',
        '404' => 'No encontramos lo que buscas. Revisa la dirección e inténtalo de nuevo.',
        '405' => 'Esta acción no está disponible para esta dirección.',
        '419' => 'Tu sesión venció. Actualiza la página e inténtalo de nuevo.',
        '422' => 'Revisa los datos que enviaste e inténtalo de nuevo.',
        '429' => 'Hiciste demasiadas solicitudes. Espera un momento e inténtalo de nuevo.',
        '500' => 'Ocurrió un problema en el servidor. Inténtalo de nuevo más tarde.',
        '503' => 'La aplicación no está disponible por el momento. Inténtalo de nuevo más tarde.',
        'default' => 'No pudimos completar tu solicitud. Inténtalo de nuevo más tarde.',
    ],
];
