

jQuery(document).ready(function($) {

    // Obligatoriedad del numero de la calle
    $('#numero_obligatorio').on('change', function() {
        var disabled = $(this).is(':checked');
        $('#numero').prop('disabled', disabled);
     
        // Si el checkbox está marcado, quitar el atributo 'required' del campo 'numero'
        if(disabled) {
            $('#numero').removeAttr('required');
        } 
    });

    // Código para la gráfica de visitas
    if ($('#graficaVisitas').length) {
        const ctx = $('#graficaVisitas').get(0).getContext('2d');
        const graficaVisitas = new Chart(ctx, {
            type: 'line', // Tipo de gráfica: línea
            data: {
                labels: fechasVisitas, // Las fechas de las visitas
                datasets: [{
                    label: 'Número de Visitas',
                    data: visitas, // Los datos de visitas
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Asegura que el eje Y muestre solo números enteros
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                }
            }
        });
    }




    /**
     * Mostrar u ocultar campos según el tipo de inmueble seleccionado
     */
    // Definir la estructura de los campos por tipo de inmueble
    var camposPorTipoInmueble = {
        piso: [
            { campo: 'campo_m_construidos', requerido: true },
            { campo: 'campo_num_dormitorios', requerido: true },
            { campo: 'campo_num_banos', requerido: true },
            { campo: 'campo_planta', requerido: true },
            { campo: 'campo_calif_consumo', requerido: true },
            { campo: 'campo_estado_cons', requerido: true },
            { campo: 'campo_int_ext', requerido: true },
            { campo: 'campo_planta', requerido: true },
            { campo: 'campo_ascensor', requerido: true },
            { campo: 'campo_calif_emis', requerido: true },
            { campo: 'campo_precio_venta', requerido: true },
            
            { campo: 'campo_bloque', requerido: false },
            { campo: 'campo_consumo', requerido: false },
            { campo: 'campo_emisiones', requerido: false },
            { campo: 'campo_escalera', requerido: false },
            { campo: 'campo_urbanizacion', requerido: false },
            { campo: 'campo_caract_inm', requerido: false },
            { campo: 'campo_otra_caract_inm', requerido: false },
            { campo: 'campo_m_utiles', requerido: false },
            { campo: 'campo_orientacion', requerido: false },
            { campo: 'campo_calefaccion', requerido: false },
            { campo: 'campo_ano_edificio', requerido: false },
            { campo: 'campo_calefaccion', requerido: false }
        ],
        
        casa_chalet: [
            { campo: 'campo_tipologia_chalet', requerido: true },
            { campo: 'campo_m_construidos', requerido: true },
            { campo: 'campo_num_dormitorios', requerido: true },
            { campo: 'campo_num_banos', requerido: true },
            { campo: 'campo_calif_emis', requerido: true },
            { campo: 'campo_calif_consumo', requerido: true },
            { campo: 'campo_estado_cons', requerido: true },
            
            { campo: 'campo_m_parcela', requerido: false },
            { campo: 'campo_m_utiles', requerido: false },
            { campo: 'campo_num_plantas', requerido: false },
            { campo: 'campo_consumo', requerido: false },
            { campo: 'campo_emisiones', requerido: false },
            { campo: 'campo_orientacion', requerido: false },
            { campo: 'campo_caract_inm', requerido: false },
            { campo: 'campo_otra_caract_inm', requerido: false },
            { campo: 'campo_calefaccion', requerido: false },
            { campo: 'campo_ano_edificio', requerido: false },
        ],
        
        casa_rustica: [
            { campo: 'campo_tipo_rustica', requerido: true },
            { campo: 'campo_m_construidos', requerido: true },
            { campo: 'campo_num_dormitorios', requerido: true },
            { campo: 'campo_num_banos', requerido: true },
            { campo: 'campo_calif_emis', requerido: true },
            { campo: 'campo_calif_consumo', requerido: true },
            { campo: 'campo_estado_cons', requerido: true },
            
            { campo: 'campo_m_parcela', requerido: false },
            { campo: 'campo_m_utiles', requerido: false },
            { campo: 'campo_consumo', requerido: false },
            { campo: 'campo_emisiones', requerido: false },
            { campo: 'campo_num_plantas', requerido: false },
            { campo: 'campo_orientacion', requerido: false },
            { campo: 'campo_caract_inm', requerido: false },
            { campo: 'campo_otra_caract_inm', requerido: false },
            { campo: 'campo_calefaccion', requerido: false },
            { campo: 'campo_ano_edificio', requerido: false },    
        ],
        
        local: [
            { campo: 'campo_planta', requerido: true },
            { campo: 'campo_m_construidos', requerido: true },
            { campo: 'campo_tipo_local', requerido: true },
            { campo: 'campo_calif_emis', requerido: true },
            { campo: 'campo_calif_consumo', requerido: true },
            { campo: 'campo_estado_cons', requerido: true },
            { campo: 'campo_ubicacion_local', requerido: true },
            
            { campo: 'campo_m_utiles', requerido: false },
            { campo: 'campo_m_lineales', requerido: false },
            { campo: 'campo_consumo', requerido: false },
            { campo: 'campo_emisiones', requerido: false },
            { campo: 'campo_num_estancias', requerido: false },
            { campo: 'campo_num_escap', requerido: false },
            { campo: 'campo_num_plantas', requerido: false },
            { campo: 'campo_num_banos', requerido: false },
            { campo: 'campo_caract_local', requerido: false },
            { campo: 'campo_ano_edificio', requerido: false },
        ],
        
        oficina: [
            { campo: 'campo_planta', requerido: true },
            { campo: 'campo_m_construidos', requerido: true },
            { campo: 'campo_uso_excl', requerido: true },
            { campo: 'campo_estado_cons', requerido: true },
            { campo: 'campo_int_ext', requerido: true },
            { campo: 'campo_distribucion_oficina', requerido: true },
            { campo: 'campo_aire_acond', requerido: true },
            { campo: 'campo_calif_emis', requerido: true },
            { campo: 'campo_calif_consumo', requerido: true },
            { campo: 'campo_num_ascensores', requerido: true },
            { campo: 'campo_num_plazas', requerido: true },
            
            { campo: 'campo_num_banos', requerido: false },
            { campo: 'campo_consumo', requerido: false },
            { campo: 'campo_emisiones', requerido: false },
            { campo: 'campo_num_plantas', requerido: false },
            { campo: 'campo_m_utiles', requerido: false },
            { campo: 'campo_orientacion', requerido: false },

        ],

        garaje: [
            { campo: 'campo_tipo_plaza', requerido: true },
            
            { campo: 'campo_m_plaza', requerido: false },
            { campo: 'campo_caract_garaje', requerido: false },
            { campo: 'campo_bloque', requerido: false },
            { campo: 'campo_escalera', requerido: false },
            { campo: 'campo_urbanizacion', requerido: false },
        ],

        terreno: [
            { campo: 'campo_tipo_terreno', requerido: true },
            { campo: 'campo_acceso_rodado', requerido: true },
            { campo: 'campo_superf_terreno', requerido: true },
            { campo: 'campo_calif_terreno', requerido: false },
            { campo: 'campo_tipo_terreno', requerido: true },
        ]
            
    };
    
    // Función para mostrar u ocultar los campos según el tipo de inmueble seleccionado
    function mostrarCamposTipoInmueble(tipoInmueble) {
        
        // Quitar el atributo required de todos los campos que comienzan con "campo_"
        $('[id^="campo_"]').find('input, select').prop('required', false);
        // Ocultar todos los campos que comienzan con "campo_"
        $('[id^="campo_"]').hide();

        // Obtener los campos correspondientes al tipo de inmueble seleccionado
        var campos = camposPorTipoInmueble[tipoInmueble];
    
        // Verificar si campos es un arreglo válido
        if (Array.isArray(campos)) {
            // Mostrar los campos correspondientes y establecer el atributo required
            campos.forEach(function(campo) {
                var campoElement = $('#' + campo.campo);
                campoElement.show();
                var selectElement = campoElement.find('select');
                if (selectElement.length > 0) {
                    selectElement.prop('required', campo.requerido);
                } else {
                    campoElement.find('input').prop('required', campo.requerido);
                }

                // Agregar el if-else para el campo con id "emisiones"
                if (campo.campo === 'emisiones' || campo.campo === 'consumo') {
                    campoElement.find('input').prop('required', false);
                }
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


    /**
     * Mostrar u ocultar campos según el valor seleccionado en el campo Acceso rodado
     */
    var accesoRodado = $('input[name="acceso_rodado"]:checked').val();
    //mostrarSelectTipoAcceso(accesoRodado);

    $('input[name="acceso_rodado"]').on('change', function() {
        var accesoRodado = $(this).val();
        mostrarSelectTipoAcceso(accesoRodado);
    });

    function mostrarSelectTipoAcceso(accesoRodado) {
        if (accesoRodado === 'si_tiene') {
            $('.campo_si_rodado').show();
        }
    }


    /**
     * Mostrar u ocultar campos según el valor seleccionado en el campo Tipo de Operación
     */
    var tipoOperacion = $('input[name="tipo_operacion"]:checked').val();
    mostrarCamposTipoOperacion(tipoOperacion);

    $('input[name="tipo_operacion"]').on('change', function() {
        var tipoOperacion = $(this).val();
        mostrarCamposTipoOperacion(tipoOperacion);
    });

    function mostrarCamposTipoOperacion(tipoOperacion) {
        if (tipoOperacion === 'venta') {
            $('#campo_precio_venta').show().find('input').prop('required', true);
            $('#campo_gastos_comunidad').show().find('input').prop('required', false);
            $('#campo_precio_alquiler').hide().find('input').prop('required', false);
            $('#campo_fianza').hide().find('input').prop('required', false);
        
        } else if (tipoOperacion === 'alquiler') {
            $('#campo_precio_venta').hide().find('input').prop('required', false);
            $('#campo_gastos_comunidad').hide().find('input').prop('required', false);
            $('#campo_precio_alquiler').show().find('input').prop('required', true);
            $('#campo_fianza').show().find('input').prop('required', false);
        
        } else {
            $('#campo_precio_venta').hide().find('input').prop('required', false);
            $('#campo_gastos_comunidad').hide().find('input').prop('required', false);
            $('#campo_precio_alquiler').hide().find('input').prop('required', false);
            $('#campo_fianza').hide().find('input').prop('required', false);
        }
    }

    
   /**
     * Galeria de imagenes
    */
   $(function() {
    function actualizarOrden() {
        $('#sortable').sortable({
            update: function(event, ui) {
                $(this).children().each(function(index) {
                    $(this).find('input').attr('name', 'galeria_imagenes[]').eq(index).val($(this).find('img').attr('src'));
                });
            }
        }).disableSelection();
    }

    // Inicializar sortable y capturar cambios de orden
    actualizarOrden();

    // Habilitar la funcionalidad de agregar imágenes
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

                var galeriaImagen = $('<li class="ui-state-default">\
                    <img src="' + imageUrl + '" alt="Imagen">\
                    <input type="hidden" name="galeria_imagenes[]" value="' + imageUrl + '">\
                    <button type="button" class="remove-imagen button-link">Eliminar</button>\
                </li>');

                $('#sortable').append(galeriaImagen);
            }

            // Reinicializar sortable después de agregar imágenes
            actualizarOrden();
        });

        frame.open();
    });

        // Eliminar imagen
        $(document).on('click', '.remove-imagen', function() {
            $(this).closest('li').remove();
            // Actualizar el orden después de eliminar una imagen
            actualizarOrden();
        });
    });


    /**
     * MAPA
    */
    var marcador;
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
                marcador = new google.maps.Marker({
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


    // Asigna el evento al botón "El mapa está correcto"
    $('#mapa_correcto').on('click', function() {
        // Obtén la ubicación del marcador
        var ubicacion = marcador.getPosition();

        // Guarda la ubicación en el campo "campo_mapa"
        $('#campo_mapa').val(ubicacion.lat() + ',' + ubicacion.lng());

        // Oculta el modal
        $('#mapaModal').hide();
    });

    // Asigna el evento al botón "Cerrar Modal"
    $('#cerrar_modal').on('click', function() {
        // Oculta el modal
        $('#mapaModal').hide();
    });

    document.addEventListener('DOMContentLoaded', (event) => {
        var mapaCorrecto = document.getElementById('mapa_correcto');
        if(mapaCorrecto) {
            mapaCorrecto.addEventListener('click', function(event) {
                event.preventDefault();
                // Obtener la ubicación del marcador en el mapa
                var ubicacionMarcador = marcador.getPosition();
                // Guardar la ubicación en el campo oculto
                document.getElementById('campo_mapa').value = ubicacionMarcador.lat() + ',' + ubicacionMarcador.lng();
            });
        }
    });

    document.addEventListener('DOMContentLoaded', (event) => {
        var mapaCorrecto = document.getElementById('mapa_correcto');
        if(mapaCorrecto) {
            mapaCorrecto.addEventListener('click', function() {
                var mapaModal = document.getElementById('mapaModal');
                if(mapaModal) {
                    mapaModal.style.display = 'none';
                }
            });
        }
    });


    // Detectar cambios en el selector de propietarios
    $('#selector-propietario').change(function() {
        // Si se ha seleccionado un propietario, ocultamos los campos para crear uno nuevo
        if ($(this).val()) {
            $('#contenedor-propietario').hide();
            
            // Remover el atributo 'required' de los campos ocultos
            $('#contenedor-propietario').find('input[required]').removeAttr('required');
        } else {
            $('#contenedor-propietario').show();
            
            // Añadir el atributo 'required' de nuevo a los campos cuando son visibles
            $('#nombre, #email, #telefono').attr('required', 'required');
        }
    }).trigger('change');  // Trigger inicial para ajustar la visualización en función de la selección actual


    /**
     * buscador en el select
    */
    $(document).ready(function() {
        $('#searchDemanda').on('input focus', function() {
            var searchText = $(this).val().toLowerCase();
            var visibleCount = 0;
            var maxVisible = 10;
    
            $('#demanda_id option').each(function() {
                var optionText = $(this).text().toLowerCase();
                if (optionText.includes(searchText) && visibleCount < maxVisible) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });
    
            var size = $('#demanda_id option:visible').length;
            $('#demanda_id').attr('size', size > 1 ? size : 2);
        });
    
        $('#demanda_id').blur(function() {
            $(this).attr('size', 1);
        });
    });

});
