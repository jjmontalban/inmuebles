<?php 

/**
 * Agrega el metabox de informe de inmueble
 */
function inmuebles_agregar_mb_informe_inmueble() {
    add_meta_box(
        'inmueble_informe_inmueble',
        'Informe de Inmueble',
        'mostrar_informe_inmueble',
        'inmueble',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'inmuebles_agregar_mb_informe_inmueble');


/**
 * Registrar la página informe de inmueble
 */
function registrar_pagina_informe_inmueble() {
    add_submenu_page(
        null, // Para que no aparezca en ningún menú
        'Informe de Inmueble',
        'Informe de Inmueble',
        'edit_posts',
        'informe-inmueble',
        'mostrar_informe_inmueble_page'
    );
}
add_action('admin_menu', 'registrar_pagina_informe_inmueble');


/**
 * Callback
 */
function mostrar_informe_inmueble($post) {
    $informe_url = admin_url('edit.php?post_type=inmueble&page=informe-inmueble&inmueble_id=' . $post->ID);
    echo '<button type="button" class="button button-primary button-large" onclick="window.location.href=\'' . 
            esc_url($informe_url) . '\'">Crear Informe de Inmueble</button>';
}


/**
 * Muestra la página de informe de inmueble
 */
function mostrar_informe_inmueble_page() {
    global $tipos_inmueble_map, $zonas_inmueble_map;
    // Obtener el ID del inmueble desde la URL
    $inmueble_id = isset($_GET['inmueble_id']) ? intval($_GET['inmueble_id']) : 0;
    if ($inmueble_id > 0) {
        $campos = obtener_campos_informe($inmueble_id);
        pintar_informe_html($inmueble_id, $campos);
    } else {
        echo 'No se ha seleccionado un inmueble para generar el informe.';
    }
}


/**
 * Obtiene los campos del informe
 */
function obtener_campos_informe($inmueble_id) {
    global $tipos_inmueble_map, $zonas_inmueble_map;

    $campos = array();

    // Obtener el tipo de inmueble
    $tipo_inmueble_key = maybe_unserialize(get_post_meta($inmueble_id, 'tipo_inmueble', true));
    $campos['tipo_inmueble'] = isset($tipos_inmueble_map[$tipo_inmueble_key]) ? $tipos_inmueble_map[$tipo_inmueble_key] : $tipo_inmueble_key;
    
    // Obtener la zona del inmueble
    $zona_inmueble_key = maybe_unserialize(get_post_meta($inmueble_id, 'zona_inmueble', true));
    $campos['zona_inmueble'] = isset($zonas_inmueble_map[$zona_inmueble_key]) ? $zonas_inmueble_map[$zona_inmueble_key] : $zona_inmueble_key;

    // Resto de campos
    $campos['nombre_calle'] = ucfirst(get_post_meta($inmueble_id, 'nombre_calle', true));
    $campos['tipo_operacion'] = get_post_meta($inmueble_id, 'tipo_operacion', true);
    $campos['referencia'] = get_post_meta($inmueble_id, 'referencia', true);
    $campos['precio'] = ($campos['tipo_operacion'] === 'venta') ? get_post_meta($inmueble_id, 'precio_venta', true) : get_post_meta($inmueble_id, 'precio_alquiler', true);
    $campos['metros_construidos'] = get_post_meta($inmueble_id, 'm_construidos', true);
    $campos['metros_utiles'] = get_post_meta($inmueble_id, 'm_utiles', true);
    $campos['num_dormitorios'] = get_post_meta($inmueble_id, 'num_dormitorios', true);
    $campos['num_banos'] = get_post_meta($inmueble_id, 'num_banos', true);

    // Obtener las visitas y fechas de visitas
    $campos['visitas'] = get_post_meta($inmueble_id, 'visitas', true);
    $campos['fechas_visitas'] = get_post_meta($inmueble_id, 'fechas_visitas', true);

    // Obtener el ID del propietario
    $propietario_id = get_post_meta($inmueble_id, 'propietario_id', true);
    if ($propietario_id) {
        $campos['propietario'] = array(
            'id' => $propietario_id,
            'nombre' => get_post_meta($propietario_id, 'nombre', true),
            'apellidos' => get_post_meta($propietario_id, 'apellidos', true),
            'telefono' => get_post_meta($propietario_id, 'telefono', true)
        );
    } else {
        $campos['propietario'] = 'Sin propietario asignado';
    }

    // Obtener las citas del inmueble
    $campos['citas'] = obtener_citas_inmueble($inmueble_id);

    // Obtener inmuebles en la misma zona
    $campos['inmuebles_zona'] = obtener_inmuebles_misma_zona($zona_inmueble_key);

    return $campos;
}



/**
 * Obtiene las citas del inmueble
 */
function obtener_citas_inmueble($inmueble_id) {

    $query = new WP_Query(array(
        'post_type' => 'cita',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'inmueble_id',
                'value' => $inmueble_id,
                'compare' => '='
            )
        )
    ));

    $citas = array();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $cita_id = get_the_ID();

            $demanda_id = get_post_meta($cita_id, 'demanda_id', true);
            
            // Datos de la demanda
            if ($demanda_id) {
                $demanda_nombre = get_post_meta($demanda_id, 'nombre', true);
                $demanda_telefono = get_post_meta($demanda_id, 'telefono', true);
            }

            // Añadir los datos de la cita y la demanda al array
            $citas[] = array(
                'fecha' => get_post_meta($cita_id, 'fecha', true),
                'hora' => get_post_meta($cita_id, 'hora', true),
                'comentario' => get_post_meta($cita_id, 'comentario', true),
                'demanda' => array(
                    'id' => $demanda_id,
                    'nombre' => $demanda_nombre,
                    'telefono' => $demanda_telefono,
                )
            );
        }
    }
    wp_reset_postdata();
    
    return $citas;
}

/**
 * Obtiene los inmuebles registrados en al misma zona
 */
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

function pintar_informe_html($inmueble_id, $campos) {

    // Preparar los datos para la gráfica
    $fechas_visitas = !empty($campos['fechas_visitas']) ? $campos['fechas_visitas'] : [];
    $visitas = !empty($campos['fechas_visitas']) ? array_fill(0, count($fechas_visitas), 1) : []; // Rellenar con un valor 1 por cada visita

    ?>
    <h1 class="wp-heading-inline"><?php echo esc_html($campos['tipo_inmueble']) . " en " . esc_html($campos['nombre_calle']) . " (" . esc_html($campos['referencia']) . ")"; ?></h1>
    
    <div class="wrap">

        <div class="postbox-container half-width float-left margin-right">
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

        <div class="postbox-container half-width float-left">
            <div class="postbox">
                <h2 class="hndle"><span>Detalles del Inmueble</span></h2>
                <div class="inside">
                    <p><strong>Tipo de Inmueble:</strong> <?php echo esc_html($campos['tipo_inmueble']); ?></p>
                    <p><strong>Tipo de Operación:</strong> <?php echo ucfirst(esc_html($campos['tipo_operacion'])); ?></p>
                    <p><strong>Precio:</strong> <?php echo esc_html($campos['precio']); ?></p>
                    <p><strong>Metros Construidos:</strong> <?php echo esc_html($campos['metros_construidos']); ?></p>
                    <p><strong>Metros Útiles:</strong> <?php echo esc_html($campos['metros_utiles']); ?></p>
                    <p><strong>Número de Dormitorios:</strong> <?php echo esc_html($campos['num_dormitorios']); ?></p>
                    <p><strong>Número de Baños:</strong> <?php echo esc_html($campos['num_banos']); ?></p>
                    <p><strong>Zona del Inmueble:</strong> <?php echo esc_html($campos['zona_inmueble']); ?></p>
                    <?php if (is_array($campos['propietario']) && !empty($campos['propietario']['id'])): ?>
                        <p><strong>Propietario:</strong> 
                            <a href="<?php echo esc_url(get_edit_post_link($campos['propietario']['id'])); ?>">
                                <?php echo esc_html($campos['propietario']['nombre'] . ' ' . $campos['propietario']['apellidos']); ?>
                                (<?php echo esc_html($campos['propietario']['telefono']); ?>)
                            </a>
                        </p>
                    <?php else: ?>
                        <p><strong>Propietario:</strong> Sin propietario asignado.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="postbox-container half-width float-left margin-right clear-both">
            <!-- Aquí colocamos el canvas donde se renderizará la gráfica -->
            <div class="postbox-container half-width float-left margin-right clear-both">
        
            </div>
            <div class="postbox">
                <h2 class="hndle"><span>Listado de Visitas</span></h2>
                <div class="inside">
                    <?php if (!empty($campos['visitas']) && is_array($campos['fechas_visitas']) && !empty($campos['fechas_visitas'])): ?>
                        <canvas id="graficaVisitas"></canvas>
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

        <div class="postbox-container half-width float-left">
            <div class="postbox">
                <h2 class="hndle"><span>Inmuebles en la misma zona</span></h2>
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

        <div class="postbox-container half-width float-left margin-right clear-both">
            <div class="postbox">
                <h2 class="hndle"><span>Citas Relacionadas</span></h2>
                <div class="inside">
                    <?php if (!empty($campos['citas'])): ?>
                        <ul>
                            <?php foreach ($campos['citas'] as $cita): ?>
                                <li>
                                    <strong>Fecha:</strong> <?php echo esc_html($cita['fecha']); ?><br>
                                    <strong>Hora:</strong> <?php echo esc_html($cita['hora']); ?><br>
                                    <strong>Comentario:</strong> <?php echo esc_html($cita['comentario']); ?><br>
                                    <strong>Demanda:</strong> 
                                    <a href="<?php echo esc_url(get_edit_post_link($cita['demanda']['id'])); ?>">
                                        <?php echo esc_html($cita['demanda']['nombre']); ?> (<?php echo esc_html($cita['demanda']['telefono']); ?>)
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No hay citas registradas para este inmueble.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Pasar los datos a JavaScript -->
    <script type="text/javascript">
        window.fechasVisitas = <?php echo json_encode($fechas_visitas); ?>;
        window.visitas = <?php echo json_encode($visitas); ?>;
    </script>

    <?php
}

