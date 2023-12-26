<?php

function registrar_cpt_cita() {
    $labels = array(
        'name' => 'Citas',
        'singular_name' => 'Cita',
        'menu_name' => 'Citas',
        'add_new' => 'Añadir Cita',
        'edit_item' => 'Editar Cita',
        'all_items' => 'Todas las Citas',
        'new_item' => 'Nueva Cita',
        'view_item' => 'Ver Cita',
        'search_items' => 'Buscar Citas',
        'not_found' => 'No se encontraron citas',
        'not_found_in_trash' => 'No se encontraron citas en la papelera',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'query_var'           => true,
        'capability_type'     => 'post',
        'supports'            => array(''),
    );

    register_post_type('cita', $args);
}

add_action('init', 'registrar_cpt_cita');
