<?php 
/**
 * Agrega el metabox "Datos de la cita" al formulario de edición.
 */
function citas_meta_box() {
    add_meta_box(
        'citas_info',
        'Información de la Cita',
        'mostrar_campos_cita',
        'cita',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'citas_meta_box');


/**
 * Muestra los campos de la cita en el formulario de edición.
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_campos_cita($post) {
    $inmueble_id = get_post_meta($post->ID, 'inmueble_id', true);
    $demanda_id = get_post_meta($post->ID, 'demanda_id', true);
    $fecha = get_post_meta($post->ID, 'fecha', true);
    
    // Obtiene la lista de inmuebles para el select
    $args = array(
        'post_type' => 'inmueble', // El nombre de tu CPT de inmuebles
        'posts_per_page' => -1,
    );
    $inmuebles = get_posts($args);
    
    // Obtiene la lista de demandas para el select
    $args = array(
        'post_type' => 'demanda', // El nombre de tu CPT de demandas
        'posts_per_page' => -1,
    );
    $demandas = get_posts($args);
    
    ?>

    
    <table class="form-table">
        <tr>
            <th><label for="inmueble_id">Inmueble</label></th>
            <td>
                <select name="inmueble_id" id="inmueble_id">
                    <option value="">Selecciona un inmueble</option>
                    <?php foreach ($inmuebles as $inmueble) : ?>
                        <?php
                        $tipo_inmueble = get_post_meta($inmueble->ID, 'tipo_inmueble', true);
                        $nombre_calle = get_post_meta($inmueble->ID, 'nombre_calle', true);
                        ?>
                        <option value="<?php echo esc_attr($inmueble->ID); ?>" <?php selected($inmueble_id, $inmueble->ID); ?>>
                            <?php echo esc_html($tipo_inmueble . ' en ' . $nombre_calle); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="demanda_id">Demanda</label></th>
            <td>
                <select name="demanda_id" id="demanda_id">
                    <option value="">Selecciona una demanda</option>
                    <?php foreach ($demandas as $demanda) : ?>
                        <option value="<?php echo esc_attr($demanda->ID); ?>" <?php selected($demanda_id, $demanda->ID); ?>>
                            <?php echo esc_html( $demanda->nombre ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
    <th><label for="fecha">Fecha y Hora</label></th>
    <td>
        <?php
        $fecha = get_post_meta($post->ID, 'fecha', true);
        $hora = get_post_meta($post->ID, 'hora', true);

        ?>
        <input type="date" name="fecha" id="fecha" value="<?php echo esc_attr( $fecha ?? ''); ?>" required>
        <input type="time" name="hora" id="hora" value="<?php echo esc_attr( $hora ?? ''); ?>" required>
    </td>
</tr>


    </table>
    <?php
}

/**
 * Guarda los valores de los campos personalizados al guardar una cita.
 * @param int $post_id ID de la cita actual.
 */
function guardar_campos_cita($post_id) {

     if ( get_post_type($post_id) !== 'cita') {
         return;
     }

    if (array_key_exists('inmueble_id', $_POST)) {
        update_post_meta($post_id, 'inmueble_id', sanitize_text_field($_POST['inmueble_id']));
    }
    if (array_key_exists('demanda_id', $_POST)) {
        update_post_meta($post_id, 'demanda_id', sanitize_text_field($_POST['demanda_id']));
    }
    if (array_key_exists('fecha', $_POST)) {
        update_post_meta($post_id, 'fecha', sanitize_text_field($_POST['fecha']));
    }
    if (array_key_exists('hora', $_POST)) {
        update_post_meta($post_id, 'hora', sanitize_text_field($_POST['hora']));
    }

    // Obtener información necesaria para el correo electrónico
    $inmueble_id = get_post_meta($post_id, 'inmueble_id', true);
    $demanda_id = get_post_meta($post_id, 'demanda_id', true);
    $fecha = get_post_meta($post_id, 'fecha', true);
    $hora = get_post_meta($post_id, 'hora', true);

    // Obtener la dirección de correo electrónico del administrador del sitio
    $admin_email = get_option('admin_email');

    // Obtener la dirección de correo electrónico de la demanda
    $demanda_email = get_post_meta($demanda_id, 'email', true);

    // Asunto y contenido del correo electrónico
    $subject = 'Nueva cita agendada';
    $message = "Se ha agendado una nueva cita:\n";
    $message .= "Fecha: $fecha\n";
    $message .= "Hora: $hora\n";
    $message .= "Inmueble ID: $inmueble_id\n";
    $message .= "Demanda ID: $demanda_id\n";

    // Enviar correo electrónico al administrador del sitio
    wp_mail($admin_email, $subject, $message);

    // Enviar correo electrónico a la demanda
    wp_mail($demanda_email, $subject, $message);

}
add_action('save_post', 'guardar_campos_cita');



/**
 * Agregar columnas personalizadas a la lista de entradas de "citas"
 * @param int $columns
 */
function agregar_columnas_personalizadas_cita($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'demanda' => 'Nombre demanda',
        'inmueble' => 'Nombre Inmueble',
        'fecha' => 'Fecha de la cita',
        'hora' => 'Hora de la cita',
    );
    return $columns;
}
add_filter('manage_cita_posts_columns', 'agregar_columnas_personalizadas_cita');

// Mostrar datos en las columnas personalizadas
function mostrar_datos_columnas_personalizadas_cita($column, $post_id) {
    switch ($column) {
        case 'inmueble':
            $inmueble_id = get_post_meta($post_id, 'inmueble_id', true);
            echo get_the_title($inmueble_id);
            break;
        case 'demanda':
            $demanda_id = get_post_meta($post_id, 'demanda_id', true);
            echo get_post_meta($demanda_id, 'nombre', true);
            break;
        case 'fecha':
            $fecha = get_post_meta($post_id, 'fecha', true);
            echo date('d/m/Y', strtotime($fecha));
            break;
        case 'hora':
            $hora = get_post_meta($post_id, 'hora', true);
            echo date('H:i', strtotime($hora));
            break;
        default:
            break;
    }
}
add_action('manage_cita_posts_custom_column', 'mostrar_datos_columnas_personalizadas_cita', 10, 2);

/**
 * Cambiar texto editar por ver
 */
function modificar_texto_accion_cita($actions, $post) {
    // Solo modificar para el tipo de publicación 'cita'
    if ($post->post_type === 'cita') {
        if (isset($actions['edit'])) {
            $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
        }
    }

    return $actions;
}
add_filter('post_row_actions', 'modificar_texto_accion_cita', 10, 2);



/**
 * Desactivar edicion rápida
 */
function desactivar_quick_edit_cita($actions, $post) {
    
    if ($post->post_type === 'cita') {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
add_filter('post_row_actions', 'desactivar_quick_edit_cita', 10, 2);
