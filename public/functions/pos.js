$(document).ready(function () {

    // Verifica la URL
    if (window.location.href.includes('invoices/pos')) {
        // Crea un nuevo elemento de estilo
        const style = document.createElement('style');
        style.innerHTML = `
        .container-logo,
        .sidebar {
            display: none !important;
        }

        .wrap {
            width: 100%;
          
        }
        `;
        // Agrega el estilo al head del documento
        document.head.appendChild(style);
    }

    function formatNumber(value) {
        if (value === null || value === undefined || value === '') return 0;
        let num = Number(value);
        return (num % 1 === 0) ? num.toFixed(0) : num;
    }


    /**============================================================= 
     * CARGAR PRODUCTOS
    ===============================================================*/

    let currentPage = 1; // Página actual
    const pageSize = 10; // Cantidad de productos por página

    // Función para cargar productos
    function loadProductsPOS(search = '', page = 1) {
        const start = (page - 1) * pageSize; // Calcular el índice de inicio

        sendAjaxRequest({
            url: 'services/products.php',
            data: {
                action: 'pos',
                draw: currentPage, // Número de solicitud (lo mismo que DataTables)
                start: start, // Índice del primer registro (inicio)
                length: pageSize, // Cantidad de productos por página
                search: search, // Término de búsqueda
                orderColumn: 0, // Índice de la columna de ordenación (por ejemplo, 0 = nombre)
                orderDir: 'asc' // Dirección de ordenación ('asc' o 'desc')
            },
            successCallback: (response) => {
                try {
                    const data = JSON.parse(response);

                    // Limpiar el grid antes de agregar los nuevos productos
                    const gridContainer = $('#product-grid');
                    gridContainer.empty();

                    // Agregar los productos a la cuadrícula
                    data.data.forEach(product => {

                        // Construir imagen si existe, de lo contrario mostrar ícono SVG
                        const productImage = product.imagen && product.imagen !== ""
                            ? `<img src="${SITE_URL}public/uploads/${product.imagen}" 
                                onerror="this.onerror=null; this.src='${SITE_URL}public/imagen/sistem/no-imagen.png';" 
                                alt="Imagen del producto">`
                            : `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags-icon lucide-tags">
                                <path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z" />
                                <path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193" />
                                <circle cx="10.5" cy="6.5" r=".5" fill="currentColor" />
                            </svg>`;

                        const productCard = `

                        <button class="product-card" action="button" data-product="${product.producto_id}" data-desc="${product.nombre_producto}">
                             ${productImage}
                            <div class="product-info">
                                <p class="pos-stock">inv. ${parseFloat(product.cantidad)}</p>
                                <span>${product.nombre_producto}</span>
                                <p class="pos-price">$${product.precio_unitario}</p>
                            </div>
                            <input type="hidden" id="price_out" value="${product.precio_unitario}">
                                <input type="hidden" id="cost" value="${product.precio_costo}">
                                </button>
                                `;
                        gridContainer.append(productCard);
                    });

                    // Hacer focus en el buscador
                    $('#search-input').trigger('focus');

                } catch (e) {
                    console.error("Error al analizar la respuesta JSON del producto:", e);
                }
            },
            errorCallback: (res) => {
                console.error(res);
            }
        })
    }


    // Manejar el evento de búsqueda 
    $('#search-input').on('input', function () {
        const searchValue = $(this).val().trim();
        currentPage = 1; // Volver a la primera página con el nuevo término de búsqueda
        loadProductsPOS(searchValue, currentPage); // Recargar la página 1 con el término de búsqueda
    });

    /**============================================================= 
     * CARGAR DETALLES
    ===============================================================*/

    // Función para mostrar los items del detalle
    function loadDetailPOS() {
        sendAjaxRequest({
            url: 'services/products.php',
            data: {
                action: 'detalle_punto_de_venta',
                order_id: $('#order_id').val() || 0
            },
            successCallback: (response) => {
                try {
                    const data = JSON.parse(response);

                    // Limpiar el grid antes de agregar los nuevos productos
                    const gridContainer = $('#pos-detail-item');
                    gridContainer.empty();

                    // Agregar los productos a la cuadrícula
                    data.data.forEach(detail => {

                        // importe
                        var total = (parseFloat(detail.cantidad) * parseFloat(detail.precio));

                        const items = `
                        <div class="pos-item-row">
                            <div class="pos-item">
                                <span class="item-name">${detail.nombre}</span>
                                ${detail.cant_input}
                                <div class="item-crud">
                                    <span class="item-price">$${total}</span>
                                    ${detail.acciones}
                                </div>
                            </div>
                        </div>
                        `;
                        gridContainer.append(items);
                    });

                    // cargar resumen de venta
                    calculateTotalInvoice();
                    loadProductsPOS($('#search-input').val());
                    loadOrdersPOS(); // Cargar ordenes

                    // Mostrar total de items en el detalle
                    var total_items = $('.pos-detail-item .pos-item-row').length;

                    if (total_items > 0) {
                        $('.pos-count-item').css('display', 'flex')
                        $('.pos-count-item p').text(total_items + ' Items');
                        $('.pos-button-cash').attr('disabled', false);
                    } else {
                        $('.pos-count-item').hide()
                        $('.pos-button-cash').attr('disabled', true);
                    }

                } catch (e) {
                    console.error("Error al analizar la respuesta JSON del detalle:", e);
                }
            },
            errorCallback: (res) => {
                console.error(res);
                notifyAlert(res, 'error');
            }
        })
    }

    /**============================================================= 
         * FUNCIONES DEL DETALLE POS
    ===============================================================*/

    // Agregar detalle
    $('#product-grid').on('click', '.product-card', function () {

        var order_id = $('#order_id').val() || 0;
        var price_list = $('#list_price').val();
        var productId = $(this).data('product');
        var productName = $(this).data('desc');
        var priceOut = $(this).find('#price_out').val();
        var cost = $(this).find('#cost').val();

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "agregar_detalle_pos",
                product_id: productId,
                piece_id: 0,
                service_id: 0,
                description: productName,
                quantity: 1,
                price: priceOut,
                cost: cost,
                order_id: order_id
            },
            successCallback: (res) => {

                loadDetailPOS(); // Cargar detalle
                updateToListPrice(price_list, productId); // usar precio de lista
                // loadProductsPOS() // Cargar productos
                // loadOrdersPOS() // Cargar ordenes

            },
            errorCallback: (res) => {
                console.error(res);
                notifyAlert(res, 'error');

            },
            verbose: false
        });
    });

    // eliminar producto
    $('#pos-detail-item').on('click', '#item-delete', function (e) {
        e.preventDefault();

        const id = $(this).data('delete');
        deleteItemPOS(id)
    });

    $('#erase_window_item').on('click', function (e) {
        e.preventDefault();

        const id = $('#windowId').val();
        deleteItemPOS(id);
        hiddenOverlay(); // Cerrar ventana
    });

    function deleteItemPOS(detailId) {

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "eliminar_detalle_venta",
                id: detailId
            },
            successCallback: () => {
                loadDetailPOS(); // Recargar los detalles
                // loadProductsPOS();
                // loadOrdersPOS(); // Cargar ordenes
                // calculateTotalInvoice(); // Recalcular el total de la factura
            },
            errorCallback: (res) => {
                console.error('Error al eliminar detalle:', res);
                notifyAlert(res, 'error');
            }
        });
    }

    // Cambiar precio
    $("#list_price").change(function () {
        const list_id = $(this).val();
        updateToListPrice(list_id);

    });

    function updateToListPrice(listId, productId = 0) {
        sendAjaxRequest({
            url: "services/price_lists.php",
            data: {
                action: "actualizar_precios_pos",
                list_id: listId,
                order_id: $('#order_id').val() || 0,
                product_id: productId
            },
            successCallback: (res) => {

                loadDetailPOS(); // Recargar los detalles
                //calculateTotalInvoice(); // Recalcular el total de la factura
            },
            errorCallback: (res) => {
                console.error(res)
                notifyAlert(res, 'error');
            }, verbose: false
        });
    }

    /**============================================================= 
    * VENTANA DE EDITAR
    ===============================================================*/

    // Cerrar la ventana y la capa de fondo
    $('.overlay, #close-window,#cancel-window').click(function () {
        hiddenOverlay();
    });

    function hiddenOverlay() {
        $('.pos-product-edit').css('right', '-100%'); // Ocultar la ventana deslizante
        $('.pos-customer-add').css('right', '-100%'); // Ventana agregar cliente
        $('.pos-order-add').css('right', '-100%'); // Ventana agregar order
        $('.overlay').fadeOut(300); // Ocultar la capa de fondo negro
    }

    // Calcular resumen de la venta editar
    function windowSummary(data) {

        let quantity;
        let base_price;
        let base_discount;
        let base_taxes;
        let final_price;

        // validar si los datos viene del servidor o de un input
        if (data) {
            $('#quantity').val(formatNumber(data.cantidad));
            $('#base_price').val(formatNumber(data.precio));
            $('#discount').val('');
            $('#final_price').val(formatNumber(data.precio));

            // Convertir valores numéricos
            quantity = parseFloat(data.cantidad) || 0;
            base_price = parseFloat(data.precio) || 0;
            base_discount = parseFloat(data.descuento) || 0;
            base_taxes = parseFloat(data.impuesto) || 0;
        } else {
            quantity = Number($('#quantity').val()) || 0;
            base_price = Number($('#base_price').val()) || 0;
            base_discount = Number($('#discount').val()) || 0;
            base_taxes = Number($('#tax_id').val()) || 0;
            final_price = Number($('#final_price').val()) || 0;
        }

        // Cálculos
        const subtotalValue = quantity * base_price;
        const totalTax = subtotalValue * base_taxes / 100;
        const totalValue = subtotalValue - base_discount + totalTax;

        // Formatear
        const subtotal = format.format(subtotalValue);
        const discount = format.format(base_discount);
        const total = format.format(totalValue);
        const taxes = format.format(totalTax);


        // Insertar valores en los span del HTML
        if (base_discount > 0) {
            $('#row-discount').css('display', 'flex')
            $('.item-discount').text('$' + discount);
        } else {
            $('#row-discount').css('display', 'none')
        }

        if (totalTax > 0) {
            $('#row-tax').css('display', 'flex')
            $('.item-taxes').text('$' + taxes);
        } else {
            $('#row-tax').css('display', 'none')
        }

        $('.item-subtotal').text('$' + subtotal);
        $('.item-total').text('$' + total);
    }

    // Editar item
    $('#pos-detail-item').on('click', '#item-edit', function (e) {
        e.preventDefault();

        var detailId = $(this).data('edit');
        $('#windowId').val(detailId);

        // Mostrar la ventana con el fondo oscuro
        $('.pos-product-edit').css('display', 'block').css('right', '0'); // Mostrar ventana deslizante desde la derecha
        $('.overlay').css('display', 'block'); // Mostrar la capa de fondo negro con transparencia

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "datos_detalle_id",
                detail_id: detailId
            },
            successCallback: (res) => {
                try {
                    const data = JSON.parse(res)[0];

                    // Cambiar todos los textos de los spans dentro de .d-flex
                    $('.d-flex span').eq(0).text(data.tipo_item);
                    $('.d-flex span').eq(1).text(data.item);
                    $('.d-flex span').eq(2).text('Sin categoria');

                    const productImage = data.imagen && data.imagen !== ""
                        ? `<img src="${SITE_URL}public/uploads/${data.imagen}" 
                                onerror="this.onerror=null; this.src='${SITE_URL}public/imagen/sistem/no-imagen.png';" 
                                alt="Imagen del producto">`
                        : `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags-icon lucide-tags">
                                <path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z" />
                                <path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193" />
                                <circle cx="10.5" cy="6.5" r=".5" fill="currentColor" />
                            </svg>`;

                    $('.item-thumb').html(productImage);


                    $('#w_product_id').val(data.producto_id);
                    $('#w_piece_id').val(data.pieza_id);
                    $('#w_service_id').val(data.servicio_id);

                    // Mostrar datos del item
                    windowSummary(data);

                } catch (e) {
                    console.error('Error al cargar datos del detalle_id', e)
                }
            },
            errorCallback: (res) => {
                console.error(res)
            },
            verbose: false
        });
    });

    // Calcular resumen de la venta al salir de un input
    $('#quantity, #discount').on('blur', function () {
        windowSummary();
    });

    // Borrar todo el detalle 
    $('.pos-count-item').on('click', function () {

        alertify.confirm("<i class='text-warning fas fa-exclamation-circle'></i> Borrar todo el detalle", "¿Desea borrar todo el detalle? ",
            function () {
                sendAjaxRequest({
                    url: "services/invoices.php",
                    data: {
                        action: "borrar_detalle_pos",
                        order_id: $('#order_id').val() || 0
                    },
                    successCallback: (res) => {
                        loadDetailPOS();
                    },
                    errorCallback: (res) => {
                        console.error(res)
                    },
                    verbose: false
                });

            },
            function () {

            });
    });

    // Actualizar detalle
    $('#updatePosItem').on('click', function () {

        const data = {
            action: "actualizar_detalle_pos",
            quantity: $('#quantity').val(),
            final_price: $('#final_price').val(),
            discount: $('#discount').val() || 0,
            detail_id: $('#windowId').val(),
            product_id: $('#w_product_id').val() || 0,
            piece_id: $('#w_piece_id').val() || 0,
            service_id: $('#w_service_id').val() || 0

        };

        sendAjaxRequest({
            url: "services/invoices.php",
            data: data,
            successCallback: (res) => {

                notifyAlert(res, 'success');
                windowSummary(); // Calcular ventana editar
                loadDetailPOS(); // Cargar detalle
            },
            errorCallback: (res) => {
                console.error(res)
                notifyAlert(res, 'error')
            }
        });
    });


    // Calcular el precio final
    $('#tax_id').change(function () {
        var tax_value = $(this).val();
        calcFinalPrice(tax_value)
    })

    $('#base_price').blur(function () {
        var tax_value = $('#tax_id').val();
        calcFinalPrice(tax_value)
    })

    function calcFinalPrice(tax_value = 0) {

        const base_price = parseFloat($('#base_price').val());

        // Calcular el impuesto
        const tax_amount = (base_price * tax_value) / 100;

        // Calcular el precio final
        const final_price = base_price + tax_amount;

        // Mostrar el precio final
        $('#final_price').val(final_price);
        // Calcular todo
        windowSummary();
    }

    // Verificar el stock del item
    $('#quantity').keyup(function () {
        var quantity = $(this).val();

        console.log('cantidad introducida ', quantity);
    })

    /**============================================================= 
   * VENTANA DE CLIENTE
   ===============================================================*/

    $('#pos-add_customer').on('click', function () {

        $('.pos-customer-add').css('display', 'block').css('right', '0'); // Mostrar ventana deslizante desde la derecha
        $('.overlay').css('display', 'block'); // Mostrar la capa de fondo negro con transparencia
    });

    //  Crear cliente
    $('#contactForm').on('submit', function (e) {
        e.preventDefault();
        addCustomerPOS();
    });

    function addCustomerPOS() {
        sendAjaxRequest({
            url: "services/contacts.php",
            data: {
                name: $('#name').val(),
                lastname: $('#lastname').val(),
                address: $('#select2-address-container').attr('title'),
                identity: $('#identity').val(),
                tel1: $('#tel1').val(),
                tel2: $('#tel2').val(),
                email: $('#email').val(),
                type: "cliente",
                action: 'crear_contacto'
            },
            successCallback: (res) => {
                $('input[type="text"], input[type="number"]').val('');
                notifyAlert(res, 'success')

            },
            errorCallback: (res) => {
                notifyAlert(res, 'error')
                console.error(res);
            },
            verbose: false
        })
    }

    // Cargar clientes
    $('#customer_id, #pos_customer_id').select2({
        placeholder: 'Selecciona un cliente',
        allowClear: true, // Permite limpiar la selección
        ajax: {
            url: SITE_URL + 'services/contacts.php',
            dataType: 'json',
            method: 'POST',
            data: function (params) {
                return {
                    action: 'obtener_clientes', // Acción que identificarás en el backend
                    q: params.term // Aquí puedes pasar el término de búsqueda si lo deseas
                };
            },
            processResults: function (data) {

                // Ajuste: Acceder a la propiedad correcta (nombre) en vez de "name"
                return {
                    results: data.results.map(function (client) {
                        return {
                            id: client.id, // El id del cliente
                            text: client.nombre + (client.apellidos ? ' ' + client.apellidos : '') // Nombre completo
                        };
                    })
                };
            }
        }
    });


    /**============================================================= 
     * FUNCIONES DE LAS ORDENES
    ===============================================================*/

    // Función para cargar las órdenes
    function loadOrdersPOS() {
        var selectedOrderId = $('#order_id').val()

        if (selectedOrderId == 0 || selectedOrderId == '') {
            $('.btn-pos_home').css('border','1px solid var(--color-primary)')
        } else {
             $('.btn-pos_home').css('border','1px solid #ccc')
        }

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "cargar_ordenes_pos"
            },
            successCallback: (res) => {
                try {
                    const data = JSON.parse(res);

                    // Limpiar el grid antes de agregar los nuevos productos
                    const gridContainer = $('.sidebar_order');
                    gridContainer.empty();

                    // Agregar los productos a la cuadrícula
                    data.data.forEach(element => {

                        const items = `
                    <div class="order-item">
                        <input type="radio" class="order-radio" name="order_select"
                            id="order${element.comanda_id}" data-order="${element.comanda_id}"
                            ${selectedOrderId == element.comanda_id ? 'checked' : ''}>  <!-- Marcar si es la orden seleccionada -->

                        <span>${element.total_items}</span>
                        <i class="fas fa-shopping-basket"></i>

                        <label for="order${element.comanda_id}" class="order-radio-label">
                            ${element.nombre}
                        </label>
                    </div>
                    `;
                        gridContainer.append(items);
                    });

                } catch (e) {
                    console.error("Error al analizar la respuesta JSON:", e);
                }
            },
            errorCallback: (res) => {
                console.error(res);
                notifyAlert(res);
            },
            verbose: true
        });
    }

    //  Cambiar de orden
    $(document).on('click', '.order-item', function () {
        const radio = $(this).find('.order-radio');

        // Marcar el radio
        radio.prop('checked', true).trigger('change');

        // Obtener el data-order
        const orderId = radio.data('order');

        $('#order_id').val(orderId);
        loadDetailPOS();

        console.log('Orden seleccionada:', orderId);

    });


    // Ir a facturacion sin orden
    $('.btn-pos_home').on('click', function () {

        // Quitar datos de las ordenes
        $('#order_id').val('');
        const radio = $('.order-item').find('.order-radio');
        radio.prop('checked', false).trigger('change');

        // Cargar detalle
        loadDetailPOS();
    });


    // Abrir ventan nueva orden
    $('.btn-add_order').on('click', function () {

        $('.pos-order-add').css('display', 'block').css('right', '0'); // Mostrar ventana deslizante desde la derecha
        $('.overlay').css('display', 'block'); // Mostrar la capa de fondo negro con transparencia
    })

    // Registrar nueva orden
    $('#orderForm').on('submit', function (e) {
        e.preventDefault();

        const data = {
            action: "registrar_orden",
            customer_id: $('#pos_customer_id').val(),
            name: $('#pos_fullname').val(),
            tel: $('#pos_tel').val(),
            direction: $('#pos_direction').val(),
            observation: $('#pos_comment').val(),
            delivery: $('#pos_delivery').val()
        }

        sendAjaxRequest({
            url: "services/invoices.php",
            data: data,
            successCallback: (res) => {

                if (res > 0) {
                    $('input[type="text"]').val('');
                    notifyAlert('Orden creada correctamente','success',1500)
                    loadOrdersPOS();
                }

            },
            errorCallback: (res) => {
                console.error(res)
            }
        })
    })


    /**============================================================= 
    * FACTURACION
    ===============================================================*/

    $('.pos-button-cash').on('click', function () {

        const data = {
            // Datos del ticket
            // customer: $('#select2-cash-in-customer-container').attr('title'),
            // seller: $('#cash-in-seller').val(),
            // payment_method: $('#select2-cash-in-method-container').attr('title'),
            // subtotal: parseFloat($('#in-subtotal').val().replace(/,/g, "")) || 0,
            // discount: parseFloat($('#in-discount').val().replace(/,/g, "")) || 0,
            // taxes: parseFloat($('#in-taxes').val().replace(/,/g, "")) || 0,
            // total: parseFloat($('#in-total').val().replace(/,/g, "")) || 0,
            // bonus: parseFloat($('#cash-bonus').val().replace(/,/g, "")) || 0,
            // date: $('#cash-in-date').val(),
            // observation: $('#observation').val(),

            // Datos para la factura
            action: "factura_contado_pos",
            order_id: $('#order_id').val() || 0,
            customer_id: $('#customer_id').val(),
            method_id: $('#method_id').val(),
            total_invoice: parseFloat($('#total_pos').val()),
        };

        // Validación rápida
        if (!data.customer_id || !data.method_id) {
            notifyAlert("Completa todos los datos obligatorios.", 'warning');
            // Limpiar los bordes de los campos antes
            $('.v_customer, .v_method').css('border', ''); // Limpiar cualquier borde previo

            // Cambiar el borde a rojo para los campos vacíos
            if (!data.customer_id) {
                $('.v_customer').css('border', '1px solid red');
            }
            if (!data.method_id) {
                $('.v_method').css('border', '1px solid red');
            }
            return;
        }

        // Si los datos son válidos, limpiar el borde (si es necesario)
        $('.v_customer, .v_method').css('border', '');

        sendAjaxRequest({
            url: "services/invoices.php",
            data: data,
            successCallback: (res) => {

                // Desactivar boton
                $('.pos-button-cash').attr('disabled', true);

                mysql_row_affected()
                $('#order_id').val('') // quitar orden
                loadDetailPOS()

            },
            errorCallback: (res) => {
                console.error(res)
                notifyAlert(res, 'error')
            }
        });
    })


    /**============================================================= 
    * INICIAR FUNCIONES
    ===============================================================*/

    // Cargar productos por primera vez
    loadProductsPOS();
    loadDetailPOS()
    loadOrdersPOS();


}); // Ready