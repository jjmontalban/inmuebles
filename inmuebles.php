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
 *   js admin
 */
function inmuebles_load_scripts() {
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_media();
    add_action('admin_menu', 'inmuebles_add_settings_page');
    
    // Google Maps API
    $api_key = get_option('inmuebles_google_maps_api_key', '');
    wp_enqueue_script('google-maps', "https://maps.googleapis.com/maps/api/js?key={$api_key}", array(), null, true);
    // Google Recaptcha
    $recaptcha_site_key = get_option('inmuebles_recaptcha_site_key', '');
    wp_enqueue_script('google-recaptcha', "https://www.google.com/recaptcha/api.js?render={$recaptcha_site_key}", array(), null, true);

    wp_enqueue_script('inmuebles-script', plugin_dir_url(__FILE__) . 'js/admin-scripts.js', array('jquery', 'jquery-ui-sortable', 'media'), '2.0', true);
}
add_action('admin_enqueue_scripts', 'inmuebles_load_scripts');

/**
 *   js front
 */
function inmuebles_load_front_scripts() {
    wp_enqueue_script('inmuebles-front-script', plugin_dir_url(__FILE__) . 'js/front-scripts.js', array('jquery'), '1.0', true);
    $recaptcha_site_key = get_option('inmuebles_recaptcha_site_key', '');
    // Google Recaptcha
    wp_localize_script('inmuebles-front-script', 'inmuebles_vars', array( 'recaptcha_site_key' => $recaptcha_site_key, ));
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
}
add_action('wp_enqueue_scripts', 'inmuebles_load_front_scripts');

/**
 * css frontend
 */
function inmuebles_load_styles_front() {
    wp_enqueue_style('inmuebles-style-front', plugin_dir_url(__FILE__) . 'css/style-front.css', array(), '2.0', 'all');
}
add_action('wp_enqueue_scripts', 'inmuebles_load_styles_front');

/**
 * css backend
 */
function inmuebles_load_styles_admin() {
    wp_enqueue_style('inmuebles-style-admin', plugin_dir_url(__FILE__) . 'css/style-admin.css', array(), '2.0', 'all');
}
add_action('admin_enqueue_scripts', 'inmuebles_load_styles_admin');
