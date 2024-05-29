jQuery(document).ready(function($) {

    var recaptcha_site_key = inmuebles_vars.recaptcha_site_key;
    // Encolar el script de reCAPTCHA v3
    $.ajax({
        url: 'https://www.google.com/recaptcha/api.js?render=' + recaptcha_site_key,
        dataType: 'script',
        cache: true,
        success: function() {
            // Callback cuando el script de reCAPTCHA se ha cargado
            grecaptcha.ready(function() {
                // Ejecutar reCAPTCHA al cargar la página
                grecaptcha.execute(inmuebles_vars.recaptcha_site_key, { action: 'formulario_contacto' })
                    .then(function(token) {
                        // Almacenar el token en el campo oculto antes de enviar el formulario
                        $('#g-recaptcha-response').val(token);
                        $('#g-recaptcha-response-comprar').val(token);
                        $('#g-recaptcha-response-vender').val(token);
                    });
            });
        }
    });

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

            beforeSend: function() {
                $('.submit-button').prop('disabled', true); // Deshabilita el botón de envío
                $('.submit-button').val('Enviando...'); // Cambia el texto del botón
                $('.spinner').show(); // Muestra el spinner
            },
            success: function(response) {
                $('#respuesta-formulario-contacto').html('<p style="color: green; background-color: white;">¡Consulta enviada con éxito! Nos pondremos en contacto contigo lo antes posible.</p>');
                $('#formulario-contacto')[0].reset();
                $('.submit-button').prop('disabled', false); // Habilita el botón de envío
                $('.submit-button').val('Enviar'); // Restaura el texto original del botón
                $('.spinner').hide(); // Oculta el spinner
            },
            error: function(error) {
                $('#respuesta-formulario-contacto').html('<p style="color: red;">Ha ocurrido un error al enviar la consulta.</p>');
                $('.submit-button').prop('disabled', false); // Habilita el botón de envío
                $('.submit-button').val('Enviar'); // Restaura el texto original del botón
                $('.spinner').hide(); // Oculta el spinner
            }
        });
    });

    // Para el formulario de comprar
    $('#formulario-contacto-comprar').submit(function(e) {
        e.preventDefault();
        // Verificar si el checkbox está marcado
        if (!$('input[name="aceptar_condiciones"]').prop('checked')) {
            $('#respuesta-formulario-comprar').html('<p style="color: red;">Debes aceptar las condiciones generales para enviar el formulario.</p>');
            return;
        }

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),

            beforeSend: function() {
                $('.submit-button').prop('disabled', true); // Deshabilita el botón de envío
                $('.submit-button').val('Enviando...'); // Cambia el texto del botón
                $('.spinner').show(); // Muestra el spinner
            },
            success: function(response) {
                $('#respuesta-formulario-comprar').html('<p style="color: green; background-color: white;">¡Consulta enviada con éxito! Nos pondremos en contacto contigo lo antes posible.</p>');
                $('#formulario-comprar')[0].reset();
                $('.submit-button').prop('disabled', false); // Habilita el botón de envío
                $('.submit-button').val('Enviar'); // Restaura el texto original del botón
                $('.spinner').hide(); // Oculta el spinner
            },
            error: function(error) {
                $('#respuesta-formulario-comprar').html('<p style="color: red;">Ha ocurrido un error al enviar la consulta.</p>');
            }
        });
    });

    // Para el formulario de vender
    $('#formulario-contacto-vender').submit(function(e) {
        e.preventDefault();
        // Verificar si el checkbox está marcado
        if (!$('input[name="aceptar_condiciones"]').prop('checked')) {
            $('#respuesta-formulario-vender').html('<p style="color: red;">Debes aceptar las condiciones generales para enviar el formulario.</p>');
            return;
        }

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),

            beforeSend: function() {
                $('.submit-button').prop('disabled', true); // Deshabilita el botón de envío
                $('.submit-button').val('Enviando...'); // Cambia el texto del botón
                $('.spinner').show(); // Muestra el spinner
            },
            success: function(response) {
                $('#respuesta-formulario-vender').html('<p style="color: green; background-color: white;">¡Consulta enviada con éxito! Nos pondremos en contacto contigo lo antes posible.</p>');
                $('#formulario-vender')[0].reset();
                $('.submit-button').prop('disabled', false); // Habilita el botón de envío
                $('.submit-button').val('Enviar'); // Restaura el texto original del botón
                $('.spinner').hide(); // Oculta el spinner
            },
            error: function(error) {
                $('#respuesta-formulario-vender').html('<p style="color: red;">Ha ocurrido un error al enviar la consulta.</p>');
                $('.submit-button').prop('disabled', false); // Habilita el botón de envío
                $('.submit-button').val('Enviar'); // Restaura el texto original del botón
                $('.spinner').hide(); // Oculta el spinner
            }
        });
    });

});