<?php
 
 /**
 * Añadir página de configuración de google captcha en el panel de ajustes
 */
function inmuebles_config_captcha() {
    // Agregar página de configuración
    add_options_page('Configuración de Google Captcha', 'Google Captcha API', 'manage_options', 'inmuebles-google-captcha-api', 'inmuebles_google_captcha_api_page');
}
add_action('admin_menu', 'inmuebles_config_captcha');



/**
 * Página de configuración de Google captcha en ajustes/Google captcha API
 */
function inmuebles_google_captcha_api_page() {
    // Verificar permisos de administrador
    if (!current_user_can('manage_options')) {
        return;
    }

    // Guardar la clave de API si se envía el formulario
    if (isset($_POST['inmuebles_google_captcha_api_key'])) {
        update_option('inmuebles_google_captcha_api_key', sanitize_text_field($_POST['inmuebles_google_captcha_api_key']));
        echo '<div class="updated"><p>Clave de API guardada.</p></div>';
    }

    // Obtener la clave de API actual
    $api_key = get_option('inmuebles_google_captcha_api_key', '');

    // Mostrar el formulario
    ?>
    <div class="wrap">
        <h1>Configuración de Google Captcha API</h1>
        <form method="post" action="">
            <label for="inmuebles_google_captcha_api_key">Clave de API de Google Captcha:</label>
            <input type="text" name="inmuebles_google_captcha_api_key" value="<?php echo esc_attr($api_key); ?>" style="width: 100%;">
            <p>Obtén una clave de API de Google Captcha en <a href="https://www.google.com/recaptcha/about/" target="_blank">https://www.google.com/recaptcha/about/</a></p>
            <?php submit_button('Guardar'); ?>
        </form>
    </div>
    <?php
}


/**
* Guardar la Configuración
*/
function inmuebles_settings_captcha() {
    register_setting('inmuebles_settings_group', 'inmuebles_google_captcha_api_key');
}
add_action('admin_init', 'inmuebles_settings_captcha');