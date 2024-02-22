<?php

class Propietario {
    public function __construct() {
        add_action('init', array($this, 'registrar_cpt_propietario'));
        add_action('add_meta_boxes', array($this, 'propietarios_meta_box'));
        add_action('save_post', array($this, 'inmuebles_guardar_campos_propietario'));
        add_filter('manage_propietario_posts_columns', array($this, 'agregar_columnas_personalizadas_propietario'));
        add_action('manage_propietario_posts_custom_column', array($this, 'mostrar_datos_columnas_personalizadas_propietario'), 10, 2);
        add_filter('post_row_actions', array($this, 'desactivar_quick_edit_propietario'), 10, 2);
        add_filter('post_row_actions', array($this, 'modificar_texto_accion_propietario'), 10, 2);
    }

    public function registrar_cpt_propietario() {
        $labels = array(
            'name' => 'Propietario',
            'singular_name' => 'Propietario',
            'menu_name' => 'Propietarios',
            'name_admin_bar' => 'Propietario',
            'add_new' => 'Añadir Propietario',
            'add_new_item' => 'Añadir Nuevo Propietario',
            'new_item' => 'Nuevo Propietario',
            'edit_item' => 'Editar Propietario',
            'view_item' => 'Ver Propietario',
            'all_items' => 'Todos los Propietarios',
            'search_items' => 'Buscar Propietario',
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
                    <th><label for="email">Email*</label></th>
                    <td><input type="text" name="email" id="email" value="<?php echo esc_attr($email ?? ''); ?>" required></td>
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
     * Guarda los valores de los campos personalizados al guardar un propietario.
     * @param int $post_id ID del propietario actual.
     */
    public function inmuebles_guardar_campos_propietario($post_id) {
        // Validar que el email, teléfono y DNI sean únicos
        $email = sanitize_text_field($_POST['email']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $dni = sanitize_text_field($_POST['dni']);
    
        $args = array(
            'post_type' => 'propietario',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'email',
                    'value' => $email,
                    'compare' => '=',
                ),
                array(
                    'key' => 'telefono',
                    'value' => $telefono,
                    'compare' => '=',
                ),
                array(
                    'key' => 'dni',
                    'value' => $dni,
                    'compare' => '=',
                ),
            ),
        );
    
        $propietarios = get_posts($args);
        // Verificar si el propietario encontrado es el mismo que se está editando
        $esPropietarioDuplicado = false;
        foreach ($propietarios as $propietario) {
            if ($propietario->ID != $post_id) {
                $esPropietarioDuplicado = true;
                break;
            }
        }
        // Si se encontró algún propietario con el mismo email, teléfono o DNI, no guardar los metadatos
        if ($esPropietarioDuplicado) {
            // Mostrar un mensaje de error
            set_transient('propietario_datos_duplicados', true, 5);
            return;
        }
    
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
    public function agregar_columnas_personalizadas_propietario($columns) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'telefono' => 'Teléfono',
            'email' => 'Email',
            'date' => 'Fecha de publicación',
        );
        return $columns;
    }

    /**
     * Mostrar_datos_columnas_personalizadas_propietario
     */
    public function mostrar_datos_columnas_personalizadas_propietario($column, $post_id) {
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


 // Mensaje de error de cliente duplicado
 function mostrar_error_propietario_datos_duplicados() {
    if (get_transient('propietario_datos_duplicados')) {
        ?>
        <div class="notice notice-error">
            <p><?php _e('El email, teléfono o DNI ingresados ya existen en otro propietario.', 'text-domain'); ?></p>
        </div>
        <?php
        delete_transient('propietario_datos_duplicados');
    }
}
add_action('admin_notices', 'mostrar_error_propietario_datos_duplicados');


//Amplia la busqueda en campos personalizados de propietario 
add_filter('posts_search', 'buscar_en_campos_propietario', 10, 2);
function buscar_en_campos_propietario($search, $wp_query) {
    global $wpdb;
    if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
        // Estamos en la búsqueda de la administración
        if (is_admin() && $wp_query->query_vars['post_type'] == 'propietario') {
            // Obtener términos de búsqueda
            $terms = $wp_query->query_vars['search_terms'];
            // Campos meta que deseas buscar
            $meta_keys = array('nombre', 'apellidos', 'telefono', 'email', 'dni');
            // Inicializar la cláusula de búsqueda de campos meta
            $meta_search = '';
            foreach ($meta_keys as $meta_key) {
                // Agregar cláusula para cada campo meta
                $meta_search .= " OR EXISTS (
                    SELECT * FROM {$wpdb->postmeta}
                    WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
                    AND {$wpdb->postmeta}.meta_key = '{$meta_key}'
                    AND {$wpdb->postmeta}.meta_value LIKE '%" . implode("%' OR {$wpdb->postmeta}.meta_value LIKE '%", $terms) . "%'
                )";
            }
            // Agregar la cláusula de búsqueda de campo meta a la consulta principal
            $search .= $meta_search;
        }
    }
    return $search;
}
