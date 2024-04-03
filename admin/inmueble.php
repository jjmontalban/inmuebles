<?php 

class Inmueble
{
    public function __construct()
    {        
        require_once 'inmueble_mostrar_campos.php';
        require_once 'inmueble_guardar_campos.php';
        
        add_action('init', [$this, 'crear_cpt_inmueble']);
        add_action('init', [$this, 'registrar_taxonomia_tipo_inmueble']);
        add_filter('manage_inmueble_posts_columns', [$this, 'agregar_columnas_inmueble']);
        add_action('manage_inmueble_posts_custom_column', [$this, 'mostrar_datos_columnas_inmueble'], 10, 2);
        add_action( 'add_meta_boxes', [$this, 'inmuebles_agregar_mb_campos_inmueble']);
        add_action('save_post', [$this, 'asignar_tipo_inmueble_taxonomia']);
        add_filter('the_title', [$this, 'modificar_valor_columna_title'], 1, 2);        
        add_filter('post_row_actions', [$this, 'desactivar_quick_edit_inmueble'], 10, 2);
        add_filter('wp_insert_post_data', [$this, 'inmuebles_custom_permalink'], 10, 2);
        add_action( 'save_post', [$this, 'inmuebles_guardar_campos_inmueble']);   
        add_action('add_meta_boxes', array($this, 'inmuebles_agregar_mb_informe_inmueble')); 
        add_action('add_meta_boxes', array($this, 'inmuebles_agregar_mb_pdf_inmueble')); 
        add_action('admin_menu', array($this, 'registrar_informe_inmueble_page'));
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
     * Agregar columnas personalizadas en el listado de inmuebles.
     */
    public function agregar_columnas_inmueble($columns)
    {
        $columns['imagen_destacada'] = 'Imagen Destacada';
        return $columns;
    }

    /**
     * Llenar las columnas personalizadas en el listado de inmuebles.
     */
    public function mostrar_datos_columnas_inmueble($column, $post_id)
    {
        if ($column === 'imagen_destacada') {
            echo get_the_post_thumbnail($post_id, array(100, 100));
        }
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
    
    /**
    * Página de informe
    */
    public function registrar_informe_inmueble_page() {
        add_submenu_page(
            'edit.php?post_type=inmueble', // Slug del menú padre
            'Informe del Inmueble', // Título de la página
            'Informe del Inmueble', // Título del menú
            'edit_posts', // Capacidad requerida para ver la página
            'informe-inmueble', // Slug de la página
            array($this, 'mostrar_informe_inmueble_page') // Función de devolución de llamada para mostrar el contenido de la página
        );
    }
    
    public function mostrar_informe_inmueble_page() {
        global $tipos_inmueble_map;
        global $zonas_inmueble_map;
    
        // Obtener el ID del inmueble desde la URL
        $inmueble_id = isset($_GET['inmueble_id']) ? intval($_GET['inmueble_id']) : 0;
        if ($inmueble_id > 0) {
            echo '<h1>' . ucfirst(get_post_meta($inmueble_id, 'nombre_calle', true)) . '</h1>'; // Muestra el nombre del inmueble
    
            // Mostrar otros campos personalizados
            $tipo_inmueble_key = get_post_meta($inmueble_id, 'tipo_inmueble', true);
            $tipo_inmueble = isset($tipos_inmueble_map[$tipo_inmueble_key]) ? $tipos_inmueble_map[$tipo_inmueble_key] : $tipo_inmueble_key;
    
            $tipo_operacion = get_post_meta($inmueble_id, 'tipo_operacion', true);
            $precio = ($tipo_operacion === 'venta') ? get_post_meta($inmueble_id, 'precio_venta', true) : get_post_meta($inmueble_id, 'precio_alquiler', true);
            $metros_construidos = get_post_meta($inmueble_id, 'm_construidos', true);
            $metros_utiles = get_post_meta($inmueble_id, 'm_utiles', true);
            $num_dormitorios = get_post_meta($inmueble_id, 'num_dormitorios', true);
            $num_banos = get_post_meta($inmueble_id, 'num_banos', true);
    
            $zona_inmueble_key = get_post_meta($inmueble_id, 'zona_inmueble', true);
            $zona_inmueble = isset($zonas_inmueble_map[$zona_inmueble_key]) ? $zonas_inmueble_map[$zona_inmueble_key] : $zona_inmueble_key;
    
            echo '<p><strong>Tipo de Inmueble:</strong> ' . $tipo_inmueble . '</p>';
            echo '<p><strong>Tipo de Operación:</strong> ' . ucfirst($tipo_operacion) . '</p>';
            echo '<p><strong>Precio:</strong> ' . $precio . '</p>';
            echo '<p><strong>Metros Construidos:</strong> ' . $metros_construidos . '</p>';
            echo '<p><strong>Metros Útiles:</strong> ' . $metros_utiles . '</p>';
            echo '<p><strong>Número de Dormitorios:</strong> ' . $num_dormitorios . '</p>';
            echo '<p><strong>Número de Baños:</strong> ' . $num_banos . '</p>';
            echo '<p><strong>Zona del Inmueble:</strong> ' . $zona_inmueble . '</p>';

            // Mostrar el listado de visitas y sus fechas
            $visitas = get_post_meta($inmueble_id, 'visitas', true);
            $fechas_visitas = get_post_meta($inmueble_id, 'fechas_visitas', true);

            if (!empty($visitas) && !empty($fechas_visitas)) {
                echo '<h2>Listado de Visitas</h2>';
                echo '<ul>';
                for ($i = 0; $i < count($fechas_visitas); $i++) {
                    echo '<li>Visita ' . ($i + 1) . ' - Fecha: ' . $fechas_visitas[$i] . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No hay visitas registradas aún.</p>';
            }

            // Nº de inmuebles en la misma zona
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
            $num_inmuebles_zona = $query->found_posts;
            echo '<p><strong>Número de inmuebles en la misma zona:</strong> ' . $num_inmuebles_zona . '</p>';
        }
    }
    

    /**
     * Informe de inmueble
     */
    public function inmuebles_agregar_mb_informe_inmueble() {
        add_meta_box(
            'inmueble_informe_inmueble',
            'Informe de Inmueble',
            array($this, 'mostrar_informe_inmueble'), // Callback
            'inmueble', // Donde se mostrará
            'side', // Contexto
            'default' // Prioridad
        );
    }

    public function mostrar_informe_inmueble($post) {
        $informe_url = admin_url('edit.php?post_type=inmueble&page=informe-inmueble&inmueble_id=' . $post->ID);
        echo '<button type="button" class="button button-primary button-large" onclick="window.location.href=\'' . esc_url($informe_url) . '\'">Crear Informe de Inmueble</button>';
    }


    /**
     * PDF de inmueble
     */
    public function inmuebles_agregar_mb_pdf_inmueble() {
        add_meta_box(
            'inmueble_pdf_inmueble',
            'PDF de Inmueble',
            array($this, 'inmuebles_boton_pdf'), // Callback
            'inmueble', // Donde se mostrará
            'side', // Contexto
            'default' // Prioridad
        );
    }

    public function inmuebles_boton_pdf($post) {
        echo '<button id="generar-pdf" type="button" class="button button-primary button-large">Crear PDF anuncio de Inmueble</button>';
    }
    

    public function inmuebles_generar_pdf($post_id) {
        // Obtén los detalles de la vivienda
        $vivienda = get_post($post_id);
        $campos = get_post_meta($post_id);

        // Crea una nueva instancia de TCPDF
        $pdf = new TCPDF();

        // Añade una página
        $pdf->AddPage();

        // Escribe los detalles de la vivienda en el PDF
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Write(0, $vivienda->post_title, '', 0, 'L', true, 0, false, false, 0);
        // Repite este paso para cada campo que quieras añadir al PDF

        // Cierra y genera el PDF
        $pdf->Output('vivienda.pdf', 'I');
    }

}

new Inmueble();


/* Cuenta las visitas de los inmuebles y registra la fecha de cada visita */
function contar_visitas_inmueble() {
    // Verificar si la página es un inmueble individual y si el usuario no es administrador ni editor
    if (is_singular('inmueble') && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts')) { 
        $inmueble_id = get_the_ID(); 

        // Obtener el número de visitas y las fechas anteriores (si existen)
        $visitas = get_post_meta($inmueble_id, 'visitas', true);
        $fechas_visitas = get_post_meta($inmueble_id, 'fechas_visitas', true);

        // Asegurarse de que $fechas_visitas sea un array
        if (!is_array($fechas_visitas)) {
            $fechas_visitas = array();
        }

        // Incrementar el número de visitas
        $visitas = empty($visitas) ? 1 : $visitas + 1;

        // Registrar la fecha de la visita actual
        $fecha_actual = current_time('mysql');
        $fechas_visitas[] = $fecha_actual;

        // Actualizar los metadatos del inmueble
        update_post_meta($inmueble_id, 'visitas', $visitas);
        update_post_meta($inmueble_id, 'fechas_visitas', $fechas_visitas);
    }
}

// Asegúrate de que la función esté agregada a la acción wp_footer
add_action('wp_footer', 'contar_visitas_inmueble');


/**
 * Añade titulo personalizado a entrada de tipo inmueble a YOAST
 */
function modificar_titulo_seo_inmueble($title) {
    if (is_singular('inmueble')) {
        $post_id = get_queried_object_id();
        $titulo_personalizado = get_post_meta($post_id, 'nombre_calle', true) . ' ' . get_post_meta($post_id, 'precio_venta', true);
        $title = $titulo_personalizado;
    }
    
    return $title;
}
add_filter('wpseo_title', 'modificar_titulo_seo_inmueble');



