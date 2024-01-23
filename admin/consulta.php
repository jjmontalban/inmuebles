<?php

class Consulta
{
    public function __construct()
    {
        add_action('init', [$this, 'crear_cpt_consulta']);
        add_action('add_meta_boxes', [$this, 'agregar_metabox_consulta']);
        add_action('admin_post_procesar_formulario_contacto', [$this, 'procesar_formulario_contacto']);
        add_action('admin_post_nopriv_procesar_formulario_contacto', [$this, 'procesar_formulario_contacto']);
        add_filter('manage_consulta_posts_columns', [$this, 'agregar_columnas_consulta']);
        add_action('manage_consulta_posts_custom_column', [$this, 'llenar_columnas_consulta'], 10, 2);
        add_filter('post_row_actions', [$this, 'modificar_texto_accion_consulta'], 10, 2);
        add_filter('post_row_actions', [$this, 'desactivar_quick_edit_consulta'], 10, 2);
        add_action('admin_menu', [$this, 'eliminar_submenu_consultas']);
        add_action('admin_head', [$this, 'ocultar_boton_anadir_nueva_consulta']);
        add_action('post_submitbox_start', [$this, 'boton_crear_demanda']);
        add_action('save_post_demanda', [$this, 'rellenar_campos_demanda'], 10, 1);
    }
    
    /**
     * Registra el tipo de entrada personalizado 'consulta'.
     */
    public function crear_cpt_consulta()
    {
        $labels = array(
            'name'                  => _x('Consultas', 'Post type general name', 'textdomain'),
            'singular_name'         => _x('Consulta', 'Post type singular name', 'textdomain'),
            'menu_name'             => _x('Consultas', 'Admin Menu text', 'textdomain'),
            'name_admin_bar'        => _x('Consulta', 'Add New on Toolbar', 'textdomain'),
            'edit_item'             => __('Ver consulta', 'textdomain'), 
        
        );
    
        $args = array(
            'labels'             => $labels, // Agregamos las etiquetas definidas anteriormente
            'public'             => false,
            'show_in_menu'       => true,
            'show_ui' => true,
            'supports'           => array('')
        );
        register_post_type('consulta', $args);
    }


    /**
     * Agrega el metabox "Datos de la consulta" a la página de consulta
     */
    public function agregar_metabox_consulta()
    {
         add_meta_box('datos_consulta', 
                 'Datos de la Consulta', 
                 array($this, 'mostrar_metabox_consulta'),
                 'consulta', 
                 'normal', 
                 'high');
    }


    /**
     * Muestra los campos de la consulta en la página de consulta.
     * @param WP_Post $post El objeto de entrada actual.
     */
    public function mostrar_metabox_consulta($post) {
        $nombre = get_post_meta($post->ID, 'nombre', true);
        $email = get_post_meta($post->ID, 'email', true);
        $telefono = get_post_meta($post->ID, 'telefono', true);
        $mensaje = get_post_meta($post->ID, 'mensaje', true);
        $inmueble_interesado_id = get_post_meta($post->ID, 'inmueble_interesado', true);
    
        // Obtener el título y el enlace del post del inmueble interesado
        $inmueble_title = get_the_title($inmueble_interesado_id);
        $inmueble_link = get_permalink($inmueble_interesado_id);
    
        echo "<strong>Título:</strong> " . esc_html($post->post_title) . "<br>";
        echo "<strong>Nombre:</strong> $nombre<br>";
        echo "<strong>Email:</strong> $email<br>";
        echo "<strong>Teléfono:</strong> $telefono<br>";
        echo "<strong>Mensaje:</strong> $mensaje<br>";
        echo "<strong>Inmueble interesado:</strong> <a href='" . esc_url($inmueble_link) . "'>" . esc_html($inmueble_title) . "</a><br>";
    }


    /**
     * Crea una nueva consulta al usar el formulario del front
     */
    public function procesar_formulario_contacto()
    {
        $nombre = sanitize_text_field($_POST['nombre']);
        $email = sanitize_email($_POST['email']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $mensaje = sanitize_textarea_field($_POST['mensaje']);
    
        // Verificar el campo oculto (honeypot)
        if (!empty($_POST['extra_field'])) {
            // Si el campo oculto está lleno, probablemente sea un bot
            exit;
        }
        // Verificar si el campo de mensaje contiene una URL
        if (preg_match('/(?:https?|ftp):\/\/[\n\S]+/i', $mensaje)) {
            // Si el mensaje contiene una URL, podría ser spam, así que lo descartamos
            wp_redirect($_SERVER['HTTP_REFERER'] . '?spam=true'); // Redireccionar con una indicación de spam
            exit;
        }
    
        //Google recaptcha
        $recaptcha_response = $_POST['g-recaptcha-response'];
        $secret_key = get_option('inmuebles_recaptcha_secret_key', '');
    
        $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify?secret={$secret_key}&response={$recaptcha_response}";
        $recaptcha_response_data = json_decode(file_get_contents($recaptcha_url));
    
        if (!$recaptcha_response_data->success) {
            wp_redirect($_SERVER['HTTP_REFERER'] . '?recaptcha_error=true');
            exit;
        }
    
        // Inicializamos la variable del ID del inmueble en 0
        $inmueble_id = 0;
    
        //el shortcode de formulario recibe "inmueble_id" si se usa desde un post de tipo inmueble
        //en caso contrario recibe "tipo_formulario"
        
        // Verificamos si se proporciona el ID del inmueble
        if (isset($_POST['inmueble_id'])) {
            $inmueble_id = intval($_POST['inmueble_id']);
        }
    
        // Creamos el título del post, usando el valor del campo 'tipo_formulario' si está presente
        $post_title = isset($_POST['tipo_formulario']) ? sanitize_text_field($_POST['tipo_formulario']) : '';
    
        // Si el título sigue siendo vacío, determinamos la fuente del formulario
        if (empty($post_title)) {
            if (is_singular('inmueble')) {
                $post_title = 'Página de inmueble';
            } elseif (is_post_type_archive('inmueble')) {
                $post_title = 'Listado de inmuebles';
            } else {
                $post_title = 'Contacto desde el sitio web';
            }
        }
    
        // Creamos el post
        $consulta_id = wp_insert_post(array(
            'post_type' => 'consulta',
            'post_title' => $post_title,
            'post_status' => 'publish',
        ));
    
        if ($consulta_id) {
            update_post_meta($consulta_id, 'nombre', $nombre);
            update_post_meta($consulta_id, 'email', $email);
            update_post_meta($consulta_id, 'telefono', $telefono);
            update_post_meta($consulta_id, 'mensaje', $mensaje);
    
            // Actualizamos el campo 'inmueble_interesado' con la información relevante según la fuente del formulario
            if ($inmueble_id) {
                update_post_meta($consulta_id, 'inmueble_interesado', $inmueble_id);
            } elseif (is_post_type_archive('inmueble')) {
                update_post_meta($consulta_id, 'inmueble_interesado', 'Listado de inmuebles');
            } else {
                update_post_meta($consulta_id, 'inmueble_interesado', 'Contacto desde el sitio web');
            }
    
    
            // Envío de correo electrónico a los usuarios con rol "editor"
            $args = array(
                'role' => 'editor',
            );
            $editores = get_users($args);
    
            $consulta_permalink = get_permalink($consulta_id);
    
            foreach ($editores as $editor) {
                $editor_email = $editor->user_email;
                $subject = 'Mensaje recibido desde chipicasa.com';
                $message = 'Se ha recibido un mensaje desde el sitio web. Puedes ver la consulta <a target="_blank" href="' . $consulta_permalink . '">pinchando aquí</a>.';
                $headers = array('Content-Type: text/html; charset=UTF-8');
                
                wp_mail($editor_email, $subject, $message, $headers);
            }
    
    
        }
    
        wp_redirect($_SERVER['HTTP_REFERER']); // Redireccionar de nuevo a la página del formulario
        exit;
    }


    /**
     * Modificar el listado de la página de consultas
     */
    public function agregar_columnas_consulta($columns)
    {
        return array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Título'),
            'nombre' => __('Nombre'),
            'email' => __('Email'),
            'telefono' => __('Teléfono'),
            'mensaje' => __('Mensaje'),
            'date' => __('Fecha'),
        );
    }

    /**
     * Modificar el listado de la página de consultas
     */
    public function llenar_columnas_consulta($column, $post_id)
    {
        switch ($column) {
            case 'nombre':
                echo get_post_meta($post_id, 'nombre', true);
                break;
            case 'email':
                echo get_post_meta($post_id, 'email', true);
                break;
            case 'telefono':
                echo get_post_meta($post_id, 'telefono', true);
                break;
            case 'mensaje':
                echo get_post_meta($post_id, 'mensaje', true);
                break;
        }
    }

    /**
     * Cambiar texto editar por ver en el listado
     */
    public function modificar_texto_accion_consulta($actions, $post)
    {
        // Solo modificar para el tipo de publicación 'consulta'
        if ($post->post_type === 'consulta') {
            if (isset($actions['edit'])) {
                $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
            }
        }

        return $actions;
    }

    /**
     * Desactivar edicion rápida
     */
    public function desactivar_quick_edit_consulta($actions, $post) {
        
        if ($post->post_type === 'consulta') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    //Eliminar el submenú "Añadir nueva"
    public function eliminar_submenu_consultas() {
        remove_submenu_page('edit.php?post_type=consulta', 'post-new.php?post_type=consulta');
    }

    //Eliminar el botón "Añadir nueva" desde la página de listado de consultas
    public function ocultar_boton_anadir_nueva_consulta() {
        global $post;
        
        if (!is_null($post) && property_exists($post, 'post_type') && $post->post_type === 'consulta') {
            echo '<style type="text/css">
                .page-title-action { display: none !important; }
            </style>';
        }
    }

     //crear un CPT "demanda"
    public function boton_crear_demanda() {
        global $post;
        if ( $post->post_type === 'consulta' ) {
            echo '<a href="' . admin_url('post-new.php?post_type=demanda&from_consulta=' . $post->ID) . '" class="button">Crear Demanda desde Consulta</a>';
        }
    }


    public function rellenar_campos_demanda( $post_id ) {
        if (isset($_GET['from_consulta'])) {
            $consulta_id = intval($_GET['from_consulta']);
            
            error_log("Consulta ID: $consulta_id");
            error_log("Post ID: $post_id");
            
            $nombre = get_post_meta($consulta_id, 'nombre', true);
            $telefono = get_post_meta($consulta_id, 'telefono', true);
            $email = get_post_meta($consulta_id, 'email', true);
            $inmueble_interesado = get_post_meta($consulta_id, 'inmueble_interesado', true);
            
            // Verificar si inmueble_interesado contiene una cadena específica
            if ($inmueble_interesado === 'Listado de inmuebles') {
                $inmueble_demanda = 'Listado de inmuebles';
            } elseif ($inmueble_interesado === 'Contacto desde el sitio web') {
                $inmueble_demanda = 'Contacto desde el sitio web';
            } else {
                $inmueble_demanda = get_the_title($inmueble_interesado);
            }
            
            error_log("Nombre: $nombre");
            error_log("Telefono: $telefono");
            error_log("Email: $email");
            error_log("Inmueble Interesado: $inmueble_interesado");
    
            update_post_meta($post_id, 'nombre', $nombre);
            update_post_meta($post_id, 'telefono', $telefono);
            update_post_meta($post_id, 'email', $email);
            update_post_meta($post_id, 'inmueble_interesado', $inmueble_demanda);
        }
    }


}

new Consulta();