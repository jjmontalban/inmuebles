<?php

class Propietario {
    public function __construct() {
        add_action('init', array($this, 'registrar_cpt_propietario'));
        add_action('add_meta_boxes', array($this, 'propietarios_meta_box'));
        add_action('save_post', array($this, 'inmuebles_guardar_campos_propietario'));
        add_filter('manage_propietario_posts_columns', array($this, 'agregar_columnas_propietario'));
        add_action('manage_propietario_posts_custom_column', array($this, 'mostrar_datos_columnas_propietario'), 10, 2);
        add_filter('post_row_actions', array($this, 'desactivar_quick_edit_propietario'), 10, 2);
        add_filter('post_row_actions', array($this, 'modificar_texto_accion_propietario'), 10, 2);
    }

    public function registrar_cpt_propietario() {
        $labels = array(
            'name' => 'Propietario',
            'singular_name' => 'Propietario',
            'add_new' => 'Añadir Propietario',
            'add_new_item' => 'Añadir Nuevo Propietario',
            'menu_name' => 'Propietarios',
            'name_admin_bar' => 'Propietario',
            'new_item' => 'Nuevo Propietario',
            'edit_item' => 'Editar Propietario',
            'view_item' => 'Ver Propietario',
            'all_items' => 'Todos los Propietarios',
            'search_items' => 'Buscar por nombre, apellidos, teléfono, email o dni',
            'not_found' => 'No se encontraron Propietarios',
        );
    
        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'has_archive' => true,
            'rewrite' => array( 'slug' => 'propietario' ),
            'menu_icon' => 'dashicons-admin-users',
            'supports' => array( '' ),
        );
        register_post_type('propietario', $args);
    }
    
    
    /**
     * Agrega el metabox "Datos del propietario" al formulario de edición de propietarios.
     */
    public function propietarios_meta_box() {
        add_meta_box('propietarios_info', 
            'Información del Propietario', 
            array($this, 'mostrar_campos_propietario'), 
            'propietario',
            'normal',
            'high' );
    }

    /**
     * Muestra los campos del propietario en el formulario de edición de propietarios.
     * @param WP_Post $post El objeto de entrada actual.
     */
    public function mostrar_campos_propietario($post) {
        $nombre = get_post_meta($post->ID, 'nombre', true);
        $apellidos = get_post_meta($post->ID, 'apellidos', true);
        $email = get_post_meta($post->ID, 'email', true);
        $telefono = get_post_meta($post->ID, 'telefono', true);
        $dni = get_post_meta($post->ID, 'dni', true);
        $inmuebles_asignados = get_post_meta($post->ID, 'inmuebles_asignados', true);
        $inmuebles_asignados = is_array($inmuebles_asignados) ? $inmuebles_asignados : array();
        // Obtener todos los Inmuebles
        $args = array(
            'post_type' => 'inmueble',
            'posts_per_page' => -1,
        );
        $inmuebles = get_posts($args);
        ?>
        <div id="contenedor-propietario">
            <table class="form-table">
                <tr>
                    <th><label for="nombre">Nombre*</label></th>
                    <td><input type="text" name="nombre" id="nombre" value="<?php echo esc_attr($nombre ?? ''); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="apellidos">Apellidos</label></th>
                    <td><input type="text" name="apellidos" id="apellidos" value="<?php echo esc_attr($apellidos ?? ''); ?>"></td>
                </tr>
                <tr>
                    <th><label for="email">Email</label></th>
                    <td><input type="text" name="email" id="email" value="<?php echo esc_attr($email ?? ''); ?>"></td>
                </tr>
                <tr>
                    <th><label for="telefono">Teléfono*</label></th>
                    <td><input type="text" name="telefono" id="telefono" value="<?php echo esc_attr($telefono ?? ''); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="dni">DNI</label></th>
                    <td><input type="text" name="dni" id="dni" value="<?php echo esc_attr($dni ?? ''); ?>"></td>
                </tr>
                <!-- Nuevo campo para seleccionar Inmuebles -->
                <tr>
                    <th><label for="inmuebles_asignados">Inmuebles Asignados</label></th>
                    <td>
                    <?php foreach ($inmuebles as $inmueble) : ?>
                        <?php
                        $propietario_asignado = get_post_meta($inmueble->ID, 'propietario_id', true);
                        if (!$propietario_asignado || $propietario_asignado == $post->ID) {
                            $tipo_inmueble = get_post_meta($inmueble->ID, 'tipo_inmueble', true);
                            $nombre_calle = get_post_meta($inmueble->ID, 'nombre_calle', true);
                            $nombre_inmueble = $tipo_inmueble . ' en ' . $nombre_calle;
                            ?>
                            <label>
                                <input type="checkbox" name="inmuebles_asignados[]" value="<?php echo esc_attr($inmueble->ID); ?>" <?php checked(in_array($inmueble->ID, $inmuebles_asignados), true); ?>>
                                <?php echo esc_html($nombre_inmueble); ?>
                            </label><br>
                        <?php } ?>
                    <?php endforeach; ?>
                    </td>
                </tr>

            </table>
        </div>
        <?php 
    }


/**
 * Saves custom field values when saving an propietario
 * @param int $post_id ID of the current propietario
 */
function inmuebles_guardar_campos_propietario($post_id) {

    // Guardar los metadatos del propietario
    if (array_key_exists('nombre', $_POST)) {
        update_post_meta($post_id, 'nombre', sanitize_text_field($_POST['nombre']));
    }
    if (array_key_exists('apellidos', $_POST)) {
        update_post_meta($post_id, 'apellidos', sanitize_text_field($_POST['apellidos']));
    }
    if (array_key_exists('email', $_POST)) {
        update_post_meta($post_id, 'email', sanitize_text_field($_POST['email']));
    }
    if (array_key_exists('telefono', $_POST)) {
        update_post_meta($post_id, 'telefono', sanitize_text_field($_POST['telefono']));
    }
    if (array_key_exists('dni', $_POST)) {
        update_post_meta($post_id, 'dni', sanitize_text_field($_POST['dni']));
    }
    // Obtén los inmuebles previamente asignados
    $inmuebles_previos = get_post_meta($post_id, 'inmuebles_asignados', true);
    $inmuebles_previos = is_array($inmuebles_previos) ? $inmuebles_previos : array();
    // Desasigna el propietario de los inmuebles previamente asignados
    foreach ($inmuebles_previos as $inmueble_id) {
        delete_post_meta($inmueble_id, 'propietario_asignado');
    }
    // Guardar Inmuebles Asignados
    if (array_key_exists('inmuebles_asignados', $_POST)) {
        $inmuebles_asignados = array_map('sanitize_text_field', $_POST['inmuebles_asignados']);
        update_post_meta($post_id, 'inmuebles_asignados', $inmuebles_asignados);
        // Asigna el propietario a los nuevos inmuebles
        foreach ($inmuebles_asignados as $inmueble_id) {
            update_post_meta($inmueble_id, 'propietario_id', $post_id);
        }
    } else {
        // Si no se seleccionó ningún inmueble, guardar un array vacío
        update_post_meta($post_id, 'inmuebles_asignados', array());
    }

}
    /**
     * Agregar columnas personalizadas a la lista de entradas de "Propietario"
     * @param int $columns
     */
    public function agregar_columnas_propietario($columns) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'titulo_inmueble' => 'Inmueble',
            'telefono' => 'Teléfono',
            'email' => 'Email',
            'date' => 'Fecha de publicación',
        );
        return $columns;
    }

    /**
     * mostrar_datos_columnas_propietario
     */
    public function mostrar_datos_columnas_propietario($column, $post_id) {
        switch ($column) {
            case 'nombre':
                echo get_post_meta($post_id, 'nombre', true);
                break;
            case 'apellidos':
                echo get_post_meta($post_id, 'apellidos', true);
                break;
            case 'telefono':
                echo get_post_meta($post_id, 'telefono', true);
                break;
            case 'email':
                echo get_post_meta($post_id, 'email', true);
                break;
            case 'titulo_inmueble':
                $inmuebles_asignados = get_post_meta($post_id, 'inmuebles_asignados', true);
                if (!empty($inmuebles_asignados) && is_array($inmuebles_asignados)) {
                    // Obtener el primer inmueble asignado
                    $primer_inmueble_id = $inmuebles_asignados[0];
                    // Obtener el título del primer inmueble asignado
                    $primer_inmueble_titulo = get_the_title($primer_inmueble_id);
                    // Mostrar el título del primer inmueble asignado
                    echo $primer_inmueble_titulo ? $primer_inmueble_titulo : 'N/A';
                } else {
                    echo 'N/A';
                }
                break;
            default:
                break;
        }
    }

    
    /**
     * Desactivar edicion rápida
     */
    public function desactivar_quick_edit_propietario($actions, $post) {
        
        if ($post->post_type === 'propietario') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    /**
     * Cambiar texto editar por ver
     */
    public function modificar_texto_accion_propietario($actions, $post) {
        // Solo modificar para el tipo de publicación 'consulta'
        if ($post->post_type === 'propietario') {
            if (isset($actions['edit'])) {
                $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
            }
        }

        return $actions;
    }
}

new Propietario();

/**
 * Amplia la búsqueda en campos personalizados de propietario.
 */
function buscar_en_campos_propietario($search, $wp_query) {
    if (!empty($search) && !empty($wp_query->query_vars['search_terms']) && $wp_query->query_vars['post_type'] == 'propietario') {
        $terms = $wp_query->query_vars['search_terms'];
        $meta_keys = array('nombre', 'apellidos', 'telefono', 'email', 'dni');
        $search .= construir_meta_search($meta_keys, $terms);
    }

    return $search;
}
add_filter('posts_search', 'buscar_en_campos_propietario', 10, 2);

/**
 * Valida
 */
function validar_datos_propietario($post_id, $data) {
    if ('propietario' !== $data['post_type'] || (isset($_GET['action']) && $_GET['action'] === 'trash')) return;

    $dni = isset($_POST['dni']) ? sanitize_text_field($_POST['dni']) : '';
    $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? sanitize_text_field($_POST['telefono']) : '';

    $meta_query = array('relation' => 'OR');
    $conflict_fields = array();

    if (!empty($dni)) {
        $meta_query[] = array(
            'key' => 'dni',
            'value' => $dni,
            'compare' => '=',
        );
        $exists_propietario_dni = new WP_Query(array(
            'post_type' => 'propietario',
            'meta_query' => array(
                array(
                    'key' => 'dni',
                    'value' => $dni,
                    'compare' => '=',
                )
            ),
            'post__not_in' => array($post_id),
        ));
        if ($exists_propietario_dni->have_posts()) {
            $conflict_fields[] = 'DNI';
        }
    }

    if (!empty($email)) {
        $meta_query[] = array(
            'key' => 'email',
            'value' => $email,
            'compare' => '=',
        );
        $exists_propietario_email = new WP_Query(array(
            'post_type' => 'propietario',
            'meta_query' => array(
                array(
                    'key' => 'email',
                    'value' => $email,
                    'compare' => '=',
                )
            ),
            'post__not_in' => array($post_id),
        ));
        if ($exists_propietario_email->have_posts()) {
            $conflict_fields[] = 'email';
        }
    }

    if (!empty($telefono)) {
        $meta_query[] = array(
            'key' => 'telefono',
            'value' => $telefono,
            'compare' => '=',
        );
        $exists_propietario_telefono = new WP_Query(array(
            'post_type' => 'propietario',
            'meta_query' => array(
                array(
                    'key' => 'telefono',
                    'value' => $telefono,
                    'compare' => '=',
                )
            ),
            'post__not_in' => array($post_id),
        ));
        if ($exists_propietario_telefono->have_posts()) {
            $conflict_fields[] = 'teléfono';
        }
    }

    if (count($meta_query) > 1) {
        $exists_propietario = new WP_Query(array(
            'post_type' => 'propietario',
            'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
            'meta_query' => $meta_query,
            'post__not_in' => array($post_id),
        ));

        if (!empty($exists_propietario) && $exists_propietario->have_posts()) {
            $conflict_fields_str = implode(', ', $conflict_fields);
            wp_die('Ya existe un propietario con el mismo ' . $conflict_fields_str . '. <br> <a href="javascript:history.back()">Volver</a>', 'Error', array('response' => 400));
        }
    }
}
add_action('pre_post_update', 'validar_datos_propietario', 10, 2);
