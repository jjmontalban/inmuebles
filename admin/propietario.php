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

    
    // registrar_cpt_propietario
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
            'search_items' => 'Buscar Propietarios',
        );
    
        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true, //solo desde el admin
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
    public function mostrar_campos_propietario( $post ) {  
        $nombre = get_post_meta($post->ID, 'nombre', true);
        $apellidos = get_post_meta($post->ID, 'apellidos', true);
        $email = get_post_meta($post->ID, 'email', true);
        $telefono = get_post_meta($post->ID, 'telefono', true);
        
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
                    <th><label for="telefono">Teléfono*</label></th>
                    <td><input type="text" name="telefono" id="telefono" value="<?php echo esc_attr( $telefono ?? ''); ?>" required></td>
                </tr>
            </table>
        </div>
        <?php 
    }


    /**
     * Guarda los valores de los campos personalizados al guardar un propietario.
     * @param int $post_id ID del propietario actual.
     */
    public function inmuebles_guardar_campos_propietario( $post_id ) {
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