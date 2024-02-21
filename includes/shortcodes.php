<?php 
/**
 * Shortcode contacto [formulario_contacto]
 */
function formulario_contacto_shortcode() 
{
    ob_start();
    ?>
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
        <input type="text" name="nombre" placeholder="Nombre y Apellidos*" required>
        <input type="email" name="email" placeholder="Email*" required>
        <input type="tel" name="telefono" placeholder="Teléfono*" required>
        <textarea name="mensaje" placeholder="Mensaje (opcional)" style="height: 100px;"></textarea>
        <input type="checkbox" name="aceptar_condiciones" required>
        <label for="aceptar_condiciones" style="font-size: 0.8em;">Usando este formulario estás aceptando nuestra <a target="blank" href="<?php echo esc_url(get_permalink(get_page_by_path('aviso-legal'))); ?>">política de privacidad</a></label>
        <button type="submit" class="submit-button">
            Enviar
            <span class="spinner" style="display: none;"><i class="fa fa-spinner fa-spin"></i></span>
        </button>
        <!-- Agregar un campo oculto en el formulario -->
        <input type="text" name="extra_field" style="display: none;">
        <!-- Agregar un campo oculto para almacenar el token de reCAPTCHA v3 -->
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response" value="">
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
    <?php endif;
    return ob_get_clean();
}   
add_shortcode('formulario_contacto', 'formulario_contacto_shortcode');


/**
 * Shortcode contacto [formulario_comprar]
 */
function formulario_comprar_shortcode()
{
    ob_start();
    ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="formulario-comprar">
        <input type="hidden" name="action" value="procesar_formulario_contacto">
        <input type="hidden" name="tipo_formulario" value="comprar">
        <h5 class="form-ppal">Rellena el siguiente formulario y obtén un plano informativo con las últimas transacciones de inmuebles realizadas en Chipiona</h5>
        <input type="text" name="nombre" placeholder="Nombre y apellidos*" required>
        <input type="email" name="email" placeholder="Email*" required>
        <input type="tel" name="telefono" placeholder="Teléfono*" required>
        <select name="zona">
            <option value="" selected>Zona deseada</option>
            <?php 
            global $zonas_inmueble_map;
            foreach ($zonas_inmueble_map as $key => $value) {
                echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
            }
            ?>
        </select>
        <input type="text" name="presupuesto" placeholder="Presupuesto">
        <textarea name="mensaje" placeholder="Mensaje (opcional)" style="height: 100px;"></textarea>
        <input type="checkbox" name="aceptar_condiciones" required>
        <label for="aceptar_condiciones" style="font-size: 0.8em;">Usando este formulario estás aceptando nuestra <a target="blank" href="<?php echo esc_url(get_permalink(get_page_by_path('aviso-legal'))); ?>">política de privacidad</a></label>
        <button type="submit" class="submit-button">
            Enviar
            <span class="spinner" style="display: none;"><i class="fa fa-spinner fa-spin"></i></span>
        </button>
        <!-- Agregar un campo oculto en el formulario -->
        <input type="text" name="extra_field" style="display: none;">
        <!-- Agregar un campo oculto para almacenar el token de reCAPTCHA v3 -->
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-comprar" value="">
    </form>
    <div id="respuesta-formulario-comprar"></div>
    <?php
    return ob_get_clean();
}   
add_shortcode('formulario_comprar', 'formulario_comprar_shortcode');


/**
 * Shortcode contacto [formulario_vender]
 */
function formulario_vender_shortcode()
{
    ob_start();
    ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="formulario-vender">
        <input type="hidden" name="action" value="procesar_formulario_contacto">
        <input type="hidden" name="tipo_formulario" value="vender">
        <h5 class="form-ppal">Rellena el siguiente formulario y obtén un plano informativo con las últimas transacciones de inmuebles realizadas en Chipiona</h5>
        <input type="text" name="nombre" placeholder="Nombre y apellidos*" required>
        <input type="email" name="email" placeholder="Email*" required>
        <input type="tel" name="telefono" placeholder="Teléfono*" required>
        <input type="text" name="direccion" placeholder="dirección de la vivienda">
        <textarea name="mensaje" placeholder="Mensaje (opcional)" style="height: 100px;"></textarea>
        <input type="checkbox" name="aceptar_condiciones" required>
        <label for="aceptar_condiciones" style="font-size: 0.8em;">Usando este formulario estás aceptando nuestra <a target="blank" href="<?php echo esc_url(get_permalink(get_page_by_path('aviso-legal'))); ?>">política de privacidad</a></label>
        <button type="submit" class="submit-button">
            Enviar
            <span class="spinner" style="display: none;"><i class="fa fa-spinner fa-spin"></i></span>
        </button>
        <!-- Agregar un campo oculto en el formulario -->
        <input type="text" name="extra_field" style="display: none;">
        <!-- Agregar un campo oculto para almacenar el token de reCAPTCHA v3 -->
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-vender" value="">
    </form>
    <div id="respuesta-formulario-vender"></div>
    <?php
    return ob_get_clean();
}   
add_shortcode('formulario_vender', 'formulario_vender_shortcode');



/**
 * Shortcode ultimos-inmuebles [ultimos_inmuebles]
 */
function ultimos_inmuebles_shortcode() 
{
    ob_start();
    ?>
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
                        <h5><?php echo $tipos_inmueble_map[$campos['tipo_inmueble']] . ' en ' . $campos['nombre_calle']; ?></h5>
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