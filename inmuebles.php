<?php
/**
 * Plugin Name: Inmuebles
 * Plugin URI: https://jjmontalban.github.io/inmuebles
 * Description: Plugin para gestionar inmuebles y mostrarlos en el front-end.
 * Version: 1.0.0
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

    // Realiza cualquier tarea adicional necesaria durante la activación
}


// Función de desinstalación del plugin
function inmuebles_uninstall_plugin() {
    // Eliminar opción de la api de gmaps
    delete_option('inmuebles_google_maps_api_key');
    // Flushing rewrite rules para que las reglas de reescritura de URL se actualicen
    flush_rewrite_rules();

    // Realizar tareas de limpieza adicionales, eliminar tablas de base de datos
}


// Función de desactivación del plugin
function inmuebles_deactivate_plugin() {
}


// Incluimos los archivos secundarios
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-type.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';


/**
 * Carga librerias js necesarias
 */
function inmuebles_load_scripts() {
    // Registrar jQuery y jQuery UI
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

    // Registrar el script 'media' de WordPress
    wp_enqueue_media();

    // Obtener la clave de API almacenada
    $api_key = get_option('inmuebles_google_maps_api_key', '');

     // Agregar la página de configuración en el menú de administración
     add_action('admin_menu', 'inmuebles_add_settings_page');

    // Cargar la biblioteca de Google Maps JavaScript API con la clave
    wp_enqueue_script('google-maps', "https://maps.googleapis.com/maps/api/js?key={$api_key}", array(), null, true);

    // Registrar el script personalizado
    wp_enqueue_script('inmuebles-script', plugin_dir_url(__FILE__) . 'admin/scripts.js', array('jquery', 'jquery-ui-sortable', 'media'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'inmuebles_load_scripts');



/**
 * Carga css personalizado
 */
function inmuebles_load_styles() {
    // Registrar el archivo CSS del plugin
    wp_enqueue_style('inmuebles-style', plugin_dir_url(__FILE__) . 'admin/styles.css', array(), '1.0', 'all');
}
add_action('admin_enqueue_scripts', 'inmuebles_load_styles');


/**
 * Elimina el editor de texto enriquecido para el tipo de entrada "inmueble".
 */
function eliminar_editor_inmueble() {
    remove_post_type_support( 'inmueble', 'editor' );
}
add_action( 'init', 'eliminar_editor_inmueble' );


/**
 * Quita el metabox de campos personalizados para el tipo de entrada "inmueble".
 */
function quitar_metabox_campos_personalizados() {
    remove_meta_box( 'postcustom', 'inmueble', 'normal' );
}
add_action( 'admin_menu', 'quitar_metabox_campos_personalizados' );
