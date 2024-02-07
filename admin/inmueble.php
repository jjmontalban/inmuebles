<?php 

class Inmueble
{
    public function __construct()
    {        
        require_once 'inmueble_mostrar_campos.php';
        require_once 'inmueble_guardar_campos.php';
        
        add_action('init', [$this, 'crear_cpt_inmueble']);
        add_action('init', [$this, 'registrar_taxonomia_tipo_inmueble']);
        add_action( 'add_meta_boxes', [$this, 'inmuebles_agregar_mb_campos_inmueble']);
        add_action('save_post', [$this, 'asignar_tipo_inmueble_taxonomia']);
        add_filter('the_title', [$this, 'modificar_valor_columna_title'], 1, 2);        
        add_filter('post_row_actions', [$this, 'desactivar_quick_edit_inmueble'], 10, 2);
        add_filter('wp_insert_post_data', [$this, 'inmuebles_custom_permalink'], 10, 2);
        add_action( 'save_post', [$this, 'inmuebles_guardar_campos_inmueble']);    
    }


    /**
    * Registra el tipo de entrada personalizado 'inmueble'.
    */
    public function crear_cpt_inmueble() {
        $labels = array(
            'name' => 'Inmueble',
            'singular_name' => 'Inmueble',
            'menu_name' => 'Inmuebles',
            'name_admin_bar' => 'Inmueble',
            'add_new' => 'Añadir Inmueble',
            'add_new_item' => 'Añadir Nuevo Inmueble',
            'new_item' => 'Nuevo Inmueble',
            'edit_item' => 'Editar Inmueble',
            'view_item' => 'Ver Inmueble',
            'all_items' => 'Todos los Inmuebles',
            'search_items' => 'Buscar Inmuebles',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'rewrite' => array( 'slug' => 'inmueble' ),
            'menu_icon' => 'dashicons-admin-multisite',
            'supports' => array( 'thumbnail' )
        );

        register_post_type( 'inmueble', $args );
    }

    /**
    * Registra la taxonomía 'tipo de inmueble'.
    */
    public function registrar_taxonomia_tipo_inmueble() {
        $labels = array(
            'name' => 'Tipo de Inmueble',
            'singular_name' => 'Tipo de Inmueble',
            'search_items' => 'Buscar Tipos de Inmuebles',
            'all_items' => 'Todos los Tipos de Inmuebles',
            'parent_item' => 'Tipo de Inmueble Padre',
            'parent_item_colon' => 'Tipo de Inmueble Padre:',
            'edit_item' => 'Editar Tipo de Inmueble',
            'update_item' => 'Actualizar Tipo de Inmueble',
            'add_new_item' => 'Agregar Nuevo Tipo de Inmueble',
            'new_item_name' => 'Nuevo Nombre de Tipo de Inmueble',
            'menu_name' => 'Tipos de Inmuebles',
        );
    
        $args = array(
            'hierarchical' => false,
            'labels' => $labels,
            'public' => true,
            'show_ui' => false,
            'show_in_quick_edit' => false,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'tipo-inmueble'), // Personaliza la URL como desees
        );
    
        register_taxonomy('tipo_inmueble', 'inmueble', $args);
    }

    /**
     * Agrega el metabox "Datos del inmueble" al formulario de edición de inmuebles.
     */
    public function inmuebles_agregar_mb_campos_inmueble() {
        add_meta_box( 'inmueble_campos_inmueble',
                      'Datos del inmueble',
                      'mostrar_campos_inmueble',
                      'inmueble',
                      'normal',
                      'high' );
    }

    /**
     * Guarda la taxonomía 'tipo de inmueble' cada vez que se guarde un inmueble cogiendo el valor del campo personalizado "tipo_inmueble"
     */
    public function asignar_tipo_inmueble_taxonomia($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
            return $post_id;
        
        // Verifica si el tipo de entrada es "inmueble"
        if ('inmueble' != get_post_type($post_id)) 
            return $post_id;
        
        // Obtiene el valor del campo personalizado "tipo_inmueble"
        $tipo_inmueble = get_post_meta($post_id, 'tipo_inmueble', true);
        
        // Actualiza los términos de taxonomía
        wp_set_post_terms($post_id, $tipo_inmueble, 'tipo_inmueble', false);
    }

    /**
     * Guarda los valores de los campos personalizados al guardar un inmueble.
     * @param int $post_id ID del inmueble actual.
     */
    public function inmuebles_guardar_campos_inmueble( $post_id ) {
        guardar_campos_inmueble( $post_id );
    }

    /**
     * Modificar el valor de la columna "title" en el listado de 'inmueble'
     */
    public function modificar_valor_columna_title($title, $post_id) {
        if (get_post_type($post_id) == 'inmueble') {
            $titulo_personalizado = get_post_meta($post_id, 'nombre_calle', true) . ' ' . get_post_meta($post_id, 'precio_venta', true);
            //valor personalizado en lugar del título original
            $title = $titulo_personalizado;
        }
        return $title;
    }


    /**
     * Desactivar edicion rápida
     */
    public function desactivar_quick_edit_inmueble($actions, $post) {
        
        if ($post->post_type === 'inmueble') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }


    /**
     * Aplica el nombre del post inmuebble
     */
    public function inmuebles_custom_permalink($data, $postarr) {
        if ($data['post_type'] == 'inmueble') {
            $tipo_inmueble = isset($postarr['tipo_inmueble']) ? $postarr['tipo_inmueble'] : '';
            $nombre_calle = isset($postarr['nombre_calle']) ? $postarr['nombre_calle'] : '';
            $data['post_name'] = sanitize_title($tipo_inmueble . ' ' . $nombre_calle);
        }
        return $data;
    }
    
}

new Inmueble();