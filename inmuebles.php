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



 function inmuebles_load_scripts() {
    // Registrar jQuery y jQuery UI
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');

    // Registrar el script 'media' de WordPress
    wp_enqueue_media();

    // Cargar la biblioteca de Google Maps JavaScript API
    wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCmfeK4KkbVFVCjYQW3XpZpzrxaY5z6tQM', array(), null, true);

    // Registrar el script personalizado
    wp_enqueue_script('inmuebles-script', plugin_dir_url(__FILE__) . 'js/main.js', array('jquery', 'jquery-ui-sortable', 'media'), '1.0', true);
}
add_action('admin_enqueue_scripts', 'inmuebles_load_scripts');



function inmuebles_load_styles() {
    // Registrar el archivo CSS del plugin
    wp_enqueue_style('inmuebles-style', plugin_dir_url(__FILE__) . 'css/style.css', array(), '1.0', 'all');
}
add_action('admin_enqueue_scripts', 'inmuebles_load_styles');



/**
 * Registra el tipo de entrada personalizado 'inmueble'.
 */
function inmuebles_registrar_tipo_entrada_inmueble() {
    $labels = array(
        'name' => 'Inmuebles',
        'singular_name' => 'Inmueble',
        'menu_name' => 'Inmuebles',
        'name_admin_bar' => 'Inmueble',
        'add_new' => 'Añadir Nuevo',
        'add_new_item' => 'Añadir Nuevo Inmueble',
        'new_item' => 'Nuevo Inmueble',
        'edit_item' => 'Editar Inmueble',
        'view_item' => 'Ver Inmueble',
        'all_items' => 'Todos los Inmuebles',
        'search_items' => 'Buscar Inmuebles',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'rewrite' => array( 'slug' => 'inmuebles' ),
        'menu_icon' => 'dashicons-admin-multisite',
        'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
    );

    register_post_type( 'inmueble', $args );
}
add_action( 'init', 'inmuebles_registrar_tipo_entrada_inmueble' );


/**
 * Registra la taxonomía 'inmuebles'.
 */
function inmuebles_registrar_taxonomia_inmuebles() {
    $labels = array(
        'name' => 'Inmuebles',
        'singular_name' => 'Inmueble',
        'search_items' => 'Buscar Inmuebles',
        'all_items' => 'Todos los Inmuebles',
        'edit_item' => 'Editar Inmueble',
        'update_item' => 'Actualizar Inmueble',
        'add_new_item' => 'Añadir Nuevo Inmueble',
        'new_item_name' => 'Nombre del Nuevo Inmueble',
        'menu_name' => 'Inmuebles',
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'inmuebles' ),
    );

    register_taxonomy( 'inmuebles', 'post', $args );
}
add_action( 'init', 'inmuebles_registrar_taxonomia_inmuebles' );


/**
 * Agrega campos personalizados al formulario de edición de inmuebles.
 */
function inmuebles_agregar_campos_personalizados() {
    add_meta_box(
        'inmueble_campos_personalizados',
        'Información Adicional',
        'inmuebles_mostrar_campos_personalizados',
        'inmueble',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'inmuebles_agregar_campos_personalizados' );



/**
 * Muestra los campos personalizados en el formulario de edición de inmuebles.
 *
 * @param WP_Post $post El objeto de entrada actual.
 */
function inmuebles_mostrar_campos_personalizados( $post ) {
    // Recuperar los valores actuales de los campos personalizados
    $nombre = get_post_meta($post->ID, 'nombre', true);
    $descripcion = get_post_meta($post->ID, 'descripcion', true);
    $tipo_inmueble = get_post_meta($post->ID, 'tipo_inmueble', true);
    $m_construidos = get_post_meta($post->ID, 'm_utiles', true);
    $m_utiles = get_post_meta($post->ID, 'm_construidos', true);
    $localidad = get_post_meta($post->ID, 'localidad', true);
    $nombre_calle = get_post_meta($post->ID, 'nombre_calle', true);
    $numero = get_post_meta($post->ID, 'numero', true);
    $planta = get_post_meta($post->ID, 'planta', true);
    $jardin = get_post_meta($post->ID, 'jardin', true);
    $bloque = get_post_meta($post->ID, 'bloque', true);
    $escalera = get_post_meta($post->ID, 'escalera', true);
    $galeria_imagenes = get_post_meta($post->ID, 'galeria_imagenes', true); // Nueva variable para la galería de imágenes
    $descripcion = get_post_meta($post->ID, 'descripcion', true);
    $visibilidad_direccion = get_post_meta($post->ID, 'visibilidad_direccion', true);
    $tipo_operacion = get_post_meta($post->ID, 'tipo_operacion', true);
    $precio_venta = get_post_meta($post->ID, 'precio_venta', true);
    $gastos_comunidad = get_post_meta($post->ID, 'gastos_comunidad', true);
    $precio_alquiler = get_post_meta($post->ID, 'precio_alquiler', true);
    $fianza = get_post_meta($post->ID, 'fianza', true);
    $metros_construidos = get_post_meta($post->ID, 'metros_construidos', true);
    $metros_utiles = get_post_meta($post->ID, 'metros_utiles', true);
    $num_dormitorios = get_post_meta($post->ID, 'num_dormitorios', true);
    $num_banos = get_post_meta($post->ID, 'num_banos', true);
    $certificacion_energetica = get_post_meta($post->ID, 'certificacion_energetica', true);
    $consumo_energetico = get_post_meta($post->ID, 'consumo_energetico', true);
    $calificacion_emisiones = get_post_meta($post->ID, 'calificacion_emisiones', true);
    $emisiones = get_post_meta($post->ID, 'emisiones', true);
    $estado_conservacion = get_post_meta($post->ID, 'estado_conservacion', true);
    $ascensor = get_post_meta($post->ID, 'ascensor', true);
    $orientacion = get_post_meta($post->ID, 'orientacion', true);

   
    // Salida de los campos personalizados en el formulario
    ?>
    <table class="form-table">
        <tr>
            <th><label for="tipo_inmueble">Tipo de Inmueble*</label></th>
            <td>
                <select name="tipo_inmueble" id="tipo_inmueble">
                    <option value="">Seleccionar</option>
                    <option value="piso" <?php selected( $tipo_inmueble, 'piso' ); ?>>Piso</option>
                    <option value="casa_chalet" <?php selected( $tipo_inmueble, 'casa_chalet' ); ?>>Casa / Chalet</option>
                    <option value="casa_rustica" <?php selected( $tipo_inmueble, 'casa_rustica' ); ?>>Casa Rústica</option>
                    <option value="local_nave" <?php selected( $tipo_inmueble, 'local_nave' ); ?>>Local o Nave</option>
                    <option value="garaje" <?php selected( $tipo_inmueble, 'garaje' ); ?>>Garaje</option>
                    <option value="oficina" <?php selected( $tipo_inmueble, 'oficina' ); ?>>Oficina</option>
                    <option value="terreno" <?php selected( $tipo_inmueble, 'terreno' ); ?>>Terreno</option>
                    <option value="trastero" <?php selected( $tipo_inmueble, 'trastero' ); ?>>Trastero</option>
                    <option value="edificio" <?php selected( $tipo_inmueble, 'edificio' ); ?>>Edificio</option>
                    <option value="habitacion" <?php selected( $tipo_inmueble, 'habitacion' ); ?>>Habitación</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="localidad">Localidad*</label></th>
            <td><input type="text" name="localidad" id="localidad" value="<?php echo esc_attr( $localidad ); ?>"></td>
        </tr>
        <tr>
            <th><label for="nombre_calle">Nombre de la Calle*</label></th>
            <td><input type="text" name="nombre_calle" id="nombre_calle" value="<?php echo esc_attr( $nombre_calle ); ?>"></td>
        </tr>
        <tr>
            <th><label for="numero">Número*</label></th>
            <td>
                <input type="text" name="numero" id="numero" value="<?php echo esc_attr( $numero ); ?>">
                <input type="checkbox" name="numero_obligatorio" id="numero_obligatorio" <?php checked( true, get_post_meta( $post->ID, 'numero_obligatorio', true ) ); ?>>
                <label for="numero_obligatorio">Sin número</label>
            </td>
        </tr>
        <tr>
            <th><label for="descripcion"></label></th>
            <td><button type="button" id="validar_direccion">Validar Dirección</button></td>
            
        </tr>

        <div id="mapa"></div>
        <tr>
            <th><label for="descripcion">Descripcion*</label></th>
            <td><textarea name="descripcion" id="descripcion"><?php echo esc_textarea( $descripcion ); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="visibilidad_direccion">Visibilidad de la Dirección*</label></th>
            <td>
                <select name="visibilidad_direccion" id="visibilidad_direccion">
                    <option value="direccion_exacta" <?php selected( $visibilidad_direccion, 'direccion_exacta' ); ?>>Mostrar dirección exacta</option>
                    <option value="solo_calle" <?php selected( $visibilidad_direccion, 'solo_calle' ); ?>>Mostrar solo la calle sin número</option>
                    <option value="ocultar_direccion" <?php selected( $visibilidad_direccion, 'ocultar_direccion' ); ?>>Ocultar toda la dirección</option>
                </select>
            </td>
        </tr>
        <tr class="campo_planta">
            <th><label for="planta">Planta*</label></th>
            <td>
                <select name="planta" id="planta">
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
        <tr class="campo_bloque">
            <th><label for="bloque">Bloque</label></th>
            <td><input type="text" name="bloque" id="bloque" value="<?php echo esc_attr( $bloque ); ?>"></td>
        </tr>
        <tr class="campo_escalera">
            <th><label for="escalera">Puerta/Escalera</label></th>
            <td><input type="text" name="escalera" id="escalera" value="<?php echo esc_attr( $escalera ); ?>"></td>
        </tr>
        <tr class="campo_urbanizacion">
            <th><label for="urbanizacion">Urbanización, si aplica</label></th>
            <td><input type="text" name="urbanizacion" id="urbanizacion" value="<?php echo esc_attr( $urbanizacion ); ?>"></td>
        </tr>
        <tr>
        <tr class="campo_ascensor">
            <th><label for="ascensor">Ascensor*</label></th>
            <td>
                <label><input type="radio" name="ascensor" value="si">Sí</label>
                <label><input type="radio" name="ascensor" value="no"> No</label>
            </td>
        </tr>
        <tr>
            <th><label for="tipo_operacion">Tipo de Operación*</label></th>
            <td>
                <label><input type="radio" name="tipo_operacion" value="venta"> Venta</label>
                <label><input type="radio" name="tipo_operacion" value="alquiler"> Alquiler</label>
            </td>
        </tr>
        <tr class="campo_precio_venta">
            <th><label for="precio_venta">Precio Venta*</label></th>
            <td><input type="number" name="precio_venta" id="precio_venta"></td>
        </tr>
        <tr class="campo_gastos_comunidad">
            <th><label for="gastos_comunidad">Gastos de Comunidad</label></th>
            <td><input type="number" name="gastos_comunidad" id="gastos_comunidad"></td>
        </tr>
        <tr class="campo_precio_alquiler">
            <th><label for="precio_alquiler">Precio Alquiler Mensual*</label></th>
            <td><input type="number" name="precio_alquiler" id="precio_alquiler"></td>
        </tr>
        <tr class="campo_fianza">
            <th><label for="fianza">Fianza</label></th>
            <td>
                <select name="fianza" id="fianza">
                    <option value="">Seleccionar</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="mas">Más</option>
                </select>
            </td>
        </tr>
        <tr class="campo_caracteristica_piso">
            <th><label>Característica adicional</label></th>
            <td>
                <label><input type="radio" name="caracteristica_piso" value="atico" required>Ático</label>
                <label><input type="radio" name="caracteristica_piso" value="estudio" required>Estudio</label>
                <label><input type="radio" name="caracteristica_piso" value="duplex" required>Dúplex</label>
            </td>
        </tr>
        <tr class="campo_metros_construidos">
            <th><label for="metros_construidos">Metros Construidos*</label></th>
            <td><input type="number" name="metros_construidos" id="metros_construidos" value="<?php echo esc_attr($metros_construidos); ?>"></td>
        </tr>

        <tr class="campo_metros_utiles">
            <th><label for="metros_utiles">Metros Útiles*</label></th>
            <td><input type="number" name="metros_utiles" id="metros_utiles" value="<?php echo esc_attr($metros_utiles); ?>"></td>
        </tr>

        <tr class="campo_metros_parcela">
            <th><label for="metros_parcela">Metros de Parcela*</label></th>
            <td><input type="number" name="metros_parcela" id="metros_parcela"></td>
        </tr>
        
        <tr class="campo_metros_lineales">
            <th><label for="metros_lineales">Metros lineales de fachada*</label></th>
            <td><input type="number" name="metros_lineales" id="metros_lineales"></td>
        </tr>
        
        <tr class="campo_metros_plaza">
            <th><label for="metros_plaza">Superficie de la plaza</label></th>
            <td><input type="number" name="metros_plaza" id="metros_plaza"></td>
        </tr>
        
        <tr class="campo_tipologia_chalet">
            <th><label>Tipología</label></th>
            <td>
                <label><input type="radio" name="tipologia" value="adosado" required> Adosado</label>
                <label><input type="radio" name="tipologia" value="pareado" required> Pareado</label>
                <label><input type="radio" name="tipologia" value="independiente" required> Independiente</label>
            </td>
        </tr>

        <tr class="campo_tipo_casa_rustica">
            <th><label for="tipo_casa_rustica">Tipo de Casa Rústica</label></th>
            <td>
                <label><input type="radio" name="tipo_casa_rustica" value="finca"> Finca</label>
                <label><input type="radio" name="tipo_casa_rustica" value="castillo"> Castillo</label>
                <label><input type="radio" name="tipo_casa_rustica" value="casa_rural"> Casa Rural</label>
                <label><input type="radio" name="tipo_casa_rustica" value="casa_pueblo"> Casa de Pueblo</label>
                <label><input type="radio" name="tipo_casa_rustica" value="cortijo"> Cortijo</label>
            </td>
        </tr>

        <tr class="campo_num_plantas">
            <th><label for="num_plantas">Número de Plantas</label></th>
            <td><input type="number" name="num_plantas" id="num_plantas" required></td>
        </tr>
        
        <tr class="campo_num_escaparates">
            <th><label for="num_escaparates">Número de escaparates*</label></th>
            <td><input type="number" name="num_escaparates" id="num_escaparates" required></td>
        </tr>

        <tr class="campo_num_dormitorios">
            <th><label for="num_dormitorios">Nº de Dormitorios*</label></th>
            <td><input type="number" name="num_dormitorios" id="num_dormitorios" value="<?php echo esc_attr($num_dormitorios); ?>"></td>
        </tr>

        <tr class="campo_num_banos">
            <th><label for="num_banos">Nº de Baños*</label></th>
            <td><input type="number" name="num_banos" id="num_banos" value="<?php echo esc_attr($num_banos); ?>"></td>
        </tr>
       
        <tr class="campo_num_estancias">
            <th><label for="num_estancias">Nº de estancias</label></th>
            <td><input type="number" name="num_estancias" id="num_estancias" value="<?php echo esc_attr($num_estancias); ?>"></td>
        </tr>

        <tr class="campo_ubicacion_local">
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
        
        <tr class="campo_plaza">
            <th><label for="plaza">Capacida de la plaza</label></th>
            <td>
                <select name="plaza" id="plaza">
                    <option value="">Seleccionar</option>
                    <option value="coche_peq" <?php selected($plaza, 'coche_peq'); ?>>Coche pequeño</option>
                    <option value="coche_grande" <?php selected($$plaza, 'coche_grande'); ?>>Coche grande</option>
                    <option value="moto" <?php selected($$plaza, 'moto'); ?>>Moto</option>
                    <option value="coche_moto" <?php selected($$plaza, 'coche_moto'); ?>>Coche + Moto</option>
                    <option value="mas_coches" <?php selected($$plaza, 'mas_coches'); ?>>2 coches o más</option>
                </select>
            </td>      
        </tr>
        
        <tr>
            <th><label for="certificacion_energetica">Certificación Energética*</label></th>
            <td>
                <select name="certificacion_energetica" id="certificacion_energetica">
                    <option value="">Seleccionar</option>
                    <option value="a" <?php selected($certificacion_energetica, 'a'); ?>>A</option>
                    <option value="b" <?php selected($certificacion_energetica, 'b'); ?>>B</option>
                    <option value="c" <?php selected($certificacion_energetica, 'c'); ?>>C</option>
                    <option value="d" <?php selected($certificacion_energetica, 'd'); ?>>D</option>
                    <option value="e" <?php selected($certificacion_energetica, 'e'); ?>>E</option>
                    <option value="f" <?php selected($certificacion_energetica, 'f'); ?>>F</option>
                    <option value="g" <?php selected($certificacion_energetica, 'g'); ?>>G</option>
                    <option value="exento" <?php selected($certificacion_energetica, 'exento'); ?>>Exento</option>
                    <option value="tramite" <?php selected($certificacion_energetica, 'tramite'); ?>>En Trámite</option>
                </select>
            </td>
        </tr>

        <tr class="campo_consumo_energetico">
            <th><label for="consumo_energetico">Consumo Energético*</label></th>
            <td><input type="number" name="consumo_energetico" id="consumo_energetico" value="<?php echo esc_attr($consumo_energetico); ?>"></td>
        </tr>

        <tr>
            <th><label for="calificacion_emisiones">Calificación de Emisiones</label></th>
            <td>
                <select name="calificacion_emisiones" id="calificacion_emisiones">
                    <option value="">Seleccionar</option>
                    <option value="a" <?php selected($calificacion_emisiones, 'a'); ?>>A</option>
                    <option value="b" <?php selected($calificacion_emisiones, 'b'); ?>>B</option>
                    <option value="c" <?php selected($calificacion_emisiones, 'c'); ?>>C</option>
                    <option value="d" <?php selected($calificacion_emisiones, 'd'); ?>>D</option>
                    <option value="e" <?php selected($calificacion_emisiones, 'e'); ?>>E</option>
                    <option value="f" <?php selected($calificacion_emisiones, 'f'); ?>>F</option>
                    <option value="g" <?php selected($calificacion_emisiones, 'g'); ?>>G</option>
                </select>
            </td>
        </tr>

        <tr class="campo_emisiones">
            <th><label for="emisiones">Emisiones</label></th>
            <td><input type="number" name="emisiones" id="emisiones" value="<?php echo esc_attr($emisiones); ?>"></td>
        </tr>
        <tr>
            <th><label for="estado_conservacion">Estado de Conservación*</label></th>
            <td>
                <label><input type="radio" name="estado_conservacion" value="buen_estado" <?php checked($estado_conservacion, 'buen_estado'); ?>> Buen Estado</label>
                <label><input type="radio" name="estado_conservacion" value="a_reformar" <?php checked($estado_conservacion, 'a_reformar'); ?>> A Reformar</label>
            </td>
        </tr>
        <tr class="campo_interior_ext">
            <th><label for="interior_ext">Interior/ Exterior*</label></th>
            <td>
                <label><input type="radio" name="interior_ext" value="interior">Interior</label>
                <label><input type="radio" name="interior_ext" value="exterior">Exterior</label>
            </td>
        </tr>

        <tr class="orientacion">
            <th><label for="orientacion">Orientacion</label></th>
                <td>
                    <label><input type="checkbox" name="norte" id="norte" value="<?php echo esc_attr($norte); ?>">Norte</label>
                    <label><input type="checkbox" name="sur" id="sur" value="<?php echo esc_attr($sur); ?>">Sur</label>
                    <label><input type="checkbox" name="este" id="este" value="<?php echo esc_attr($este); ?>">Este</label>
                    <label><input type="checkbox" name="oeste" id="oeste" value="<?php echo esc_attr($oeste); ?>">Oeste</label>
                </td>
        </tr>
        
        <tr class="caracteristicas">
            <th><label for="caracteristicas">Otras caracteristicas</label></th>
            <td>
                <label><input type="checkbox" name="armario" id="armario" value="<?php echo esc_attr($armario); ?>">Armarios empotrados</label>
                <label><input type="checkbox" name="aire" id="aire" value="<?php echo esc_attr($aire); ?>">Aire Acondicionado</label>
                <label><input type="checkbox" name="calefaccion" id="calefaccion" value="<?php echo esc_attr($calefaccion); ?>">Calefacción</label>
                <label><input type="checkbox" name="terraza" id="terraza" value="<?php echo esc_attr($terraza); ?>">Terraza</label>
                <label><input type="checkbox" name="chimenea" id="chimenea" value="<?php echo esc_attr($chimenea); ?>">Chimenea</label>
                <label><input type="checkbox" name="balcon" id="balcon" value="<?php echo esc_attr($balcon); ?>">Balcón</label>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>    
                <label><input type="checkbox" name="garaje" id="garaje" value="<?php echo esc_attr($garaje); ?>">plaza de garaje</label>
                <label><input type="checkbox" name="trastero" id="trastero" value="<?php echo esc_attr($trastero); ?>">Trastero</label>
                <label><input type="checkbox" name="piscina" id="piscina" value="<?php echo esc_attr($piscina); ?>">Piscina</label>
                <label><input type="checkbox" name="jardin" id="jardin" value="<?php echo esc_attr($jardin); ?>">Jardín</label>
            </td>   
        </tr>
        <tr class="campos_caracteristicas_local">
            <th><label for="caracteristicas_local">Equipamiento</label></th>
            <td>    
                <label><input type="checkbox" name="humos" id="humos" value="<?php echo esc_attr($humos); ?>">Salida de humos</label>
                <label><input type="checkbox" name="cocina_equipada" id="jardin" value="<?php echo esc_attr($cocina_equipada); ?>">Cocina totalmente equipada</label>
                <label><input type="checkbox" name="puerta_seguridad" id="puerta_seguridad" value="<?php echo esc_attr($puerta_seguridad); ?>">Puerta de seguridad</label>
                <label><input type="checkbox" name="alarma" id="alarma" value="<?php echo esc_attr($alarma); ?>">Sistemas de alarma</label>
                <label><input type="checkbox" name="almacen" id="almacen" value="<?php echo esc_attr($almacen); ?>">Almacén</label>
            </td>
        </tr>
        
        <tr class="caracteristicas_garaje">
            <th><label for="caracteristicas_garaje">Características</label></th>
            <td>    
                <label><input type="checkbox" name="ascensor_garaje" id="ascensor_garaje" value="<?php echo esc_attr($ascensor_garaje); ?>">Ascensor</label>
                <label><input type="checkbox" name="persona_seguridad" id="persona_seguridad" value="<?php echo esc_attr($persona_seguridad); ?>">Personal de seguridad</label>
                <label><input type="checkbox" name="plaza_cubierta" id="plaza_cubierta" value="<?php echo esc_attr($plaza_cubierta); ?>">Plaza cubierta</label>
                <label><input type="checkbox" name="alarma" id="alarma" value="<?php echo esc_attr($alarma); ?>">Alarma</label>
                <label><input type="checkbox" name="puerta_auto" id="puerta_auto" value="<?php echo esc_attr($puerta_auto); ?>">Puerta automática</label>
            </td>
        </tr>



        <!-- Campo de Galería de Imágenes -->
        <tr>
            <th><label for="galeria_imagenes">Galería de Imágenes*</label></th>
            <td>
            <div id="galeria-imagenes-container" class="sortable-container ui-sortable">
                <?php if (!empty($galeria_imagenes)) : ?>
                    <?php foreach ($galeria_imagenes as $index => $imagen) : ?>
                        <div class="galeria-imagen">
                            <div class="galeria-imagen-inner">
                                <img src="<?php echo esc_url($imagen); ?>" alt="Imagen">
                                <button type="button" class="remove-imagen button-link">Eliminar</button>
                            </div>
                            <input type="hidden" name="galeria_imagenes[]" value="<?php echo esc_attr($imagen); ?>">
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
    if (isset($_POST['nombre'])) {
        update_post_meta($post_id, 'nombre', sanitize_text_field($_POST['nombre']));
    }
    if (isset($_POST['descripcion'])) {
        update_post_meta($post_id, 'descripcion', sanitize_text_field($_POST['descripcion']));
    }
    if (isset($_POST['precio'])) {
        update_post_meta($post_id, 'precio', sanitize_text_field($_POST['precio']));
    }
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
    if ( isset( $_POST['descripcion'] ) ) {
        update_post_meta( $post_id, 'descripcion', sanitize_textarea_field( $_POST['descripcion'] ) );
    }
    if ( isset( $_POST['visibilidad_direccion'] ) ) {
        update_post_meta( $post_id, 'visibilidad_direccion', sanitize_text_field( $_POST['visibilidad_direccion'] ) );
    }
    if ( isset( $_POST['jardin'] ) ) {
        update_post_meta( $post_id, 'jardin', sanitize_text_field( $_POST['jardin'] ) );
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
 * Verifica los campos obligatorios antes de guardar un inmueble.
 *
 * @param int $post_id ID del inmueble actual.
 */
function verificar_campos_obligatorios( $post_id ) {
    // Verificar si se está guardando un inmueble
    if ( isset( $_POST['publish'] ) && $_POST['publish'] ) {
        // Obtener los valores de los campos
        $tipo_inmueble = sanitize_text_field( $_POST['tipo_inmueble'] );
        $localidad = sanitize_text_field( $_POST['localidad'] );
        $planta = sanitize_text_field( $_POST['planta'] );
        $nombre_calle = sanitize_text_field( $_POST['nombre_calle'] );

        // Verificar si los campos obligatorios están vacíos
       // Verificar si el campo "Planta" debe ser obligatorio
       if ( $tipo_inmueble === 'piso' && empty( $planta ) ) {
        $error_message = 'El campo Planta es obligatorio para el tipo de inmueble seleccionado. Por favor, completa el formulario correctamente.';
        wp_die( $error_message, 'Error', [
            'back_link' => true,
            ] );
        }

        if ( $tipo_inmueble === 'casa_chalet' && empty( $jardin ) ) {
            $error_message = 'El campo jardin es obligatorio para el tipo de inmueble seleccionado. Por favor, completa el formulario correctamente.';
            wp_die( $error_message, 'Error', [
                'back_link' => true,
                ] );
            }
        
    }
}
add_action( 'pre_post_update', 'verificar_campos_obligatorios' );








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