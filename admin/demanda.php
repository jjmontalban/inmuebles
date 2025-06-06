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
        add_action('wp_ajax_marcar_mensaje_enviado', [$this, 'marcar_mensaje_enviado_callback']);
        add_action('wp_ajax_nopriv_marcar_mensaje_enviado', [$this, 'marcar_mensaje_enviado_callback']);
        add_action('admin_post_marcar_mensaje_enviado', [$this, 'marcar_mensaje_enviado_post']);

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
            'supports' => [],
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
        $localidad = get_post_meta($post->ID, 'localidad', true);
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
                <th><label for="localidad">Localidad</label></th>
                <td><input type="text" name="localidad" id="localidad" value="<?php echo esc_attr( $localidad ?? ''); ?>"></td>
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
        if (array_key_exists('localidad', $_POST)) {
            update_post_meta($post_id, 'localidad', sanitize_text_field($_POST['localidad']));
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
        $presupuesto = intval(get_post_meta($post_id, 'presupuesto', true));
        $localidad_deseada = get_post_meta($post_id, 'localidad', true); // Obtener la localidad deseada de la demanda
        $zona_deseada = maybe_unserialize(get_post_meta($post_id, 'zona_deseada', true)); // Puede ser un array
        $tipo_inmueble = maybe_unserialize(get_post_meta($post_id, 'tipo_inmueble', true)); // Puede ser un array
        $num_hab = intval(get_post_meta($post_id, 'num_hab', true));
        $inmueble_interesado = get_post_meta($post_id, 'inmueble_interesado', true); // Obtener el inmueble interesado

        // Si la localidad deseada está vacía, retornamos vacío ya que no hay nada que cruzar
        if (empty($localidad_deseada)) {
            return [];
        }

        $localidad_deseada = normalizar_texto($localidad_deseada);

        // Configurar la consulta para buscar inmuebles
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key' => 'precio_venta',
                'value' => $presupuesto,
                'compare' => '<=',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => 'localidad',
                'value' => $localidad_deseada,
                'compare' => 'LIKE'
            ),
        );
    
        // Añadir el filtro de tipo_inmueble
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

        // Zona
        if (!empty($zona_deseada) && is_array($zona_deseada)) {
            $meta_query[] = array(
                'key' => 'zona_inmueble',
                'value' => $zona_deseada,
                'compare' => 'IN', // Usar IN para comparar con el array
            );
        }
        
        // Número de dormitorios
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

         // Excluir el inmueble interesado
        if (!empty($inmueble_interesado) && is_numeric($inmueble_interesado)) {
            $args['post__not_in'] = array($inmueble_interesado);
        }
    
        
        $cruces_inmuebles = new WP_Query($args);
    
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
            'cruces_inmuebles',
            'Inmuebles Sugeridos (Cruces)',
            [$this, 'mostrar_cruces_inmuebles'],
            'demanda',
            'normal',
            'default'
        );
    }

    /**
     * Muestra los inmuebles sugeridos (cruces) en el formulario de edición de demandas.
     * @param WP_Post $post El objeto de entrada actual.
     */
    public function mostrar_cruces_inmuebles($post) {
        $inmuebles_sugeridos = $this->obtener_cruces_inmuebles($post->ID);
        $telefono = get_post_meta($post->ID, 'telefono', true);
        $email = get_post_meta($post->ID, 'email', true);
        $mensajes_enviados = get_post_meta($post->ID, 'mensajes_enviados', true) ?: [];
    
        if (!empty($inmuebles_sugeridos)) {
            echo '<ul>';
            foreach ($inmuebles_sugeridos as $inmueble) {
                $titulo_inmueble = get_the_title($inmueble->ID);
                $link_inmueble = get_permalink($inmueble->ID);
                $id_inmueble = $inmueble->ID;
    
                echo '<li>';
                echo '<strong><a href="' . esc_url($link_inmueble) . '" target="_blank">' . esc_html($titulo_inmueble) . '</a></strong>';
    
                if (!empty($telefono)) {
                    if (isset($mensajes_enviados[$id_inmueble]['whatsapp'])) {
                        echo ' <span style="color:green;">Mensaje enviado por WhatsApp</span>';
                    } else {
                        $update_url = add_query_arg([
                            'action' => 'marcar_mensaje_enviado',
                            'post_id' => $post->ID,
                            'inmueble_id' => $id_inmueble,
                            'tipo' => 'whatsapp'
                        ], admin_url('admin-post.php'));
                
                        echo ' <a href="' . esc_url($update_url) . '" class="button button-primary" target="_blank">Enviar por WhatsApp</a>';
                    }
                }
    
                if (!empty($email)) {
                    if (isset($mensajes_enviados[$id_inmueble]['email'])) {
                        echo ' <span style="color:green;">Mensaje enviado por Email</span>';
                    } else {
                        $update_url = add_query_arg([
                            'action' => 'marcar_mensaje_enviado',
                            'post_id' => $post->ID,
                            'inmueble_id' => $id_inmueble,
                            'tipo' => 'email'
                        ], admin_url('admin-ajax.php'));
                        echo ' <a href="' . esc_url($update_url) . '" class="button button-primary">Enviar por Email</a>';
                    }
                }
    
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    public function marcar_mensaje_enviado_post() {
        $post_id = intval($_GET['post_id']);
        $inmueble_id = intval($_GET['inmueble_id']);
        $tipo = sanitize_text_field($_GET['tipo']);
    
        if ($post_id && $inmueble_id && $tipo === 'whatsapp') {
            $mensajes_enviados = get_post_meta($post_id, 'mensajes_enviados', true) ?: [];
            $mensajes_enviados[$inmueble_id][$tipo] = true;
            update_post_meta($post_id, 'mensajes_enviados', $mensajes_enviados);
    
            // Generar el mensaje y enlace de WhatsApp
            $telefono = get_post_meta($post_id, 'telefono', true);
            $titulo_inmueble = get_the_title($inmueble_id);
            $link_inmueble = get_permalink($inmueble_id);
    
            $mensaje = sprintf(
                'Hola, tenemos este inmueble que podría interesarte: %s. Mira los detalles aquí: %s',
                $titulo_inmueble,
                $link_inmueble
            );
    
            $link_whatsapp = 'https://wa.me/' . urlencode($telefono) . '?text=' . urlencode($mensaje);
            wp_redirect($link_whatsapp);
            exit;
        }
    
        // Si falla algo, volver a donde estaba
        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
    

    public function marcar_mensaje_enviado_callback() {
        $post_id = intval($_GET['post_id']);
        $inmueble_id = intval($_GET['inmueble_id']);
        $tipo = sanitize_text_field($_GET['tipo']);
    
        if ($post_id && $inmueble_id && in_array($tipo, ['whatsapp', 'email'])) {
            $mensajes_enviados = get_post_meta($post_id, 'mensajes_enviados', true) ?: [];
            $mensajes_enviados[$inmueble_id][$tipo] = true;
            update_post_meta($post_id, 'mensajes_enviados', $mensajes_enviados);
    
            if ($tipo === 'email') {
                $this->enviar_correo_inmueble($post_id, $inmueble_id);
            }
    
            wp_redirect($_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    private function enviar_correo_inmueble($post_id, $inmueble_id) {
        $email_destino = get_post_meta($post_id, 'email', true);
        if (empty($email_destino)) {
            return;
        }
    
        $titulo_inmueble = get_the_title($inmueble_id);
        $link_inmueble = get_permalink($inmueble_id);
    
        $subject = 'Recomendación de inmueble desde chipicasa.com';
        $message = "Hola,\n\n";
        $message .= "Tenemos un inmueble que podría interesarte.\n\n";
        $message .= "Título: " . esc_html($titulo_inmueble) . "\n";
        $message .= esc_url($link_inmueble) . "\n\n";
        $message .= "Saludos,\n";
        $message .= "El equipo de Chipicasa.com";
    
        wp_mail($email_destino, $subject, $message);
    }


}

new Demanda();


add_action('admin_init', 'demanda_quitar_soportes', 20);
function demanda_quitar_soportes() {
    remove_post_type_support('demanda', 'title');       // Quita el título
    remove_post_type_support('demanda', 'editor');      // Quita el editor
    remove_post_type_support('demanda', 'thumbnail');   // Quita la imagen destacada
    remove_post_type_support('demanda', 'custom-fields'); 
}


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



function agregar_opcion_exportar_demandas() {
    // Agregar un submenú bajo el CPT "Demandas"
    add_submenu_page(
        'edit.php?post_type=demanda',  // Menú padre (CPT Demandas)
        'Exportar Demandas',          // Título de la página
        'Exportar Demandas',          // Título del submenú
        'edit_posts',                 // Capacidad requerida
        'exportar-demandas',          // Slug del submenú
        'exportar_demandas_callback'  // Función de callback para mostrar contenido
    );
}
add_action('admin_menu', 'agregar_opcion_exportar_demandas');

function exportar_demandas_callback() {
    echo '<div class="wrap">';
    echo '<h1>Exportar Demandas</h1>';
    echo '<p>Haz clic en el botón para descargar las demandas en un archivo CSV.</p>';
    echo '<a href="' . esc_url(admin_url('edit.php?post_type=demanda&page=exportar-demandas&exportar=csv')) . '" class="button button-primary">Exportar CSV</a>';
    echo '</div>';
}

add_action('admin_init', 'procesar_exportar_demandas');

function procesar_exportar_demandas() {
    if (isset($_GET['exportar']) && $_GET['exportar'] === 'csv') {
        // Limpia
        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=demandas.csv');

        $output = fopen('php://output', 'w');

        // Encabezados
        fputcsv($output, [
            'ID', 
            'Nombre', 
            'Email', 
            'Teléfono', 
            'DNI',
            'Localidad', 
            'Operación', 
            'Presupuesto', 
            'Nº de Habitaciones', 
            'Notas',
            'Zona Deseada',
            'Tipo de Inmueble',
            'Inmueble Interesado',
            'fecha de registro'
        ]);

        // Obtener demandas
        $demandas = get_posts([
            'post_type'      => 'demanda',
            'posts_per_page' => -1,
        ]);

        foreach ($demandas as $demanda) {
            $id = $demanda->ID;

            // Campos
            $nombre = get_post_meta($id, 'nombre', true);
            $email = get_post_meta($id, 'email', true);
            $telefono = get_post_meta($id, 'telefono', true);
            $dni = get_post_meta($id, 'dni', true);
            $localidad = get_post_meta($id, 'localidad', true);
            $operacion = get_post_meta($id, 'operacion', true);
            $presupuesto = get_post_meta($id, 'presupuesto', true);
            $num_hab = get_post_meta($id, 'num_hab', true);

            // Campo Notas
            // Campo complejo: Notas
            $notas = get_post_meta($id, 'notas', true);
            $notas_str = '';
            if (is_array($notas)) {
                foreach ($notas as $nota) {
                    $notas_str .= '(' . $nota['fecha'] . ') Nota: ' . $nota['nota'] . PHP_EOL;
                }
            } else {
                $notas_str = $notas;
            }


            // Campo Zona deseada
            $zona_deseada = maybe_unserialize(get_post_meta($id, 'zona_deseada', true));
            $zona_deseada_str = is_array($zona_deseada) ? implode(', ', $zona_deseada) : $zona_deseada;

            // Campo Tipo de inmueble
            $tipo_inmueble = maybe_unserialize(get_post_meta($id, 'tipo_inmueble', true));
            $tipo_inmueble_str = is_array($tipo_inmueble) ? implode(', ', $tipo_inmueble) : $tipo_inmueble;

            // Campo Inmueble interesado
            $inmueble_interesado = get_post_meta($id, 'inmueble_interesado', true);
            if (!empty($inmueble_interesado)) {
                $tipo_inmueble_interesado = maybe_unserialize(get_post_meta($inmueble_interesado, 'tipo_inmueble', true));
                $nombre_calle = get_post_meta($inmueble_interesado, 'nombre_calle', true);

                $tipo_inmueble_interesado_str = is_array($tipo_inmueble_interesado) 
                    ? implode(', ', $tipo_inmueble_interesado) 
                    : $tipo_inmueble_interesado;

                $inmueble_interesado_str = $tipo_inmueble_interesado_str . ' en ' . $nombre_calle;
            } else {
                $inmueble_interesado_str = '';
            }

            // Fecha de creación (post_date)
            $fecha_creacion = $demanda->post_date;

            fputcsv($output, [
                $id,
                $nombre,
                $email,
                $telefono,
                $dni,
                $localidad,
                $operacion,
                $presupuesto,
                $num_hab,
                $notas_str,
                $zona_deseada_str,
                $tipo_inmueble_str,
                $inmueble_interesado_str,
                $fecha_creacion
            ]);
        }

        fclose($output);
        exit();
    }
}


add_action('wp_dashboard_setup', 'agregar_widget_cruces_demanda');

function agregar_widget_cruces_demanda() {
    wp_add_dashboard_widget(
        'cruces_demanda_dashboard', // ID del widget
        'Demandas con Cruces', // Título del widget
        'mostrar_cruces_demanda_dashboard' // Función de callback
    );
}

function mostrar_cruces_demanda_dashboard() {
    // Obtener todas las demandas
    $demandas = get_posts([
        'post_type'      => 'demanda',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);

    if (empty($demandas)) {
        echo '<p>No hay demandas creadas.</p>';
        return;
    }

    echo '<ul>';
    foreach ($demandas as $demanda) {
        $cruces = (new Demanda())->obtener_cruces_inmuebles($demanda->ID);

        // Si la demanda tiene cruces, mostrar el enlace
        if (!empty($cruces)) {
            $numero_cruces = count($cruces);
            $nombre_demanda = get_post_meta($demanda->ID, 'nombre', true) ?: 'Demanda sin nombre';
            $link = get_edit_post_link($demanda->ID);
            echo '<li><a href="' . esc_url($link) . '">' . esc_html($nombre_demanda) . ' (' . $numero_cruces . ')</a></li>';
        }
    }
    echo '</ul>';
}

