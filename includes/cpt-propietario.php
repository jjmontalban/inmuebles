<?php 

/**
 * Registra el tipo de entrada personalizado 'propietario'.
 */
function registrar_cpt_propietario() {
    $labels = array(
        'name' => 'Propietario',
        'singular_name' => 'Propietario',
        'menu_name' => 'Propietarios',
        'name_admin_bar' => 'Propietario',
        'add_new' => 'Añadir Propietario',
        'add_new_item' => 'Añadir Nuevo Propietario',
        'new_item' => 'Nuevo Propietario',
        'edit_item' => 'Editar Propietario',
        'view_item' => 'Ver Propietario',
        'all_items' => 'Todos los Propietarios',
        'search_items' => 'Buscar Propietarios',
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true, //solo desde el admin
        'has_archive' => true,
        'rewrite' => array( 'slug' => 'propietario' ),
        'menu_icon' => 'dashicons-admin-users',
        'supports' => array( '' ),
    );
    
    register_post_type('propietario', $args);
}
add_action('init', 'registrar_cpt_propietario');
