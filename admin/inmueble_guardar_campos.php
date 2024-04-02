<?php

/**
 * Guarda los valores de los campos personalizados al guardar un inmueble.
 * @param int $post_id ID del inmueble actual.
 */
function guardar_campos_inmueble( $post_id ) {
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
    // Comprueba si el checkbox 'numero_obligatorio' está marcado
    $numero_obligatorio = isset($_POST['numero_obligatorio']) ? '1' : '0';
    update_post_meta($post_id, 'numero_obligatorio', $numero_obligatorio);

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
    if (isset($_POST['calif_terreno'])) {
        update_post_meta($post_id, 'calif_terreno', sanitize_text_field($_POST['calif_terreno']));
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
    if (isset($_POST['num_ascensores'])) {
        update_post_meta($post_id, 'num_ascensores', sanitize_text_field($_POST['num_ascensores']));
    }
    if (isset($_POST['m_utiles'])) {
        update_post_meta($post_id, 'm_utiles', sanitize_text_field($_POST['m_utiles']));
    }
    if (isset($_POST['m_lineales'])) {
        update_post_meta($post_id, 'm_lineales', sanitize_text_field($_POST['m_lineales']));
    }
    if (isset($_POST['superf_terreno'])) {
        update_post_meta($post_id, 'superf_terreno', sanitize_text_field($_POST['superf_terreno']));
    }
    if (isset($_POST['m_plaza'])) {
        update_post_meta($post_id, 'm_plaza', sanitize_text_field($_POST['m_plaza']));
    }
    if (isset($_POST['calif_consumo'])) {
        update_post_meta($post_id, 'calif_consumo', sanitize_text_field($_POST['calif_consumo']));
    }
    if (isset($_POST['consumo'])) {
        update_post_meta($post_id, 'consumo', sanitize_text_field($_POST['consumo']));
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
    if (isset($_POST['calif_emis'])) {
        update_post_meta($post_id, 'calif_emis', sanitize_text_field($_POST['calif_emis']));
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
    if (isset($_POST['num_plazas'])) {
        update_post_meta($post_id, 'num_plazas', sanitize_text_field($_POST['num_plazas']));
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
    //video
    // Manejar la carga del vídeo si se ha subido uno nuevo
    if (!empty($_FILES['video_embed']['name'])) {
        $file = $_FILES['video_embed'];
        $upload_overrides = array('test_form' => false);
        $uploaded_file = wp_handle_upload($file, $upload_overrides);
        
        if (!isset($uploaded_file['error'])) {
            // Archivo subido correctamente, guardamos la URL del vídeo
            $video_url = $uploaded_file['url'];
            update_post_meta($post_id, 'video_embed', $video_url);
        } else {
            // Ocurrió un error al subir el archivo, puedes manejarlo aquí
            // Por ejemplo, mostrar un mensaje de error o registrar el error en algún lugar
            error_log('Error al subir el vídeo: ' . $uploaded_file['error']);
        }
    }
    //campo mapa
    if (isset($_POST['campo_mapa'])) {
        update_post_meta($post_id, 'campo_mapa', sanitize_text_field($_POST['campo_mapa']));
    }
    //Campos plano
    if (isset($_FILES['plano1'])) {
        $uploadedfile = $_FILES['plano1'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if ($movefile && !isset($movefile['error'])) {
            update_post_meta($post_id, 'plano1', $movefile['url']);
        } else {
            echo $movefile['error'];
        }
    }
    if (isset($_FILES['plano2'])) {
        $uploadedfile = $_FILES['plano2'];
    
        $upload_overrides = array('test_form' => false);
    
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if ($movefile && !isset($movefile['error'])) {
            update_post_meta($post_id, 'plano2', $movefile['url']);
        } else {
            echo $movefile['error'];
        }
    }
    if (isset($_FILES['plano3'])) {
        $uploadedfile = $_FILES['plano3'];
    
        $upload_overrides = array('test_form' => false);
    
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if ($movefile && !isset($movefile['error'])) {
            update_post_meta($post_id, 'plano3', $movefile['url']);
        } else {
            echo $movefile['error'];
        }
    }
    if (isset($_FILES['plano4'])) {
        $uploadedfile = $_FILES['plano4'];
    
        $upload_overrides = array('test_form' => false);
    
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
        if ($movefile && !isset($movefile['error'])) {
            update_post_meta($post_id, 'plano4', $movefile['url']);
        } else {
            echo $movefile['error'];
        }
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
    // Obtén el propietario previamente asignado
    $propietario_previo = get_post_meta($post_id, 'propietario_id', true);

    // Si se seleccionó un propietario
    if (isset($_POST['propietario_id'])) {
        $propietario_id = $_POST['propietario_id'];

        // Actualizar el propietario del inmueble
        update_post_meta($post_id, 'propietario_id', $propietario_id);

        // Obtener los inmuebles asignados al propietario
        $inmuebles_asignados = get_post_meta($propietario_id, 'inmuebles_asignados', true);
        $inmuebles_asignados = is_array($inmuebles_asignados) ? $inmuebles_asignados : array();

        // Añadir el inmueble a la lista de inmuebles asignados del propietario
        if (!in_array($post_id, $inmuebles_asignados)) {
            $inmuebles_asignados[] = $post_id;
            update_post_meta($propietario_id, 'inmuebles_asignados', $inmuebles_asignados);
        }
    }

    // Si se desasignó el propietario
    if ($propietario_previo && (!isset($_POST['propietario_id']) || $_POST['propietario_id'] != $propietario_previo)) {
        // Obtener los inmuebles asignados al propietario previo
        $inmuebles_asignados = get_post_meta($propietario_previo, 'inmuebles_asignados', true);
        $inmuebles_asignados = is_array($inmuebles_asignados) ? $inmuebles_asignados : array();

        // Eliminar el inmueble de la lista de inmuebles asignados del propietario previo
        $index = array_search($post_id, $inmuebles_asignados);
        if ($index !== false) {
            unset($inmuebles_asignados[$index]);
            update_post_meta($propietario_previo, 'inmuebles_asignados', $inmuebles_asignados);
        }
    }
}