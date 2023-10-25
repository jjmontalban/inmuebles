<?php

/**
 * Registra el tipo de entrada personalizado 'inmueble'.
 */
function inmuebles_registrar_cpt_inmueble() {
    $labels = array(
        'name' => 'Inmueble',
        'singular_name' => 'Inmueble',
        'menu_name' => 'Inmuebles',
        'name_admin_bar' => 'Inmueble',
        'add_new' => 'Añadir Inmueble',
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
        'rewrite' => array( 'slug' => 'inmueble' ),
        'menu_icon' => 'dashicons-admin-multisite',
        'supports' => array( 'thumbnail' ),
    );

    register_post_type( 'inmueble', $args );
}
add_action( 'init', 'inmuebles_registrar_cpt_inmueble' );


/**
 * Registra la taxonomía 'tipo de inmueble'.
 */
function registrar_taxonomia_tipo_inmueble() {
    $labels = array(
        'name' => 'Tipo de Inmueble',
        'singular_name' => 'Tipo de Inmueble',
        'search_items' => 'Buscar Tipos de Inmuebles',
        'all_items' => 'Todos los Tipos de Inmuebles',
        'parent_item' => 'Tipo de Inmueble Padre',
        'parent_item_colon' => 'Tipo de Inmueble Padre:',
        'edit_item' => 'Editar Tipo de Inmueble',
        'update_item' => 'Actualizar Tipo de Inmueble',
        'add_new_item' => 'Agregar Nuevo Tipo de Inmueble',
        'new_item_name' => 'Nuevo Nombre de Tipo de Inmueble',
        'menu_name' => 'Tipos de Inmuebles',
    );

    $args = array(
        'hierarchical' => false,
        'labels' => $labels,
        'public' => true,
        'show_ui' => false,
        'show_in_quick_edit' => false,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'tipo-inmueble'), // Personaliza la URL como desees
    );

    register_taxonomy('tipo_inmueble', 'inmueble', $args);
}
add_action('init', 'registrar_taxonomia_tipo_inmueble');




