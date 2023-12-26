<?php 

function crear_cpt_demanda() {

    $labels = array(
        'name' => 'Demandas',
        'singular_name' => 'Demanda',
        'add_new' => 'Añadir Demanda',
        'menu_name' => 'Demandas',
        'name_admin_bar' => 'Demanda',
        'edit_item' => 'Ver Demanda',
        'all_items' => 'Todas las Demandas',
        'search_items' => 'Buscar Demanda',
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'supports' => array('')
    );
    register_post_type('demanda', $args);
}
add_action('init', 'crear_cpt_demanda');