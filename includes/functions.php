<?php

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
        
        if( !isset( $tipo_inmueble ) ) {
            $error_message = 'El campo tipode inmueble es obligatorio. Por favor, completa el formulario correctamente.';
            wp_die( $error_message, 'Error', [
                'back_link' => true,
                ] );
        }

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
//add_action( 'pre_post_update', 'verificar_campos_obligatorios' );