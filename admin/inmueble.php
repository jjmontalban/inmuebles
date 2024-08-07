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
     * Desactivar edicion rápida
     */
    public function desactivar_quick_edit_inmueble($actions, $post) {
        
        if ($post->post_type === 'inmueble') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }


    /**
     * Aplica el nombre del post inmueble
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





function inmueble_add_meta_boxes() {
    add_meta_box(
        'inmueble_meta_box', // ID de la caja de meta
        'Información de Identificación (se asigna automáticamente)', // Título de la caja de meta
        'inmueble_meta_box_identification', // Función que muestra el contenido de la caja de meta
        'inmueble', // Tipo de publicación donde se mostrará la caja de meta
        'normal', // Contexto donde se mostrará la caja de meta
        'high' // Prioridad de la caja de meta
    );
}
add_action('add_meta_boxes', 'inmueble_add_meta_boxes');

function inmueble_meta_box_identification($post) {
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

function inmueble_save_post($post_id) {
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
add_action('save_post', 'inmueble_save_post');

// Asegurarse de que la opción exista al activar el plugin
function inmueble_activate() {
    if (false === get_option('last_inmueble_reference')) {
        add_option('last_inmueble_reference', 0);
    }
}
register_activation_hook(__FILE__, 'inmueble_activate');





/**
 * Asigna titulo del post al front del inmueble
 */
function modificar_titulo_pagina() {
    if (is_singular('inmueble')) {
        global $post;
        $nombre_calle = get_post_meta($post->ID, 'nombre_calle', true);
        if ($nombre_calle) {
            echo '<script>document.title = "' . esc_js($nombre_calle) . '";</script>';
        }
    }
}
add_action('wp_head', 'modificar_titulo_pagina');



// Automatizar SEO. Actualizar Título y Descripción al Guardar un Inmueble
// Función para actualizar el título y la descripción SEO al guardar un inmueble
function actualizar_seo_para_inmueble($post_id) {
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
add_action('save_post', 'actualizar_seo_para_inmueble');



