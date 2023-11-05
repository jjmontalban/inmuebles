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

    // Eliminar la página de configuración del menú
    remove_menu_page('inmuebles-settings'); // Reemplaza 'inmuebles-settings' con el slug de tu página de configuración

    // Flushing rewrite rules para que las reglas de reescritura de URL se actualicen
    flush_rewrite_rules();
}


// Función de desactivación del plugin
function inmuebles_deactivate_plugin() {
}


// Incluimos los archivos secundarios
require_once plugin_dir_path(__FILE__) . 'includes/cpt-inmueble.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-propietario.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-consulta.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-demanda.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-cita.php';
require_once plugin_dir_path(__FILE__) . 'admin/inmueble-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/propietario-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/consulta-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/demanda-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/cita-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/gmaps.php';


/**
 * Carga librerias js necesarias
 */
function inmuebles_load_scripts() {
    // Registrar jQuery y jQuery UI
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

    // Registrar los scripts para celandario de citas
    wp_enqueue_style('fullcalendar-css', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css');
    wp_enqueue_script('moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js', array('jquery'), null, true);
    wp_enqueue_script('fullcalendar', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js', array('jquery', 'moment'), null, true);
    wp_enqueue_script('fullcalendar-es', 'https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.js', array('fullcalendar'), null, true);

    // Registrar el script 'media' de WordPress
    wp_enqueue_media();

    // Obtener la clave de API almacenada
    $api_key = get_option('inmuebles_google_maps_api_key', '');

     // Agregar la página de configuración en el menú de administración
     add_action('admin_menu', 'inmuebles_add_settings_page');

    // Cargar la biblioteca de Google Maps JavaScript API con la clave
    wp_enqueue_script('google-maps', "https://maps.googleapis.com/maps/api/js?key={$api_key}", array(), null, true);

    // Registrar el script personalizado
    wp_enqueue_script('inmuebles-script', plugin_dir_url(__FILE__) . 'js/scripts.js', array('jquery', 'jquery-ui-sortable', 'media'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'inmuebles_load_scripts');


/**
 * Carga css personalizado
 */
function inmuebles_load_styles() {
    // Registrar el archivo CSS del plugin
    wp_enqueue_style('inmuebles-style', plugin_dir_url(__FILE__) . 'css/styles.css', array(), '1.0', 'all');
}
add_action('admin_enqueue_scripts', 'inmuebles_load_styles');