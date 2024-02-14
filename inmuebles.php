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

require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'admin/inmueble.php';
require_once plugin_dir_path(__FILE__) . 'admin/cita.php';
require_once plugin_dir_path(__FILE__) . 'admin/propietario.php';
require_once plugin_dir_path(__FILE__) . 'admin/consulta.php';
require_once plugin_dir_path(__FILE__) . 'admin/demanda.php';
require_once plugin_dir_path(__FILE__) . 'admin/gmaps.php';
require_once plugin_dir_path(__FILE__) . 'admin/recaptcha.php';

register_activation_hook(__FILE__, 'inmuebles_activate_plugin');

register_deactivation_hook(__FILE__, 'inmuebles_deactivate_plugin');

register_uninstall_hook(__FILE__, 'inmuebles_uninstall_plugin');

function inmuebles_activate_plugin() {
    add_option('inmuebles_google_maps_api_key', '');
    flush_rewrite_rules();
}

function inmuebles_uninstall_plugin() {
    delete_option('inmuebles_google_maps_api_key');
    remove_menu_page('inmuebles');
    // Eliminar tipos de contenido personalizados (CPT)
    unregister_post_type('inmueble');
    unregister_post_type('propietario');
    unregister_post_type('consulta');
    unregister_post_type('cita');
    unregister_post_type('demanda');
    
    flush_rewrite_rules();
}

function inmuebles_deactivate_plugin() {
}

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
    wp_enqueue_style('inmuebles-style', plugin_dir_url(__FILE__) . 'css/style.css', array(), '2.0', 'all');
}
add_action('admin_enqueue_scripts', 'inmuebles_load_styles');




/**
 * Personalizar la página principal del panel de administración de WordPress para diferentes roles de usuario
 */
add_action('admin_menu', 'inmuebles_custom_admin_dashboard');

function inmuebles_custom_admin_dashboard() {
    // Para usuarios no administradores
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

    // Verifica que estemos en la página principal del panel de administración
    if ($pagenow === 'index.php') {
        ?>
        <div class="wrap">
            <h1>Bienvenido a la Página Principal del CRM Chipicasa</h1>
            <p>Agregar tu contenido principal.</p>
        </div>
        <?php
    }
}
