<?php 

function crear_cpt_demanda() {

    $labels = array(
        'name'                  => _x('Demandas', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Demanda', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Demandas', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Demanda', 'Add New on Toolbar', 'textdomain'),
        'edit_item'             => __('Ver Demanda', 'textdomain'), // Cambio aquí
    );

    $args = array(
        'labels'             => $labels, // Agregamos las etiquetas definidas anteriormente
        'public' => false, //no accesible desde el front
        'show_ui' => true, //solo desde el admin
        'show_in_menu' => true,
        'supports' => array('')
    );
    register_post_type('demanda', $args);
}
add_action('init', 'crear_cpt_demanda');