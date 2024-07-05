<?php 

//agregar el atributo enctype al formulario de edición de publicaciones utilizando JavaScript.
function inmuebles_admin_scripts() {
    echo '
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $("#post").attr("enctype", "multipart/form-data");
        });
    </script>';
}
add_action('admin_footer', 'inmuebles_admin_scripts');



/**
 * Obtiene todos los campos personalizados del inmueble.
 * @param int $post_id El ID del post actual.
 * @return array Un array con todos los campos personalizados y sus valores.
 */
function obtener_campos_inmueble($post_id) {
    $meta_values = get_post_meta($post_id);

    // Verificar si algún valor está serializado y deserializarlo
    foreach ($meta_values as $key => $value) {
        $meta_values[$key] = maybe_unserialize($value[0]);
    }

    // Verificación específica para 'tipo_inmueble'
    if (isset($meta_values['tipo_inmueble']) && is_serialized($meta_values['tipo_inmueble'])) {
        $meta_values['tipo_inmueble'] = maybe_unserialize($meta_values['tipo_inmueble']);
    }

    return $meta_values;
}



//array asociativo para mapear valores de los tipos de inmueble
global $tipos_inmueble_map;

$tipos_inmueble_map = array(
    'piso' => 'Piso',
    'casa_rustica' => 'Casa rústica',
    'apartamento' => 'Apartamento',
    'casa_chalet' => 'Casa/Chalet',
    'local' => 'Local',
    'garaje' => 'Garaje',
    'oficina' => 'Oficina',
    'terreno' => 'Terreno',
);

global $zonas_inmueble_map;

$zonas_inmueble_map = array(
    'centro' => 'Centro',
    'regla' => 'Regla',
    'cruz_mar' => 'Cruz del Mar',
    'faro' => 'Faro',
    'muelle' => 'Muelle',
    'alcancia' => 'La Alcancía',
    'laguna' => 'La Laguna',
    'pinar' => 'Pinar',
    'rota' => 'Avda. Rota',
    'ballena' => 'Costa Ballena',
    '3_piedras' => 'Las 3 Piedras',
    'estacion' => 'Estación autobuses',
    'lagunilla' => 'La Lagunilla',
    'montijo' => 'Montijo',
);

/* Ocultar las opciones de publicación y visibilidad para los CPT personalizados */
function inmuebles_personalizar_metabox_publicar($post_type) {
    $cpts_personalizados = array('cita', 'consulta', 'demanda', 'propietario');
    
    if (in_array($post_type, $cpts_personalizados)) {
        ?>
        <style>
            #minor-publishing, #misc-publishing-actions {
                display: none;
            }
        </style>
        <?php
    }
}
add_action('admin_footer', function() { global $post_type; inmuebles_personalizar_metabox_publicar($post_type); });

/* Cambia el texto del botón de "Publicar" a "Crear"  */
function inmuebles_cambiar_boton_publicar($translated_text, $text, $domain) {
    global $pagenow, $post;

    if (is_admin() && $pagenow == 'post-new.php' && isset($post) && $post instanceof WP_Post) {
        $cpts_personalizados = array('cita', 'consulta', 'demanda', 'propietario');
        if (in_array($post->post_type, $cpts_personalizados)) {
            if ($translated_text === 'Publicar') {
                return 'Crear';
            }
        }
    }

    return $translated_text;
}
add_filter('gettext', 'inmuebles_cambiar_boton_publicar', 10, 3);


/**
 * Personalizar la página principal del panel de administración de WordPress para diferentes roles de usuario
 */
add_action('admin_menu', 'inmuebles_custom_admin_dashboard');

function inmuebles_custom_admin_dashboard() {
    // no admin
    if (!current_user_can('administrator')) {
        remove_meta_box('dashboard_activity', 'dashboard', 'normal');
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
        remove_meta_box('wpseo-wincher-dashboard-overview', 'dashboard', 'normal');
        remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
        remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
        remove_meta_box('dashboard_primary', 'dashboard', 'side');
        remove_meta_box('dashboard_secondary', 'dashboard', 'side');
        add_action('admin_notices', 'inmuebles_admin_dashboard_content');
    }
}

function inmuebles_admin_dashboard_content() {
    global $pagenow;

    if ($pagenow === 'index.php') {
        ?>
        <div class="wrap">
            <h1>Bienvenido a la Página Principal del CRM Chipicasa</h1>
            <p>Agregar tu contenido principal.</p>
        </div>
        <?php
    }
}


/**
 * Oculta opciones del menú para usuarios con rol de Editor
 */
function ocultar_opciones_menu_para_editor() {
    // Verifica si el usuario actual es un Editor
    if (current_user_can('editor')) {
        // Oculta el enlace del perfil
        remove_menu_page('profile.php');
        // Oculta la opción de Yoast SEO
        remove_menu_page('wpseo_workouts');
    }
}
add_action('admin_menu', 'ocultar_opciones_menu_para_editor');