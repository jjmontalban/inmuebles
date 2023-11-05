<?php

function registrar_cpt_cita() {
    $labels = array(
        'name'               => 'Citas',
        'singular_name'      => 'Cita',
        'menu_name'          => 'Citas',
        'add_new'            => 'Añadir Nueva',
        'add_new_item'       => 'Añadir Nueva Cita',
        'edit_item'          => 'Editar Cita',
        'new_item'           => 'Nueva Cita',
        'view_item'          => 'Ver Cita',
        'search_items'       => 'Buscar Citas',
        'not_found'          => 'No se encontraron citas',
        'not_found_in_trash' => 'No se encontraron citas en la papelera',
        'parent_item_colon'  => 'Cita Padre:',
        'menu_name'          => 'Citas',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => false, // No es visible en el frontend
        'publicly_queryable'  => false, // No se puede consultar en el frontend
        'show_ui'             => true, // Mostrar en el área de administración
        'query_var'           => true,
        'rewrite'             => array('slug' => 'cita'), // Slug del CPT
        'capability_type'     => 'post',
        'has_archive'         => false, // No tiene archivo de entradas
        'hierarchical'        => false,
        'menu_position'       => null,
        'supports'            => array(''), // Campos personalizados necesarios
    );

    register_post_type('cita', $args);
}

add_action('init', 'registrar_cpt_cita');
