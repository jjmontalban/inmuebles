<?php 

/* Cuenta las visitas de los inmuebles y registra la fecha de cada visita */
function contar_visitas_inmueble() {
    // Verificar si la página es un inmueble individual y si el usuario no es administrador ni editor
    if (is_singular('inmueble') && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts')) { 
        $inmueble_id = get_the_ID(); 

        // Obtener el número de visitas y las fechas anteriores (si existen)
        $visitas = get_post_meta($inmueble_id, 'visitas', true);
        $fechas_visitas = get_post_meta($inmueble_id, 'fechas_visitas', true);

        // Asegurarse de que $fechas_visitas sea un array
        if (!is_array($fechas_visitas)) {
            $fechas_visitas = array();
        }

        // Incrementar el número de visitas
        $visitas = empty($visitas) ? 1 : $visitas + 1;

        // Registrar la fecha de la visita actual
        $fecha_actual = current_time('mysql');
        $fechas_visitas[] = $fecha_actual;

        // Actualizar los metadatos del inmueble
        update_post_meta($inmueble_id, 'visitas', $visitas);
        update_post_meta($inmueble_id, 'fechas_visitas', $fechas_visitas);
    }
}
add_action('wp_footer', 'contar_visitas_inmueble');



/**
 * Asigna titulo del post al front del inmueble
 */
function modificar_titulo_pagina() {
    if (is_singular('inmueble')) {
        global $post;
        $nombre_calle = get_post_meta($post->ID, 'nombre_calle', true);
        if ($nombre_calle) {
            echo '<script>document.title = "' . esc_js($nombre_calle) . '";</script>';
        }
    }
}
add_action('wp_head', 'modificar_titulo_pagina');