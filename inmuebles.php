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



function inmuebles_activate_plugin() {

    
    // Agregar la opción para la clave de API
    add_option('inmuebles_google_maps_api_key', '');
    
    // Registra cualquier otra configuración inicial que puedas necesitar
    // Por ejemplo:
    // add_option('inmuebles_default_color', '#ffffff');
    // add_option('inmuebles_enable_feature', true);
    // ...

    // Realiza cualquier tarea adicional necesaria durante la activación
    // Por ejemplo: crear tablas de base de datos personalizadas, configurar cron jobs, etc.
    // ...

    // Flushing rewrite rules para que las reglas de reescritura de URL se generen correctamente
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'inmuebles_activate_plugin');

function inmuebles_uninstall_plugin() {
    // Eliminar todas las opciones creadas por el plugin
    delete_option('inmuebles_google_maps_api_key');
    // Eliminar cualquier otra opción que hayas agregado
    // ...

    // Realizar tareas de limpieza adicionales, como eliminar tablas de base de datos
    // Por ejemplo: borrar tablas personalizadas si las tienes
    // ...

    // Flushing rewrite rules para que las reglas de reescritura de URL se actualicen
    flush_rewrite_rules();
}
register_uninstall_hook(__FILE__, 'inmuebles_uninstall_plugin');



function inmuebles_deactivate_plugin() {
    // Realizar acciones de limpieza o reversión al desactivar el plugin
    // Por ejemplo: quitar opciones creadas, eliminar cron jobs temporales, etc.
    // ...
}
register_deactivation_hook(__FILE__, 'inmuebles_deactivate_plugin');



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

    // Obtener la clave de API almacenada
    $api_key = get_option('inmuebles_google_maps_api_key', '');

     // Agregar la página de configuración en el menú de administración
     add_action('admin_menu', 'inmuebles_add_settings_page');

    // Cargar la biblioteca de Google Maps JavaScript API con la clave
    wp_enqueue_script('google-maps', "https://maps.googleapis.com/maps/api/js?key={$api_key}", array(), null, true);

    // Registrar el script personalizado
    wp_enqueue_script('inmuebles-script', plugin_dir_url(__FILE__) . 'js/main.js', array('jquery', 'jquery-ui-sortable', 'media'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'inmuebles_load_scripts');



// Función para agregar la página de configuración en el menú de Inmuebles
function inmuebles_add_settings_page() {
    add_menu_page(
        'Inmuebles Settings',   // Título de la página
        'Inmuebles',            // Título en el menú
        'manage_options',       // Capacidad requerida para acceder
        'inmuebles-settings',   // Slug de la página
        'inmuebles_settings_page', // Función que renderiza la página
        'dashicons-admin-home', // Icono (puedes cambiarlo)
        30 // Posición en el menú
    );
}

// Renderizar la Página de Configuración
function inmuebles_settings_page() {
    // Verificar si el usuario tiene la capacidad requerida para acceder a esta página
    if (!current_user_can('manage_options')) {
        return;
    }

    // Renderizar el formulario de configuración
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('inmuebles_settings_group'); ?>
            <?php do_settings_sections('inmuebles-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php

    echo '<form method="post" action="options.php">';
    settings_fields('inmuebles_settings_group');
    do_settings_sections('inmuebles-settings');
    
    echo '<label for="inmuebles_google_maps_api_key">Google Maps API Key:</label><br>';
    echo '<input type="text" id="inmuebles_google_maps_api_key" name="inmuebles_google_maps_api_key" value="' . esc_attr(get_option('inmuebles_google_maps_api_key')) . '" /><br>';
    
    submit_button();
    echo '</form>';
}


// Guardar la Configuración

function inmuebles_settings_init() {
    register_setting('inmuebles_settings_group', 'inmuebles_google_maps_api_key');
}
add_action('admin_init', 'inmuebles_settings_init');






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



/**
 * Página de configuración en el panel de administración
 */
function inmuebles_plugin_menu() {
    // Agregar página de configuración
    add_options_page('Configuración de Google Maps API', 'Google Maps API', 'manage_options', 'inmuebles-google-maps-api', 'inmuebles_google_maps_api_page');
}
add_action('admin_menu', 'inmuebles_plugin_menu');


/**
 * Mostrar el formulario de configuración
 */
function inmuebles_google_maps_api_page() {
    // Verificar permisos de administrador
    if (!current_user_can('manage_options')) {
        return;
    }

    // Guardar la clave de API si se envía el formulario
    if (isset($_POST['inmuebles_google_maps_api_key'])) {
        update_option('inmuebles_google_maps_api_key', sanitize_text_field($_POST['inmuebles_google_maps_api_key']));
        echo '<div class="updated"><p>Clave de API guardada.</p></div>';
    }

    // Obtener la clave de API actual
    $api_key = get_option('inmuebles_google_maps_api_key', '');

    // Mostrar el formulario
    ?>
    <div class="wrap">
        <h1>Configuración de Google Maps API</h1>
        <form method="post" action="">
            <label for="inmuebles_google_maps_api_key">Clave de API de Google Maps:</label>
            <input type="text" name="inmuebles_google_maps_api_key" value="<?php echo esc_attr($api_key); ?>" style="width: 100%;">
            <p>Obtén una clave de API de Google Maps en <a href="https://cloud.google.com/maps-platform/" target="_blank">https://cloud.google.com/maps-platform/</a></p>
            <?php submit_button('Guardar'); ?>
        </form>
    </div>
    <?php
}
