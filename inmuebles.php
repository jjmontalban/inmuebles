<?php
/**
 * Plugin Name: Inmuebles
 * Plugin URI: https://jjmontalban.github.io/inmuebles
 * Description: Plugin inmobiliario
 * Version: 2.0.0
 * Author: JJMontalban
 * Author URI: https://jjmontalban.github.io
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: inmuebles
 */


// Hook para la activación del plugin
register_activation_hook(__FILE__, 'inmuebles_activate_plugin');

// Hook para la desactivación del plugin
register_deactivation_hook(__FILE__, 'inmuebles_deactivate_plugin');

// Hook para la desinstalación del plugin
register_uninstall_hook(__FILE__, 'inmuebles_uninstall_plugin');


// Función de activación del plugin
function inmuebles_activate_plugin() {
    // Agregar la opción para la clave de API
    add_option('inmuebles_google_maps_api_key', '');
    // Flushing rewrite rules para que las reglas de reescritura de URL se generen correctamente
    flush_rewrite_rules();
}

// Función de desinstalación del plugin
function inmuebles_uninstall_plugin() {
    // Eliminar opción de la API de Google Maps
    delete_option('inmuebles_google_maps_api_key');

    // Eliminar tipos de contenido personalizados (CPT)
    unregister_post_type('inmueble');
    unregister_post_type('propietario');
    unregister_post_type('consulta');
    unregister_post_type('cita');
    unregister_post_type('demanda');

    // Eliminar la página de configuración del menú
    remove_menu_page('inmuebles-settings'); // Reemplaza 'inmuebles-settings' con el slug de tu página de configuración

    // Flushing rewrite rules para que las reglas de reescritura de URL se actualicen
    flush_rewrite_rules();
}


// Función de desactivación del plugin
function inmuebles_deactivate_plugin() {
}

// Incluimos los archivos secundarios
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'admin/inmueble.php';
require_once plugin_dir_path(__FILE__) . 'admin/cita.php';
require_once plugin_dir_path(__FILE__) . 'admin/propietario.php';
require_once plugin_dir_path(__FILE__) . 'admin/consulta.php';
require_once plugin_dir_path(__FILE__) . 'admin/demanda.php';
require_once plugin_dir_path(__FILE__) . 'admin/gmaps.php';
require_once plugin_dir_path(__FILE__) . 'admin/recaptcha.php';

/**
 * Carga librerias js necesarias
 */
function inmuebles_load_scripts() {
    // Registrar jQuery UI
    wp_enqueue_script('jquery-ui-sortable');

    // Registrar el script 'media' de WordPress
    wp_enqueue_media();

    // Agregar la página de configuración en el menú de administración
    add_action('admin_menu', 'inmuebles_add_settings_page');
    
    // Obtener la clave de API almacenada
    $api_key = get_option('inmuebles_google_maps_api_key', '');
    // Cargar la biblioteca de Google Maps JavaScript API con la clave
    wp_enqueue_script('google-maps', "https://maps.googleapis.com/maps/api/js?key={$api_key}", array(), null, true);

    // Obtener la clave almacenada
    $recaptcha_site_key = get_option('inmuebles_recaptcha_site_key', '');
    // Cargar la biblioteca de Google Recaptcha con la clave del sitio
    wp_enqueue_script('google-recaptcha', "https://www.google.com/recaptcha/api.js?render={$recaptcha_site_key}", array(), null, true);


    // Registrar el script personalizado
    wp_enqueue_script('inmuebles-script', plugin_dir_url(__FILE__) . 'js/scripts.js', array('jquery', 'jquery-ui-sortable', 'media'), '2.0', true);
    
}
add_action('admin_enqueue_scripts', 'inmuebles_load_scripts');


/**
 * Carga css personalizado
 */
function inmuebles_load_styles() {
    // Registrar el archivo CSS del plugin
    wp_enqueue_style('inmuebles-style', plugin_dir_url(__FILE__) . 'css/style.css', array(), '2.0', 'all');
}
add_action('admin_enqueue_scripts', 'inmuebles_load_styles');



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




function cambiar_texto_boton_publicar($translated_text, $text, $domain) {
    global $pagenow, $post;

    if (is_admin() && $pagenow == 'post-new.php') {
        switch($post->post_type) {
            case 'propietario':
                if($translated_text === 'Publish') {
                    return 'Crear Propietario';
                }
                break;

            case 'otro_cpt':
                if($translated_text === 'Publish'){
                    return 'Crear Otro CPT';
                }
                break;
        }
    }
    return $translated_text;
}

add_filter('gettext_with_context', 'cambiar_texto_boton_publicar', 10, 3);


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
        'num_estancias','ubicacion_local','tipo_plaza','m_plaza'
    );

    $valores = array();

    // Ahora, recorremos el array de campos y obtenemos su valor.
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
    'casa_chalet' => 'Chalet',
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
    'garaje' => 'Garaje',
    'alcancia' => 'La Alcancía',
    'laguna' => 'La Laguna',
    'pinar' => 'Pinar',
);