<?php

class Demanda
{
    public function __construct()
    {
        add_action('init', [$this, 'crear_cpt_demanda']);
        add_action('add_meta_boxes_demanda', [$this, 'demanda_meta_box']); // Usamos add_meta_boxes_demanda
        add_action('add_meta_boxes_demanda', [$this, 'cruces_meta_box']); // Usamos add_meta_boxes_demanda
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
            'search_items' => 'Buscar por nombre, email o teléfono',
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
        $num_hab = get_post_meta($post->ID, 'num_hab', true);
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
                </td>
            </tr>
            
            <tr>
                <th><label for="presupuesto">Presupuesto</label></th>
                <td><input type="text" name="presupuesto" id="presupuesto" value="<?php echo esc_attr( $presupuesto ?? ''); ?>"></td>
            </tr>
            
            <tr>
                <th><label for="num_hab">Nº de habitaciones</label></th>
                <td><input type="number" name="num_hab" id="num_hab" value="<?php echo esc_attr( $num_hab ?? ''); ?>"></td>
            </tr>
            
            <tr>
                <th><label for="inmueble_interesado">Inmueble interesado</label></th>
                <td>
                    <select name="inmueble_interesado" id="inmueble_interesado">
                        <option value="">Selecciona un inmueble</option>
                        <?php foreach ($inmuebles as $inmueble) : ?>
                            <?php
                            $tipo_inmueble = get_post_meta($inmueble->ID, 'tipo_inmueble', true);
                            // Deserializa el tipo de inmueble si es necesario
                            if (is_serialized($tipo_inmueble)) {
                                $tipo_inmueble = maybe_unserialize($tipo_inmueble);
                            }
                            // Verifica si el tipo de inmueble está mapeado correctamente
                            if (is_array($tipo_inmueble)) {
                                // Si es un array, toma el primer valor
                                $tipo_inmueble_key = reset($tipo_inmueble);
                            } else {
                                $tipo_inmueble_key = $tipo_inmueble;
                            }
                            
                            if (array_key_exists($tipo_inmueble_key, $tipos_inmueble_map)) {
                                $tipo_inmueble = $tipos_inmueble_map[$tipo_inmueble_key];
                            }
                            $nombre_calle = get_post_meta($inmueble->ID, 'nombre_calle', true);
                            $inmueble_id = $inmueble->ID;
                            $referencia = get_post_meta($inmueble->ID, 'referencia', true);
                            $selected = ($inmueble_id == $inmueble_interesado) ? 'selected' : '';

                            ?>
                            <option value="<?php echo esc_attr($inmueble_id); ?>" <?php echo esc_attr($selected); ?>>
                                <?php echo esc_html('('. $referencia .') ' . $tipo_inmueble . ' en ' . $nombre_calle); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

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
            update_post_meta($post_id, 'email', sanitize_email($_POST['email']));
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
            update_post_meta($post_id, 'presupuesto', intval($_POST['presupuesto']));
        }
        if (array_key_exists('num_hab', $_POST)) {
            update_post_meta($post_id, 'num_hab', intval($_POST['num_hab']));
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
                        $tipo_inmueble_serialized = get_post_meta($inmueble_interesado, 'tipo_inmueble', true);
                        $tipo_inmueble = maybe_unserialize($tipo_inmueble_serialized);
                        $nombre_calle = get_post_meta($inmueble_interesado, 'nombre_calle', true);
        
                        if (is_array($tipo_inmueble)) {
                            $tipo_inmueble_key = reset($tipo_inmueble);
                        } else {
                            $tipo_inmueble_key = $tipo_inmueble;
                        }
        
                        if (array_key_exists($tipo_inmueble_key, $tipos_inmueble_map)) {
                            $tipo_inmueble = $tipos_inmueble_map[$tipo_inmueble_key];
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


    /**
     * Obtener inmuebles sugeridos (cruces) para una demanda.
     */
    public function obtener_cruces_inmuebles($post_id) {
        // Obtener los campos de la demanda
        $presupuesto = intval(get_post_meta($post_id, 'presupuesto', true));
        $zona_deseada = maybe_unserialize(get_post_meta($post_id, 'zona_deseada', true)); // Puede ser un array
        $tipo_inmueble = maybe_unserialize(get_post_meta($post_id, 'tipo_inmueble', true)); // Puede ser un array
        $num_hab = intval(get_post_meta($post_id, 'num_hab', true));
    
        // Configurar la consulta para buscar inmuebles
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key' => 'precio_venta',
                'value' => $presupuesto,
                'compare' => '<=',
                'type' => 'NUMERIC'
            ),
        );
    
        // Añadir el filtro de tipo_inmueble solo si está definido en la demanda
        if (!empty($tipo_inmueble) && is_array($tipo_inmueble)) {
            $tipo_query = array(
                'relation' => 'OR',
            );
            foreach ($tipo_inmueble as $tipo) {
                $tipo_query[] = array(
                    'key' => 'tipo_inmueble',
                    'value' => '"' . $tipo . '"', // Comparar dentro del array serializado
                    'compare' => 'LIKE',
                );
            }
            $meta_query[] = $tipo_query; // Añadir la subconsulta de tipo de inmueble
        }

        // Añadir el filtro de zona solo si está definido en la demanda
        if (!empty($zona_deseada) && is_array($zona_deseada)) {
            $meta_query[] = array(
                'key' => 'zona_inmueble', // Campo en el inmueble
                'value' => $zona_deseada, // Array de zonas deseadas de la demanda
                'compare' => 'IN', // Usar IN para comparar con el array
            );
        }
        
        // Añadir el filtro de número de dormitorios solo si está definido en la demanda
        if (!empty($num_hab)) {
            $meta_query[] = array(  
                'key' => 'num_dormitorios',
                'value' => $num_hab,
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }
    
        // Ejecutar la consulta con los filtros construidos
        $args = array(
            'post_type' => 'inmueble',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => $meta_query,
        );
    
        // Ejecutar la consulta
        $cruces_inmuebles = new WP_Query($args);
    
        // Si hay resultados, retornar los inmuebles
        if ($cruces_inmuebles->have_posts()) {
            return $cruces_inmuebles->posts;
        }
    
        return [];
    }
    


    /**
     * Metabox (cruces) para una demanda.
     */
    public function cruces_meta_box() {
        add_meta_box(
            'cruces_inmuebles', // ID del metabox
            'Inmuebles Sugeridos (Cruces)', // Título del metabox
            [$this, 'mostrar_cruces_inmuebles'], // Callback que mostrará el contenido
            'demanda', // Tipo de post donde aparecerá
            'normal', // Cambiado a 'side' para mostrar en la barra lateral
            'default' // Prioridad 'default'
        );
    }

    /**
     * Mostrar los inmuebles sugeridos (cruces) en el metabox.
     */
    public function mostrar_cruces_inmuebles($post) {
        $inmuebles_sugeridos = $this->obtener_cruces_inmuebles($post->ID); // Obtener los inmuebles sugeridos (cruces)
    
        if (!empty($inmuebles_sugeridos)) {
            echo '<ul>';
            foreach ($inmuebles_sugeridos as $inmueble) {
                $titulo_inmueble = get_the_title($inmueble->ID);
                $link_inmueble = get_edit_post_link($inmueble->ID);
                echo '<li><a href="' . esc_url($link_inmueble) . '" target="_blank">' . esc_html($titulo_inmueble) . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No se encontraron inmuebles sugeridos para esta demanda.</p>';
        }
    }


}

new Demanda();

/**
 * Amplia la búsqueda en campos personalizados de demandas.
 */
function buscar_en_campos_demanda($search, $wp_query) {
    if (!empty($search) && !empty($wp_query->query_vars['search_terms']) && $wp_query->query_vars['post_type'] == 'demanda') {
        $terms = $wp_query->query_vars['search_terms'];
        $meta_keys = array('nombre', 'email', 'telefono');
        $search .= construir_meta_search($meta_keys, $terms);
    }

    return $search;
}
add_filter('posts_search', 'buscar_en_campos_demanda', 10, 2);


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
            'post__not_in' => array($post_ID), // excluye el post que se esta editando
            'meta_query' => $meta_query,
        ));

        if ($exists_demanda->have_posts()) {
            wp_die('Ya existe una demanda con el mismo DNI, teléfono o email. <br> <a href="javascript:history.back()">Volver</a>', 'Error', array('response' => 400));
        }
    }
}
add_action('pre_post_update', 'validar_datos_demanda', 10, 2);



