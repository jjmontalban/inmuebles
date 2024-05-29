<?php


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
        'rewrite' => array( 'slug' => 'inmueble' ),
        'template' => get_template_directory() . '/single-inmueble.php', // Asigna la plantilla personalizada aquí
    );

    register_taxonomy( 'inmuebles', 'post', $args );
}
add_action( 'init', 'inmuebles_registrar_taxonomia_inmuebles' );