<?php


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

/**
 * Muestra los campos personalizados en el formulario de edición de inmuebles.
 *
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_campos_personalizados( $post ) {
    // Recuperar los valores actuales de los campos personalizados
    $tipo_inmueble = get_post_meta($post->ID, 'tipo_inmueble', true);
    $localidad = get_post_meta($post->ID, 'localidad', true);
    $nombre_calle = get_post_meta($post->ID, 'nombre_calle', true);
    $numero = get_post_meta($post->ID, 'numero', true);
    $planta = get_post_meta($post->ID, 'planta', true);
    $bloque = get_post_meta($post->ID, 'bloque', true);
    $escalera = get_post_meta($post->ID, 'escalera', true);
    $urbanizacion = get_post_meta($post->ID, 'urbanizacion', true);
    $visibilidad_direccion = get_post_meta($post->ID, 'visibilidad_direccion', true);

    $tipo_operacion = get_post_meta($post->ID, 'tipo_operacion', true);
    $precio_venta = get_post_meta($post->ID, 'precio_venta', true);
    $gastos_comunidad = get_post_meta($post->ID, 'gastos_comunidad', true);
    $precio_alquiler = get_post_meta($post->ID, 'precio_alquiler', true);
    $fianza = get_post_meta($post->ID, 'fianza', true);
    $calefaccion = get_post_meta($post->ID, 'calefaccion', true);


    // Obtener los valores de los checkboxes de caract_inm como un array
    // Verificar si el valor existe y, de lo contrario, asignar un array vacío
    $caract_inm = is_array($caract_inm = get_post_meta($post->ID, 'caract_inm', true)) ? $caract_inm : array();
    

    $m_construidos = get_post_meta($post->ID, 'm_construidos', true);
    $m_utiles = get_post_meta($post->ID, 'm_utiles', true);
    $m_lineales = get_post_meta($post->ID, 'm_lineales', true);
    $num_dormitorios = get_post_meta($post->ID, 'num_dormitorios', true);
    $num_banos = get_post_meta($post->ID, 'num_banos', true);
    $num_escap = get_post_meta($post->ID, 'num_escap', true);

    $calif_consumo_energ = get_post_meta($post->ID, 'calif_consumo_energ', true);
    $consumo_energ = get_post_meta($post->ID, 'consumo_energ', true);
    $cal_emis = get_post_meta($post->ID, 'cal_emis', true);
    $emisiones = get_post_meta($post->ID, 'emisiones', true);
    $tipo_local = get_post_meta($post->ID, 'tipo_local', true);
    $plaza = get_post_meta($post->ID, 'plaza', true);
    $ac = get_post_meta($post->ID, 'ac', true);
    
    $estado_cons = get_post_meta($post->ID, 'estado_cons', true);
    $interior_ext = get_post_meta($post->ID, 'interior_ext', true);
    $ascensor = get_post_meta($post->ID, 'ascensor', true);
    $galeria_imagenes = get_post_meta($post->ID, 'galeria_imagenes', true); // Nueva variable para la galería de imágenes

    $descripcion = get_post_meta($post->ID, 'descripcion', true);
    $ano_edificio = get_post_meta($post->ID, 'ano_edificio', true);
    
    $acceso_rodado = get_post_meta($post->ID, 'acceso_rodado', true);
    $uso_excl = get_post_meta($post->ID, 'uso_excl', true);
    $distribucion = get_post_meta($post->ID, 'distribucion', true);
    $aire_acond = get_post_meta($post->ID, 'aire_acond', true);
    
    // Obtener los valores de los checkboxes de orientación como un array
    // Verificar si el valor existe y, de lo contrario, asignar un array vacío
    $orientacion = is_array($orientacion = get_post_meta($post->ID, 'orientacion', true)) ? $orientacion : array();
    
    // Obtener los valores de los checkboxes de orientación como un array
    // Verificar si el valor existe y, de lo contrario, asignar un array vacío
    $otra_caract_inm = is_array($otra_caract_inm = get_post_meta($post->ID, 'otra_caract_inm', true)) ? $otra_caract_inm : array();
   
    // Obtener los valores de los checkboxes de orientación como un array
    // Verificar si el valor existe y, de lo contrario, asignar un array vacío
    $caract_local = is_array($caract_local = get_post_meta($post->ID, 'caract_local', true)) ? $caract_local : array();
    
    $residencial_altura = get_post_meta($post->ID, 'residencial_altura', true);
    $residencial_unif = get_post_meta($post->ID, 'residencial_unif', true);
    $terciario_ofi = get_post_meta($post->ID, 'terciario_ofi', true);
    $terciario_com = get_post_meta($post->ID, 'terciario_com', true);
    $terciario_hotel = get_post_meta($post->ID, 'terciario_hotel', true);
    $industrial = get_post_meta($post->ID, 'industrial', true);
    $dotaciones = get_post_meta($post->ID, 'dotaciones', true);
    $otra = get_post_meta($post->ID, 'otra', true);
    
    $ascensor_garaje = get_post_meta($post->ID, 'ascensor_garaje', true);
    $persona_seguridad = get_post_meta($post->ID, 'persona_seguridad', true);
    $plaza_cubierta = get_post_meta($post->ID, 'plaza_cubierta', true);
    $alarma_cerrada = get_post_meta($post->ID, 'alarma_cerrada', true);
    $puerta_auto = get_post_meta($post->ID, 'puerta_auto', true);


    $caracteristica_garaje = get_post_meta($post->ID, 'caracteristica_garaje', false);
    $m_parcela = get_post_meta($post->ID, 'm_parcela', true);
    $m_fachada = get_post_meta($post->ID, 'm_fachada', true);
    $m_plaza = get_post_meta($post->ID, 'm_plaza', true);
    $tipologia_chalet = get_post_meta($post->ID, 'tipologia_chalet', true);
    $tipo_rustica = get_post_meta($post->ID, 'tipo_rustica', true);
    $num_plantas = get_post_meta($post->ID, 'num_plantas', true);
    $num_estancias = get_post_meta($post->ID, 'num_estancias', true);
    $ubicacion_local = get_post_meta($post->ID, 'ubicacion_local', true);
    $tipo_plaza = get_post_meta($post->ID, 'tipo_plaza', true);
   
    // Salida de los campos personalizados en el formulario
    ?>
    <table class="form-table">
        <tr>
            <th><label for="tipo_inmueble">Tipo de Inmueble*</label></th>
            <td>
                <select name="tipo_inmueble" id="tipo_inmueble" required>
                    <option value="">Seleccionar</option>
                    <option value="piso" <?php selected( $tipo_inmueble, 'piso' ); ?>>Piso</option>
                    <option value="casa_chalet" <?php selected( $tipo_inmueble, 'casa_chalet' ); ?>>Casa / Chalet</option>
                    <option value="casa_rustica" <?php selected( $tipo_inmueble, 'casa_rustica' ); ?>>Casa Rústica</option>
                    <option value="local" <?php selected( $tipo_inmueble, 'local' ); ?>>Local o Nave</option>
                    <option value="garaje" <?php selected( $tipo_inmueble, 'garaje' ); ?>>Garaje</option>
                    <option value="oficina" <?php selected( $tipo_inmueble, 'oficina' ); ?>>Oficina</option>
                    <option value="terreno" <?php selected( $tipo_inmueble, 'terreno' ); ?>>Terreno</option>
                </select>
            </td>
        </tr>   
        <tr>
            <th><label for="localidad">Localidad*</label></th>
            <td><input type="text" name="localidad" id="localidad" value="<?php echo esc_attr( $localidad ); ?>" required></td>
        </tr>
        <tr>
            <th><label for="nombre_calle">Nombre de la Calle*</label></th>
            <td><input type="text" name="nombre_calle" id="nombre_calle" value="<?php echo esc_attr( $nombre_calle ); ?>" required></td>
        </tr>
        <tr>
            <th><label for="numero">Número*</label></th>
            <td>
                <input type="text" name="numero" id="numero" value="<?php echo esc_attr( $numero ); ?>" required>
                <input type="checkbox" name="numero_obligatorio" id="numero_obligatorio" <?php checked( true, get_post_meta( $post->ID, 'numero_obligatorio', true ) ); ?>>
                <label for="numero_obligatorio">Sin número</label>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><button class="btn btn-primary" type="button" id="validar_direccion">Validar Dirección</button></td>
        </tr>
        <div id="mapaModal" style="display: none;">
            <div id="mapa"></div>
            <button id="mapa_correcto">El mapa está correcto</button>
            <button id="cerrar_modal">Cancelar</button>
        </div>
        <input type="hidden" id="campo_mapa" name="campo_mapa" value="">
        <tr id="campo_planta">
            <th><label for="planta">Planta*</label></th>
            <td>
                <select name="planta" id="planta" required>
                    <option value="">Seleccionar</option>
                    <option value="sotano" <?php selected( $planta, 'sotano' ); ?>>Sótano</option>
                    <option value="bajo" <?php selected( $planta, 'bajo' ); ?>>Bajo</option>
                    <option value="1" <?php selected( $planta, '1' ); ?>>1</option>
                    <option value="2" <?php selected( $planta, '2' ); ?>>2</option>
                    <option value="3" <?php selected( $planta, '3' ); ?>>3</option>
                    <option value="4" <?php selected( $planta, '4' ); ?>>4</option>
                    <option value="5" <?php selected( $planta, '5' ); ?>>5</option>
                    <option value="6" <?php selected( $planta, '6' ); ?>>6</option>
                    <option value="7" <?php selected( $planta, '7' ); ?>>7</option>
                    <option value="8" <?php selected( $planta, '8' ); ?>>8</option>
                </select>
            </td>
        </tr>
        <tr id="campo_bloque">
            <th><label for="bloque">Bloque</label></th>
            <td><input type="text" name="bloque" id="bloque" value="<?php echo esc_attr( $bloque ); ?>" required></td>
        </tr>
        <tr id="campo_escalera">
            <th><label for="escalera">Puerta/Escalera</label></th>
            <td><input type="text" name="escalera" id="escalera" value="<?php echo esc_attr( $escalera ); ?>" required></td>
        </tr>
        <tr id="campo_urbanizacion">
            <th><label for="urbanizacion">Urbanización, si aplica</label></th>
            <td><input type="text" name="urbanizacion" id="urbanizacion" value="<?php echo esc_attr( $urbanizacion ); ?>" required></td>
        </tr>

        <tr>
            <th><label for="visibilidad_direccion">Visibilidad de la Dirección*</label></th>
            <td>
                <select name="visibilidad_direccion" id="visibilidad_direccion" required>
                    <option value="direccion_exacta" <?php selected( $visibilidad_direccion, 'direccion_exacta' ); ?>>Mostrar dirección exacta</option>
                    <option value="solo_calle" <?php selected( $visibilidad_direccion, 'solo_calle' ); ?>>Mostrar solo la calle sin número</option>
                    <option value="ocultar_direccion" <?php selected( $visibilidad_direccion, 'ocultar_direccion' ); ?>>Ocultar toda la dirección</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Tipo de Operación*</th>
            <td>
                <label><input type="radio" name="tipo_operacion" value="venta" <?php checked($tipo_operacion, 'venta'); ?> required>Venta</label>
                <label><input type="radio" name="tipo_operacion" value="alquiler" <?php checked($tipo_operacion, 'alquiler'); ?> required>Alquiler</label>
            </td>
        </tr>
        <tr id="campo_precio_venta">
            <th><label for="precio_venta">Precio Venta*</label></th>
            <td><input type="number" name="precio_venta" id="precio_venta" value="<?php echo esc_attr( $precio_venta ); ?>" required></td>
        </tr>
        <tr id="campo_gastos_comunidad">
            <th><label for="gastos_comunidad">Gastos de Comunidad</label></th>
            <td><input type="number" name="gastos_comunidad" id="gastos_comunidad" value="<?php echo esc_attr( $gastos_comunidad ); ?>"></td>
        </tr>
        <tr id="campo_precio_alquiler">
            <th><label for="precio_alquiler">Precio Alquiler Mensual*</label></th>
            <td><input type="number" name="precio_alquiler" id="precio_alquiler" value="<?php echo esc_attr( $precio_alquiler ); ?>" required></td>
        </tr>
        <tr id="campo_fianza">
            <th><label for="fianza">Fianza</label></th>
            <td>
                <select name="fianza" id="fianza" required>
                    <option value="">Seleccionar</option>
                    <option value="1" <?php selected( $fianza, '1' ); ?>>1 mes</option>
                    <option value="2" <?php selected( $fianza, '2' ); ?>>2 mes</option>
                    <option value="3" <?php selected( $fianza, '3' ); ?>>3 mes</option>
                    <option value="4" <?php selected( $fianza, '4' ); ?>>4 mes</option>
                    <option value="5" <?php selected( $fianza, '5' ); ?>>5 mes</option>
                    <option value="6" <?php selected( $fianza, '6' ); ?>>6 mes</option>
                    <option value="mas" <?php selected( $fianza, 'mas' ); ?>>Más</option>
                </select>
            </td>
        </tr>
        <tr id="campo_tipologia_chalet">
            <th>Tipo de Chalet</th>
            <td>
                <label><input type="radio" name="tipologia_chalet" value="atico" <?php checked($tipologia_chalet, 'atico'); ?> required>Adosado</label>
                <label><input type="radio" name="tipologia_chalet" value="estudio" <?php checked($tipologia_chalet, 'estudio'); ?>>Pareado</label>
                <label><input type="radio" name="tipologia_chalet" value="duplex" <?php checked($tipologia_chalet, 'duplex'); ?>>Independiente</label>
            </td>
        </tr>
        <tr id="campo_tipo_rustica">
            <th>Tipo de Casa Rústica*</th>
            <td>
                <label><input type="radio" name="tipo_rustica" value="finca" <?php checked($tipo_rustica, 'finca'); ?> required>Finca</label>
                <label><input type="radio" name="tipo_rustica" value="castillo" <?php checked($tipo_rustica, 'castillo'); ?>>Castillo</label>
                <label><input type="radio" name="tipo_rustica" value="casa_rural" <?php checked($tipo_rustica, 'casa_rural'); ?>>Casa Rural</label>
                <label><input type="radio" name="tipo_rustica" value="casa_pueblo" <?php checked($tipo_rustica, 'casa_pueblo'); ?>>Casa de Pueblo</label>
                <label><input type="radio" name="tipo_rustica" value="cortijo" <?php checked($tipo_rustica, 'cortijo'); ?>>Cortijo</label>
            </td>
        </tr>
        <tr id="campo_tipo_local">
            <th>Tipo de local</th>
            <td>
                <label><input type="radio" name="tipo_local" value="local" <?php checked($tipo_local, 'local'); ?> required>Local</label>
                <label><input type="radio" name="tipo_local" value="nave" <?php checked($tipo_local, 'nave'); ?>>Nave</label>
            </td>
        </tr>
        <tr id="campo_tipo_plaza">
            <th><label for="tipo_plaza">Tipo de plaza</label></th>
            <td>
                <select name="tipo_plaza" id="tipo_plaza" required>
                    <option value="">Seleccionar</option>
                    <option value="coche_peq" <?php selected($plaza, 'coche_peq'); ?>>Coche pequeño</option>
                    <option value="coche_grande" <?php selected($plaza, 'coche_grande'); ?>>Coche grande</option>
                    <option value="moto" <?php selected($plaza, 'moto'); ?>>Moto</option>
                    <option value="coche_moto" <?php selected($plaza, 'coche_moto'); ?>>Coche + Moto</option>
                    <option value="mas_coches" <?php selected($plaza, 'mas_coches'); ?>>2 coches o más</option>
                </select>
            </td>      
        </tr>
        <tr id="campo_caract_inm">
            <th>Característica adicional</th>
            <td>
                <label><input type="checkbox" name="caract_inm[]" id="atico" value="atico" <?php if (in_array('atico', $caract_inm)) echo 'checked'; ?>>Ático</label>
                <label><input type="checkbox" name="caract_inm[]" id="estudio" value="estudio" <?php if (in_array('estudio', $caract_inm)) echo 'checked'; ?>>Estudio</label>
                <label><input type="checkbox" name="caract_inm[]" id="duplex" value="duplex"<?php if (in_array('duplex', $caract_inm)) echo 'checked'; ?>>Dúplex</label>
            </td>
        </tr>


        <tr id="campo_m_construidos">
            <th><label for="m_construidos">Metros Construidos*</label></th>
            <td><input type="number" name="m_construidos" id="m_construidos" value="<?php echo esc_attr($m_construidos);?>" placeholder="m²" required></td>
        </tr>
        <tr id="campo_m_utiles">
            <th><label for="m_utiles">Metros Útiles</label></th>
            <td><input type="number" name="m_utiles" id="m_utiles" value="<?php echo esc_attr($m_utiles); ?>" placeholder="m²" required></td>
        </tr>
        <tr id="campo_m_parcela">
            <th><label for="m_parcela">Metros de Parcela</label></th>
            <td><input type="number" name="m_parcela" id="m_parcela" value="<?php echo esc_attr($m_parcela); ?>" placeholder="m²" required></td>
        </tr>
        <tr id="campo_m_lineales">
            <th><label for="m_lineales">Metros lineales de fachada*</label></th>
            <td><input type="number" name="m_lineales" id="m_lineales" value="<?php echo esc_attr($m_lineales); ?>" required></td>
        </tr>
        <tr id="campo_m_plaza">
            <th><label for="m_plaza">Superficie de la plaza</label></th>
            <td><input type="number" name="m_plaza" id="m_plaza" value="<?php echo esc_attr($m_plaza); ?>" required></td>
        </tr>
        <tr id="campo_superf_terreno">
            <th><label for="superf_terreno">Superficie total</label></th>
            <td><input type="number" name="superf_terreno" id="superf_terreno" value="<?php echo esc_attr($superf_terreno); ?>" required></td>
        </tr>
        <tr id="campo_num_dormitorios">
            <th><label for="num_dormitorios">Nº de Dormitorios*</label></th>
            <td><input type="number" name="num_dormitorios" id="num_dormitorios" value="<?php echo esc_attr($num_dormitorios); ?>"></td>
        </tr>
        <tr id="campo_num_banos">
            <th><label for="num_banos">Nº de Baños*</label></th>
            <td><input type="number" name="num_banos" id="num_banos" value="<?php echo esc_attr($num_banos); ?>"></td>
        </tr>
        <tr id="campo_num_estancias">
            <th><label for="num_estancias">Nº de estancias</label></th>
            <td><input type="number" name="num_estancias" id="num_estancias" value="<?php echo esc_attr($num_estancias); ?>"></td>
        </tr>
        <tr id="campo_num_plantas">
            <th><label for="num_plantas">Número de plantas</label></th>
            <td><input type="number" name="num_plantas" id="num_plantas" value="<?php echo esc_attr($num_plantas); ?>"></td>
        </tr>
        <tr id="campo_num_escap">
            <th><label for="num_escap">Número de escaparates*</label></th>
            <td><input type="number" name="num_escap" id="num_escap" value="<?php echo esc_attr($num_escap); ?>"></td>
        </tr>
        <tr id="campo_num_ascensores">
            <th><label for="num_ascensores">Número de ascensores*</label></th>
            <td><input type="number" name="num_ascensores" id="num_ascensores" value="<?php echo esc_attr($num_ascensores); ?>"></td>
        </tr>
        <tr id="campo_num_plazas">
            <th><label for="num_plazas">Número de plazas de garaje*</label></th>
            <td><input type="number" name="num_plazas" id="num_plazas" value="<?php echo esc_attr($num_plazas); ?>"></td>
        </tr>
        <tr id="campo_calif_consumo_energ">
            <th><label for="calif_consumo_energ">Calificación de consumo de energía*</label></th>
            <td>
                <select name="calif_consumo_energ" id="calif_consumo_energ" required>
                    <option value="" disabled <?php if (empty($calif_consumo_energ)) echo 'selected'; ?> >Seleccionar</option>
                    <option value="a" <?php selected($calif_consumo_energ, 'a'); ?>>A</option>
                    <option value="b" <?php selected($calif_consumo_energ, 'b'); ?>>B</option>
                    <option value="c" <?php selected($calif_consumo_energ, 'c'); ?>>C</option>
                    <option value="d" <?php selected($calif_consumo_energ, 'd'); ?>>D</option>
                    <option value="e" <?php selected($calif_consumo_energ, 'e'); ?>>E</option>
                    <option value="f" <?php selected($calif_consumo_energ, 'f'); ?>>F</option>
                    <option value="g" <?php selected($calif_consumo_energ, 'g'); ?>>G</option>
                    <option value="exento" <?php selected($calif_consumo_energ, 'exento'); ?>>Exento</option>
                    <option value="tramite" <?php selected($calif_consumo_energ, 'tramite'); ?>>En Trámite</option>
                </select>
                
                <th><label for="consumo_energ">Consumo de energía</label></th>
                <td><input type="number" name="consumo_energ" id="consumo_energ" value="<?php echo esc_attr($consumo_energ); ?>" placeholder="kwh/m2 año"></td>
            </td>
        </tr>
        <div id="campo_cal_emis">
            <tr>
                <th><label for="cal_emis">Calificación de Emisiones*</label></th>
                <td>
                    <select name="cal_emis" id="cal_emis" required>
                        <option value="">Seleccionar</option>
                        <option value="a" <?php selected($cal_emis, 'a'); ?>>A</option>
                        <option value="b" <?php selected($cal_emis, 'b'); ?>>B</option>
                        <option value="c" <?php selected($cal_emis, 'c'); ?>>C</option>
                        <option value="d" <?php selected($cal_emis, 'd'); ?>>D</option>
                        <option value="e" <?php selected($cal_emis, 'e'); ?>>E</option>
                        <option value="f" <?php selected($cal_emis, 'f'); ?>>F</option>
                        <option value="g" <?php selected($cal_emis, 'g'); ?>>G</option>
                    </select>
                </td>
                <th><label for="emisiones">Emisiones</label></th>
                <td><input type="number" name="emisiones" id="emisiones" value="<?php echo esc_attr($emisiones); ?>" placeholder="kg CO / m2 año"></td>
            </tr>        
        </div>
        
        <tr id="campo_acceso_rodado">
            <th>Acceso rodado*</th>
            <td>
                <label><input type="radio" name="acceso_rodado" value="no_disponible" <?php checked($acceso_rodado, 'no_disponible'); ?> >No disponible</label>
                <label><input type="radio" name="acceso_rodado" value="si_tiene" <?php checked($acceso_rodado, 'si_tiene'); ?>>Sí, tiene</label>
            </td>
        </tr>
        <tr id="campo_si_rodado">
            <td>
                <select name="si_rodado" id="si_rodado">
                    <option value="">Seleccionar</option>
                    <option value="no" <?php selected( $no, '1' ); ?>>No lo sé</option>
                    <option value="urbana" <?php selected( $urbana, '1' ); ?>>Vía urbana</option>
                    <option value="carretera" <?php selected( $carretera, '1' ); ?>>Por carretera</option>
                    <option value="tierra" <?php selected( $tierra, '1' ); ?>>Camino de tierra</option>
                    <option value="autovia" <?php selected( $autovia, '1' ); ?>>Por autovía</option>
                </select>
            </td>
        </tr>
        <tr id="campo_estado_cons">
            <th>Estado de Conservación*</th>
            <td>
                <label><input type="radio" name="estado_cons" value="buen_estado" <?php checked($estado_cons, 'buen_estado'); ?> required> Buen Estado</label>
                <label><input type="radio" name="estado_cons" value="a_reformar" <?php checked($estado_cons, 'a_reformar'); ?>> A Reformar</label>
            </td>
        </tr>
        <tr id="campo_int_ext">
            <th>Interior/ Exterior*</th>
            <td>
                <label><input type="radio" name="interior_ext" value="interior" <?php checked($interior_ext, 'interior'); ?> required>Interior</label>
                <label><input type="radio" name="interior_ext" value="exterior" <?php checked($interior_ext, 'exterior'); ?> >Exterior</label>
            </td>
        </tr>
        <tr id="campo_ascensor">
            <th>Ascensor*</th>
            <td>
                <label><input type="radio" name="ascensor" value="si" <?php checked($ascensor, 'si'); ?> required>Sí</label>
                <label><input type="radio" name="ascensor" value="no" <?php checked($ascensor, 'no'); ?>> No</label>
            </td>
        </tr>
        <tr id="campo_ac">
            <th>Aire acondicionado*</th>
            <td>
                <label><input type="radio" name="ac" value="no" <?php checked($ac, 'no'); ?>>No disponible</label>
                <label><input type="radio" name="ac" value="frio" <?php checked($ac, 'frio'); ?>>Frío</label>
                <label><input type="radio" name="ac" value="frio_calor" <?php checked($ac, 'frio_calor'); ?>>Frío/calor</label>
                <label><input type="radio" name="ac" value="preinst" <?php checked($ac, 'preinst'); ?>>Preinstalación</label>
            </td>
        </tr>
        <tr id="campo_uso_excl">
            <th>Uso exclusivo*</th>
            <td>
                <label><input type="radio" name="uso_excl" value="si" <?php checked($uso_excl, 'si'); ?>>Sí</label>
                <label><input type="radio" name="uso_excl" value="no" <?php checked($uso_excl, 'no'); ?>>No</label>
            </td>
        </tr>
        <tr id="campo_ubicacion_local">
            <th><label for="ubicacion_local">Ubicación</label></th>
            <td>
                <select name="ubicacion_local" id="ubicacion_local">
                    <option value="">Seleccionar</option>
                    <option value="pie_calle" <?php selected($ubicacion_local, 'pie_calle'); ?>>A pie de calle</option>
                    <option value="centro_com" <?php selected($ubicacion_local, 'centro_com'); ?>>Centro comercial</option>
                    <option value="entreplanta" <?php selected($ubicacion_local, 'entreplanta'); ?>>Entreplanta</option>
                    <option value="subterraneo" <?php selected($ubicacion_local, 'subterraneo'); ?>>Subterráneo</option>
                </select>
            </td>
        </tr>
        <tr id="campo_distribucion">
            <th><label for="distribucion">Distribución*</label></th>
            <td>
                <select name="distribucion" id="distribucion">
                    <option value="">Seleccionar</option>
                    <option value="diafana" <?php selected($distribucion, 'diafana'); ?>>Diáfana</option>
                    <option value="mamparas" <?php selected($distribucion, 'mamparas'); ?>>Dividida con mamparas</option>
                    <option value="tabiques" <?php selected($distribucion, 'tabiques'); ?>>Dividida con tabiques</option>
                </select>
            </td>
        </tr>
        <tr id="campo_aire_acond">
            <th><label for="aire_acond">Aire acondicionado*</label></th>
            <td>
                <select name="aire_acond" id="aire_acond">
                    <option value="">Seleccionar</option>
                    <option value="no_disponible" <?php selected($aire_acond, 'no_disponible'); ?>>No disponible</option>
                    <option value="frio" <?php selected($aire_acond, 'frio'); ?>>Frío</option>
                    <option value="frio_calor" <?php selected($aire_acond, 'frio_calor'); ?>>Frío/calor</option>
                    <option value="preinstalado" <?php selected($aire_acond, 'preinstalado'); ?>>Preinstalación</option>
                </select>
            </td>
        </tr>
        <tr id="campo_orientacion">
    <th>Orientacion</th>
    <td>
        <label><input type="checkbox" name="orientacion[]" id="norte" value="norte" <?php if (in_array('norte', $orientacion)) echo 'checked'; ?>>Norte</label>
        <label><input type="checkbox" name="orientacion[]" id="sur" value="sur" <?php if (in_array('sur', $orientacion)) echo 'checked'; ?>>Sur</label>
        <label><input type="checkbox" name="orientacion[]" id="este" value="este" <?php if (in_array('este', $orientacion)) echo 'checked'; ?>>Este</label>
        <label><input type="checkbox" name="orientacion[]" id="oeste" value="oeste" <?php if (in_array('oeste', $orientacion)) echo 'checked'; ?>>Oeste</label>
    </td>
</tr>

        <tr id="campo_calificacion_terreno">
            <th>Tipo de calificación*</th>
                <td>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="residencial_altura" value="<?php echo esc_attr($residencial_altura); ?>">Residencial en altura</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="residencial_unif" value="<?php echo esc_attr($residencial_unif); ?>">Residencial unifamiliar</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="terciario_ofi" value="<?php echo esc_attr($terciario_ofi); ?>">Terciario oficinas</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="terciario_com" value="<?php echo esc_attr($terciario_com); ?>">Terciario comercial</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="terciario_hotel" value="<?php echo esc_attr($terciario_hotel); ?>">Terciario hoteles</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="industrial" value="<?php echo esc_attr($industrial); ?>">Industrial</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="dotaciones" value="<?php echo esc_attr($dotaciones); ?>">Dotaciones</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="otra" value="<?php echo esc_attr($otra); ?>">Otra</label>
                </td>
        </tr>
        <div id="campo_otra_caract_inm">
            <tr>
                <th>Otras caracteristicas de la vivienda</th>
                <td>
                    <label><input type="checkbox" name="otra_caract_inm[]" id="armario" value="armario" <?php if (in_array('armario', $otra_caract_inm)) echo 'checked'; ?> >Armarios empotrados</label>
                    <label><input type="checkbox" name="otra_caract_inm[]" id="aire" value="aire" <?php if (in_array('aire', $otra_caract_inm)) echo 'checked'; ?> >Aire Acondicionado</label>
                    <label><input type="checkbox" name="otra_caract_inm[]" id="terraza" value="terraza" <?php if (in_array('terraza', $otra_caract_inm)) echo 'checked'; ?> >Terraza</label>
                    <label><input type="checkbox" name="otra_caract_inm[]" id="balcon" value="balcon" <?php if (in_array('balcon', $otra_caract_inm)) echo 'checked'; ?> >Balcón</label>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>    
                    <label><input type="checkbox" name="otra_caract_inm[]" id="garaje" value="garaje" <?php if (in_array('garaje', $otra_caract_inm)) echo 'checked'; ?> >plaza de garaje</label>
                    <label><input type="checkbox" name="otra_caract_inm[]" id="chimenea" value="chimenea" <?php if (in_array('chimenea', $otra_caract_inm)) echo 'checked'; ?> >Chimenea</label>
                    <label><input type="checkbox" name="otra_caract_inm[]" id="trastero" value="trastero" <?php if (in_array('trastero', $otra_caract_inm)) echo 'checked'; ?> >Trastero</label>
                    <label><input type="checkbox" name="otra_caract_inm[]" id="piscina" value="piscina" <?php if (in_array('piscina', $otra_caract_inm)) echo 'checked'; ?> >Piscina</label>
                    <label><input type="checkbox" name="otra_caract_inm[]" id="jardin" value="jardin" <?php if (in_array('jardin', $otra_caract_inm)) echo 'checked'; ?> >Jardín</label>
                </td>   
            </tr>
        </div>
        <tr id="campo_calefaccion">
            <th><label for="calefaccion">Tipo de calefacción</label></th>
            <td>
                <select name="calefaccion" id="calefaccion">
                    <option value="">Seleccionar</option>
                    <option value="individual" <?php selected($calefaccion, 'individual'); ?>>Individual</option>
                    <option value="centralizada" <?php selected($calefaccion, 'centralizada'); ?>>Centralizada</option>
                    <option value="no_dispone" <?php selected($calefaccion, 'no_dispone'); ?>>No dispone</option>
                </select>
            </td>
        </tr>
        <div id="campo_caract_local">
            <tr>
                <th>Equipamiento</th>
                <td>    
                    <label><input type="checkbox" name="caract_local[]" id="calefaccion" value="calefaccion" <?php if (in_array('calefaccion', $caract_local)) echo 'checked'; ?> >Calefacción</label>
                    <label><input type="checkbox" name="caract_local[]" id="humos" value="humos" <?php if (in_array('humos', $caract_local)) echo 'checked'; ?> >Salida de humos</label>
                    <label><input type="checkbox" name="caract_local[]" id="cocina_equipada" value="cocina_equipada" <?php if (in_array('cocina_equipada', $caract_local)) echo 'checked'; ?> >Cocina totalmente equipada</label>
                    <label><input type="checkbox" name="caract_local[]" id="puerta_seguridad" value="puerta_seguridad" <?php if (in_array('puerta_seguridad', $caract_local)) echo 'checked'; ?> >Puerta de seguridad</label>
                    <label><input type="checkbox" name="caract_local[]" id="alarma" value="alarma" <?php if (in_array('alarma', $caract_local)) echo 'checked'; ?> >Sistema de alarma</label>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>    
                    <label><input type="checkbox" name="caract_local[]" id="almacen" value="almacen" <?php if (in_array('almacen', $caract_local)) echo 'checked'; ?> >Almacén</label>
                    <label><input type="checkbox" name="caract_local[]" id="circuito" value="circuito" <?php if (in_array('circuito', $caract_local)) echo 'checked'; ?> >Circuito cerrado de seguridad</label>
                    <label><input type="checkbox" name="caract_local[]" id="esquina" value="esquina" <?php if (in_array('esquina', $caract_local)) echo 'checked'; ?> >Hace esquina</label>
                    <label><input type="checkbox" name="caract_local[]" id="oficina" value="oficina" <?php if (in_array('oficina', $caract_local)) echo 'checked'; ?> >Tiene oficina</label>
                </td>
            </tr>
        </div>
        <tr id="campo_caract_garaje">
            <th>Características del garaje</th>
            <td>    
                <label><input type="checkbox" name="ascensor_garaje" id="ascensor_garaje" value="<?php echo esc_attr($ascensor_garaje); ?>">Ascensor</label>
                <label><input type="checkbox" name="persona_seguridad" id="persona_seguridad" value="<?php echo esc_attr($persona_seguridad); ?>">Personal de seguridad</label>
                <label><input type="checkbox" name="plaza_cubierta" id="plaza_cubierta" value="<?php echo esc_attr($plaza_cubierta); ?>">Plaza cubierta</label>
                <label><input type="checkbox" name="alarma_cerarada" id="alarma_cerarada" value="<?php echo esc_attr($alarma_cerrada); ?>">Alarma con circuito cerrado</label>
                <label><input type="checkbox" name="puerta_auto" id="puerta_auto" value="<?php echo esc_attr($puerta_auto); ?>">Puerta automática</label>
            </td>
        </tr>
        <tr id="campo_ano_edificio">
            <th><label for="ano_edificio">Año de construcción del edificio</label></th>
            <td><input type="number" name="ano_edificio" id="ano_edificio" value="<?php echo esc_attr($ano_edificio); ?>" ></td>
        </tr>
        <tr>
            <th><label for="descripcion">Descripción de la propiedad</label></th>
            <td><textarea name="descripcion" id="descripcion"><?php echo esc_textarea( $descripcion ); ?></textarea></td>
        </tr>


        <!-- Campo de Galería de Imágenes -->
        <tr>
            <th>Galería de Imágenes</th>
            <td>
                <div id="galeria-imagenes-container" class="sortable-container ui-sortable">
                    <?php if (!empty($galeria_imagenes)) : ?>
                        <?php foreach ($galeria_imagenes as $index => $imagen) : ?>
                            <div class="galeria-imagen">
                                <div class="galeria-imagen-inner">
                                    <img src="<?php echo esc_url($imagen); ?>" alt="Imagen">
                                    <button type="button" class="remove-imagen button-link">Eliminar</button>
                                </div>
                                <input type="hidden" name="galeria_imagenes[]" value="<?php echo esc_attr( $imagen ); ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="agregar-imagen button button-primary">Agregar Imagen</button>
            </td>
        </tr>

    </table>
   
    <?php
}


/**
 * Guarda los valores de los campos personalizados al guardar un inmueble.
 *
 * @param int $post_id ID del inmueble actual.
 */
function inmuebles_guardar_campos_personalizados( $post_id ) {
    // Verificar si se está guardando un inmueble
    if ( get_post_type( $post_id ) !== 'inmueble' ) {
        return;
    }

    // Guardar los valores de los campos personalizados
    if ( isset( $_POST['tipo_inmueble'] ) ) {
        update_post_meta( $post_id, 'tipo_inmueble', sanitize_text_field( $_POST['tipo_inmueble'] ) );
    }
    if ( isset( $_POST['localidad'] ) ) {
        update_post_meta( $post_id, 'localidad', sanitize_text_field( $_POST['localidad'] ) );
    }
    if ( isset( $_POST['nombre_calle'] ) ) {
        update_post_meta( $post_id, 'nombre_calle', sanitize_text_field( $_POST['nombre_calle'] ) );
    }
    if ( isset( $_POST['numero'] ) ) {
        update_post_meta( $post_id, 'numero', sanitize_text_field( $_POST['numero'] ) );
    }
    if ( isset( $_POST['planta'] ) ) {
        update_post_meta( $post_id, 'planta', sanitize_text_field( $_POST['planta'] ) );
    }
    if ( isset( $_POST['bloque'] ) ) {
        update_post_meta( $post_id, 'bloque', sanitize_text_field( $_POST['bloque'] ) );
    }
    if ( isset( $_POST['escalera'] ) ) {
        update_post_meta( $post_id, 'escalera', sanitize_text_field( $_POST['escalera'] ) );
    }
    if ( isset( $_POST['urbanizacion'] ) ) {
        update_post_meta( $post_id, 'urbanizacion', sanitize_text_field( $_POST['urbanizacion'] ) );
    }
    if ( isset( $_POST['visibilidad_direccion'] ) ) {
        update_post_meta( $post_id, 'visibilidad_direccion', sanitize_text_field( $_POST['visibilidad_direccion'] ) );
    }
    if ( isset( $_POST['tipo_operacion'] ) ) {
        update_post_meta( $post_id, 'tipo_operacion', sanitize_text_field( $_POST['tipo_operacion'] ) );
    }
    if ( isset( $_POST['precio_venta'] ) ) {
        update_post_meta( $post_id, 'precio_venta', sanitize_text_field( $_POST['precio_venta'] ) );
    }
    if ( isset( $_POST['precio_alquiler'] ) ) {
        update_post_meta( $post_id, 'precio_alquiler', sanitize_text_field( $_POST['precio_alquiler'] ) );
    }
    if ( isset( $_POST['gastos_comunidad'] ) ) {
        update_post_meta( $post_id, 'gastos_comunidad', sanitize_text_field( $_POST['gastos_comunidad'] ) );
    }
    if ( isset( $_POST['fianza'] ) ) {
        update_post_meta( $post_id, 'fianza', sanitize_text_field( $_POST['fianza'] ) );
    }
    if ( isset( $_POST['calefaccion'] ) ) {
        update_post_meta( $post_id, 'calefaccion', sanitize_text_field( $_POST['calefaccion'] ) );
    }



    if (isset($_POST['acceso_rodado'])) {
        update_post_meta($post_id, 'acceso_rodado', sanitize_text_field($_POST['acceso_rodado']));
    }
    
    if (isset($_POST['uso_excl'])) {
        update_post_meta($post_id, 'uso_excl', sanitize_text_field($_POST['uso_excl']));
    }
    
    if (isset($_POST['distribucion'])) {
        update_post_meta($post_id, 'distribucion', sanitize_text_field($_POST['distribucion']));
    }
    if (isset($_POST['aire_acond'])) {
        update_post_meta($post_id, 'aire_acond', sanitize_text_field($_POST['aire_acond']));
    }

    // Verificamos si se han enviado los checkboxes y si el valor enviado es un array
    if (isset($_POST['orientacion']) && is_array($_POST['orientacion'])) {
        // Sanitizamos los valores del array
        $orientacion = array_map('sanitize_text_field', $_POST['orientacion']);
        
        // Actualizamos el campo personalizado 'orientacion' con el array de valores
        update_post_meta($post_id, 'orientacion', $orientacion);
    } else {
        // Si no se ha seleccionado ninguna orientación, guardamos un array vacío para asegurarnos de que el campo esté limpio
        update_post_meta($post_id, 'orientacion', array());
    }

    // Verificamos si se han enviado los checkboxes y si el valor enviado es un array
    if (isset($_POST['caract_inm']) && is_array($_POST['caract_inm'])) {
        // Sanitizamos los valores del array
        $caract_inm = array_map('sanitize_text_field', $_POST['caract_inm']);
        
        // Actualizamos el campo personalizado 'caract_inm' con el array de valores
        update_post_meta($post_id, 'caract_inm', $caract_inm);
    } else {
        // Si no se ha seleccionado ninguna caract_inm, guardamos un array vacío para asegurarnos de que el campo esté limpio
        update_post_meta($post_id, 'caract_inm', array());
    }

    // Verificamos si se han enviado los checkboxes y si el valor enviado es un array
    if (isset($_POST['otra_caract_inm']) && is_array($_POST['otra_caract_inm'])) {
        // Sanitizamos los valores del array
        $otra_caract_inm = array_map('sanitize_text_field', $_POST['otra_caract_inm']);
        // Actualizamos el campo personalizado 'otra_caract_inm' con el array de valores
        update_post_meta($post_id, 'otra_caract_inm', $otra_caract_inm);
    } else {
        // Si no se ha seleccionado ninguna otra_caract_inm, guardamos un array vacío para asegurarnos de que el campo esté limpio
        update_post_meta($post_id, 'otra_caract_inm', array());
    }

    // Verificamos si se han enviado los checkboxes y si el valor enviado es un array
    if (isset($_POST['caract_local']) && is_array($_POST['caract_local'])) {
        // Sanitizamos los valores del array
        $caract_local = array_map('sanitize_text_field', $_POST['caract_local']);
        // Actualizamos el campo personalizado 'caract_local' con el array de valores
        update_post_meta($post_id, 'caract_local', $caract_local);
    } else {
        // Si no se ha seleccionado ninguna caract_local, guardamos un array vacío para asegurarnos de que el campo esté limpio
        update_post_meta($post_id, 'caract_local', array());
    }

    
    if (isset($_POST['residencial_altura'])) {
        update_post_meta($post_id, 'residencial_altura', sanitize_text_field($_POST['residencial_altura']));
    }
    
    if (isset($_POST['residencial_unif'])) {
        update_post_meta($post_id, 'residencial_unif', sanitize_text_field($_POST['residencial_unif']));
    }
    
    if (isset($_POST['terciario_ofi'])) {
        update_post_meta($post_id, 'terciario_ofi', sanitize_text_field($_POST['terciario_ofi']));
    }
    
    if (isset($_POST['terciario_com'])) {
        update_post_meta($post_id, 'terciario_com', sanitize_text_field($_POST['terciario_com']));
    }
    
    if (isset($_POST['terciario_hotel'])) {
        update_post_meta($post_id, 'terciario_hotel', sanitize_text_field($_POST['terciario_hotel']));
    }
    
    if (isset($_POST['industrial'])) {
        update_post_meta($post_id, 'industrial', sanitize_text_field($_POST['industrial']));
    }
    
    if (isset($_POST['dotaciones'])) {
        update_post_meta($post_id, 'dotaciones', sanitize_text_field($_POST['dotaciones']));
    }
    
    if (isset($_POST['otra'])) {
        update_post_meta($post_id, 'otra', sanitize_text_field($_POST['otra']));
    }
    


    if (isset($_POST['humos'])) {
        update_post_meta($post_id, 'humos', sanitize_text_field($_POST['humos']));
    }
    if (isset($_POST['m_construidos'])) {
        update_post_meta($post_id, 'm_construidos', sanitize_text_field($_POST['m_construidos']));
    }
    if (isset($_POST['num_dormitorios'])) {
        update_post_meta($post_id, 'num_dormitorios', sanitize_text_field($_POST['num_dormitorios']));
    }
    if (isset($_POST['num_banos'])) {
        update_post_meta($post_id, 'num_banos', sanitize_text_field($_POST['num_banos']));
    }
    if (isset($_POST['m_utiles'])) {
        update_post_meta($post_id, 'm_utiles', sanitize_text_field($_POST['m_utiles']));
    }
    if (isset($_POST['m_lineales'])) {
        update_post_meta($post_id, 'm_lineales', sanitize_text_field($_POST['m_lineales']));
    }
    if (isset($_POST['calif_consumo_energ'])) {
        update_post_meta($post_id, 'calif_consumo_energ', sanitize_text_field($_POST['calif_consumo_energ']));
    }


    if (isset($_POST['consumo_energ'])) {
        update_post_meta($post_id, 'consumo_energ', sanitize_text_field($_POST['consumo_energ']));
    }
    




    if (isset($_POST['cocina_equipada'])) {
        update_post_meta($post_id, 'cocina_equipada', sanitize_text_field($_POST['cocina_equipada']));
    }
    
    if (isset($_POST['puerta_seguridad'])) {
        update_post_meta($post_id, 'puerta_seguridad', sanitize_text_field($_POST['puerta_seguridad']));
    }
    
    if (isset($_POST['alarma'])) {
        update_post_meta($post_id, 'alarma', sanitize_text_field($_POST['alarma']));
    }
    
    if (isset($_POST['almacen'])) {
        update_post_meta($post_id, 'almacen', sanitize_text_field($_POST['almacen']));
    }
    
    if (isset($_POST['ascensor_garaje'])) {
        update_post_meta($post_id, 'ascensor_garaje', sanitize_text_field($_POST['ascensor_garaje']));
    }
    
    if (isset($_POST['persona_seguridad'])) {
        update_post_meta($post_id, 'persona_seguridad', sanitize_text_field($_POST['persona_seguridad']));
    }
    
    if (isset($_POST['plaza_cubierta'])) {
        update_post_meta($post_id, 'plaza_cubierta', sanitize_text_field($_POST['plaza_cubierta']));
    }
    
    if (isset($_POST['alarma_cerrada'])) {
        update_post_meta($post_id, 'alarma_cerrada', sanitize_text_field($_POST['alarma_cerrada']));
    }
    
    if (isset($_POST['puerta_auto'])) {
        update_post_meta($post_id, 'puerta_auto', sanitize_text_field($_POST['puerta_auto']));
    }
    
    
    
    
    if (isset($_POST['emisiones'])) {
        update_post_meta($post_id, 'emisiones', sanitize_text_field($_POST['emisiones']));
    }
    
    if (isset($_POST['chimenea'])) {
        update_post_meta($post_id, 'chimenea', sanitize_text_field($_POST['chimenea']));
    }
    
    
    if (isset($_POST['ascensor'])) {
        update_post_meta($post_id, 'ascensor', sanitize_text_field($_POST['ascensor']));
    }
    if (isset($_POST['interior_ext'])) {
        update_post_meta($post_id, 'interior_ext', sanitize_text_field($_POST['interior_ext']));
    }
    
    if (isset($_POST['centralizada'])) {
        update_post_meta($post_id, 'centralizada', sanitize_text_field($_POST['centralizada']));
    }
    
    if (isset($_POST['no_dispone'])) {
        update_post_meta($post_id, 'no_dispone', sanitize_text_field($_POST['no_dispone']));
    }
    if (isset($_POST['estado_cons'])) {
        update_post_meta($post_id, 'estado_cons', sanitize_text_field($_POST['estado_cons']));
    }
    if (isset($_POST['cal_emis'])) {
        update_post_meta($post_id, 'cal_emis', sanitize_text_field($_POST['cal_emis']));
    }
    
    if (isset($_POST['caracteristica_garaje'])) {
        update_post_meta($post_id, 'caracteristica_garaje', sanitize_text_field($_POST['caracteristica_garaje']));
    }
    
    if (isset($_POST['m_parcela'])) {
        update_post_meta($post_id, 'm_parcela', sanitize_text_field($_POST['m_parcela']));
    }
    
    if (isset($_POST['m_fachada'])) {
        update_post_meta($post_id, 'm_fachada', sanitize_text_field($_POST['m_fachada']));
    }
    
    if (isset($_POST['m_plaza'])) {
        update_post_meta($post_id, 'm_plaza', sanitize_text_field($_POST['m_plaza']));
    }
    
    if (isset($_POST['tipologia_chalet'])) {
        update_post_meta($post_id, 'tipologia_chalet', sanitize_text_field($_POST['tipologia_chalet']));
    }
    if (isset($_POST['tipo_local'])) {
        update_post_meta($post_id, 'tipo_local', sanitize_text_field($_POST['tipo_local']));
    }
    
    if (isset($_POST['tipo_rustica'])) {
        update_post_meta($post_id, 'tipo_rustica', sanitize_text_field($_POST['tipo_rustica']));
    }
    
    if (isset($_POST['num_plantas'])) {
        update_post_meta($post_id, 'num_plantas', sanitize_text_field($_POST['num_plantas']));
    }
    
    if (isset($_POST['num_escap'])) {
        update_post_meta($post_id, 'num_escap', sanitize_text_field($_POST['num_escap']));
    }
    
    if (isset($_POST['num_estancias'])) {
        update_post_meta($post_id, 'num_estancias', sanitize_text_field($_POST['num_estancias']));
    }
    
    if (isset($_POST['ubicacion_local'])) {
        update_post_meta($post_id, 'ubicacion_local', sanitize_text_field($_POST['ubicacion_local']));
    }
    
    if (isset($_POST['tipo_plaza'])) {
        update_post_meta($post_id, 'tipo_plaza', sanitize_text_field($_POST['tipo_plaza']));
    }
    






    if (isset($_POST['descripcion'])) {
        update_post_meta($post_id, 'descripcion', sanitize_text_field($_POST['descripcion']));
    }
    if (isset($_POST['ano_edificio'])) {
        update_post_meta($post_id, 'ano_edificio', sanitize_text_field($_POST['ano_edificio']));
    }
    if ( isset( $_POST['galeria_imagenes'] ) ) {
        $galeria_imagenes = array_map( 'sanitize_text_field', $_POST['galeria_imagenes'] );
        update_post_meta( $post_id, 'galeria_imagenes', $galeria_imagenes );
    } else {
        update_post_meta( $post_id, 'galeria_imagenes', array() );
    }

}
add_action( 'save_post', 'inmuebles_guardar_campos_personalizados' );


/**
 * Agrega campos personalizados al formulario de edición de inmuebles.
 */
function inmuebles_agregar_campos_personalizados() {
    add_meta_box(
        'inmueble_campos_personalizados',
        'Datos del inmueble',
        'mostrar_campos_personalizados',
        'inmueble',
        'normal',
        'high'
    );

}
add_action( 'add_meta_boxes', 'inmuebles_agregar_campos_personalizados' );