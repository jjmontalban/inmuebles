<?php

/**
 * Agregar página de configuración de reCAPTCHA en el panel de ajustes
 */
function inmuebles_config_recaptcha() {
    // Agregar página de configuración
    add_options_page('Configuración de reCAPTCHA', 'reCAPTCHA', 'manage_options', 'inmuebles-recaptcha-settings', 'inmuebles_recaptcha_settings_page');
}
add_action('admin_menu', 'inmuebles_config_recaptcha');

/**
 * Página de configuración de reCAPTCHA en Ajustes/reCAPTCHA
 */
function inmuebles_recaptcha_settings_page() {
    // Verificar permisos de administrador
    if (!current_user_can('manage_options')) {
        return;
    }

    // Guardar las claves de reCAPTCHA si se envía el formulario
    if (isset($_POST['inmuebles_recaptcha_site_key']) && isset($_POST['inmuebles_recaptcha_secret_key'])) {
        update_option('inmuebles_recaptcha_site_key', sanitize_text_field($_POST['inmuebles_recaptcha_site_key']));
        update_option('inmuebles_recaptcha_secret_key', sanitize_text_field($_POST['inmuebles_recaptcha_secret_key']));
        echo '<div class="updated"><p>Claves de reCAPTCHA guardadas.</p></div>';
    }

    // Obtener las claves de reCAPTCHA actuales
    $site_key = get_option('inmuebles_recaptcha_site_key', '');
    $secret_key = get_option('inmuebles_recaptcha_secret_key', '');

    // Mostrar el formulario
    ?>
    <div class="wrap">
        <h1>Configuración de reCAPTCHA</h1>
        <form method="post" action="">
            <label for="inmuebles_recaptcha_site_key">Site Key de reCAPTCHA:</label>
            <input type="text" name="inmuebles_recaptcha_site_key" value="<?php echo esc_attr($site_key); ?>" style="width: 100%; margin-bottom: 10px;">

            <label for="inmuebles_recaptcha_secret_key">Secret Key de reCAPTCHA:</label>
            <input type="text" name="inmuebles_recaptcha_secret_key" value="<?php echo esc_attr($secret_key); ?>" style="width: 100%; margin-bottom: 20px;">

            <p>Obtén tus claves de reCAPTCHA en <a href="https://www.google.com/recaptcha" target="_blank">https://www.google.com/recaptcha</a></p>

            <?php submit_button('Guardar'); ?>
        </form>
    </div>
    <?php
}

/**
 * Guardar la Configuración de reCAPTCHA
 */
function inmuebles_recaptcha_settings_init() {
    register_setting('inmuebles_recaptcha_settings_group', 'inmuebles_recaptcha_site_key');
    register_setting('inmuebles_recaptcha_settings_group', 'inmuebles_recaptcha_secret_key');
}
add_action('admin_init', 'inmuebles_recaptcha_settings_init');
