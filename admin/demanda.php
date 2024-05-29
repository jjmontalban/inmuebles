<?php

class Demanda
{
    public function __construct()
    {
        add_action('init', [$this, 'crear_cpt_demanda']);
        add_action('add_meta_boxes', [$this, 'demanda_meta_box']);
        add_action('save_post', [$this, 'inmuebles_guardar_campos_demanda']);
        add_filter('post_row_actions', [$this, 'modificar_texto_accion_demanda'], 10, 2);
        add_filter('post_row_actions', [$this, 'desactivar_quick_edit_demanda'], 10, 2);
        add_filter('manage_demanda_posts_columns', [$this, 'agregar_columnas_demanda']);
        add_action('manage_demanda_posts_custom_column', [$this, 'mostrar_datos_columnas_demanda'], 10, 2);  
    }

    public function crear_cpt_demanda() {
        $labels = array(
            'name' => 'Demandas',
            'singular_name' => 'Demanda',
            'add_new' => 'Añadir Demanda',
            'add_new_item' => 'Añadir Nueva Demanda',
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

    /**
     * Agrega el metabox "Datos de la demanda" al formulario de edición de propietarios.
     */
    public function demanda_meta_box() {
        add_meta_box('demanda_info', 
        'Información de la demanda', 
        array($this, 'mostrar_campos_demanda'),
        'demanda',
        'normal',
        'high' );
    }

    /**
     * Muestra los campos de la demanda en el formulario de edición.
     * @param WP_Post $post El objeto de entrada actual.
     */
    public function mostrar_campos_demanda( $post ) {

        global $tipos_inmueble_map, $zonas_inmueble_map;
        
        $nombre = get_post_meta($post->ID, 'nombre', true);
        $email = get_post_meta($post->ID, 'email', true);
        $telefono = get_post_meta($post->ID, 'telefono', true);
        $dni = get_post_meta($post->ID, 'dni', true);
        $operacion = get_post_meta($post->ID, 'operacion', true);
        $tipo_inmueble = unserialize(get_post_meta($post->ID, 'tipo_inmueble', true));
        $zona_deseada = unserialize(get_post_meta($post->ID, 'zona_deseada', true));
        $presupuesto = get_post_meta($post->ID, 'presupuesto', true);
        $inmueble_interesado = get_post_meta($post->ID, 'inmueble_interesado', true);
        $notas = get_post_meta($post->ID, 'notas', true);
        $notas_str = '';
        if (is_array($notas)) {
            foreach ($notas as $nota) {
                $notas_str .= '(' . $nota['fecha'] . '), Nota: ' . $nota['nota'] . "\n";
            }
        } else {
            $notas_str = '(' . date('Y-m-d H:i:s') . '), Nota: ' . $notas;
        }
        // Obtiene la lista de inmuebles para el select
        $args = array(
            'post_type' => 'inmueble',
            'posts_per_page' => -1,
        );
        $inmuebles = get_posts($args);
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
                <th><label for="telefono">Teléfono</label></th>
                <td><input type="text" name="telefono" id="telefono" value="<?php echo esc_attr( $telefono ?? ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="dni">DNI</label></th>
                <td><input type="text" name="dni" id="dni" value="<?php echo esc_attr( $dni ?? ''); ?>"></td>
            </tr>

            <tr>
                <th><label for="operacion">Operación</label></th>
                <td>
                <select name="operacion" id="operacion">
                    <option value="">Selecciona una operación</option>
                    <option value="alquiler" <?php echo ($operacion == 'alquiler') ? 'selected' : ''; ?>>Alquiler</option>
                    <option value="compra" <?php echo ($operacion == 'compra') ? 'selected' : ''; ?>>Compra</option>
                </select>
                </td>
            </tr>

            <tr>
                <th><label for="tipo_inmueble">Tipo de inmueble</label></th>
                <td>
                    <select name="tipo_inmueble[]" id="tipo_inmueble" multiple size="<?php echo count($tipos_inmueble_map); ?>">
                        <?php
                        if (is_array($tipos_inmueble_map)) {
                            foreach ($tipos_inmueble_map as $key => $value) {
                                $selected = (is_array($tipo_inmueble) && in_array($key, $tipo_inmueble)) ? 'selected' : '';
                                echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th><label for="zona_deseada">Zona deseada</label></th>
                <td>
                    <select name="zona_deseada[]" id="zona_deseada" multiple size="<?php echo count($zonas_inmueble_map); ?>">
                        <?php
                        if (is_array($zonas_inmueble_map)) {
                            foreach ($zonas_inmueble_map as $key => $value) {
                                $selected = (is_array($zona_deseada) && in_array($key, $zona_deseada)) ? 'selected' : '';
                                echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($value) . '</option>';
                            }
                        }
                        ?>
                    </select>
            </tr>
            
            <tr>
                <th><label for="presupuesto">Presupuesto</label></th>
                <td><input type="text" name="presupuesto" id="presupuesto" value="<?php echo esc_attr( $presupuesto ?? ''); ?>"></td>
            </tr>
            <tr>
                <th><label for="inmueble_interesado">Inmueble interesado</label></th>
                <td>
                    <select name="inmueble_interesado" id="inmueble_interesado">
                        <option value="">Selecciona un inmueble</option>
                        <?php foreach ($inmuebles as $inmueble) : ?>
                            <?php
                            $tipo_inmueble = get_post_meta($inmueble->ID, 'tipo_inmueble', true);
                            $nombre_calle = get_post_meta($inmueble->ID, 'nombre_calle', true);
                            $inmueble_id = $inmueble->ID;
                            $selected = ($inmueble_id == $inmueble_interesado) ? 'selected' : '';
                            if (array_key_exists($tipo_inmueble, $tipos_inmueble_map)) {
                                $tipo_inmueble = $tipos_inmueble_map[$tipo_inmueble];
                            }
                            ?>
                            <option value="<?php echo esc_attr($inmueble_id); ?>" <?php echo esc_attr($selected); ?>>
                                <?php echo esc_html($tipo_inmueble . ' en ' . $nombre_calle); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
    <th><label for="notas">Notas</label></th>
    <td>
        <?php 
        $notas_arr = explode("\n", $notas_str);
        foreach ($notas_arr as $nota) {
            if (!empty(trim($nota))) {
                echo '<p><input type="text" name="notas[]" value="' . esc_attr( $nota ) . '" readonly style="width: 100%;"></p>';
            }
        }
        ?>
        <p><input type="text" name="nueva_nota" id="nueva_nota" placeholder="Nueva nota" style="width: 100%;"></p>
    </td>
</tr>
        </table>
        <?php 
    }

    /**
     * Guarda los valores de los campos personalizados al guardar una demanda.
     * @param int $post_id ID de la demanda actual.
     */
    public function inmuebles_guardar_campos_demanda( $post_id ) {
        if (array_key_exists('nombre', $_POST)) {
            update_post_meta($post_id, 'nombre', sanitize_text_field($_POST['nombre']));
        }
        if (array_key_exists('email', $_POST)) {
            update_post_meta($post_id, 'email', sanitize_text_field($_POST['email']));
        }
        if (array_key_exists('telefono', $_POST)) {
            update_post_meta($post_id, 'telefono', sanitize_text_field($_POST['telefono']));
        }
        if (array_key_exists('dni', $_POST)) {
            update_post_meta($post_id, 'dni', sanitize_text_field($_POST['dni']));
        }
        if (array_key_exists('zona_deseada', $_POST)) {
            update_post_meta($post_id, 'zona_deseada', serialize($_POST['zona_deseada']));
        }        
        if (array_key_exists('operacion', $_POST)) {
            update_post_meta($post_id, 'operacion', sanitize_text_field($_POST['operacion']));
        }
        if (array_key_exists('tipo_inmueble', $_POST)) {
            update_post_meta($post_id, 'tipo_inmueble', serialize($_POST['tipo_inmueble']));
        }        
        if (array_key_exists('presupuesto', $_POST)) {
            update_post_meta($post_id, 'presupuesto', sanitize_text_field($_POST['presupuesto']));
        }
        if (array_key_exists('nueva_nota', $_POST)) {
            $nueva_nota = sanitize_text_field($_POST['nueva_nota']);
            // Verificar si la nueva nota no está vacía
            if (!empty($nueva_nota)) {
                $notas = get_post_meta($post_id, 'notas', true);
                if (!is_array($notas)) {
                    $notas = array(array('nota' => $notas, 'fecha' => date('Y-m-d H:i:s')));
                }
                $notas[] = array('nota' => $nueva_nota, 'fecha' => date('Y-m-d H:i:s'));
                update_post_meta($post_id, 'notas', $notas);
            }
        }

        if (array_key_exists('inmueble_interesado', $_POST)) {
            update_post_meta($post_id, 'inmueble_interesado', sanitize_text_field($_POST['inmueble_interesado']));
        }
    }
    
    /**
     * Cambiar texto editar por ver en el menu de acciones
     */
    public function modificar_texto_accion_demanda($actions, $post) {
        if ($post->post_type === 'demanda') {
            if (isset($actions['edit'])) {
                $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
            }
        }
    
        return $actions;
    }

    /**
     * Desactivar edicion rápida
     */
    public function desactivar_quick_edit_demanda($actions, $post) {
        if ($post->post_type === 'demanda') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    /**
     * Agregar columnas personalizadas a la lista de entradas de demandas
     * @param int $columns
     */
    public function agregar_columnas_demanda($columns) {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'nombre' => 'Nombre',
            'telefono' => 'Teléfono',
            'email' => 'Email',
            'inmueble_interesado' => 'Inmueble por el que se interesó',
            'date' => 'Fecha de publicación',
        );
        return $columns;
    }

    public function mostrar_datos_columnas_demanda($column, $post_id) {

        global $tipos_inmueble_map;
        
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
                $inmueble_interesado = get_post_meta($post_id, 'inmueble_interesado', true);
                if (is_numeric($inmueble_interesado)) {
                    $tipo_inmueble = get_post_meta($inmueble_interesado, 'tipo_inmueble', true);
                    $nombre_calle = get_post_meta($inmueble_interesado, 'nombre_calle', true);
                    if (array_key_exists($tipo_inmueble, $tipos_inmueble_map)) {
                        $tipo_inmueble = $tipos_inmueble_map[$tipo_inmueble];
                    }
                    echo esc_html($tipo_inmueble . ' en ' . $nombre_calle);
                } else {
                    echo esc_html($inmueble_interesado);
                }
                break;
            default:
                break;
        }
    }
    
}

new Demanda();

//Amplia la busqueda en campos personalizados de demandas 
add_filter('posts_search', 'buscar_en_campos_demanda', 10, 2);
function buscar_en_campos_demanda($search, $wp_query) {
    global $wpdb;
    if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
        // Obtener términos de búsqueda
        $terms = $wp_query->query_vars['search_terms'];
        // Campos meta que deseas buscar
        $meta_keys = array('nombre', 'email', 'telefono');
        // Inicializar la cláusula de búsqueda de campos meta
        $meta_search = '';
        foreach ($meta_keys as $meta_key) {
            // Agregar cláusula para cada campo meta
            $meta_search .= " OR EXISTS (
                SELECT * FROM {$wpdb->postmeta} 
                WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id 
                AND {$wpdb->postmeta}.meta_key = '{$meta_key}' 
                AND {$wpdb->postmeta}.meta_value LIKE '%" . implode("%' OR {$wpdb->postmeta}.meta_value LIKE '%", $terms) . "%'
            )";
        }
        // Agregar la cláusula de búsqueda de campo meta a la consulta principal
        $search .= $meta_search;
    }
    return $search;
}

/**
 * Valida
 */
function validar_datos_demanda($post_ID, $data) {
    if ('demanda' !== $data['post_type'] || (isset($_GET['action']) && $_GET['action'] === 'trash')) return;

    $dni = isset($_POST['dni']) ? sanitize_text_field($_POST['dni']) : '';
    $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : '';
    $telefono = isset($_POST['telefono']) ? sanitize_text_field($_POST['telefono']) : '';

    $meta_query = array('relation' => 'OR');

    if (!empty($dni) && $dni != get_post_meta($post_ID, 'dni', true)) {
        $meta_query[] = array(
            'key' => 'dni',
            'value' => $dni,
            'compare' => '='
        );
    }

    if (!empty($email) && $email != get_post_meta($post_ID, 'email', true)) {
        $meta_query[] = array(
            'key' => 'email',
            'value' => $email,
            'compare' => '='
        );
    }

    if (!empty($telefono) && $telefono != get_post_meta($post_ID, 'telefono', true)) {
        $meta_query[] = array(
            'key' => 'telefono',
            'value' => $telefono,
            'compare' => '='
        );
    }

    if(count($meta_query) > 1){
        $exists_demanda = new WP_Query(array(
            'post_type' => 'demanda',
            'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
            'post__not_in' => array($post_ID), // excluye el post que estás editando
            'meta_query' => $meta_query,
        ));

        if ($exists_demanda->have_posts()) {
            wp_die('Ya existe una demanda con el mismo DNI, teléfono o email. <br> <a href="javascript:history.back()">Volver</a>', 'Error', array('response' => 400));
        }
    }
}
add_action('pre_post_update', 'validar_datos_demanda', 10, 2);

