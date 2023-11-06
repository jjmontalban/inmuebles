<?php 

 //array asociativo para mapear valores de los tipos de inmueble
global $tipos_inmueble_map;
global $zonas_inmueble_map;

$tipos_inmueble_map = array(
    'piso' => 'Piso',
    'casa_rustica' => 'Casa rústica',
    'apartamento' => 'Apartamento',
    'casa_chalet' => 'Chalet',
    'local' => 'Local',
    'garaje' => 'Garaje',
    'oficina' => 'Oficina',
    'terreno' => 'Terreno',
);

$zonas_inmueble_map = array(
    'centro' => 'Centro',
    'regla' => 'Regla',
    'cruz_mar' => 'Cruz del Mar',
    'faro' => 'Faro',
    'muelle' => 'Muelle',
    'garaje' => 'Garaje',
    'alcancia' => 'La Alcancía',
    'laguna' => 'La Laguna',
    'pinar' => 'Pinar',
);

/**
 * Obtiene  los campos personalizados del inmueble.
 * @param WP_Post $post El objeto de entrada actual.
 */
function obtener_campos_inmueble($post_id) {
    $campos = array(
        'tipo_inmueble', 'zona_inmueble', 'localidad', 'nombre_calle', 'numero', 
        'planta', 'bloque', 'escalera', 'urbanizacion', 
        'visibilidad_direccion', 'tipo_operacion', 'precio_venta', 
        'gastos_comunidad', 'precio_alquiler', 'fianza', 'calefaccion',
        'caract_inm', 'm_construidos','m_utiles','m_lineales','superf_terreno',
        'num_dormitorios','num_banos','num_escap','calif_consumo_energ',
        'consumo_energ','cal_emis','emisiones','tipo_local','tipo_terreno',
        'ac','estado_cons','interior_ext','ascensor',
        'descripcion','ano_edificio','acceso_rodado',
        'uso_excl','distribucion_oficina','aire_acond',
        'residencial_altura','residencial_unif',
        'terciario_ofi','terciario_com','terciario_hotel','industrial','dotaciones','otra',
        'm_parcela','m_fachada','tipologia_chalet','tipo_rustica','num_plantas',
        'num_estancias','ubicacion_local','tipo_plaza','m_plaza'
    );

    $valores = array();

    // Ahora, recorremos el array de campos y obtenemos su valor.
    foreach($campos as $campo) {
        $valores[$campo] = get_post_meta($post_id, $campo, true);
    }

    // Casos especiales para campos que podrían devolver arrays:
    $caract_inm_value = get_post_meta($post_id, 'caract_inm', true);
    $valores['caract_inm'] = is_array($caract_inm_value) ? $caract_inm_value : array();

    $valores['galeria_imagenes'] = is_array(get_post_meta($post_id, 'galeria_imagenes', true)) ? get_post_meta($post_id, 'galeria_imagenes', true) : array();
    $valores['orientacion'] = is_array(get_post_meta($post_id, 'orientacion', true)) ? get_post_meta($post_id, 'orientacion', true) : array();
    $valores['otra_caract_inm'] = is_array(get_post_meta($post_id, 'otra_caract_inm', true)) ? get_post_meta($post_id, 'otra_caract_inm', true) : array();
    $valores['caract_local'] = is_array(get_post_meta($post_id, 'caract_local', true)) ? get_post_meta($post_id, 'caract_local', true) : array();
    $valores['caract_garaje'] = is_array(get_post_meta($post_id, 'caract_garaje', true)) ? get_post_meta($post_id, 'caract_garaje', true) : array();
    $valores['tipo_calif_terreno'] = is_array(get_post_meta($post_id, 'tipo_calif_terreno', true)) ? get_post_meta($post_id, 'tipo_calif_terreno', true) : array();
    
    return $valores;
}


/**
 * Agrega el metabox "Datos del inmueble" al formulario de edición de inmuebles.
 */
function inmuebles_agregar_mb_campos_inmueble() {
    add_meta_box( 'inmueble_campos_inmueble', 
                  'Datos del inmueble', 
                  'mostrar_campos_inmueble', 
                  'inmueble', 
                  'normal', 
                  'high' );
}
add_action( 'add_meta_boxes', 'inmuebles_agregar_mb_campos_inmueble' );


/**
 * Agrega el metabox "Datos del propietario" al formulario de edición de inmuebles.
 */
function inmuebles_agregar_mb_campos_propietario_inmueble() {
    add_meta_box( 'inmueble_propietario', 
                  'Registrar nuevo Propietario para el inmueble (para seleccionar uno nuevo deseleccionar el propeitario del selector anterior)', 
                  'mostrar_campos_propietario', 
                  'inmueble',
                  'normal', 
                  'high' );
}
add_action('add_meta_boxes', 'inmuebles_agregar_mb_campos_propietario_inmueble');



/**
 * Muestra los campos del inmueble en el formulario de edición de inmuebles.
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_campos_inmueble( $post ) {  

     // Usamos nuestra función para obtener todos los valores del inmueble.
    $campos = obtener_campos_inmueble($post->ID);
    
    //relativo al propietario
    $selected = get_post_meta($post->ID, 'propietario_id', true);
    $propietarios = get_posts(array('post_type' => 'propietario', 'numberposts' => -1));


    ?>
    <table class="form-table">
        <tr>
            <th><label for="tipo_inmueble">Tipo de Inmueble*</label></th>
            <td>
                <select name="tipo_inmueble" id="tipo_inmueble" required>
                    <option value="">Seleccionar</option>
                    <option value="piso" <?php selected(  $campos['tipo_inmueble'] ?? '', 'piso' ); ?>>Piso</option>
                    <option value="casa_chalet" <?php selected( $campos['tipo_inmueble'] ?? '', 'casa_chalet' ); ?>>Casa / Chalet</option>
                    <option value="casa_rustica" <?php selected( $campos['tipo_inmueble'] ?? '', 'casa_rustica' ); ?>>Casa Rústica</option>
                    <option value="local" <?php selected( $campos['tipo_inmueble'] ?? '', 'local' ); ?>>Local o Nave</option>
                    <option value="garaje" <?php selected( $campos['tipo_inmueble'] ?? '', 'garaje' ); ?>>Garaje</option>
                    <option value="oficina" <?php selected( $campos['tipo_inmueble'] ?? '', 'oficina' ); ?>>Oficina</option>
                    <option value="terreno" <?php selected( $campos['tipo_inmueble'] ?? '', 'terreno' ); ?>>Terreno</option>
                </select>
            </td>
        </tr>   
        <tr>
            <th><label for="zona_inmueble">Zona del Inmueble</label></th>
            <td>
                <select name="zona_inmueble" id="zona_inmueble" required>
                    <option value="">Seleccionar</option>
                    <option value="piso" <?php selected(  $campos['zona_inmueble'] ?? '', 'centro' ); ?>>Centro</option>
                    <option value="casa_chalet" <?php selected( $campos['zona_inmueble'] ?? '', 'regla' ); ?>>Regla</option>
                    <option value="casa_rustica" <?php selected( $campos['zona_inmueble'] ?? '', 'cruz_mar' ); ?>>Curz del Mar</option>
                    <option value="local" <?php selected( $campos['zona_inmueble'] ?? '', 'faro' ); ?>>Faro</option>
                    <option value="garaje" <?php selected( $campos['zona_inmueble'] ?? '', 'muelle' ); ?>>Muelle</option>
                    <option value="oficina" <?php selected( $campos['zona_inmueble'] ?? '', 'alcancia' ); ?>>La Alcancía</option>
                    <option value="terreno" <?php selected( $campos['zona_inmueble'] ?? '', 'pinar' ); ?>>Pinar</option>
                    <option value="terreno" <?php selected( $campos['zona_inmueble'] ?? '', 'laguna' ); ?>>La Laguna</option>
                </select>
            </td>
        </tr>   
        <tr>
            <th><label for="localidad">Localidad*</label></th>
            <td><input type="text" name="localidad" id="localidad" value="<?php echo esc_attr( $campos['localidad'] ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="nombre_calle">Nombre de la Calle*</label></th>
            <td><input type="text" name="nombre_calle" id="nombre_calle" value="<?php echo esc_attr( $campos['nombre_calle'] ?? '' ); ?>" required></td>
        </tr>
        <tr>
            <th><label for="numero">Número*</label></th>
            <td>
                <input type="text" name="numero" id="numero" value="<?php echo esc_attr( $campos['numero'] ?? '' ); ?>" required>
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
                    <option value="sotano" <?php selected( $campos['planta'] ?? '', 'sotano' ); ?>>Sótano</option>
                    <option value="bajo" <?php selected( $campos['planta'] ?? '', 'bajo' ); ?>>Bajo</option>
                    <option value="1" <?php selected( $campos['planta'] ?? '', '1' ); ?>>1</option>
                    <option value="2" <?php selected( $campos['planta'] ?? '', '2' ); ?>>2</option>
                    <option value="3" <?php selected( $campos['planta'] ?? '', '3' ); ?>>3</option>
                    <option value="4" <?php selected( $campos['planta'] ?? '', '4' ); ?>>4</option>
                    <option value="5" <?php selected( $campos['planta'] ?? '', '5' ); ?>>5</option>
                    <option value="6" <?php selected( $campos['planta'] ?? '', '6' ); ?>>6</option>
                    <option value="7" <?php selected( $campos['planta'] ?? '', '7' ); ?>>7</option>
                    <option value="8" <?php selected( $campos['planta'] ?? '', '8' ); ?>>8</option>
                </select>
            </td>
        </tr>
        <tr id="campo_bloque">
            <th><label for="bloque">Bloque</label></th>
            <td><input type="text" name="bloque" id="bloque" value="<?php echo esc_attr( $campos['bloque'] ?? '' ); ?>" required></td>
        </tr>
        <tr id="campo_escalera">
            <th><label for="escalera">Puerta/Escalera</label></th>
            <td><input type="text" name="escalera" id="escalera" value="<?php echo esc_attr( $campos['escalera'] ?? '' ); ?>" required></td>
        </tr>
        <tr id="campo_urbanizacion">
            <th><label for="urbanizacion">Urbanización, si aplica</label></th>
            <td><input type="text" name="urbanizacion" id="urbanizacion" value="<?php echo esc_attr( $campos['urbanizacion'] ?? '' ); ?>" required></td>
        </tr>

        <tr>
            <th><label for="visibilidad_direccion">Visibilidad de la Dirección*</label></th>
            <td>
                <select name="visibilidad_direccion" id="visibilidad_direccion" required>
                    <option value="direccion_exacta" <?php selected( $campos['visibilidad_direccion'] ?? '', 'direccion_exacta' ); ?>>Mostrar dirección exacta</option>
                    <option value="solo_calle" <?php selected( $campos['visibilidad_direccion'] ?? '', 'solo_calle' ); ?>>Mostrar solo la calle sin número</option>
                    <option value="ocultar_direccion" <?php selected( $campos['visibilidad_direccion'] ?? '', 'ocultar_direccion' ); ?>>Ocultar toda la dirección</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Tipo de Operación*</th>
            <td>
                <label><input type="radio" name="tipo_operacion" value="venta" <?php checked($campos['tipo_operacion'] ?? '', 'venta'); ?> required>Venta</label>
                <label><input type="radio" name="tipo_operacion" value="alquiler" <?php checked($campos['tipo_operacion'] ?? '', 'alquiler'); ?> required>Alquiler</label>
            </td>
        </tr>
        <tr id="campo_precio_venta">
            <th><label for="precio_venta">Precio Venta*</label></th>
            <td><input type="number" name="precio_venta" id="precio_venta" value="<?php echo esc_attr( $campos['precio_venta'] ?? '' ); ?>" required></td>
        </tr>
        <tr id="campo_gastos_comunidad">
            <th><label for="gastos_comunidad">Gastos de Comunidad</label></th>
            <td><input type="number" name="gastos_comunidad" id="gastos_comunidad" value="<?php echo esc_attr( $campos['gastos_comunidad'] ?? '' ); ?>"></td>
        </tr>
        <tr id="campo_precio_alquiler">
            <th><label for="precio_alquiler">Precio Alquiler Mensual*</label></th>
            <td><input type="number" name="precio_alquiler" id="precio_alquiler" value="<?php echo esc_attr( $campos['precio_alquiler'] ?? '' ); ?>" required></td>
        </tr>
        <tr id="campo_fianza">
            <th><label for="fianza">Fianza</label></th>
            <td>
                <select name="fianza" id="fianza" required>
                    <option value="">Seleccionar</option>
                    <option value="1" <?php selected( $campos['fianza'] ?? '', '1' ); ?>>1 mes</option>
                    <option value="2" <?php selected( $campos['fianza'] ?? '', '2' ); ?>>2 mes</option>
                    <option value="3" <?php selected( $campos['fianza'] ?? '', '3' ); ?>>3 mes</option>
                    <option value="4" <?php selected( $campos['fianza'] ?? '', '4' ); ?>>4 mes</option>
                    <option value="5" <?php selected( $campos['fianza'] ?? '', '5' ); ?>>5 mes</option>
                    <option value="6" <?php selected( $campos['fianza'] ?? '', '6' ); ?>>6 mes</option>
                    <option value="mas" <?php selected( $campos['fianza'] ?? '', 'mas' ); ?>>Más</option>
                </select>
            </td>
        </tr>
        <tr id="campo_tipologia_chalet">
            <th>Tipo de Chalet*</th>
            <td>
                <label><input type="radio" name="tipologia_chalet" value="atico" <?php checked($campos['tipologia_chalet'] ?? '', 'atico'); ?> required>Adosado</label>
                <label><input type="radio" name="tipologia_chalet" value="estudio" <?php checked($campos['tipologia_chalet'] ?? '', 'estudio'); ?>>Pareado</label>
                <label><input type="radio" name="tipologia_chalet" value="duplex" <?php checked($campos['tipologia_chalet'] ?? '', 'duplex'); ?>>Independiente</label>
            </td>
        </tr>
        <tr id="campo_tipo_rustica">
            <th>Tipo de Casa Rústica*</th>
            <td>
                <label><input type="radio" name="tipo_rustica" value="finca" <?php checked($campos['tipo_rustica'] ?? '', 'finca'); ?> required>Finca</label>
                <label><input type="radio" name="tipo_rustica" value="castillo" <?php checked($campos['tipo_rustica'] ?? '', 'castillo'); ?>>Castillo</label>
                <label><input type="radio" name="tipo_rustica" value="casa_rural" <?php checked($campos['tipo_rustica'] ?? '', 'casa_rural'); ?>>Casa Rural</label>
                <label><input type="radio" name="tipo_rustica" value="casa_pueblo" <?php checked($campos['tipo_rustica'] ?? '', 'casa_pueblo'); ?>>Casa de Pueblo</label>
                <label><input type="radio" name="tipo_rustica" value="cortijo" <?php checked($campos['tipo_rustica'] ?? '', 'cortijo'); ?>>Cortijo</label>
            </td>
        </tr>
        <tr id="campo_tipo_local">
            <th>Tipo de local</th>
            <td>
                <label><input type="radio" name="tipo_local" value="local" <?php checked($campos['tipo_local'] ?? '', 'local'); ?> required>Local</label>
                <label><input type="radio" name="tipo_local" value="nave" <?php checked($campos['tipo_local'] ?? '', 'nave'); ?>>Nave</label>
            </td>
        </tr>
        <tr id="campo_tipo_terreno">
            <th>Tipo de terreno</th>
            <td>
                <label><input type="radio" name="tipo_terreno" value="urbano" <?php checked($campos['tipo_terreno'] ?? '', 'local'); ?> required>Urbano (solar)</label>
                <label><input type="radio" name="tipo_terreno" value="urbanizable" <?php checked($campos['tipo_terreno'] ?? '', 'nave'); ?>>Urbanizable</label>
                <label><input type="radio" name="tipo_terreno" value="urbanizable" <?php checked($campos['tipo_terreno'] ?? '', 'nave'); ?>>No urbanizable</label>
            </td>
        </tr>
        <tr id="campo_tipo_plaza">
            <th><label for="tipo_plaza">Tipo de plaza*</label></th>
            <td>
                <select name="tipo_plaza" id="tipo_plaza" required>
                    <option value="">Seleccionar</option>
                    <option value="coche_peq" <?php selected($campos['tipo_plaza'] ?? '', 'coche_peq'); ?>>Coche pequeño</option>
                    <option value="coche_grande" <?php selected($campos['tipo_plaza'] ?? '', 'coche_grande'); ?>>Coche grande</option>
                    <option value="moto" <?php selected($campos['tipo_plaza'] ?? '', 'moto'); ?>>Moto</option>
                    <option value="coche_moto" <?php selected($campos['tipo_plaza'] ?? '', 'coche_moto'); ?>>Coche + Moto</option>
                    <option value="mas_coches" <?php selected($campos['tipo_plaza'] ?? '', 'mas_coches'); ?>>2 coches o más</option>
                </select>
            </td>      
        </tr>
        <tr id="campo_m_plaza">
            <th><label for="m_plaza">Superficie de la plaza</label></th>
            <td><input type="number" name="m_plaza" id="m_plaza" value="<?php echo esc_attr($campos['m_plaza'] ?? '');?>" placeholder="m²"></td>
        </tr>
        <tr id="campo_caract_inm">
            <th>Característica adicional</th>
            <td>
                <label><input type="checkbox" name="caract_inm[]" id="atico" value="atico" <?php if (in_array('atico', $campos['caract_inm'] ?? array())) echo 'checked'; ?>>Ático</label>
                <label><input type="checkbox" name="caract_inm[]" id="estudio" value="estudio" <?php if (in_array('estudio', $campos['caract_inm'] ?? array())) echo 'checked'; ?>>Estudio</label>
                <label><input type="checkbox" name="caract_inm[]" id="duplex" value="duplex" <?php if (in_array('duplex', $campos['caract_inm'] ?? array())) echo 'checked'; ?>>Dúplex</label>
            </td>
        </tr>


        <tr id="campo_m_construidos">
            <th><label for="m_construidos">Metros Construidos*</label></th>
            <td><input type="number" name="m_construidos" id="m_construidos" value="<?php echo esc_attr($campos['m_construidos'] ?? '');?>" placeholder="m²" required></td>
        </tr>
        <tr id="campo_m_utiles">
            <th><label for="m_utiles">Metros Útiles</label></th>
            <td><input type="number" name="m_utiles" id="m_utiles" value="<?php echo esc_attr($campos['m_utiles'] ?? ''); ?>" placeholder="m²" required></td>
        </tr>
        <tr id="campo_m_parcela">
            <th><label for="m_parcela">Metros de Parcela</label></th>
            <td><input type="number" name="m_parcela" id="m_parcela" value="<?php echo esc_attr($campos['m_parcela'] ?? ''); ?>" placeholder="m²" required></td>
        </tr>
        <tr id="campo_m_lineales">
            <th><label for="m_lineales">Metros lineales de fachada*</label></th>
            <td><input type="number" name="m_lineales" id="m_lineales" value="<?php echo esc_attr($campos['m_lineales'] ?? ''); ?>" required></td>
        </tr>
        <tr id="campo_superf_terreno">
            <th><label for="superf_terreno">Superficie total</label></th>
            <td><input type="number" name="superf_terreno" id="superf_terreno" value="<?php echo esc_attr($campos['superf_terreno'] ?? ''); ?>" required></td>
        </tr>
        <tr id="campo_num_dormitorios">
            <th><label for="num_dormitorios">Nº de Dormitorios*</label></th>
            <td><input type="number" name="num_dormitorios" id="num_dormitorios" value="<?php echo esc_attr($campos['num_dormitorios'] ?? ''); ?>"></td>
        </tr>
        <tr id="campo_num_banos">
            <th><label for="num_banos">Nº de Baños*</label></th>
            <td><input type="number" name="num_banos" id="num_banos" value="<?php echo esc_attr($campos['num_banos'] ?? ''); ?>"></td>
        </tr>
        <tr id="campo_num_estancias">
            <th><label for="num_estancias">Nº de estancias</label></th>
            <td><input type="number" name="num_estancias" id="num_estancias" value="<?php echo esc_attr($campos['num_estancias'] ?? ''); ?>"></td>
        </tr>
        <tr id="campo_num_plantas">
            <th><label for="num_plantas">Número de plantas</label></th>
            <td><input type="number" name="num_plantas" id="num_plantas" value="<?php echo esc_attr($campos['num_plantas'] ?? ''); ?>"></td>
        </tr>
        <tr id="campo_num_escap">
            <th><label for="num_escap">Número de escaparates*</label></th>
            <td><input type="number" name="num_escap" id="num_escap" value="<?php echo esc_attr($campos['num_escap'] ?? ''); ?>"></td>
        </tr>
        <tr id="campo_num_ascensores">
            <th><label for="num_ascensores">Número de ascensores*</label></th>
            <td><input type="number" name="num_ascensores" id="num_ascensores" value="<?php echo esc_attr($campos['num_ascensores'] ?? ''); ?>"></td>
        </tr>
        <tr id="campo_num_plazas">
            <th><label for="num_plazas">Número de plazas de garaje*</label></th>
            <td><input type="number" name="num_plazas" id="num_plazas" value="<?php echo esc_attr($campos['num_plazas'] ?? ''); ?>"></td>
        </tr>
        <tr id="campo_calif_consumo_energ">
            <th><label for="calif_consumo_energ">Calificación de consumo de energía*</label></th>
            <td>
                <select name="calif_consumo_energ" id="calif_consumo_energ" required>
                    <option value="">Seleccionar</option>
                    <option value="a" <?php selected($campos['calif_consumo_energ'] ?? '', 'a'); ?>>A</option>
                    <option value="b" <?php selected($campos['calif_consumo_energ'] ?? '', 'b'); ?>>B</option>
                    <option value="c" <?php selected($campos['calif_consumo_energ'] ?? '', 'c'); ?>>C</option>
                    <option value="d" <?php selected($campos['calif_consumo_energ'] ?? '', 'd'); ?>>D</option>
                    <option value="e" <?php selected($campos['calif_consumo_energ'] ?? '', 'e'); ?>>E</option>
                    <option value="f" <?php selected($campos['calif_consumo_energ'] ?? '', 'f'); ?>>F</option>
                    <option value="g" <?php selected($campos['calif_consumo_energ'] ?? '', 'g'); ?>>G</option>
                    <option value="exento" <?php selected($campos['calif_consumo_energ'] ?? '', 'exento'); ?>>Exento</option>
                    <option value="tramite" <?php selected($campos['calif_consumo_energ'] ?? '', 'tramite'); ?>>En Trámite</option>
                </select>
            </td>
            <th><label for="consumo_energ">Consumo de energía</label></th>
            <td><input type="number" name="consumo_energ" id="consumo_energ" value="<?php echo esc_attr($campos['consumo_energ'] ?? ''); ?>" placeholder="kwh/m2 año"></td>
        </tr>
        <tr id="campo_cal_emis">
            <th><label for="cal_emis">Calificación de Emisiones*</label></th>
            <td>
                <select name="cal_emis" id="cal_emis" required>
                    <option value="">Seleccionar</option>
                    <option value="a" <?php selected($campos['cal_emis'] ?? '', 'a'); ?>>A</option>
                    <option value="b" <?php selected($campos['cal_emis'] ?? '', 'b'); ?>>B</option>
                    <option value="c" <?php selected($campos['cal_emis'] ?? '', 'c'); ?>>C</option>
                    <option value="d" <?php selected($campos['cal_emis'] ?? '', 'd'); ?>>D</option>
                    <option value="e" <?php selected($campos['cal_emis'] ?? '', 'e'); ?>>E</option>
                    <option value="f" <?php selected($campos['cal_emis'] ?? '', 'f'); ?>>F</option>
                    <option value="g" <?php selected($campos['cal_emis'] ?? '', 'g'); ?>>G</option>
                </select>
            </td>
            <th><label for="emisiones">Emisiones</label></th>
            <td><input type="number" name="emisiones" id="emisiones" value="<?php echo esc_attr($campos['emisiones'] ?? ''); ?>" placeholder="kg CO / m2 año"></td>
        </tr>        
        
        <tr id="campo_acceso_rodado">
            <th>Acceso rodado*</th>
            <td>
                <label><input type="radio" name="acceso_rodado" value="no_disponible" <?php checked($campos['acceso_rodado'] ?? '', 'no_disponible'); ?> >No disponible</label>
                <label><input type="radio" name="acceso_rodado" value="si_tiene" <?php checked($campos['acceso_rodado'] ?? '', 'si_tiene'); ?>>Sí, tiene</label>
            </td>
        </tr>
        <tr id="campo_si_rodado">
            <td>
                <select name="si_rodado" id="si_rodado">
                    <option value="">Seleccionar</option>
                    <option value="no" <?php selected( $campos['no'] ?? '', '1' ); ?>>No lo sé</option>
                    <option value="urbana" <?php selected( $campos['urbana'] ?? '', '1' ); ?>>Vía urbana</option>
                    <option value="carretera" <?php selected( $campos['carretera'] ?? '', '1' ); ?>>Por carretera</option>
                    <option value="tierra" <?php selected( $campos['tierra'] ?? '', '1' ); ?>>Camino de tierra</option>
                    <option value="autovia" <?php selected( $campos['autovia'] ?? '', '1' ); ?>>Por autovía</option>
                </select>
            </td>
        </tr>
        <tr id="campo_estado_cons">
            <th>Estado de Conservación*</th>
            <td>
                <label><input type="radio" name="estado_cons" value="buen_estado" <?php checked($campos['estado_cons'] ?? '', 'buen_estado'); ?> required> Buen Estado</label>
                <label><input type="radio" name="estado_cons" value="a_reformar" <?php checked($campos['estado_cons'] ?? '', 'a_reformar'); ?>> A Reformar</label>
            </td>
        </tr>
        <tr id="campo_int_ext">
            <th>Interior/ Exterior*</th>
            <td>
                <label><input type="radio" name="interior_ext" value="interior" <?php checked($campos['interior_ext'] ?? '', 'interior'); ?> required>Interior</label>
                <label><input type="radio" name="interior_ext" value="exterior" <?php checked($campos['interior_ext'] ?? '', 'exterior'); ?> >Exterior</label>
            </td>
        </tr>
        <tr id="campo_ascensor">
            <th>Ascensor*</th>
            <td>
                <label><input type="radio" name="ascensor" value="si" <?php checked($campos['ascensor'] ?? '', 'si'); ?> required>Sí</label>
                <label><input type="radio" name="ascensor" value="no" <?php checked($campos['ascensor'] ?? '', 'no'); ?>> No</label>
            </td>
        </tr>
        <tr id="campo_ac">
            <th>Aire acondicionado*</th>
            <td>
                <label><input type="radio" name="ac" value="no" <?php checked($campos['ac'] ?? '', 'no'); ?>>No disponible</label>
                <label><input type="radio" name="ac" value="frio" <?php checked($campos['ac'] ?? '', 'frio'); ?>>Frío</label>
                <label><input type="radio" name="ac" value="frio_calor" <?php checked($campos['ac'] ?? '', 'frio_calor'); ?>>Frío/calor</label>
                <label><input type="radio" name="ac" value="preinst" <?php checked($campos['ac'] ?? '', 'preinst'); ?>>Preinstalación</label>
            </td>
        </tr>
        <tr id="campo_uso_excl">
            <th>Uso exclusivo*</th>
            <td>
                <label><input type="radio" name="uso_excl" value="si" <?php checked($campos['uso_excl'] ?? '', 'si'); ?>>Sí</label>
                <label><input type="radio" name="uso_excl" value="no" <?php checked($campos['uso_excl'] ?? '', 'no'); ?>>No</label>
            </td>
        </tr>
        <tr id="campo_ubicacion_local">
            <th><label for="ubicacion_local">Ubicación</label></th>
            <td>
                <select name="ubicacion_local" id="ubicacion_local">
                    <option value="">Seleccionar</option>
                    <option value="pie_calle" <?php selected($campos['ubicacion_local'] ?? '', 'pie_calle'); ?>>A pie de calle</option>
                    <option value="centro_com" <?php selected($campos['ubicacion_local'] ?? '', 'centro_com'); ?>>Centro comercial</option>
                    <option value="entreplanta" <?php selected($campos['ubicacion_local'] ?? '', 'entreplanta'); ?>>Entreplanta</option>
                    <option value="subterraneo" <?php selected($campos['ubicacion_local'] ?? '', 'subterraneo'); ?>>Subterráneo</option>
                </select>
            </td>
        </tr>
        <tr id="campo_distribucion_oficina">
            <th><label for="distribucion">Distribución*</label></th>
            <td>
                <select name="distribucion" id="distribucion">
                    <option value="">Seleccionar</option>
                    <option value="diafana" <?php selected($campos['distribucion_oficina'] ?? '', 'diafana'); ?>>Diáfana</option>
                    <option value="mamparas" <?php selected($campos['distribucion_oficina'] ?? '', 'mamparas'); ?>>Dividida con mamparas</option>
                    <option value="tabiques" <?php selected($campos['distribucion_oficina'] ?? '', 'tabiques'); ?>>Dividida con tabiques</option>
                </select>
            </td>
        </tr>
        <tr id="campo_aire_acond">
            <th><label for="aire_acond">Aire acondicionado*</label></th>
            <td>
                <select name="aire_acond" id="aire_acond">
                    <option value="">Seleccionar</option>
                    <option value="no_disponible" <?php selected($campos['aire_acond'] ?? '', 'no_disponible'); ?>>No disponible</option>
                    <option value="frio" <?php selected($campos['aire_acond'] ?? '', 'frio'); ?>>Frío</option>
                    <option value="frio_calor" <?php selected($campos['aire_acond'] ?? '', 'frio_calor'); ?>>Frío/calor</option>
                    <option value="preinstalado" <?php selected($campos['aire_acond'] ?? '', 'preinstalado'); ?>>Preinstalación</option>
                </select>
            </td>
        </tr>
        <tr id="campo_orientacion">
            <th>Orientacion</th>
            <td>
                <label><input type="checkbox" name="orientacion[]" id="norte" value="norte" <?php if (in_array('norte', $campos['orientacion'] ?? array())) echo 'checked'; ?>>Norte</label>
                <label><input type="checkbox" name="orientacion[]" id="sur" value="sur" <?php if (in_array('sur', $campos['orientacion'] ?? array() )) echo 'checked'; ?>>Sur</label>
                <label><input type="checkbox" name="orientacion[]" id="este" value="este" <?php if (in_array('este', $campos['orientacion'] ?? array() )) echo 'checked'; ?>>Este</label>
                <label><input type="checkbox" name="orientacion[]" id="oeste" value="oeste" <?php if (in_array('oeste', $campos['orientacion'] ?? array() )) echo 'checked'; ?>>Oeste</label>
            </td>
        </tr>

        <tr id="campo_calificacion_terreno">
            <th>Tipo de calificación*</th>
                <td>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="residencial_altura" value="<?php echo esc_attr($campos['residencial_altura'] ?? ''); ?>">Residencial en altura</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="residencial_unif" value="<?php echo esc_attr($campos['residencial_unif'] ?? ''); ?>">Residencial unifamiliar</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="terciario_ofi" value="<?php echo esc_attr($campos['terciario_ofi'] ?? ''); ?>">Terciario oficinas</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="terciario_com" value="<?php echo esc_attr($campos['terciario_com'] ?? ''); ?>">Terciario comercial</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="terciario_hotel" value="<?php echo esc_attr($$campos['erciario_hotel'] ?? ''); ?>">Terciario hoteles</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="industrial" value="<?php echo esc_attr($campos['industrial'] ?? ''); ?>">Industrial</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="dotaciones" value="<?php echo esc_attr($campos['dotaciones'] ?? ''); ?>">Dotaciones</label>
                    <label><input type="checkbox" name="calificacion_terreno[]" id="otra" value="<?php echo esc_attr($campos['otra'] ?? ''); ?>">Otra</label>
                </td>
        </tr>
        <tr id="campo_otra_caract_inm">
            <th>Otras caracteristicas de la vivienda</th>
            <td>
                <label><input type="checkbox" name="otra_caract_inm[]" id="armario" value="armario" <?php if (in_array('armario', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >Armarios empotrados</label>
                <label><input type="checkbox" name="otra_caract_inm[]" id="aire" value="aire" <?php if (in_array('aire', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >Aire Acondicionado</label>
                <label><input type="checkbox" name="otra_caract_inm[]" id="terraza" value="terraza" <?php if (in_array('terraza', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >Terraza</label>
                <label><input type="checkbox" name="otra_caract_inm[]" id="balcon" value="balcon" <?php if (in_array('balcon', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >Balcón</label>
                <br><br>
                <label><input type="checkbox" name="otra_caract_inm[]" id="garaje" value="garaje" <?php if (in_array('garaje', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >plaza de garaje</label>
                <label><input type="checkbox" name="otra_caract_inm[]" id="chimenea" value="chimenea" <?php if (in_array('chimenea', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >Chimenea</label>
                <label><input type="checkbox" name="otra_caract_inm[]" id="trastero" value="trastero" <?php if (in_array('trastero', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >Trastero</label>
                <label><input type="checkbox" name="otra_caract_inm[]" id="piscina" value="piscina" <?php if (in_array('piscina', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >Piscina</label>
                <label><input type="checkbox" name="otra_caract_inm[]" id="jardin" value="jardin" <?php if (in_array('jardin', $campos['otra_caract_inm'] ?? array() )) echo 'checked'; ?> >Jardín</label>
            </td>   
        </tr>
        <tr id="campo_calefaccion">
            <th><label for="calefaccion">Tipo de calefacción</label></th>
            <td>
                <select name="calefaccion" id="calefaccion">
                    <option value="">Seleccionar</option>
                    <option value="individual" <?php selected($campos['calefaccion'] ?? '', 'individual'); ?>>Individual</option>
                    <option value="centralizada" <?php selected($campos['calefaccion'] ?? '', 'centralizada'); ?>>Centralizada</option>
                    <option value="no_dispone" <?php selected($campos['calefaccion'] ?? '', 'no_dispone'); ?>>No dispone</option>
                </select>
            </td>
        </tr>
        <tr id="campo_caract_local">
            <th>Equipamiento</th>
            <td>    
                <label><input type="checkbox" name="caract_local[]" id="calefaccion" value="calefaccion" <?php if (in_array('calefaccion', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Calefacción</label>
                <label><input type="checkbox" name="caract_local[]" id="humos" value="humos" <?php if (in_array('humos', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Salida de humos</label>
                <label><input type="checkbox" name="caract_local[]" id="cocina_equipada" value="cocina_equipada" <?php if (in_array('cocina_equipada', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Cocina totalmente equipada</label>
                <label><input type="checkbox" name="caract_local[]" id="puerta_seguridad" value="puerta_seguridad" <?php if (in_array('puerta_seguridad', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Puerta de seguridad</label>
                <label><input type="checkbox" name="caract_local[]" id="alarma" value="alarma" <?php if (in_array('alarma', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Sistema de alarma</label>
                <br><br>
                <label><input type="checkbox" name="caract_local[]" id="almacen" value="almacen" <?php if (in_array('almacen', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Almacén</label>
                <label><input type="checkbox" name="caract_local[]" id="circuito" value="circuito" <?php if (in_array('circuito', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Circuito cerrado de seguridad</label>
                <label><input type="checkbox" name="caract_local[]" id="esquina" value="esquina" <?php if (in_array('esquina', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Hace esquina</label>
                <label><input type="checkbox" name="caract_local[]" id="oficina" value="oficina" <?php if (in_array('oficina', $campos['caract_local'] ?? array() )) echo 'checked'; ?> >Tiene oficina</label>
            </td>
        </tr>
        <tr id="campo_caract_garaje">
            <th>Características del garaje</th>
            <td>    
                <label><input type="checkbox" name="caract_garaje[]" id="ascensor_garaje" value="ascensor_garaje" <?php if (in_array('ascensor_garaje', $campos['caract_garaje'] ?? array() )) echo 'checked'; ?> >Ascensor</label>
                <label><input type="checkbox" name="caract_garaje[]" id="persona_seguridad" value="persona_seguridad" <?php if (in_array('persona_seguridad', $campos['caract_garaje'] ?? array() )) echo 'checked'; ?>>Personal de seguridad</label>
                <label><input type="checkbox" name="caract_garaje[]" id="plaza_cubierta" value="plaza_cubierta" <?php if (in_array('plaza_cubierta', $campos['caract_garaje'] ?? array() )) echo 'checked'; ?>>Plaza cubierta</label>
                <label><input type="checkbox" name="caract_garaje[]" id="alarma_cerarada" value="alarma_cerarada" <?php if (in_array('alarma_cerarada', $campos['caract_garaje'] ?? array() )) echo 'checked'; ?>>Alarma con circuito cerrado</label>
                <label><input type="checkbox" name="caract_garaje[]" id="puerta_auto" value="puerta_auto" <?php if (in_array('puerta_auto', $campos['caract_garaje'] ?? array() )) echo 'checked'; ?>>Puerta automática</label>
            </td>
        </tr>
        <tr id="campo_tipo_calif_terreno">
            <th>Tipo de Calificación*</th>
            <td>    
                <label><input type="checkbox" name="tipo_calif_terreno[]" id="residencial_altura" value="residencial_altura" <?php if (in_array('residencial_altura', $campos['tipo_calif_terreno'] ?? array() )) echo 'checked'; ?>>Residencial en altura (bloques)</label>
                <label><input type="checkbox" name="tipo_calif_terreno[]" id="residencial_unifamiliar" value="residencial_unifamiliar" <?php if (in_array('residencial_unifamiliar', $campos['tipo_calif_terreno'] ?? array() )) echo 'checked'; ?>>Residencial unifamiliar (Chalets)</label>
                <label><input type="checkbox" name="tipo_calif_terreno[]" id="terciario_oficinas" value="terciario_oficinas" <?php if (in_array('terciario_oficinas', $campos['tipo_calif_terreno'] ?? array() )) echo 'checked'; ?>>Terciario oficinas</label>
                <label><input type="checkbox" name="tipo_calif_terreno[]" id="terciario_comercial" value="terciario_comercial" <?php if (in_array('terciario_comercial', $campos['tipo_calif_terreno'] ?? array() )) echo 'checked'; ?>>Terciario comercial</label>
                <label><input type="checkbox" name="tipo_calif_terreno[]" id="terciario_hoteles" value="terciario_hoteles" <?php if (in_array('terciario_hoteles', $campos['tipo_calif_terreno'] ?? array() )) echo 'checked'; ?>>Terciario hoteles</label>
                <label><input type="checkbox" name="tipo_calif_terreno[]" id="terciario_hoteles" value="industrial" <?php if (in_array('industrial', $campos['tipo_calif_terreno'] ?? array() )) echo 'checked'; ?>>Industrial</label>
                <label><input type="checkbox" name="tipo_calif_terreno[]" id="dotaciones" value="dotaciones" <?php if (in_array('dotaciones', $campos['tipo_calif_terreno'] ?? array() )) echo 'checked'; ?>>Dotaciones</label>
                <label><input type="checkbox" name="tipo_calif_terreno[]" id="otra" value="otra" <?php if (in_array('otra', $campos['tipo_calif_terreno'] ?? array() )) echo 'checked'; ?>>Otra</label>
            </td>
        </tr>
        <tr id="campo_ano_edificio">
            <th><label for="ano_edificio">Año de construcción del edificio</label></th>
            <td><input type="number" name="ano_edificio" id="ano_edificio" value="<?php echo esc_attr($campos['ano_edificio'] ?? ''); ?>" ></td>
        </tr>
        <tr>
            <th><label for="descripcion">Descripción de la propiedad</label></th>
            <td><textarea name="descripcion" id="descripcion"><?php echo esc_textarea( $campos['descripcion'] ?? '' ); ?></textarea></td>
        </tr>


        <!-- Campo de Galería de Imágenes -->
        <tr>
            <th>Galería de Imágenes</th>
            <td>
                <div id="galeria-imagenes-container" class="sortable-container ui-sortable">
                    <?php if (!empty($campos['galeria_imagenes'])) : ?>
                        <?php foreach ($campos['galeria_imagenes'] as $index => $imagen) : ?>
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
        
        

        <tr>
            <th>Propietario del Inmueble</th>
            <td>
                <select name="propietario_id" id="selector-propietario">
                    <option value="">Seleccionar</option>
                    <?php foreach ($propietarios as $propietario) :
                        $nombre = trim(get_post_meta($propietario->ID, 'nombre', true));
                        $apellidos = trim(get_post_meta($propietario->ID, 'apellidos', true));
                        $valor = ($nombre && $apellidos) ? $nombre . ' ' . $apellidos : $nombre . $apellidos;
                    ?>
                        <option value="<?php echo $propietario->ID; ?>" <?php selected($propietario->ID, $selected); ?>>
                            <?php echo $valor; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        

    </table>
   
    <?php
}


/**
 * Guarda los valores de los campos personalizados al guardar un inmueble.
 * @param int $post_id ID del inmueble actual.
 */
function inmuebles_guardar_campos_inmueble( $post_id ) {
    // Verificar si se está guardando un inmueble
    if ( get_post_type( $post_id ) !== 'inmueble' ) {
        return;
    }

    // Guardar los valores de los campos personalizados del inmueble
    if ( isset( $_POST['tipo_inmueble'] ) ) {
        update_post_meta( $post_id, 'tipo_inmueble', sanitize_text_field( $_POST['tipo_inmueble'] ) );
    }
    if ( isset( $_POST['zona_inmueble'] ) ) {
        update_post_meta( $post_id, 'zona_inmueble', sanitize_text_field( $_POST['zona_inmueble'] ) );
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
    if ( isset( $_POST['tipo_plaza'] ) ) {
        update_post_meta( $post_id, 'tipo_plaza', sanitize_text_field( $_POST['tipo_plaza'] ) );
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
    if (isset($_POST['distribucion_oficina'])) {
        update_post_meta($post_id, 'distribucion_oficina', sanitize_text_field($_POST['distribucion_oficina']));
    }
    if (isset($_POST['aire_acond'])) {
        update_post_meta($post_id, 'aire_acond', sanitize_text_field($_POST['aire_acond']));
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
    if (isset($_POST['superf_terreno'])) {
        update_post_meta($post_id, 'm_lineales', sanitize_text_field($_POST['m_lineales']));
    }
    if (isset($_POST['m_plaza'])) {
        update_post_meta($post_id, 'superf_terreno', sanitize_text_field($_POST['superf_terreno']));
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
    if (isset($_POST['m_parcela'])) {
        update_post_meta($post_id, 'm_parcela', sanitize_text_field($_POST['m_parcela']));
    }
    if (isset($_POST['m_fachada'])) {
        update_post_meta($post_id, 'm_fachada', sanitize_text_field($_POST['m_fachada']));
    }  
    if (isset($_POST['tipologia_chalet'])) {
        update_post_meta($post_id, 'tipologia_chalet', sanitize_text_field($_POST['tipologia_chalet']));
    }
    if (isset($_POST['tipo_local'])) {
        update_post_meta($post_id, 'tipo_local', sanitize_text_field($_POST['tipo_local']));
    }
    if (isset($_POST['tipo_terreno'])) {
        update_post_meta($post_id, 'tipo_terreno', sanitize_text_field($_POST['tipo_terreno']));
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
    //Arrays
    // Verificamos si se han enviado los checkboxes y si el valor enviado es un array
    if ( isset( $_POST['galeria_imagenes'] ) ) {
        // Sanitizamos los valores del array
        $galeria_imagenes = array_map( 'sanitize_text_field', $_POST['galeria_imagenes'] );
        // Actualizamos el campo personalizado 'galeria_imagenes' con el array de valores
        update_post_meta( $post_id, 'galeria_imagenes', $galeria_imagenes );
    } else {
        update_post_meta( $post_id, 'galeria_imagenes', array() );
    }
    if (isset($_POST['orientacion']) && is_array($_POST['orientacion'])) {
        $orientacion = array_map('sanitize_text_field', $_POST['orientacion']);   
        update_post_meta($post_id, 'orientacion', $orientacion);
    } else {
        update_post_meta($post_id, 'orientacion', array());
    }
    if (isset($_POST['caract_inm']) && is_array($_POST['caract_inm'])) {
        $caract_inm = array_map('sanitize_text_field', $_POST['caract_inm']);
        update_post_meta($post_id, 'caract_inm', $caract_inm);
    } else {
        update_post_meta($post_id, 'caract_inm', array());
    }
    if (isset($_POST['otra_caract_inm']) && is_array($_POST['otra_caract_inm'])) {
        $otra_caract_inm = array_map('sanitize_text_field', $_POST['otra_caract_inm']);
        update_post_meta($post_id, 'otra_caract_inm', $otra_caract_inm);
    } else {
        update_post_meta($post_id, 'otra_caract_inm', array());
    }
    if (isset($_POST['caract_local']) && is_array($_POST['caract_local'])) {
        $caract_local = array_map('sanitize_text_field', $_POST['caract_local']);
        update_post_meta($post_id, 'caract_local', $caract_local);
    } else {
        update_post_meta($post_id, 'caract_local', array());
    }
    if (isset($_POST['caract_garaje']) && is_array($_POST['caract_garaje'])) {
        $caract_garaje = array_map('sanitize_text_field', $_POST['caract_garaje']);
        update_post_meta($post_id, 'caract_garaje', $caract_garaje);
    } else {
        update_post_meta($post_id, 'caract_garaje', array());
    }

    //Relativo al propietario
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $apellidos = isset($_POST['apellidos']) ? $_POST['apellidos'] : '';
    $email = isset($_POST['email']) ? $_POST ['email'] : '';
    $telefono1 = isset($_POST['telefono1']) ? $_POST['telefono1'] : '';
    $telefono2 = isset($_POST['telefono2']) ? $_POST['telefono2'] : '';

    //Guardando un propietario desde la página de inmueble
    if( $nombre && $email && $telefono1 ){
        // Crear una nueva entrada de propietario
        $propietario_id = wp_insert_post(array(
            'post_type' => 'propietario',
            'post_status' => 'publish'
            ));
    
        if($propietario_id) {
            // Establecer los campos personalizados del propietario
            update_post_meta($propietario_id, 'nombre', $nombre);
            update_post_meta($propietario_id, 'apellidos', $apellidos);
            update_post_meta($propietario_id, 'email', $email);
            update_post_meta($propietario_id, 'telefono1', $telefono1);
            update_post_meta($propietario_id, 'telefono2', $telefono2);
    
            // Establecer este propietario al inmueble actual
            update_post_meta($post_id, 'propietario_id', $propietario_id);
        }
    }

    //Guardar el propietario seleccionado del dropdown en la meta del inmueble
    if(isset($_POST['propietario_id'])) {
        update_post_meta($post_id, 'propietario_id', $_POST['propietario_id']);
    }
    


}
add_action( 'save_post', 'inmuebles_guardar_campos_inmueble' );


/**
 * Guarda la taxonomía 'tipo de inmueble' cada vez que se guarde un inmueble cogiendo el valor del campo personalizado "tipo_inmueble"
 */
function asignar_tipo_inmueble_taxonomia($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
        return $post_id;
    
    // Verifica si el tipo de entrada es "inmueble"
    if ('inmueble' != get_post_type($post_id)) 
        return $post_id;
    
    // Obtiene el valor del campo personalizado "tipo_inmueble"
    $tipo_inmueble = get_post_meta($post_id, 'tipo_inmueble', true);
    
    // Actualiza los términos de taxonomía
    wp_set_post_terms($post_id, $tipo_inmueble, 'tipo_inmueble', false);
}
add_action('save_post', 'asignar_tipo_inmueble_taxonomia');



/**
 * Modificar el valor de la columna "title" en el listado de 'inmueble'
 */
function modificar_valor_columna_title($title, $post_id) {
    if (get_post_type($post_id) == 'inmueble') {
        $titulo_personalizado = get_post_meta($post_id, 'nombre_calle', true) . ' ' . get_post_meta($post_id, 'precio_venta', true);
        //valor personalizado en lugar del título original
        $title = $titulo_personalizado;
    }
    return $title;
}
add_filter('the_title', 'modificar_valor_columna_title', 10, 2);


/**
 * Cambiar texto editar por ver en el menu de acciones
 */
function modificar_texto_accion_inmueble($actions, $post) {
    if ($post->post_type === 'inmueble') {
        if (isset($actions['edit'])) {
            $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
        }
    }

    return $actions;
}
add_filter('post_row_actions', 'modificar_texto_accion_inmueble', 10, 2);


/**
 * Desactivar edicion rápida
 */
function desactivar_quick_edit_inmueble($actions, $post) {
    
    if ($post->post_type === 'inmueble') {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
add_filter('post_row_actions', 'desactivar_quick_edit_inmueble', 10, 2);
