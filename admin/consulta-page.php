<?php 

/**
 * Crea una nueva consulta al usar el formulario del front
 */
function procesar_formulario_contacto() {

    $nombre = sanitize_text_field($_POST['nombre']);
    $email = sanitize_email($_POST['email']);
    $telefono = sanitize_text_field($_POST['telefono']);
    $mensaje = sanitize_textarea_field($_POST['mensaje']);

    // Verificar el campo oculto (honeypot)
    if (!empty($_POST['campo_trampa'])) {
        // Si el campo oculto está lleno, probablemente sea un bot
        exit;
    }
    
    // Validación del CAPTCHA
    $recaptcha_secret_key = get_option('inmuebles_google_maps_api_key', '');
    $recaptcha_response = $_POST['g-recaptcha-response'];

    $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify";
    $recaptcha_data = [
        'secret'   => $recaptcha_secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR'],
    ];

    $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($recaptcha_data),
            ],
        ];

    $context  = stream_context_create($options);
    $result = file_get_contents($recaptcha_url, false, $context);
    $recaptcha_result = json_decode($result);

    if (!$recaptcha_result->success) {
        echo "Fallo en la validación del CAPTCHA.";
        return;
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
            $subject = 'Mensaje recibido desde el sitio web';
            $message = 'Se ha recibido un mensaje desde el sitio web. Puedes ver la consulta aquí: ' . $consulta_permalink;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            wp_mail($editor_email, $subject, $message, $headers);
        }


    }

    wp_redirect($_SERVER['HTTP_REFERER']); // Redireccionar de nuevo a la página del formulario
    exit;
}

add_action('admin_post_procesar_formulario_contacto', 'procesar_formulario_contacto');
add_action('admin_post_nopriv_procesar_formulario_contacto', 'procesar_formulario_contacto');




/**
 * Modificar el listado de la página de consultas
 */
function agregar_columnas_consulta($columns) {
    return array(
        'cb' => '<input type="checkbox" />', // Checkbox para selección
        'title' => __('Título'),
        'nombre' => __('Nombre'),
        'email' => __('Email'),
        'telefono' => __('Teléfono'),
        'mensaje' => __('Mensaje'),
        'date' => __('Fecha'),
    );
}
add_filter('manage_consulta_posts_columns', 'agregar_columnas_consulta');

function llenar_columnas_consulta($column, $post_id) {
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
add_action('manage_consulta_posts_custom_column', 'llenar_columnas_consulta', 10, 2);



/**
 * Agrega el metabox "Datos de la consulta" a la página de consulta
 */
function agregar_metabox_consulta() {
    add_meta_box('datos_consulta', 
                 'Datos de la Consulta', 
                 'mostrar_metabox_consulta', 
                 'consulta', 
                 'normal', 
                 'high');
}
add_action('add_meta_boxes', 'agregar_metabox_consulta');


/**
 * Muestra los campos de la consulta en la página de consulta.
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_metabox_consulta($post) {
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
 * Cambiar texto editar por ver en el listado
 */
function modificar_texto_accion_consulta($actions, $post) {
    // Solo modificar para el tipo de publicación 'consulta'
    if ($post->post_type === 'consulta') {
        if (isset($actions['edit'])) {
            $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
        }
    }

    return $actions;
}
add_filter('post_row_actions', 'modificar_texto_accion_consulta', 10, 2);


/**
 * Desactivar edicion rápida
 */
function desactivar_quick_edit_consulta($actions, $post) {
    
    if ($post->post_type === 'consulta') {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
add_filter('post_row_actions', 'desactivar_quick_edit_consulta', 10, 2);



//Eliminar el submenú "Añadir nueva"
function eliminar_submenu_consultas() {
    remove_submenu_page('edit.php?post_type=consulta', 'post-new.php?post_type=consulta');
}
add_action('admin_menu', 'eliminar_submenu_consultas');


//Eliminar el botón "Añadir nueva" desde la página de listado de consultas
function ocultar_boton_anadir_nueva_consulta() {
    global $post;

    if (isset($post) && $post->post_type === 'consulta') {
        echo '<style type="text/css">
            .page-title-action { display: none !important; }
        </style>';
    }
}
add_action('admin_head', 'ocultar_boton_anadir_nueva_consulta');


//crear un CPT "demanda"
function boton_crear_demanda() {
    global $post;
    if ( $post->post_type === 'consulta' ) {
        echo '<a href="' . admin_url('post-new.php?post_type=demanda&from_consulta=' . $post->ID) . '" class="button">Crear Demanda desde Consulta</a>';
    }
}
add_action('post_submitbox_start', 'boton_crear_demanda');

function rellenar_campos_demanda( $post_id ) {
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
add_action('save_post_demanda', 'rellenar_campos_demanda', 10, 1);
