<?php 

require_once 'inmueble.php';

/**
 * Muestra los campos del inmueble en el formulario de edición de inmuebles.
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_campos_inmueble( $post ) {  

    global $tipos_inmueble_map;
    global $zonas_inmueble_map;
    //Usamos nuestra función para obtener todos los valores del inmueble.
    $campos = obtener_campos_inmueble($post->ID);
    
    //Relativo al propietario
    $selected = get_post_meta($post->ID, 'propietario_id', true);
    $propietarios = get_posts(array(
        'post_type' => 'propietario',
        'numberposts' => -1,
        'meta_query' => array(
            array(
                'key' => 'propietario_id',
                'compare' => 'NOT EXISTS' // Esto excluye los inmuebles que ya tienen un propietario asignado
            )
        )
    ));
    
    ?>

    <table class="form-table">
    <tr>
            <th><label for="tipo_inmueble">Tipo de Inmueble*</label></th>
            <td>
                <select name="tipo_inmueble" id="tipo_inmueble" required>
                    <option value="">Seleccionar</option>
                    <?php foreach ($tipos_inmueble_map as $key => $value): ?>
                        <option value="<?php echo $key; ?>" <?php selected($campos['tipo_inmueble'] ?? '', $key); ?>><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>   
        <tr>
            <th><label for="zona_inmueble">Zona del Inmueble</label></th>
            <td>
                <select name="zona_inmueble" id="zona_inmueble" required>
                    <option value="">Seleccionar</option>
                    <?php foreach ($zonas_inmueble_map as $key => $value): ?>
                        <option value="<?php echo $key; ?>" <?php selected($campos['zona_inmueble'] ?? '', $key); ?>><?php echo $value; ?></option>
                    <?php endforeach; ?>
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
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button class="btn btn-primary" type="button" id="validar_direccion">Validar Dirección</button>
            </td>
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
                <label><input type="radio" name="tipologia_chalet" value="adosado" <?php checked($campos['tipologia_chalet'] ?? '', 'adosado'); ?> required>Adosado</label>
                <label><input type="radio" name="tipologia_chalet" value="pareado" <?php checked($campos['tipologia_chalet'] ?? '', 'pareado'); ?>>Pareado</label>
                <label><input type="radio" name="tipologia_chalet" value="independiente" <?php checked($campos['tipologia_chalet'] ?? '', 'independiente'); ?>>Independiente</label>
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
            <th>Tipo de local*</th>
            <td>
                <label><input type="radio" name="tipo_local" value="local" <?php checked($campos['tipo_local'] ?? '', 'local'); ?> required>Local</label>
                <label><input type="radio" name="tipo_local" value="nave" <?php checked($campos['tipo_local'] ?? '', 'nave'); ?>>Nave</label>
            </td>
        </tr>
        <tr id="campo_tipo_terreno">
            <th>Tipo de terreno</th>
            <td>
                <label><input type="radio" name="tipo_terreno" value="urbano" <?php checked($campos['tipo_terreno'] ?? '', 'urbano'); ?> required>Urbano (solar)</label>
                <label><input type="radio" name="tipo_terreno" value="urbanizable" <?php checked($campos['tipo_terreno'] ?? '', 'urbanizable'); ?>>Urbanizable</label>
                <label><input type="radio" name="tipo_terreno" value="no_urbanizable" <?php checked($campos['tipo_terreno'] ?? '', 'no_urbanizable'); ?>>No urbanizable</label>
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
            <th><label for="superf_terreno">Superficie total*</label></th>
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
        <tr id="campo_calif_consumo">
            <th><label for="calif_consumo">Calificación de consumo de energía*</label></th>
            <td>
                <select name="calif_consumo" id="calif_consumo" required>
                    <option value="">Seleccionar</option>
                    <option value="a" <?php selected($campos['calif_consumo'] ?? '', 'a'); ?>>A</option>
                    <option value="b" <?php selected($campos['calif_consumo'] ?? '', 'b'); ?>>B</option>
                    <option value="c" <?php selected($campos['calif_consumo'] ?? '', 'c'); ?>>C</option>
                    <option value="d" <?php selected($campos['calif_consumo'] ?? '', 'd'); ?>>D</option>
                    <option value="e" <?php selected($campos['calif_consumo'] ?? '', 'e'); ?>>E</option>
                    <option value="f" <?php selected($campos['calif_consumo'] ?? '', 'f'); ?>>F</option>
                    <option value="g" <?php selected($campos['calif_consumo'] ?? '', 'g'); ?>>G</option>
                    <option value="exento" <?php selected($campos['calif_consumo'] ?? '', 'exento'); ?>>Exento</option>
                    <option value="tramite" <?php selected($campos['calif_consumo'] ?? '', 'tramite'); ?>>En Trámite</option>
                </select>
                <br>
            </td>
        </tr>
        <tr id="campo_consumo">
            <th><label for="consumo">Consumo de energía</label></th>
            <td><input type="text" name="consumo" id="consumo" value="<?php echo esc_attr($campos['consumo'] ?? ''); ?>" placeholder="kwh/m2 año"></td>
        </tr>
        <tr id="campo_calif_emis">
            <th><label for="calif_emis">Calificación de Emisiones*</label></th>
            <td>
                <select name="calif_emis" id="calif_emis" required>
                    <option value="">Seleccionar</option>
                    <option value="a" <?php selected($campos['calif_emis'] ?? '', 'a'); ?>>A</option>
                    <option value="b" <?php selected($campos['calif_emis'] ?? '', 'b'); ?>>B</option>
                    <option value="c" <?php selected($campos['calif_emis'] ?? '', 'c'); ?>>C</option>
                    <option value="d" <?php selected($campos['calif_emis'] ?? '', 'd'); ?>>D</option>
                    <option value="e" <?php selected($campos['calif_emis'] ?? '', 'e'); ?>>E</option>
                    <option value="f" <?php selected($campos['calif_emis'] ?? '', 'f'); ?>>F</option>
                    <option value="g" <?php selected($campos['calif_emis'] ?? '', 'g'); ?>>G</option>
                    <option value="exento" <?php selected($campos['calif_emis'] ?? '', 'exento'); ?>>Exento</option>
                    <option value="tramite" <?php selected($campos['calif_emis'] ?? '', 'tramite'); ?>>En trámite</option>
                </select>
            </td>
        </tr>      
        <tr id="campo_emisiones">
            <th><label for="emisiones">Emisiones</label></th>
            <td><input type="text" name="emisiones" id="emisiones" value="<?php echo esc_attr($campos['emisiones'] ?? ''); ?>" placeholder="kg CO / m2 año"></td>
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
        <!-- <tr id="campo_ac">
            <th>Aire acondicionado*</th>
            <td>
                <label><input type="radio" name="ac" value="no" <?php checked($campos['ac'] ?? '', 'no'); ?>>No disponible</label>
                <label><input type="radio" name="ac" value="frio" <?php checked($campos['ac'] ?? '', 'frio'); ?>>Frío</label>
                <label><input type="radio" name="ac" value="frio_calor" <?php checked($campos['ac'] ?? '', 'frio_calor'); ?>>Frío/calor</label>
                <label><input type="radio" name="ac" value="preinst" <?php checked($campos['ac'] ?? '', 'preinst'); ?>>Preinstalación</label>
            </td>
        </tr> -->
        <tr id="campo_uso_excl">
            <th>Uso exclusivo*</th>
            <td>
                <label><input type="radio" name="uso_excl" value="si" <?php checked($campos['uso_excl'] ?? '', 'si'); ?>>Sí</label>
                <label><input type="radio" name="uso_excl" value="no" <?php checked($campos['uso_excl'] ?? '', 'no'); ?>>No</label>
            </td>
        </tr>
        <tr id="campo_ubicacion_local">
            <th><label for="ubicacion_local">Ubicación*</label></th>
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
            <th><label for="distribucion_oficina">Distribución*</label></th>
            <td>
                <select name="distribucion_oficina" id="distribucion_oficina">
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

        <tr id="campo_calif_terreno">
            <th><label for="calif_terreno">Tipo de calificación*</label></th>
            <td>
                <?php
                // Asegurarse de que $campos['calif_terreno'] sea un array
                $calif_terreno_values = isset($campos['calif_terreno']) ? (array)$campos['calif_terreno'] : array();
                ?>
                <label><input type="checkbox" name="calif_terreno[]" value="residencial_altura" <?php if (in_array('residencial_altura', $calif_terreno_values)) echo 'checked'; ?> >Residencial en altura</label>
                <label><input type="checkbox" name="calif_terreno[]" value="residencial_unif" <?php if (in_array('residencial_unif', $calif_terreno_values)) echo 'checked'; ?> >Residencial unifamiliar</label>
                <label><input type="checkbox" name="calif_terreno[]" value="terciario_ofi" <?php if (in_array('terciario_ofi', $calif_terreno_values)) echo 'checked'; ?> >Terciario oficinas</label>
                <label><input type="checkbox" name="calif_terreno[]" value="terciario_com" <?php if (in_array('terciario_com', $calif_terreno_values)) echo 'checked'; ?> >Terciario comercial</label>
                <label><input type="checkbox" name="calif_terreno[]" value="terciario_hotel" <?php if (in_array('terciario_hotel', $calif_terreno_values)) echo 'checked'; ?> >Terciario hoteles</label>
                <label><input type="checkbox" name="calif_terreno[]" value="industrial" <?php if (in_array('industrial', $calif_terreno_values)) echo 'checked'; ?> >Industrial</label>
                <label><input type="checkbox" name="calif_terreno[]" value="dotaciones" <?php if (in_array('dotaciones', $calif_terreno_values)) echo 'checked'; ?> >Dotaciones</label>
                <label><input type="checkbox" name="calif_terreno[]" value="otra" <?php if (in_array('otra', $calif_terreno_values)) echo 'checked'; ?> >Otra</label>
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
                <label><input type="checkbox" name="caract_garaje[]" id="alarma_cerrada" value="alarma_cerrada" <?php if (in_array('alarma_cerrada', $campos['caract_garaje'] ?? array() )) echo 'checked'; ?>>Alarma con circuito cerrado</label>
                <label><input type="checkbox" name="caract_garaje[]" id="puerta_auto" value="puerta_auto" <?php if (in_array('puerta_auto', $campos['caract_garaje'] ?? array() )) echo 'checked'; ?>>Puerta automática</label>
            </td>
        </tr>
        <tr id="campo_ano_edificio">
            <th><label for="ano_edificio">Año de construcción del edificio</label></th>
            <td><input type="number" name="ano_edificio" id="ano_edificio" value="<?php echo esc_attr($campos['ano_edificio'] ?? ''); ?>" ></td>
        </tr>
        <tr>
            <th><label for="descripcion">Descripción de la propiedad</label></th>
            <td><textarea name="descripcion" id="descripcion" rows="10" cols="100"><?php echo esc_textarea( $campos['descripcion'] ?? '' ); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="plano">Plano</label></th>
            <td>
                <input type="file" name="plano1" id="plano">
                <?php
                $plano1_url = get_post_meta($post->ID, 'plano1', true);
                if ($plano1_url) {
                    echo '<img src="' . esc_url($plano1_url) . '" width="150px">';
                    echo '<p>Ya existe un plano para este inmueble. Si seleccionas un nuevo archivo, reemplazará el plano existente.</p>';
                }
                ?>
                <br>
                <input type="file" name="plano2" id="plano">
                <?php
                $plano2_url = get_post_meta($post->ID, 'plano2', true);
                if ($plano2_url) {
                    echo '<img src="' . esc_url($plano2_url) . '" width="150px">';
                    echo '<p>Ya existe un plano para este inmueble. Si seleccionas un nuevo archivo, reemplazará el plano existente.</p>';
                }
                ?>
                <br>
                <input type="file" name="plano3" id="plano">
                <?php
                $plano3_url = get_post_meta($post->ID, 'plano3', true);
                if ($plano3_url) {
                    echo '<img src="' . esc_url($plano3_url) . '" width="150px">';
                    echo '<p>Ya existe un plano para este inmueble. Si seleccionas un nuevo archivo, reemplazará el plano existente.</p>';
                }
                ?>
                <br>
                <input type="file" name="plano4" id="plano">
                <?php
                $plano4_url = get_post_meta($post->ID, 'plano4', true);
                if ($plano4_url) {
                    echo '<img src="' . esc_url($plano4_url) . '" width="150px">';
                    echo '<p>Ya existe un plano para este inmueble. Si seleccionas un nuevo archivo, reemplazará el plano existente.</p>';
                }
                ?>
            </td>
        </tr>
        <!-- Campo de Galería de Imágenes -->
        <tr>
            <th>Galería de Imágenes</th>
            <td>
                <ul id="sortable" class="sortable-container">
                    <?php if (!empty($campos['galeria_imagenes'])) : ?>
                        <?php foreach ($campos['galeria_imagenes'] as $index => $imagen) : ?>
                            <li class="ui-state-default" data-index="<?php echo esc_attr($index); ?>">
                                <img src="<?php echo esc_url($imagen); ?>" alt="Imagen">
                                <button type="button" class="remove-imagen button-link">Eliminar</button>
                                <input type="hidden" name="galeria_imagenes[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($imagen); ?>">
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <button type="button" class="agregar-imagen button button-primary">Agregar Imagen</button>
                <input type="hidden" id="orden-imagenes" name="orden_imagenes" value="">
            </td>
        </tr>
        <tr>
            <th><label for="video_embed">Vídeo</label></th>
            <td>
                <input type="file" name="video_embed" id="video_embed">
                <?php
                $video_embed_url = esc_attr($campos['video_embed']);
                if (!empty($video_embed_url)) {
                    echo '<p><strong>Vídeo actual:</strong> <a href="' . $video_embed_url . '">' . basename($video_embed_url) . '</a></p>';
                }
                ?>
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
        <tr>
            <th>Visitas:</th>
            <td>
                <span id="visitas"><?php echo esc_html(get_post_meta($post->ID, 'visitas', true) ?? ''); ?></span>
            </td>
        </tr>


    </table>
    
    <?php
}