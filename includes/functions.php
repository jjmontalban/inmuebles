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
 * Obtiene  los campos personalizados del inmueble.
 * @param WP_Post $post El objeto de entrada actual.
 */
function obtener_campos_inmueble($post_id) {
    $campos = array(
        'tipo_inmueble', 'zona_inmueble', 'localidad', 'nombre_calle', 'numero', 'numero_obligatorio',
        'visibilidad_direccion', 'tipo_operacion', 'precio_venta', 'planta', 'bloque',
        'gastos_comunidad', 'precio_alquiler', 'fianza', 'calefaccion', 'num_ascensores',
        'caract_inm', 'm_construidos','m_utiles','m_lineales','superf_terreno',
        'num_dormitorios','num_banos','num_escap','calif_consumo', 'num_plazas',
        'consumo','calif_emis','emisiones','tipo_local','tipo_terreno',
        'ac','estado_cons','interior_ext','ascensor', 'calif_terreno',
        'descripcion','ano_edificio','acceso_rodado', 'plano1', 'plano2', 'plano3', 'plano4',
        'uso_excl','distribucion_oficina','aire_acond',
        'residencial_altura','residencial_unif',
        'terciario_ofi','terciario_com','terciario_hotel','industrial','dotaciones','otra',
        'm_parcela','m_fachada','tipologia_chalet','tipo_rustica','num_plantas',
        'num_estancias','ubicacion_local','tipo_plaza','m_plaza', 'visitas'
    );

    $valores = array();

    foreach($campos as $campo) {
        $valores[$campo] = get_post_meta($post_id, $campo, true);
    }

    // Casos especiales para campos que podrían devolver arrays:
    $caract_inm_value = get_post_meta($post_id, 'caract_inm', true);
    $valores['caract_inm'] = is_array($caract_inm_value) ? $caract_inm_value : array();
    $valores['galeria_imagenes'] = is_array(get_post_meta($post_id, 'galeria_imagenes', true)) ? get_post_meta($post_id, 'galeria_imagenes', true) : array();
    $valores['orientacion'] = is_array(get_post_meta($post_id, 'orientacion', true)) ? get_post_meta($post_id, 'orientacion', true) : array();
    $valores['otra_caract_inm'] = is_array(get_post_meta($post_id, 'otra_caract_inm', true)) ? get_post_meta($post_id, 'otra_caract_inm', true) : array();
    $valores['caract_local'] = is_array(get_post_meta($post_id, 'caract_local', true)) ? get_post_meta($post_id, 'caract_local', true) : array();
    $valores['caract_garaje'] = is_array(get_post_meta($post_id, 'caract_garaje', true)) ? get_post_meta($post_id, 'caract_garaje', true) : array();
    
    return $valores;
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