<?php 

/**
 * Shortcode contacto [formulario_contacto]
 */
function formulario_contacto_shortcode() 
{
    ob_start();
    ?>

    <style>

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

        .submit-button {
            background-color: #eb5d0b;
            margin-top: 20px;
            border: none;
            cursor: pointer;
        }

        .contact-link:hover,
        .submit-button:hover {
            opacity: 0.8;
        }

        .whatsapp-link {
            background-color: #25d366;
            margin-top: 25%;
        }

        .phone-link {
            background-color: #ccc;
        }

        .contact-link i {
            margin-right: 5px;
            vertical-align: middle;
        }

    </style>
   
    <!-- Agregar un campo RECAPTCHA -->
    <?php $recaptcha_site_key = get_option('inmuebles_google_captcha_api_key', ''); ?>

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

        <input type="checkbox" name="aceptar_condiciones" required>
        <label for="aceptar_condiciones" style="font-size: 0.8em;">Usando este formulario estás aceptando nuestra <a target="blank" href="<?php echo esc_url(get_permalink(get_page_by_path('aviso-legal'))); ?>">política de privacidad</a></label>

        <input type="submit" value="Enviar" class="submit-button">

        <!-- Campo oculto para almacenar el token reCAPTCHA -->
        <div id="recaptcha-container" class="g-recaptcha" data-sitekey="<?php echo esc_attr($recaptcha_site_key); ?>" data-callback="capturaRespuestaRecaptcha"></div>
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

        <!-- Agregar un campo oculto en el formulario -->
        <input type="text" name="extra_field" style="display: none;">
    </form>

    <div id="respuesta-formulario-contacto"></div>
    
    <!-- Para mostrar los dos últimos botones solo en las páginas de inmueble, archivo de inmuebles y -->
    <?php if (is_singular('inmueble') || is_post_type_archive('inmueble')) : ?>

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

    <?php endif; ?>

    <script>

        jQuery(document).ready(function($) {

            // Función para capturar la respuesta del reCAPTCHA
            function capturaRespuestaRecaptcha(token) {
                // Asigna el valor del token al campo oculto del formulario
                $('#g-recaptcha-response').val(token);
            }

            $('#formulario-contacto').submit(function(e) {
                e.preventDefault();

                // Verificar si el checkbox está marcado
                if (!$('input[name="aceptar_condiciones"]').prop('checked')) {
                    $('#respuesta-formulario-contacto').html('<p style="color: red;">Debes aceptar las condiciones generales para enviar el formulario.</p>');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#respuesta-formulario-contacto').html('<p style="color: green; background-color: white;">¡Consulta enviada con éxito! Nos pondremos en contacto contigo lo antes posible.</p>');
                        $('#formulario-contacto')[0].reset();
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
add_shortcode('formulario_contacto', 'formulario_contacto_shortcode');



/**
 * Shortcode ultimos-inmuebles [ultimos_inmuebles]
 */
function ultimos_inmuebles_shortcode() 
{
    ob_start();
    ?>

    <style>
        .latest-inmuebles {
            padding-top: 2%;
            text-align: center;
            
        }

        .latest-inmuebles .inmueble-row {
            flex-wrap: wrap;
            display: flex;
            justify-content: space-between;
        }
    
        .inmueble-item {
            width: 32%;
            margin-bottom: 20px;
            border-radius: 5px;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: box-shadow 0.3s ease;
            overflow: hidden;
            height: 400px;
        }

        .inmueble-item img {
            width: 100%;
            height: 40%;
            object-fit: cover; 
            margin-bottom: inherit;
        }
    
        .inmueble-precio {
            position: absolute;
            border-radius: 5px;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.7);
            font-weight: bold;
            padding: 5px 10px; 
        }
    
        .inmueble-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-bottom: 20px;
            position: relative;
        }

        .btn-inmueble {
            display: block;
            background-color: #333;
            border-radius: 5px;
            color: #fff;
            font-size: 0.8em;
            width: 50%;
            margin-left: 25%;
            text-align: center;
            padding: 5px 10px;
            text-decoration: none;
            transition: background-color 0.3s;
            margin-bottom: 4%;
        }
    
        .btn-inmueble:hover {
            color: #fff;
            background-color: #eb5d0b;
        }
    
        @media (max-width: 768px) {
            .inmueble-item {
                width: 100%;
            }
            .info-item {
                width: 100%;
            }
        }
    
    </style>

    <section class="latest-inmuebles">
        <h3>Últimos inmuebles añadidos</h3>
        <?php
        $args = array(
            'post_type' => 'inmueble',
            'posts_per_page' => 9,
            'orderby' => 'date',
            'order' => 'DESC'
        );
        $query = new WP_Query($args);
        global $tipos_inmueble_map;
    
        if ($query->have_posts()): ?>
            <div class="inmueble-row">
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <?php $campos = obtener_campos_inmueble(get_the_ID());  ?>
                    <div class="inmueble-item">
                        <!-- Mostrar la imagen por defecto -->
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail(); ?>
                        <?php endif; ?>
                        <div class="inmueble-precio">
                            <p class="inmuebles-price">
                                <?php if ($campos['precio_venta']) : ?>
                                    <?php echo $campos['precio_venta']; ?> €
                                <?php else: ?>
                                    <?php echo $campos['precio_alquiler']; ?> €/mes
                                <?php endif; ?>
                            </p>
                        </div>
                        <h5><?php echo $tipos_inmueble_map[$campos['tipo_inmueble']] . ' en ' . $campos['nombre_calle'] . ', ' . $campos['localidad']; ?></h5>
                        <div class="inmueble-info">
                            <span><?php echo $campos['m_construidos'] . ' m2'; ?></span>
                            <span><?php echo $campos['num_dormitorios'] . ' Dorm'; ?></span>
                            <span><?php echo $campos['num_dormitorios'] . ' Baño'; ?></span>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="btn-inmueble">Ver inmueble</a>


                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; wp_reset_postdata(); ?>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('ultimos_inmuebles', 'ultimos_inmuebles_shortcode');