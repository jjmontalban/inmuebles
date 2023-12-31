<?php 
/**
 * Agregar columnas personalizadas a la lista de entradas de demandas
 * @param int $columns
 */
function agregar_columnas_personalizadas_demanda($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'nombre' => 'Nombre',
        'telefono' => 'Teléfono',
        'email' => 'Email',
        'inmueble_interesado' => 'Inmueble por el que se interesó',
        'date' => 'Fecha de publicación',
    );
    return $columns;
}
add_filter('manage_demanda_posts_columns', 'agregar_columnas_personalizadas_demanda');

function mostrar_datos_columnas_personalizadas_demanda($column, $post_id) {
    switch ($column) {
        case 'nombre':
            echo get_post_meta($post_id, 'nombre', true);
            break;
        case 'telefono':
            echo get_post_meta($post_id, 'telefono', true);
            break;
        case 'email':
            echo get_post_meta($post_id, 'email', true);
            break;
        case 'inmueble_interesado':
            $inmueble_interesado = get_post_meta($post_id, 'inmueble_interesado', true);
            if (is_numeric($inmueble_interesado)) {
                $tipo_inmueble = get_post_meta($inmueble_interesado, 'tipo_inmueble', true);
                $nombre_calle = get_post_meta($inmueble_interesado, 'nombre_calle', true);
                echo esc_html($tipo_inmueble . ' en ' . $nombre_calle);
            } else {
                echo esc_html($inmueble_interesado);
            }
            break;
    }
}
add_action('manage_demanda_posts_custom_column', 'mostrar_datos_columnas_personalizadas_demanda', 10, 2);




/**
 * Agrega el metabox "Datos de la demanda" al formulario de edición de propietarios.
 */
function demanda_meta_box() {
    add_meta_box('demanda_info', 
                 'Información de la demanda', 
                 'mostrar_campos_demanda', 
                 'demanda',
                 'normal',
                 'high' );
}
add_action('add_meta_boxes', 'demanda_meta_box');


/**
 * Muestra los campos de la demanda en el formulario de edición.
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_campos_demanda( $post ) {  
    $nombre = get_post_meta($post->ID, 'nombre', true);
    $email = get_post_meta($post->ID, 'email', true);
    $telefono = get_post_meta($post->ID, 'telefono', true);
    $email = get_post_meta($post->ID, 'email', true);
    $notas = get_post_meta($post->ID, 'notas', true);
    $inmueble_interesado = get_post_meta($post->ID, 'inmueble_interesado', true);
    
    // Obtiene la lista de inmuebles para el select
    $args = array(
        'post_type' => 'inmueble',
        'posts_per_page' => -1,
    );
    $inmuebles = get_posts($args);
    ?>

    <table class="form-table">
        <tr>
            <th><label for="nombre">Nombre*</label></th>
            <td><input type="text" name="nombre" id="nombre" value="<?php echo esc_attr( $nombre ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="email">Email</label></th>
            <td><input type="text" name="email" id="email" value="<?php echo esc_attr( $email ?? ''); ?>" ></td>
        </tr>
        <tr>
            <th><label for="telefono">Teléfono*</label></th>
            <td><input type="text" name="telefono" id="telefono" value="<?php echo esc_attr( $telefono ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="inmueble_interesado">Inmueble interesado</label></th>
            <td>
                <select name="inmueble_interesado" id="inmueble_interesado">
                    <option value="">Selecciona un inmueble</option>
                    <?php foreach ($inmuebles as $inmueble) : ?>
                        <?php
                        $tipo_inmueble = get_post_meta($inmueble->ID, 'tipo_inmueble', true);
                        $nombre_calle = get_post_meta($inmueble->ID, 'nombre_calle', true);
                        $inmueble_id = $inmueble->ID;
                        $selected = ($inmueble_id == $inmueble_interesado) ? 'selected' : '';
                        ?>
                        <option value="<?php echo esc_attr($inmueble_id); ?>" <?php echo esc_attr($selected); ?>>
                            <?php echo esc_html($tipo_inmueble . ' en ' . $nombre_calle); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="notas">Notas</label></th>
            <td><input type="text" name="notas" id="notas" value="<?php echo esc_attr( $notas ?? ''); ?>"></td>
        </tr>
    </table>
    <?php 
}

/**
 * Guarda los valores de los campos personalizados al guardar una demanda.
 * @param int $post_id ID de la demanda actual.
 */
function inmuebles_guardar_campos_demanda( $post_id ) {
    if (array_key_exists('nombre', $_POST)) {
        update_post_meta($post_id, 'nombre', sanitize_text_field($_POST['nombre']));
    }
    if (array_key_exists('email', $_POST)) {
        update_post_meta($post_id, 'email', sanitize_text_field($_POST['email']));
    }
    if (array_key_exists('telefono', $_POST)) {
        update_post_meta($post_id, 'telefono', sanitize_text_field($_POST['telefono']));
    }
    if (array_key_exists('notas', $_POST)) {
        update_post_meta($post_id, 'notas', sanitize_text_field($_POST['notas']));
    }
}
add_action('save_post', 'inmuebles_guardar_campos_demanda');



/**
 * Cambiar texto editar por ver en el menu de acciones
 */
function modificar_texto_accion_demanda($actions, $post) {
    if ($post->post_type === 'demanda') {
        if (isset($actions['edit'])) {
            $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
        }
    }

    return $actions;
}
add_filter('post_row_actions', 'modificar_texto_accion_demanda', 10, 2);


/**
 * Desactivar edicion rápida
 */
function desactivar_quick_edit_demanda($actions, $post) {
    
    if ($post->post_type === 'demanda') {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
add_filter('post_row_actions', 'desactivar_quick_edit_demanda', 10, 2);
