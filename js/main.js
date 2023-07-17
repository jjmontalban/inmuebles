

jQuery(document).ready(function($) {

    // Obligatoriedad del numero de la calle
    $('#numero_obligatorio').on('change', function() {
        var disabled = $(this).is(':checked');
        $('#numero').prop('disabled', disabled);
    });



       // Definir la estructura de los campos por tipo de inmueble
       var camposPorTipoInmueble = {
        piso: [
        // Campos para el tipo de inmueble "piso"
        { campo: 'campo_m_construidos', requerido: true },
        { campo: 'campo_num_dormitorios', requerido: true },
        { campo: 'campo_num_banos', requerido: true },
        { campo: 'campo_planta', requerido: true },
        { campo: 'campo_cert_energ', requerido: true },
        { campo: 'campo_estado_cons', requerido: true },
        { campo: 'campo_int_ext', requerido: true },
        { campo: 'campo_planta', requerido: true },
        { campo: 'campo_ascensor', requerido: true },
        { campo: 'campo_caracteristica_piso', requerido: false }
        ],
        casa_chalet: [
        // Campos para el tipo de inmueble "casa_chalet"
        { campo: 'campo_m_construidos', requerido: true },
        { campo: 'campo_num_dormitorios', requerido: true },
        { campo: 'campo_num_banos', requerido: true },
        { campo: 'campo_cal_emis', requerido: true },
        { campo: 'campo_cert_energ', requerido: true },
        { campo: 'campo_estado_cons', requerido: true },
        { campo: 'campo_tipologia_chalet', requerido: true },
        { campo: 'campo_m_parcela', requerido: false },
        { campo: 'campo_num_plantas', requerido: false }
        ],
        casa_rustica: [
        // Campos para el tipo de inmueble "casa_rustica"
        { campo: 'campo_m_construidos', requerido: true },
        { campo: 'campo_num_dormitorios', requerido: true },
        { campo: 'campo_num_banos', requerido: true },
        // Resto de campos para "casa_rustica"
        ],
        local: [
        // Campos para el tipo de inmueble "local"
        { campo: 'campo_tipo_local', requerido: true },
        { campo: 'campo_ubicacion_local', requerido: true },
        { campo: 'campo_metros_lineales', requerido: true },
        // Resto de campos para "local"
        ],
        oficina: [
        // Campos para el tipo de inmueble "oficina"
        { campo: 'campo_tipo_oficina', requerido: true },
        { campo: 'campo_m_construidos', requerido: true },
        // Resto de campos para "oficina"
        ],
        garaje: [
        // Campos para el tipo de inmueble "garaje"
        { campo: 'campo_num_plazas', requerido: true },
        // Resto de campos para "garaje"
        ],
        terreno: [
        // Campos para el tipo de inmueble "terreno"
        { campo: 'campo_tipo_terreno', requerido: true },
        // Resto de campos para "terreno"
        ]
    };


/**
 * Mostrar u ocultar campos según el tipo de inmueble seleccionado
 */
    
// Función para mostrar u ocultar los campos según el tipo de inmueble seleccionado
function mostrarCamposTipoInmueble(tipoInmueble) {
    // Quitar el atributo required de todos los campos que comienzan con "campo_"
    $('[id^="campo_"] input').prop('required', false);
    
    // Ocultar todos los campos que comienzan con "campo_"
    $('[id^="campo_"]').hide();
    
    // Obtener los campos correspondientes al tipo de inmueble seleccionado
    var campos = camposPorTipoInmueble[tipoInmueble];
    
    // Verificar si campos es un arreglo válido
    if (Array.isArray(campos)) {
        // Mostrar los campos correspondientes y establecer el atributo required
        campos.forEach(function(campo) {
            console.log('Mostrando campo: ' + campo.campo); // Verificar si se muestra en la consola

            var campoElement = $('#' + campo.campo);
            campoElement.show().find('input').prop('required', campo.requerido);
        });
    }
}

    // Obtener el tipo de inmueble inicial
    var tipoInmueble = $('#tipo_inmueble').val();

     // Mostrar los campos iniciales según el tipo de inmueble seleccionado
     mostrarCamposTipoInmueble(tipoInmueble);

     // Asignar el evento 'change' al elemento #tipo_inmueble
    $('#tipo_inmueble').on('change', function() {
        var tipoInmueble = $(this).val();
        mostrarCamposTipoInmueble(tipoInmueble);
    });





























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



    /* MAPA */
    
    // Asigna el evento al botón "Validar Dirección"
    $('#validar_direccion').on('click', function(event) {
        event.preventDefault(); // Evita el comportamiento predeterminado del enlace
        
        // Obtén los valores de los campos necesarios
        var nombreCalle = document.getElementById('nombre_calle').value;
        var numero = document.getElementById('numero').value;
        var localidad = document.getElementById('localidad').value;
    
        // Verificar si algún campo está vacío
        if (nombreCalle === '' || numero === '' || localidad === '') {
            alert('Por favor, complete todos los campos de dirección.');
            return;
        }

        // Concatenar los valores para formar la dirección completa
        var direccion = nombreCalle + ' ' + numero + ', ' + localidad;
        
        // Crea el objeto Geocoder
        var geocoder = new google.maps.Geocoder();
    
        // Geocodifica la dirección para obtener la ubicación
        geocoder.geocode({ address: direccion }, function(results, status) {
        if (status === 'OK' && results.length > 0) {
            // Obtén la ubicación geográfica
            var ubicacion = results[0].geometry.location;
    
            // Crea el mapa centrado en la ubicación
            var mapa = new google.maps.Map(document.getElementById('mapa'), {
            center: ubicacion,
            zoom: 18
            });
    
            // Agrega un marcador en la ubicación
            var marcador = new google.maps.Marker({
            position: ubicacion,
            map: mapa,
            title: 'Ubicación'
            });
    
            // Muestra el modal con el mapa
            $('#mapaModal').show();
        } else {
            // Maneja el error si la dirección no es válida
            console.error('Error al geocodificar la dirección:', status);
        }
        });
    });
  
    // Asigna el evento al botón "Cerrar Modal"
    $('#cerrar_modal').on('click', function() {
        // Oculta el modal
        $('#mapaModal').hide();
    });


    document.getElementById('mapa_correcto').addEventListener('click', function(event) {
        event.preventDefault();
        
        // Obtener la ubicación del marcador en el mapa
        var ubicacionMarcador = marcador.getPosition();
      
        // Guardar la ubicación en el campo oculto
        document.getElementById('campo_mapa').value = ubicacionMarcador.lat() + ',' + ubicacionMarcador.lng();
      });
      
      document.getElementById('mapa_correcto').addEventListener('click', function() {
        document.getElementById('mapaModal').style.display = 'none';
      });


});

