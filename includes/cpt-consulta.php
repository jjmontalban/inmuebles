<?php 

/**
 * Registra el tipo de entrada personalizado 'consulta'.
 */
function crear_cpt_consulta() {
    $labels = array(
        'name'                  => _x('Consultas', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Consulta', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Consultas', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Consulta', 'Add New on Toolbar', 'textdomain'),
        'edit_item'             => __('Ver consulta', 'textdomain'), 
    
    );

    $args = array(
        'labels'             => $labels, // Agregamos las etiquetas definidas anteriormente
        'public'             => false,
        'show_in_menu'       => true,
        'show_ui' => true,
        'supports'           => array('')
    );
    register_post_type('consulta', $args);
}
add_action('init', 'crear_cpt_consulta');

