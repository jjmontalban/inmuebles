<?php

class Inmueble_PDF {

    public function __construct() {
        add_action('add_meta_boxes', [$this, 'inmuebles_agregar_mb_pdf_inmueble']);
        add_action('admin_post_generar_pdf_inmueble', [$this, 'inmuebles_generar_pdf']); // Acción para generar PDF
    }

    /**
     * Agregar el metabox para generar PDF
     */
    public function inmuebles_agregar_mb_pdf_inmueble() {
        add_meta_box(
            'inmueble_pdf_inmueble',
            'PDF de Inmueble',
            [$this, 'inmuebles_boton_pdf'], // Callback
            'inmueble', // Donde se mostrará
            'side', // Contexto
            'default' // Prioridad
        );
    }

    /**
     * Mostrar botón para generar PDF
     */
    public function inmuebles_boton_pdf($post) {
        $pdf_url = admin_url('admin-post.php?action=generar_pdf_inmueble&post_id=' . $post->ID);
        echo '<a href="' . esc_url($pdf_url) . '" class="button button-primary button-large">Crear PDF anuncio de Inmueble</a>';
    }

    /**
     * Función para generar el PDF del inmueble
     */
    public function inmuebles_generar_pdf() {
        // Verifica si el usuario tiene permisos
        if (!current_user_can('edit_posts')) {
            wp_die(__('No tienes permisos para generar el PDF.'));
        }

        // Obtener el ID del inmueble
        $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
        if (!$post_id) {
            wp_die(__('Inmueble no encontrado.'));
        }

        // Obtener los detalles del inmueble
        $vivienda = get_post($post_id);
        $campos = get_post_meta($post_id);

        // Generar el PDF usando TCPDF
        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Write(0, $vivienda->post_title, '', 0, 'L', true, 0, false, false, 0);
        // Repetir para otros campos del inmueble

        // Entregar el PDF al navegador
        $pdf->Output('inmueble_' . $post_id . '.pdf', 'I');

        // Detener ejecución después de generar el PDF
        exit;
    }
}

// Inicializar la clase
new Inmueble_PDF();
