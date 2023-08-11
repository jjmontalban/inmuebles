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

/**
 * Carga librerias sj necesarias
 */
 function inmuebles_load_scripts() {
    // Registrar jQuery y jQuery UI
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

    // Registrar el script 'media' de WordPress
    wp_enqueue_media();

    // Cargar la biblioteca de Google Maps JavaScript API
    wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=', array(), null, true);

    // Registrar el script personalizado
    wp_enqueue_script('inmuebles-script', plugin_dir_url(__FILE__) . 'js/main.js', array('jquery', 'jquery-ui-sortable', 'media'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'inmuebles_load_scripts');


/**
 * Carga css personalizado
 */
function inmuebles_load_styles() {
    // Registrar el archivo CSS del plugin
    wp_enqueue_style('inmuebles-style', plugin_dir_url(__FILE__) . 'css/style.css', array(), '1.0', 'all');
}
add_action('admin_enqueue_scripts', 'inmuebles_load_styles');


// Incluimos los archivos secundarios
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-post-type-taxonomy.php';
require_once plugin_dir_path(__FILE__) . 'includes/metaboxes.php';


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
