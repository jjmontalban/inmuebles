<?php 

/**
 * Agrega el metabox de informe de inmueble
 */
function inmuebles_agregar_mb_informe_inmueble() {
    add_meta_box(
        'inmueble_informe_inmueble',
        'Informe de Inmueble',
        'mostrar_informe_inmueble', // Callback
        'inmueble', // Donde se mostrará
        'side', // Contexto
        'default' // Prioridad
    );
}
add_action('add_meta_boxes', 'inmuebles_agregar_mb_informe_inmueble');


function mostrar_informe_inmueble($post) {
    $informe_url = admin_url('edit.php?post_type=inmueble&page=informe-inmueble&inmueble_id=' . $post->ID);
    echo '<button type="button" class="button button-primary button-large" onclick="window.location.href=\'' . esc_url($informe_url) . '\'">Crear Informe de Inmueble</button>';
}

/**
 * Registrar la página informe de inmueble
 */
function registrar_pagina_informe_inmueble() {
    add_submenu_page(
        null, // Esto hará que no aparezca en ningún menú
        'Informe de Inmueble',
        'Informe de Inmueble', // Título del menú (no relevante aquí)
        'edit_posts',
        'informe-inmueble',
        'mostrar_informe_inmueble_page'
    );
}
add_action('admin_menu', 'registrar_pagina_informe_inmueble');


/**
 * Muestra la página de informe de inmueble
 */
function mostrar_informe_inmueble_page() {
    if (!current_user_can('edit_posts')) {
        wp_die(__('Lo siento, no tienes permisos para acceder a esta página.'));
    }

    global $tipos_inmueble_map, $zonas_inmueble_map;

    // Obtener el ID del inmueble desde la URL
    $inmueble_id = isset($_GET['inmueble_id']) ? intval($_GET['inmueble_id']) : 0;
    if ($inmueble_id > 0) {
        $campos = obtener_campos_informe($inmueble_id, $tipos_inmueble_map, $zonas_inmueble_map);
        pintar_informe_html($inmueble_id, $campos, $tipos_inmueble_map, $zonas_inmueble_map);
    } else {
        echo 'No se ha seleccionado un inmueble para generar el informe.';
    }
}

/**
 * Obtiene los campos del inmueble, incluyendo la información del propietario.
 * 
 * @param int $inmueble_id El ID del inmueble.
 * @param array $tipos_inmueble_map Mapa de tipos de inmueble.
 * @param array $zonas_inmueble_map Mapa de zonas de inmueble.
 * @return array Un array con los campos del inmueble y la información del propietario.
 */
function obtener_campos_informe($inmueble_id, $tipos_inmueble_map, $zonas_inmueble_map) {
    $campos = array();

    // Información básica del inmueble
    $campos['nombre_calle'] = ucfirst(get_post_meta($inmueble_id, 'nombre_calle', true));

    $campos['tipo_inmueble_key'] = maybe_unserialize(get_post_meta($inmueble_id, 'tipo_inmueble', true));
    $campos['tipo_inmueble'] = isset($tipos_inmueble_map[$campos['tipo_inmueble_key']]) ? $tipos_inmueble_map[$campos['tipo_inmueble_key']] : $campos['tipo_inmueble_key'];

    $campos['tipo_operacion'] = get_post_meta($inmueble_id, 'tipo_operacion', true);
    $campos['referencia'] = get_post_meta($inmueble_id, 'referencia', true);
    $campos['precio'] = ($campos['tipo_operacion'] === 'venta') ? get_post_meta($inmueble_id, 'precio_venta', true) : get_post_meta($inmueble_id, 'precio_alquiler', true);

    $campos['metros_construidos'] = get_post_meta($inmueble_id, 'm_construidos', true);
    $campos['metros_utiles'] = get_post_meta($inmueble_id, 'm_utiles', true);
    $campos['num_dormitorios'] = get_post_meta($inmueble_id, 'num_dormitorios', true);
    $campos['num_banos'] = get_post_meta($inmueble_id, 'num_banos', true);

    $campos['zona_inmueble_key'] = get_post_meta($inmueble_id, 'zona_inmueble', true);
    $campos['zona_inmueble'] = isset($zonas_inmueble_map[$campos['zona_inmueble_key']]) ? $zonas_inmueble_map[$campos['zona_inmueble_key']] : $campos['zona_inmueble_key'];

    $campos['visitas'] = get_post_meta($inmueble_id, 'visitas', true);
    $campos['fechas_visitas'] = get_post_meta($inmueble_id, 'fechas_visitas', true);

    // Obtener el ID del propietario
    $propietario_id = get_post_meta($inmueble_id, 'propietario_id', true);
    if ($propietario_id) {
        // Obtener el objeto del propietario
        $propietario = get_post($propietario_id);

        // Verificar si el propietario existe
        if ($propietario) {
            // Añadir información del propietario al array de campos
            $campos['propietario'] = array(
                'id' => $propietario_id,
                'nombre' => get_the_title($propietario_id),
                'email' => get_post_meta($propietario_id, 'email', true), // Ejemplo de campo adicional
                'telefono' => get_post_meta($propietario_id, 'telefono', true) // Ejemplo de campo adicional
            );
        } else {
            // Si el propietario no existe, dejar el campo como null
            $campos['propietario'] = null;
        }
    } else {
        // Si no hay ID de propietario, dejar el campo como null
        $campos['propietario'] = null;
    }

    // Obtener inmuebles en la misma zona
    $campos['inmuebles_zona'] = obtener_inmuebles_misma_zona($campos['zona_inmueble_key']);
    
    return $campos;
}

function obtener_inmuebles_misma_zona($zona_inmueble_key) {
    $query = new WP_Query(array(
        'post_type' => 'inmueble',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'zona_inmueble',
                'value' => $zona_inmueble_key,
            )
        )
    ));

    $inmuebles = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $inmuebles[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'edit_link' => get_edit_post_link()
            );
        }
    }

    wp_reset_postdata();
    return $inmuebles;
}

function pintar_informe_html($inmueble_id, $campos, $tipos_inmueble_map, $zonas_inmueble_map) {
    ?>
    <h1 class="wp-heading-inline"><?php echo esc_html($campos['tipo_inmueble']) . " en " . esc_html($campos['nombre_calle']) . " (" . esc_html($campos['referencia']) . ")"; ?></h1>
    
    <div class="wrap">

        <div class="postbox-container" style="width: 48%; float: left; margin-right: 2%;">
            <div class="postbox">
                <div class="inside">
                    <?php if (has_post_thumbnail($inmueble_id)): ?>
                        <?php echo get_the_post_thumbnail($inmueble_id, 'medium'); ?>
                    <?php else: ?>
                        <p>No hay imagen destacada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="postbox-container" style="width: 48%; float: left;">
            <div class="postbox">
                <h2 class="hndle" style="padding: 0 10px;"><span>Detalles del Inmueble</span></h2>
                <div class="inside">
                    <p><strong>Tipo de Inmueble:</strong> <?php echo esc_html($campos['tipo_inmueble']); ?></p>
                    <p><strong>Tipo de Operación:</strong> <?php echo ucfirst(esc_html($campos['tipo_operacion'])); ?></p>
                    <p><strong>Precio:</strong> <?php echo esc_html($campos['precio']); ?></p>
                    <p><strong>Metros Construidos:</strong> <?php echo esc_html($campos['metros_construidos']); ?></p>
                    <p><strong>Metros Útiles:</strong> <?php echo esc_html($campos['metros_utiles']); ?></p>
                    <p><strong>Número de Dormitorios:</strong> <?php echo esc_html($campos['num_dormitorios']); ?></p>
                    <p><strong>Número de Baños:</strong> <?php echo esc_html($campos['num_banos']); ?></p>
                    <p><strong>Zona del Inmueble:</strong> <?php echo esc_html($campos['zona_inmueble']); ?></p>
                </div>
            </div>
        </div>

        <div class="postbox-container" style="width: 48%; float: left; margin-right: 2%; clear: both;">
            <div class="postbox">
                <h2 class="hndle" style="padding: 0 10px;"><span>Listado de Visitas</span></h2>
                <div class="inside">
                    <?php if (!empty($campos['visitas']) && !empty($campos['fechas_visitas'])): ?>
                        <ul>
                            <?php foreach ($campos['fechas_visitas'] as $index => $fecha_visita): ?>
                                <li>Visita <?php echo $index + 1; ?> - Fecha: <?php echo esc_html($fecha_visita); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay visitas registradas aún.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="postbox-container" style="width: 48%; float: left;">
            <div class="postbox">
                <h2 class="hndle" style="padding: 0 10px;"><span>Inmuebles en la misma zona</span></h2>
                <div class="inside">
                    <p><strong>Número de inmuebles en la misma zona:</strong> <?php echo count($campos['inmuebles_zona']); ?></p>
                    <?php if (!empty($campos['inmuebles_zona'])): ?>
                        <ul>
                            <?php foreach ($campos['inmuebles_zona'] as $inmueble): ?>
                                <li><a href="<?php echo esc_url($inmueble['edit_link']); ?>"><?php echo esc_html($inmueble['title']); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay otros inmuebles en esta zona.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}