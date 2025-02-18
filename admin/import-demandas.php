<?php

// Importar las clases de PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * Archivo para importar demandas desde un XLSX (Idealista) a Chipicasa CRM.
 * Se integra en el menú de Demandas.
 */

// 1) Agregar submenú en Demandas
add_action('admin_menu', 'chipicasa_importar_demandas_submenu');
function chipicasa_importar_demandas_submenu() {
    // Esto añadirá la opción “Importar XLSX” bajo el menú "Demandas"
    add_submenu_page(
        'edit.php?post_type=demanda',   // Menú padre (CPT Demandas)
        'Importar Demandas',       // Título de la página
        'Importar Demandas desde archivo',                // Título del submenú
        'manage_options',               // Capability requerida
        'importar-demandas',            // Slug del submenú
        'chipicasa_importar_demandas_page'  // Callback para la pantalla
    );
}

// 2) Callback que muestra el formulario de subida
function chipicasa_importar_demandas_page() {
    ?>
    <div class="wrap">
        <h1>Importar Demandas desde XLSX</h1>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('chipicasa_importar_xlsx', 'chipicasa_importar_xlsx_nonce'); ?>
            <input type="file" name="archivo_xlsx" accept=".xlsx" required />
            <input type="submit" name="importar_xlsx_submit" value="Importar XLSX" class="button button-primary">
        </form>

        <p style="margin-top:20px;">
            <strong>Nota:</strong> El archivo debe tener formato <code>.xlsx</code>
        </p>
    </div>
    <?php
}

// 3) Procesar el archivo una vez enviado
// add_action y callback:
add_action('admin_init', 'chipicasa_procesar_demandas_xlsx');

function chipicasa_procesar_demandas_xlsx() {

    if (isset($_POST['importar_xlsx_submit'])) {

        // Verifica nonce
        if (
            ! isset($_POST['chipicasa_importar_xlsx_nonce']) ||
            ! wp_verify_nonce($_POST['chipicasa_importar_xlsx_nonce'], 'chipicasa_importar_xlsx')
        ) {
            wp_die('Fallo de seguridad. Por favor, recarga la página e intenta de nuevo.');
        }

        // Verifica archivo
        if (!empty($_FILES['archivo_xlsx']['name'])) {
            $uploaded_file   = $_FILES['archivo_xlsx'];
            $file_extension = pathinfo($uploaded_file['name'], PATHINFO_EXTENSION);

            if (strtolower($file_extension) !== 'xlsx') {
                wp_die('El archivo debe tener extensión .xlsx');
            }

            // Subir a carpeta temporal
            $upload_overrides = ['test_form' => false];
            $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

            if ($movefile && ! isset($movefile['error'])) {
                $xlsx_file_path = $movefile['file'];

                // Verificar que exista la clase
                if (! class_exists('\PhpOffice\PhpSpreadsheet\Reader\Xlsx')) {
                    wp_die('No se encontró PhpSpreadsheet. Asegúrate de haberlo instalado.');
                }

                try {
                    $reader      = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $reader->setReadDataOnly(true); // Ignora estilos y formateos
                    $spreadsheet = $reader->load($xlsx_file_path);
                    $worksheet   = $spreadsheet->getActiveSheet();

                    // Contadores
                    $fila     = 0;
                    $creadas  = 0;
                    $saltadas = 0;

                    foreach ($worksheet->getRowIterator() as $row) {
                        $fila++;
                        // Saltar encabezados
                        if ($fila === 1) {
                            continue;
                        }

                        // Extraer celdas
                        $cellIterator = $row->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(false);

                        $rowData = [];
                        foreach ($cellIterator as $cell) {
                            $rowData[] = $cell->getValue();
                        }

                        // Mapeo de columnas a variables:

                        // rowData[0] => código (no se necesita)
                        $nombreDemandante   = isset($rowData[1]) ? sanitize_text_field($rowData[1]) : '';
                        $apellidosDemandante= isset($rowData[2]) ? sanitize_text_field($rowData[2]) : '';
                        $tel1               = isset($rowData[3]) ? sanitize_text_field($rowData[3]) : '';
                        $tel2               = isset($rowData[4]) ? sanitize_text_field($rowData[4]) : '';
                        $email              = isset($rowData[5]) ? sanitize_email($rowData[5]) : '';
                        // rowData[6] => fecha alta (no se necesita)
                        $comprarAlquilar    = isset($rowData[7]) ? strtolower(sanitize_text_field($rowData[7])) : '';
                        // rowData[8] => categoría (no se necesita)
                        $zonas              = isset($rowData[9]) ? sanitize_text_field($rowData[9]) : '';
                        // rowData[10..13] => no se necesitan
                        // rowData[15] => presupuesto
                        $presupuesto        = isset($rowData[15]) ? intval($rowData[15]) : 0;
                        // rowData[18] => dormitorios
                        $dormitorios        = isset($rowData[18]) ? intval($rowData[18]) : 0;
                        // 1) Construir nombre completo => "Nombre + Apellidos"
                        $nombreCompleto = trim($nombreDemandante . ' ' . $apellidosDemandante);
                        // 2) Si tel1 está vacío, usar tel2
                        $telefono = $tel1;
                        if (empty($telefono) && !empty($tel2)) {
                            $telefono = $tel2;
                        }
                        // 3) Determinar operación
                        //    si "comprar" => "compra", si "alquilar" => "alquiler"
                        //    (ajusta a tus valores)
                        $operacion = '';
                        if ($comprarAlquilar === 'comprar') {
                            $operacion = 'compra';
                        } elseif ($comprarAlquilar === 'alquilar') {
                            $operacion = 'alquiler';
                        }
                        // 4) Convertir la cadena de "zonas" en un array (ej: "centro, pinar" => ['centro','pinar'])
                        //    si en el Excel se separa por comas
                        $arrayZonas = [];
                        if (!empty($zonas)) {
                            // Dividir por coma
                            $arrayZonas = array_map('trim', explode(',', $zonas));
                        }
                        // 5) Verificar email duplicado
                        if (!empty($email)) {
                            $existe = new WP_Query([
                                'post_type'   => 'demanda',
                                'post_status' => 'any',
                                'meta_query'  => [
                                    [
                                        'key'     => 'email',
                                        'value'   => $email,
                                        'compare' => '=',
                                    ]
                                ]
                            ]);
                            if ($existe->have_posts()) {
                                $saltadas++;
                                continue;
                            }
                        } else {
                            // Sin email, no creamos
                            $saltadas++;
                            continue;
                        }
                        // 6) Crear la Demanda
                        $demanda_id = wp_insert_post([
                            'post_type'   => 'demanda',
                            'post_title'  => $nombreCompleto ?: 'Demanda sin nombre',
                            'post_status' => 'publish',
                        ]);

                        if (!is_wp_error($demanda_id)) {
                            // Guardar metas
                            update_post_meta($demanda_id, 'nombre',       $nombreCompleto);
                            update_post_meta($demanda_id, 'telefono',     $telefono);
                            update_post_meta($demanda_id, 'email',        $email);
                            update_post_meta($demanda_id, 'operacion',    $operacion);
                            update_post_meta($demanda_id, 'zona_deseada', serialize($arrayZonas));
                            update_post_meta($demanda_id, 'presupuesto',  $presupuesto);
                            update_post_meta($demanda_id, 'num_hab',      $dormitorios);

                            $creadas++;
                        } else {
                            // Si algo falla, consideramos la fila saltada
                            $saltadas++;
                        }

                    } // fin foreach fila

                    // (Opcional) eliminar archivo
                    // unlink($xlsx_file_path);

                    // Mostrar aviso con contadores
                    add_action('admin_notices', function() use ($creadas, $saltadas) {
                        echo '<div class="notice notice-success is-dismissible">';
                        echo '<p><strong>¡Importación completada!</strong><br>';
                        echo 'Demandas creadas: ' . esc_html($creadas) . '<br>';
                        echo 'Filas saltadas (duplicadas o sin email): ' . esc_html($saltadas);
                        echo '</p></div>';
                    });

                } catch (\Exception $e) {
                    wp_die('Error al leer XLSX: ' . $e->getMessage());
                }

            } else {
                wp_die('Error subiendo el archivo: ' . $movefile['error']);
            }
        }
    }
}


// 4) Mensaje de éxito
function chipicasa_importar_demandas_exito() {
    echo '<div class="notice notice-success is-dismissible">
            <p>Se han importado las demandas correctamente.</p>
        </div>';
}
