<?php

class Cita
{
    public function __construct()
    {
        add_action('init', array($this, 'registrar_cpt_cita'));
        add_action('add_meta_boxes', [$this, 'citas_meta_box']);
        add_action('save_post', [$this, 'guardar_campos_cita']);
        add_filter('manage_cita_posts_columns', [$this, 'agregar_columnas_personalizadas_cita']);
        add_action('manage_cita_posts_custom_column', [$this, 'mostrar_datos_columnas_personalizadas_cita'], 10, 2);
        add_filter('post_row_actions', [$this, 'modificar_texto_accion_cita'], 10, 2);
        add_filter('post_row_actions', [$this, 'desactivar_quick_edit_cita'], 10, 2);
    }

    // Registrar cpt Cita
    public function registrar_cpt_cita() {
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

    public function citas_meta_box()
    {
        add_meta_box(
            'citas_info',
            'Información de la Cita',
            [$this, 'mostrar_campos_cita'],
            'cita',
            'normal',
            'high'
        );
    }

    public function mostrar_campos_cita($post)
    {
        $inmueble_id = get_post_meta($post->ID, 'inmueble_id', true);
        $demanda_id = get_post_meta($post->ID, 'demanda_id', true);
        $fecha = get_post_meta($post->ID, 'fecha', true);
        
        // Obtiene la lista de inmuebles para el select
        $args = array(
            'post_type' => 'inmueble', // El nombre de tu CPT de inmuebles
            'posts_per_page' => -1,
        );
        $inmuebles = get_posts($args);
        
        // Obtiene la lista de demandas para el select
        $args = array(
            'post_type' => 'demanda', // El nombre de tu CPT de demandas
            'posts_per_page' => -1,
        );
        $demandas = get_posts($args);
        ?>
    
        <table class="form-table">
            <tr>
                <th><label for="inmueble_id">Inmueble</label></th>
                <td>
                    <select name="inmueble_id" id="inmueble_id">
                        <option value="">Selecciona un inmueble</option>
                        <?php foreach ($inmuebles as $inmueble) : ?>
                            <?php
                            $tipo_inmueble = get_post_meta($inmueble->ID, 'tipo_inmueble', true);
                            $nombre_calle = get_post_meta($inmueble->ID, 'nombre_calle', true);
                            ?>
                            <option value="<?php echo esc_attr($inmueble->ID); ?>" <?php selected($inmueble_id, $inmueble->ID); ?>>
                                <?php echo esc_html($tipo_inmueble . ' en ' . $nombre_calle); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="demanda_id">Demanda</label></th>
                <td>
                    <select name="demanda_id" id="demanda_id">
                        <option value="">Selecciona una demanda</option>
                        <?php foreach ($demandas as $demanda) : ?>
                            <option value="<?php echo esc_attr($demanda->ID); ?>" <?php selected($demanda_id, $demanda->ID); ?>>
                                <?php echo esc_html( $demanda->nombre ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="fecha">Fecha y Hora</label></th>
                <td>
                    <?php
                    $fecha = get_post_meta($post->ID, 'fecha', true);
                    $hora = get_post_meta($post->ID, 'hora', true);
            
                    ?>
                    <input type="date" name="fecha" id="fecha" value="<?php echo esc_attr( $fecha ?? ''); ?>" required>
                    <input type="time" name="hora" id="hora" value="<?php echo esc_attr( $hora ?? ''); ?>" required>
                </td>
            </tr>
        </table>
        <?php
    }

    public function guardar_campos_cita($post_id)
    {
       if ( get_post_type($post_id) !== 'cita') {
           return;
       }
       if (array_key_exists('inmueble_id', $_POST)) {
           update_post_meta($post_id, 'inmueble_id', sanitize_text_field($_POST['inmueble_id']));
       }
       if (array_key_exists('demanda_id', $_POST)) {
           update_post_meta($post_id, 'demanda_id', sanitize_text_field($_POST['demanda_id']));
       }
       if (array_key_exists('fecha', $_POST)) {
           update_post_meta($post_id, 'fecha', sanitize_text_field($_POST['fecha']));
       }
       if (array_key_exists('hora', $_POST)) {
           update_post_meta($post_id, 'hora', sanitize_text_field($_POST['hora']));
       }
   
       // Obtener información necesaria para el correo electrónico
       $inmueble_id = get_post_meta($post_id, 'inmueble_id', true);
       $demanda_id = get_post_meta($post_id, 'demanda_id', true);
       $fecha = get_post_meta($post_id, 'fecha', true);
       $hora = get_post_meta($post_id, 'hora', true);
   
       // Obtener la dirección de correo electrónico del administrador del sitio
       $admin_email = get_option('admin_email');
   
       // Obtener la dirección de correo electrónico de la demanda
       $demanda_email = get_post_meta($demanda_id, 'email', true);
   
       // Asunto y contenido del correo electrónico
       $subject = 'Nueva cita agendada';
       $message = "Se ha agendado una nueva cita:\n";
       $message .= "Fecha: $fecha\n";
       $message .= "Hora: $hora\n";
       $message .= "Inmueble ID: $inmueble_id\n";
       $message .= "Demanda ID: $demanda_id\n";
   
       // Enviar correo electrónico al administrador del sitio
       wp_mail($admin_email, $subject, $message);
   
       // Enviar correo electrónico a la demanda
       wp_mail($demanda_email, $subject, $message);
    }

    public function agregar_columnas_personalizadas_cita($columns)
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'demanda' => 'Nombre demanda',
            'inmueble' => 'Nombre Inmueble',
            'fecha' => 'Fecha de la cita',
            'hora' => 'Hora de la cita',
        );
        return $columns;
    }

    public function mostrar_datos_columnas_personalizadas_cita($column, $post_id)
    {
        switch ($column) {
            case 'inmueble':
                $inmueble_id = get_post_meta($post_id, 'inmueble_id', true);
                echo get_the_title($inmueble_id);
                break;
            case 'demanda':
                $demanda_id = get_post_meta($post_id, 'demanda_id', true);
                echo get_post_meta($demanda_id, 'nombre', true);
                break;
            case 'fecha':
                $fecha = get_post_meta($post_id, 'fecha', true);
                echo date('d/m/Y', strtotime($fecha));
                break;
            case 'hora':
                $hora = get_post_meta($post_id, 'hora', true);
                echo date('H:i', strtotime($hora));
                break;
            default:
                break;
        }
    }

    public function modificar_texto_accion_cita($actions, $post)
    {
        // Solo modificar para el tipo de publicación 'cita'
        if ($post->post_type === 'cita') {
            if (isset($actions['edit'])) {
                $actions['edit'] = str_replace('Editar', 'Ver', $actions['edit']);
            }
        }

    return $actions;
    }

    public function desactivar_quick_edit_cita($actions, $post)
    {
        if ($post->post_type === 'cita') {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }
}

new Cita();