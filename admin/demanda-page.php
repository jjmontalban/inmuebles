<?php 
/**
 * Modificar el listado de la página de demandas
 */
function agregar_columnas_demandas($columns) {
    return array(
        'nombre' => 'Nombre',
        'telefono' => 'Teléfono',
        'email' => 'Email',
        'inmueble_interesado' => 'Inmueble por el que se interesó',
    );
}
add_filter('manage_demandas_posts_columns', 'agregar_columnas_demandas');

function llenar_columnas_demandas($column, $post_id) {
    switch ($column) {
        case 'nombre':
            echo get_post_meta($post_id, 'nombre', true);
            break;
        case 'telefono':
            echo get_post_meta($post_id, 'telefono', true);
            break;
        case 'email':
            echo get_post_meta($post_id, 'email', true);
            break;
        case 'inmueble_interesado':
            echo get_post_meta($post_id, 'inmueble_interesado', true);
            break;
    }
}
add_action('manage_demandas_posts_custom_column', 'llenar_columnas_demandas', 10, 2);


/**
 * Agrega el metabox "Datos de la demanda" al formulario de edición de propietarios.
 */
function demanda_meta_box() {
    add_meta_box('demanda_info', 
                 'Información de la demanda', 
                 'mostrar_campos_demanda', 
                 'demanda',
                 'normal',
                 'high' );
}
add_action('add_meta_boxes', 'demanda_meta_box');


/**
 * Muestra los campos de la demanda en el formulario de edición.
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_campos_demanda( $post ) {  
    $nombre = get_post_meta($post->ID, 'nombre', true);
    $email = get_post_meta($post->ID, 'email', true);
    $telefono = get_post_meta($post->ID, 'telefono', true);
    $email = get_post_meta($post->ID, 'email', true);
    $inmueble_interesado = get_post_meta($post->ID, 'inmueble_interesado', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="nombre">Nombre*</label></th>
            <td><input type="text" name="nombre" id="nombre" value="<?php echo esc_attr( $nombre ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="email">Email</label></th>
            <td><input type="text" name="email" id="email" value="<?php echo esc_attr( $email ?? ''); ?>" ></td>
        </tr>
        <tr>
            <th><label for="telefono">Teléfono*</label></th>
            <td><input type="text" name="telefono" id="telefono" value="<?php echo esc_attr( $telefono ?? ''); ?>" required></td>
        </tr>
        <tr>
            <th><label for="inmueble_interesado">Inmueble por el que se interesó</label></th>
            <td>
                <a href="<?php echo esc_url( get_permalink( $inmueble_interesado ) ); ?>">
                <?php echo esc_html( get_the_title( $inmueble_interesado ) ); ?></a>
            </td>
        </tr>
        <tr>
            <th><label for="notas">Notas</label></th>
            <td><input type="text" name="notas" id="notas" value="<?php echo esc_attr( $notas ?? ''); ?>"></td>
        </tr>
    </table>
    <?php 
}

/**
 * Guarda los valores de los campos personalizados al guardar una demanda.
 * @param int $post_id ID de la demanda actual.
 */
function inmuebles_guardar_campos_demanda( $post_id ) {
    if (array_key_exists('nombre', $_POST)) {
        update_post_meta($post_id, 'nombre', sanitize_text_field($_POST['nombre']));
    }
    if (array_key_exists('email', $_POST)) {
        update_post_meta($post_id, 'email', sanitize_text_field($_POST['email']));
    }
    if (array_key_exists('telefono', $_POST)) {
        update_post_meta($post_id, 'telefono', sanitize_text_field($_POST['telefono']));
    }
    if (array_key_exists('notas', $_POST)) {
        update_post_meta($post_id, 'notas', sanitize_text_field($_POST['notas']));
    }
}
add_action('save_post', 'inmuebles_guardar_campos_demanda');



/**
 * Cambiar texto editar por ver en el menu de acciones
 */
function chipicasa_modificar_texto_accion($actions, $post) {
    if ($post->post_type === 'demanda') {
        if (isset($actions['edit'])) {
            $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
        }
    }
        
    return $actions;
}
add_filter('post_row_actions', 'chipicasa_modificar_texto_accion', 10, 2);