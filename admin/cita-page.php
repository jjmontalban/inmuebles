<?php 


/**
 * Agrega el metabox "Datos de la cita" al formulario de edición.
 */
function citas_meta_box() {
    add_meta_box(
        'citas_info',
        'Información de la Cita',
        'mostrar_campos_cita',
        'cita',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'citas_meta_box');

/**
 * Muestra los campos de la cita en el formulario de edición.
 * @param WP_Post $post El objeto de entrada actual.
 */
function mostrar_campos_cita($post) {



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

<style>
        #calendario {
            height: 400px; /* Ajusta la altura del calendario según tus necesidades */
            max-width: 100%; /* Ajusta el ancho máximo según tus necesidades */
        }
    </style>

    
    <table class="form-table">
        <tr>
            <th><label for="inmueble_id">Inmueble</label></th>
            <td>
                <select name="inmueble_id" id="inmueble_id">
                    <option value="">Selecciona un inmueble</option>
                    <?php foreach ($inmuebles as $inmueble) : ?>
                        <option value="<?php echo esc_attr($inmueble->ID); ?>" <?php selected($inmueble_id, $inmueble->ID); ?>>
                            <?php echo esc_html(get_term_field('tipo_inmueble', $inmueble->ID) . ' en ' . get_post_meta($inmueble->ID, 'direccion', true)); ?>
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
                            <?php echo esc_html(get_the_title($demanda->ID)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="fecha">Fecha</label></th>
            <td>
                <div id="calendario"></div>
                <input type="hidden" name="fecha" id="fecha" value="<?php echo esc_attr($fecha); ?>">
            </td>
        </tr>
    </table>
    <script>
    jQuery(document).ready(function($) {
        $('#calendario').fullCalendar({
            defaultView: 'month',
            editable: true,
            eventStartEditable: true,
            eventOverlap: false,
            locale: 'es', // Establece el idioma a español
            events: [], // Puedes cargar eventos aquí si los necesitas
            select: function(start, end, jsEvent, view) {
                // Captura la fecha seleccionada y guárdala en el campo de fecha
                $('#fecha').val(start.format());
            }
        });
    });
</script>
    <?php
}

/**
 * Guarda los valores de los campos personalizados al guardar una cita.
 * @param int $post_id ID de la cita actual.
 */
function guardar_campos_cita($post_id) {
    if (array_key_exists('inmueble_id', $_POST)) {
        update_post_meta($post_id, 'inmueble_id', sanitize_text_field($_POST['inmueble_id']));
    }
    if (array_key_exists('demanda_id', $_POST)) {
        update_post_meta($post_id, 'demanda_id', sanitize_text_field($_POST['demanda_id']));
    }
    if (array_key_exists('fecha', $_POST)) {
        update_post_meta($post_id, 'fecha', sanitize_text_field($_POST['fecha']));
    }
}
add_action('save_post', 'guardar_campos_cita');



/**
 * Agregar columnas personalizadas a la lista de entradas de "citas"
 * @param int $columns
 */
function agregar_columnas_personalizadas_cita($columns) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'nombre' => 'Nombre',
        'apellidos' => 'Apellidos',
        'telefono1' => 'Teléfono 1',
        'telefono2' => 'Teléfono 2',
        'email' => 'Email',
        'date' => 'Fecha de publicación',
    );
    return $columns;
}
//add_filter('manage_propietario_posts_columns', 'agregar_columnas_personalizadas_propietario');

// Mostrar datos en las columnas personalizadas
function mostrar_datos_columnas_personalizadas_cita($column, $post_id) {
    switch ($column) {
        case 'nombre':
            echo get_post_meta($post_id, 'nombre', true);
            break;
        case 'apellidos':
            echo get_post_meta($post_id, 'apellidos', true);
            break;
        case 'telefono1':
            echo get_post_meta($post_id, 'telefono1', true);
            break;
        case 'telefono2':
            echo get_post_meta($post_id, 'telefono2', true);
            break;
        case 'email':
            echo get_post_meta($post_id, 'email', true);
            break;
        default:
            break;
    }
}
//add_action('manage_propietario_posts_custom_column', 'mostrar_datos_columnas_personalizadas_propietario', 10, 2);

