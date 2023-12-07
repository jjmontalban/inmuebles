<?php 

// Función para generar el formulario de contacto
function formulario_contacto_shortcode() {
    ob_start(); ?>

        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="formulario-contacto">
            <input type="hidden" name="action" value="procesar_formulario_contacto">
            <?php if (is_singular('inmueble')) : ?>
                <h5>Solicita más información de este inmueble</h5>
                <input type="hidden" name="inmueble_id" value="<?php echo get_the_ID(); ?>">
            <?php elseif (is_post_type_archive('inmueble')) : ?>
                <h5>Solicita más información</h5>
                <input type="hidden" name="tipo_formulario" value="Contacto desde listado">
            <?php elseif (is_post_type_archive('page')) : ?>
                <h5>Dinos quién eres y te contactamos:</h5>
                <input type="hidden" name="tipo_formulario" value="desde pagina">
            <?php endif; ?>
                <input type="text" name="nombre" placeholder="Nombre" required ><br>
                <input type="email" name="email" placeholder="Email (opcional)"><br>
                <input type="tel" name="telefono" placeholder="telefono" required><br>
                <textarea name="mensaje" placeholder="Mensaje (opcional)" style="height: 100px;"></textarea><br>
                <input type="submit" value="Enviar">
        </form>
        <div id="respuesta-formulario-contacto"></div>

        <script>
        jQuery(document).ready(function($) {
            $('#formulario-contacto').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#respuesta-formulario-contacto').html('<p style="color: green; background-color: white;">¡Consulta enviada con éxito! Nos podremos en contacto contigo lo antes posible</p>');
                        $('#formulario-contacto')[0].reset(); // Limpia los campos del formulario
                    },
                    error: function(error) {
                        $('#respuesta-formulario-contacto').html('<p style="color: red;">Ha ocurrido un error al enviar la consulta.</p>');
                    }
                });
            });
        });
    </script>

    <?php
    return ob_get_clean();
}   





// Registrar el shortcode
add_shortcode('formulario_contacto', 'formulario_contacto_shortcode');


