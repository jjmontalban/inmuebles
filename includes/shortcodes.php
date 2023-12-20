<?php 

function formulario_contacto_shortcode() {
    ob_start();
    ?>

    <style>
        /* Estilos para los botones */
        .contact-link,
        .submit-button {
            display: block;
            width: 100%;
            text-decoration: none;
            text-align: center;
            color: #fff;
            padding: 10px 0;
            border-radius: 5px;
            margin-top: 10px;
        }
        

        .whatsapp-link {
            background-color: #25d366;
            margin-top: 25%;
        }

        .phone-link {
            background-color: #ccc;
        }

        .submit-button {
            background-color: #eb5d0b;
            margin-top: 20px; /* Ajusta el margen superior del botón Enviar */
        }

        /* Estilos para todos los botones al pasar el mouse */
        .contact-link:hover,
        .submit-button:hover {
            opacity: 0.8;
        }

        /* Estilos para los iconos */
        .contact-link i {
            margin-right: 5px;
            vertical-align: middle;
        }

        .submit-button {
            border: none;
            cursor: pointer;
        }
    </style>

    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="formulario-contacto">
        <input type="hidden" name="action" value="procesar_formulario_contacto">

        <?php if (is_singular('inmueble')) : ?>
            <h5>¿Te intenresa este inmueble?</h5>
            <input type="hidden" name="inmueble_id" value="<?php echo get_the_ID(); ?>">
        <?php elseif (is_post_type_archive('inmueble')) : ?>
            <h5>Solicita más información</h5>
            <input type="hidden" name="tipo_formulario" value="Contacto desde listado">
        <?php elseif (is_post_type_archive('page')) : ?>
            <h5>Dinos quién eres y te contactamos:</h5>
            <input type="hidden" name="tipo_formulario" value="desde pagina">
        <?php endif; ?>

        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email (opcional)">
        <input type="tel" name="telefono" placeholder="Teléfono" required>
        <textarea name="mensaje" placeholder="Mensaje (opcional)" style="height: 100px;"></textarea>
        <input type="submit" value="Enviar" class="submit-button">
    </form>
    <div id="respuesta-formulario-contacto"></div>
    
    <!-- Botón de WhatsApp con icono -->
    <a class="contact-link whatsapp-link" href="https://api.whatsapp.com/send?phone=+346232969 00&text=Hola,%20estoy%20interesado%20en%20obtener%20más%20información." target="_blank" rel="nofollow">
        <i class="fab fa-whatsapp"></i>
        <span class="button-text">Envíanos un WhatsApp</span>
    </a>

    <!-- Botón de contacto por teléfono con icono -->
    <a class="contact-link phone-link" href="tel:+34648736312">
        <i class="fa fa-phone"></i>
        <span class="button-text">Llámanos!</span>
    </a>



    <?php
    return ob_get_clean();
}   
add_shortcode('formulario_contacto', 'formulario_contacto_shortcode');
