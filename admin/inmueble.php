<?php 

class Inmueble
{
    public function __construct() {
        // Cargar dependencias para guardar y mostrar campos personalizados
        require_once 'inmueble_mostrar_campos.php';
        require_once 'inmueble_guardar_campos.php';
        require_once 'inmueble-pdf.php'; // Incluir la clase de PDF
        require_once 'inmueble-functions.php';

        // Registrar CPT y taxonomía
        add_action('init', [$this, 'crear_cpt_inmueble']);
        add_action('init', [$this, 'registrar_taxonomia_tipo_inmueble']);
        // Agregar columnas personalizadas y metaboxes
        add_filter('manage_inmueble_posts_columns', [$this, 'agregar_columnas_inmueble']);
        add_action('manage_inmueble_posts_custom_column', [$this, 'mostrar_datos_columnas_inmueble'], 10, 2);
        add_action('add_meta_boxes', [$this, 'inmuebles_agregar_mb_campos_inmueble']);
        add_action('add_meta_boxes', [$this, 'inmuebles_agregar_mb_campos_identificacion']);            
        // Guardar datos al guardar el post
        add_action('save_post', [$this, 'inmuebles_guardar_campos_inmueble'], $post_id);
        add_action('save_post', [$this, 'asignar_tipo_inmueble_taxonomia'], $post_id);
        add_action('save_post', [$this, 'guardar_meta_inmueble'], $post_id);
        add_action('save_post', [$this, 'actualizar_seo_inmueble'], $post_id);
        // Personalizar URL del inmueble
        add_filter('wp_insert_post_data', [$this, 'inmuebles_custom_permalink'], 10, 2);
        // Otras funcionalidades
        add_filter('the_title', [$this, 'modificar_valor_columna_title'], 1, 2);  
        add_filter('post_row_actions', [$this, 'desactivar_quick_edit_inmueble'], 10, 2);
    }


    /**
    * Registra CPT 'inmueble'.
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
            'supports' => array( 'thumbnail' ),
            'show_in_rest' => true
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
            'show_admin_column' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'tipo-inmueble'), // Personaliza la URL como desees
        );
    
        register_taxonomy('tipo_inmueble', 'inmueble', $args);
    }

    /**
     * Agregar columnas personalizadas en el listado de inmuebles.
     */
    public function agregar_columnas_inmueble($columns)
    {
        // Crear un nuevo array de columnas ordenadas
        $new_columns = array();
        // Añadir columnas en el orden deseado
        $new_columns['title'] = $columns['title']; 
        $new_columns['tipo_inmueble'] = 'Tipo de Inmueble';
        $new_columns['referencia'] = 'Referencia';
        $new_columns['imagen_destacada'] = 'Imagen Destacada';
        $new_columns['date'] = $columns['date'];
        $new_columns['tsf-seo-bar-wrap'] = $columns['tsf-seo-bar-wrap'];
    
        // Devolver el nuevo array de columnas ordenadas
        return $new_columns;
    }


    /**
     * Llenar las columnas personalizadas en el listado de inmuebles.
     */
    public function mostrar_datos_columnas_inmueble($column, $post_id)
    {
        global $tipos_inmueble_map;

        switch ($column) {
            case 'imagen_destacada':
                echo get_the_post_thumbnail($post_id, array(100, 100));
                break;
            case 'tipo_inmueble':
                $tipo_inmueble_serializado = get_post_meta($post_id, 'tipo_inmueble', true);
                $tipo_inmueble = maybe_unserialize($tipo_inmueble_serializado); // Deserializar el valor
                
                if (!empty($tipo_inmueble) && isset($tipos_inmueble_map[$tipo_inmueble])) {
                    echo esc_html($tipos_inmueble_map[$tipo_inmueble]);
                } else {
                    echo 'No definido';
                }
                break;
            case 'referencia':
                $referencia = get_post_meta($post_id, 'referencia', true);
                echo !empty($referencia) ? esc_html($referencia) : 'No definido';
                break;
            default:
                break;
        }
    }


    /**
     * Agrega el metabox "Datos del inmueble" al formulario de edición de inmuebles.
     */
    public function inmuebles_agregar_mb_campos_inmueble() {
        add_meta_box( 
                    'inmueble_campos_inmueble',
                    'Datos del inmueble',
                    'mostrar_campos_inmueble',
                    'inmueble',
                    'normal',
                    'high' );
    }


    /**
     * Guarda los valores de los campos personalizados al guardar un inmueble.
     * @param int $post_id ID del inmueble actual.
     */
    public function inmuebles_guardar_campos_inmueble( $post_id ) {
        guardar_campos_inmueble( $post_id );
    }


    /**
     * Aplica el nombre del post inmueble a la URL
     */
    public function inmuebles_custom_permalink($data, $postarr) {
        if ($data['post_type'] == 'inmueble') {
            // Obtener el tipo de inmueble, la dirección y la referencia
            $tipo_inmueble = isset($postarr['tipo_inmueble']) ? $postarr['tipo_inmueble'] : '';
            $nombre_calle = isset($postarr['nombre_calle']) ? $postarr['nombre_calle'] : '';
            $referencia = isset($postarr['referencia']) ? $postarr['referencia'] : '';
            // Eliminar el prefijo "chipi-" de la referencia si existe
            if (strpos($referencia, 'chipi-') === 0) {
                $referencia = str_replace('chipi-', '', $referencia);
            }
            // Generar el slug añadiendo la referencia para hacerlo único
            $data['post_name'] = sanitize_title($tipo_inmueble . ' ' . $nombre_calle . ' ' . $referencia);
        }
        return $data;
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
     * Guarda la taxonomía 'tipo de inmueble' cada vez que se guarde un inmueble cogiendo el valor del campo personalizado "tipo_inmueble"
     */
    function asignar_tipo_inmueble_taxonomia($post_id) {
        global $tipos_inmueble_map;
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        $tipo_inmueble = get_post_meta($post_id, 'tipo_inmueble', true);
    
        if (isset($tipos_inmueble_map[$tipo_inmueble])) {
            // Añadir un prefijo o sufijo único al slug del término
            $slug = $tipo_inmueble . '_' . uniqid();
    
            wp_insert_term($tipos_inmueble_map[$tipo_inmueble], 'tipo_inmueble', array(
                'slug' => $slug
            ));
    
            wp_set_object_terms($post_id, $slug, 'tipo_inmueble', false);
        }
    }

    /**
     * Guarda los metadatos del inmueble al guardar un post
     */
    function guardar_meta_inmueble($post_id) {
        // Comprobar si el nonce es válido
        if (!isset($_POST['inmueble_meta_box_nonce']) || !wp_verify_nonce($_POST['inmueble_meta_box_nonce'], 'inmueble_save_meta_box_data')) {
            return;
        }
        // Comprobar si es una autoguardado
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        // Comprobar permisos del usuario
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        // Asignar código si no existe
        if (!get_post_meta($post_id, 'codigo', true)) {
            $codigo = $post_id;
            update_post_meta($post_id, 'codigo', $codigo);
        }
        // Asignar referencia si no existe
        if (!get_post_meta($post_id, 'referencia', true)) {
            $last_reference = get_option('last_inmueble_reference', 0);
            $new_reference = sprintf('chipi-%04d', $last_reference + 1);
            update_post_meta($post_id, 'referencia', $new_reference);
            update_option('last_inmueble_reference', $last_reference + 1);
        }
    }


    /**
     * Método para actualizar el título y la descripción SEO al guardar un inmueble
     */
    public function actualizar_seo_inmueble($post_id) {
        // Verificar que el post sea del tipo 'inmueble'
        if (get_post_type($post_id) != 'inmueble') {
            return;
        }

        // Comprobar permisos del usuario
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Obtener instancias de The SEO Framework
        if (class_exists('The_SEO_Framework')) {
            $seo_framework = The_SEO_Framework();

            // Obtener el título y la descripción personalizados
            $title = get_post_meta($post_id, 'nombre_calle', true);
            $description = get_post_meta($post_id, 'descripcion', true);

            // Actualizar el título SEO si existe
            if ($title) {
                $seo_framework->set_title($post_id, $title);
            }

            // Actualizar la descripción SEO si existe
            if ($description) {
                $seo_framework->set_description($post_id, $description);
            }
        }
    }

    /**
     * Agregar metabox de indetificacion de inmueble
     */
    public function inmuebles_agregar_mb_campos_identificacion() {
        add_meta_box(
            'inmueble_meta_box_indentificacion',
            'Información de Identificación (se asigna automáticamente)',
            [$this, 'inmueble_meta_box_identification'],
            'inmueble',
            'side',
            'high'
        );
    }

    
    /**
     * Mostrar el contenido del metabox
     */
    public function inmueble_meta_box_identification($post) {
        $codigo = get_post_meta($post->ID, 'codigo', true);
        $referencia = get_post_meta($post->ID, 'referencia', true);
        // Agregar nonce para la verificación de seguridad
        wp_nonce_field('inmueble_save_meta_box_data', 'inmueble_meta_box_nonce');
        ?>
        <table>
            <tr>
                <th><label for="codigo">Código</label></th>
                <td><input type="text" id="codigo" name="codigo" value="<?php echo esc_attr($codigo); ?>" readonly></td>
            </tr>
            <tr>
                <th><label for="referencia">Referencia</label></th>
                <td><input type="text" id="referencia" name="referencia" value="<?php echo esc_attr($referencia); ?>" readonly></td>
            </tr>
        </table>
        <?php
    }
    
}

new Inmueble();



// Asegurarse de que la opción exista al activar el plugin
function inmueble_activate() {
    if (false === get_option('last_inmueble_reference')) {
        add_option('last_inmueble_reference', 0);
    }
}
register_activation_hook(__FILE__, 'inmueble_activate');
