jQuery(document).ready(function($) {

    // Obligatoriedad del numero de la calle
    $('#numero_obligatorio').on('change', function() {
        var disabled = $(this).is(':checked');
        $('#numero').prop('disabled', disabled);
    });


    // Mostrar u ocultar campos según el tipo de inmueble seleccionado
    var tipoInmueble = $('#tipo_inmueble').val();
    mostrarCamposTipoInmueble(tipoInmueble);

    $('#tipo_inmueble').on('change', function() {
        var tipoInmueble = $(this).val();
        mostrarCamposTipoInmueble(tipoInmueble);
    });

    function mostrarCamposTipoInmueble(tipoInmueble) {

        if (tipoInmueble === 'piso') {
            $('.campo_planta').show();
            $('.campo_planta input').prop('required', true);
            $('.campo_caracteristica_piso').show();
            $('.campo_caracteristica_piso input').prop('required', true);
            $('.campo_ascensor').show();
            $('.campo_ascensor input').prop('required', true);
        }
        else {
            $('.campo_planta').hide();
            $('.campo_planta input').prop('required', false);
            $('.campo_caracteristica_piso').hide();
            $('.campo_caracteristica_piso input').prop('required', false);
            $('.campo_ascensor').hide();
            $('.campo_ascensor input').prop('required', false);
        } 

        if (tipoInmueble === 'casa_chalet') {
            $('.campo_metros_parcela').show();
            $('.campo_metros_parcela input').prop('required', true);
            $('.campo_tipologia_chalet').show();
            $('.campo_tipologia_chalet input').prop('required', true);
            $('.campo_num_plantas').show();
            $('.campo_num_plantas input').prop('required', true);
        } else {
            $('.campo_metros_parcela').hide();
            $('.campo_metros_parcela input').prop('required', false);
            $('.campo_tipologia_chalet').hide();
            $('.campo_tipologia_chalet input').prop('required', false);
            $('.campo_num_plantas').hide();
            $('.campo_num_plantas input').prop('required', false);
        }

        if (tipoInmueble === 'casa_rustica') {
            $('.campo_metros_parcela').show();
            $('.campo_metros_parcela input').prop('required', true);
            $('.campo_num_plantas').show();
            $('.campo_num_plantas input').prop('required', true);
            $('.campo_tipo_casa_rustica').show();
            $('.campo_tipo_casa_rustica input').prop('required', true);
        } else {
            $('.campo_metros_parcela').hide();
            $('.campo_metros_parcela input').prop('required', false);
            $('.campo_num_plantas').hide();
            $('.campo_num_plantas input').prop('required', false);
            $('.campo_tipo_casa_rustica').hide();
            $('.campo_tipo_casa_rustica input').prop('required', false);
        }

        if (tipoInmueble === 'local') {
            $('.campo_num_estancias').show();
            $('.campo_num_estancias input').prop('required', true);
            $('.campo_num_banos').show();
            $('.campo_num_banos input').prop('required', true);
            $('.campos_caracteristicas_local').show();
            $('.campos_caracteristicas_local input').prop('required', true);
            $('.campo_ubicacion_local').show();
            $('.campo_ubicacion_local input').prop('required', true);
            $('.campo_metros_lineales').show();
            $('.campo_metros_lineales input').prop('required', true);
            $('.campo_num_escaparates').show();
            $('.campo_num_escaparates input').prop('required', true);
            
        } else {
            $('.campo_num_estancias').hide();
            $('.campo_num_estancias input').prop('required', false);
            $('.campo_num_banos').hide();
            $('.campo_num_banos input').prop('required', false);
            $('.campos_caracteristicas_local').hide();
            $('.campos_caracteristicas_local input').prop('required', false);
            $('.campo_ubicacion_local').hide();
            $('.campo_ubicacion_local input').prop('required', false);
            $('.campo_metros_lineales').hide();
            $('.campo_metros_lineales input').prop('required', false);
            $('.campo_num_escaparates').hide();
            $('.campo_num_escaparates input').prop('required', false);
        }
        
        if (tipoInmueble === 'oficina') {
            /* $('.campo_num_plantas').show();
            $('.campo_num_plantas input').prop('required', true);
            $('.campo_tipo_casa_rustica').show();
            $('.campo_tipo_casa_rustica input').prop('required', true); */
        } else {
            /* $('.campo_num_plantas').hide();
            $('.campo_num_plantas input').prop('required', false);
            $('.campo_tipo_casa_rustica').hide();
            $('.campo_tipo_casa_rustica input').prop('required', false); */
        }
        
        if (tipoInmueble === 'garaje') {
            $('.campo_metros_plaza').show();
            $('.campo_metros_plaza input').prop('required', true);
            $('.campo_plaza').show();
            $('.campo_plaza input').prop('required', true);
        } else {
            $('.campo_metros_plaza').hide();
            $('.campo_metros_plaza input').prop('required', false);
            $('.campo_plaza').hide();
            $('.campo_plaza input').prop('required', false);
        }
        
        if (tipoInmueble === 'terreno') {
            /* $('.campo_num_plantas').show();
            $('.campo_num_plantas input').prop('required', true);
            $('.campo_tipo_casa_rustica').show();
            $('.campo_tipo_casa_rustica input').prop('required', true); */
        } else {
            /* $('.campo_num_plantas').hide();
            $('.campo_num_plantas input').prop('required', false);
            $('.campo_tipo_casa_rustica').hide();
            $('.campo_tipo_casa_rustica input').prop('required', false); */
        }
        
    }


    // Mostrar u ocultar campos según el valor seleccionado en el campo Tipo de Operación
    var tipoOperacion = $('input[name="tipo_operacion"]:checked').val();
    mostrarCamposTipoOperacion(tipoOperacion);

    $('input[name="tipo_operacion"]').on('change', function() {
        var tipoOperacion = $(this).val();
        mostrarCamposTipoOperacion(tipoOperacion);
    });

    function mostrarCamposTipoOperacion(tipoOperacion) {
        if (tipoOperacion === 'venta') {
            $('.campo_precio_venta').show();
            $('.campo_gastos_comunidad').show();
        } else {
            $('.campo_precio_venta').hide();
            $('.campo_gastos_comunidad').hide();
        }

        if (tipoOperacion === 'alquiler') {
            $('.campo_precio_alquiler').show();
            $('.campo_fianza').show();
        } else {
            $('.campo_precio_alquiler').hide();
            $('.campo_fianza').hide();
        }
    }


   /* GALERIA DE IMAGENES */ 
   
   // Agregar imagen
   $('.agregar-imagen').on('click', function() {
    var frame = wp.media({
        title: 'Seleccionar Imagen',
        multiple: true,
        library: { type: 'image' },
        button: { text: 'Usar Imagen(es)' },
    });

    frame.on('select', function() {
        var attachments = frame.state().get('selection').toArray();

        for (var i = 0; i < attachments.length; i++) {
            var attachment = attachments[i];
            var imageUrl = attachment.attributes.url;

            var galeriaImagen = $('<div class="galeria-imagen">\
                <img src="' + imageUrl + '" alt="Imagen">\
                <input type="hidden" name="galeria_imagenes[]" value="' + imageUrl + '">\
                <button type="button" class="remove-imagen button-link">Eliminar</button>\
            </div>');

            $('#galeria-imagenes-container').append(galeriaImagen);
        }
    });

    frame.open();
    });

    // Ordenar imágenes
    $('#galeria-imagenes-container').sortable({
        axis: 'x', // Ordenar horizontalmente
        containment: 'parent', // Limitar a su contenedor padre
    });

    // Eliminar imagen
    $(document).on('click', '.remove-imagen', function() {
        $(this).closest('.galeria-imagen').remove();
    });




    // Obtén el valor de la dirección del formulario
    var direccion = $('#direccion').val();

    // Crea un objeto geocoder de Google Maps
    var geocoder = new google.maps.Geocoder();

    // Realiza la geocodificación de la dirección
    geocoder.geocode({ 'address': direccion }, function(results, status) {
    if (status === 'OK') {
        // Obtiene la latitud y longitud de los resultados de la geocodificación
        var latitud = results[0].geometry.location.lat();
        var longitud = results[0].geometry.location.lng();

        // Crea un objeto de mapa de Google Maps y muestra el mapa en un elemento HTML con el ID "mapa"
        var mapa = new google.maps.Map(document.getElementById('mapa'), {
        center: { lat: latitud, lng: longitud },
        zoom: 14
        });

        // Agrega un marcador en la ubicación del inmueble en el mapa
        var marcador = new google.maps.Marker({
        position: { lat: latitud, lng: longitud },
        map: mapa,
        title: 'Ubicación del Inmueble'
        });
    } else {
        console.log('Error al geolocalizar la dirección: ' + status);
    }
    });



    $('#validar_direccion').on('click', function() {
        var nombreCalle = $('#nombre_calle').val();
        var numero = $('#numero').val();
        var localidad = $('#localidad').val();
      
        var direccion = nombreCalle + ' ' + numero + ', ' + localidad;
      
        var geocoder = new google.maps.Geocoder();
      
        geocoder.geocode({ 'address': direccion }, function(results, status) {
          if (status === 'OK') {
            var latitud = results[0].geometry.location.lat();
            var longitud = results[0].geometry.location.lng();
      
            var mapa = new google.maps.Map(document.getElementById('mapa'), {
              center: { lat: latitud, lng: longitud },
              zoom: 14
            });
      
            var marcador = new google.maps.Marker({
              position: { lat: latitud, lng: longitud },
              map: mapa,
              title: 'Ubicación del Inmueble'
            });
          } else {
            console.log('Error al geolocalizar la dirección: ' + status);
          }
        });
      });






});
