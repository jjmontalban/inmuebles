<?php 


/**
 * Agrega el metabox "Datos del propietario" al formulario de edición de propietarios.
 */
function propietarios_meta_box() {
    add_meta_box('propietarios_info', 
                 'Información del Propietario', 
                 'mostrar_campos_propietario', 
                 'propietario',
                 'normal',
                 'high' );
}
add_action('add_meta_boxes', 'propietarios_meta_box');


/**
 * Muestra los campos del propietario en el formulario de edición de propietarios.
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_campos_propietario( $post ) {  
    $nombre = get_post_meta($post->ID, 'nombre', true);
    $apellidos = get_post_meta($post->ID, 'apellidos', true);
    $email = get_post_meta($post->ID, 'email', true);
    $telefono1 = get_post_meta($post->ID, 'telefono1', true);
    $telefono2 = get_post_meta($post->ID, 'telefono2', true);
    
    ?>
    <div id="contenedor-propietario">
        <table class="form-table">
            <tr>
                <th><label for="nombre">Nombre*</label></th>
                <td><input type="text" name="nombre" id="nombre" value="<?php echo esc_attr( $nombre ?? ''); ?>" required></td>
            </tr>
            <tr>
                <th><label for="apellidos">Apellidos</label></th>
                <td><input type="text" name="apellidos" id="apellidos" value="<?php echo esc_attr( $apellidos ?? ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="email">Email*</label></th>
                <td><input type="text" name="email" id="email" value="<?php echo esc_attr( $email ?? ''); ?>" required></td>
            </tr>
            <tr>
                <th><label for="telefono1">Teléfono 1*</label></th>
                <td><input type="text" name="telefono1" id="telefono1" value="<?php echo esc_attr( $telefono1 ?? ''); ?>" required></td>
            </tr>
            <tr>
                <th><label for="telefono2">Teléfono 2</label></th>
                <td><input type="text" name="telefono2" id="telefono2" value="<?php echo esc_attr( $telefono2 ?? ''); ?>"></td>
            </tr>
        </table>
    </div>
    <?php 
}

/**
 * Guarda los valores de los campos personalizados al guardar un propietario.
 * @param int $post_id ID del propietario actual.
 */
function inmuebles_guardar_campos_propietario( $post_id ) {
    if (array_key_exists('nombre', $_POST)) {
        update_post_meta($post_id, 'nombre', sanitize_text_field($_POST['nombre']));
    }
    if (array_key_exists('apellidos', $_POST)) {
        update_post_meta($post_id, 'apellidos', sanitize_text_field($_POST['apellidos']));
    }
    if (array_key_exists('email', $_POST)) {
        update_post_meta($post_id, 'email', sanitize_text_field($_POST['email']));
    }
    if (array_key_exists('telefono1', $_POST)) {
        update_post_meta($post_id, 'telefono1', sanitize_text_field($_POST['telefono1']));
    }
    if (array_key_exists('telefono2', $_POST)) {
        update_post_meta($post_id, 'telefono2', sanitize_text_field($_POST['telefono2']));
    }
}
add_action('save_post', 'inmuebles_guardar_campos_propietario');






/**
 * Agregar columnas personalizadas a la lista de entradas de "Propietario"
 * @param int $columns
 */
function agregar_columnas_personalizadas_propietario($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'nombre' => 'Nombre',
        'apellidos' => 'Apellidos',
        'telefono1' => 'Teléfono 1',
        'telefono2' => 'Teléfono 2',
        'email' => 'Email',
        'date' => 'Fecha de publicación',
    );
    return $columns;
}
add_filter('manage_propietario_posts_columns', 'agregar_columnas_personalizadas_propietario');

// Mostrar datos en las columnas personalizadas
function mostrar_datos_columnas_personalizadas_propietario($column, $post_id) {
    switch ($column) {
        case 'nombre':
            echo get_post_meta($post_id, 'nombre', true);
            break;
        case 'apellidos':
            echo get_post_meta($post_id, 'apellidos', true);
            break;
        case 'telefono1':
            echo get_post_meta($post_id, 'telefono1', true);
            break;
        case 'telefono2':
            echo get_post_meta($post_id, 'telefono2', true);
            break;
        case 'email':
            echo get_post_meta($post_id, 'email', true);
            break;
        default:
            break;
    }
}
add_action('manage_propietario_posts_custom_column', 'mostrar_datos_columnas_personalizadas_propietario', 10, 2);



/**
 * Desactivar edicion rápida
 */
function desactivar_quick_edit_propietario($actions, $post) {
    
    if ($post->post_type === 'propietario') {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
add_filter('post_row_actions', 'desactivar_quick_edit_propietario', 10, 2);


/**
 * Cambiar texto editar por ver
 */
function modificar_texto_accion_propietario($actions, $post) {
    // Solo modificar para el tipo de publicación 'consulta'
    if ($post->post_type === 'propietario') {
        if (isset($actions['edit'])) {
            $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
        }
    }

    return $actions;
}
add_filter('post_row_actions', 'modificar_texto_accion_propietario', 10, 2);

