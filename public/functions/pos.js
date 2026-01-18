$(document).ready(function () {

    let wsPOS = null;
    let wsConnected = false;

    function initPOSWebSocket() {

        const protocol = location.protocol === 'https:' ? 'wss://' : 'ws://';
        const wsURL = `${protocol}${location.host}/ws`;

        wsPOS = new WebSocket(wsURL);

        wsPOS.onopen = () => {
            console.log('✅ WS POS conectado:', wsURL);
            wsConnected = true;
        };

        wsPOS.onclose = () => {
            console.warn('⚠️ WS POS desconectado');
            wsConnected = false;
        };

        wsPOS.onerror = (err) => {
            console.error('❌ WS error', err);
            wsConnected = false;
        };

        wsPOS.onmessage = (e) => {
            try {
                const data = JSON.parse(e.data);
                console.log('📨 WS mensaje:', data);

                if (data.type === 'detalle_actualizado') {
                    loadDetailPOS();
                }

                if (data.type === 'orden_actualizada') {
                    loadOrdersPOS();
                }

            } catch (err) {
                console.error('❌ JSON inválido:', e.data);
            }
        };
    }



    //-------------------------------------

    // Cargar metodos de pagos
    function initMethodSelect2(selector, selectedId = null) {
        const $select = $(selector);

        $select.select2({
            placeholder: 'Selecciona un metodo',
            allowClear: true, // Permite limpiar la selección
            ajax: {
                url: SITE_URL + 'services/invoices.php',
                method: 'POST',
                dataType: 'json',
                data: params => ({
                    action: 'obtener_metodos_de_pago',
                    q: params.term || ''
                }),
                processResults: data => ({
                    results: data.results.map(m => ({
                        id: m.id,
                        text: m.nombre
                    }))
                })
            }
        });

        // Obtener nombre automáticamente por ID
        if (selectedId) {
            sendAjaxRequest({
                url: 'services/invoices.php',
                data: {
                    action: 'obtener_metodo_por_id',
                    id: selectedId
                },
                successCallback: (method) => {
                    const data = JSON.parse(method);
                    if (method) {
                        const option = new Option(data.text, data.id, true, true);
                        $select.append(option).trigger('change');
                    }
                }
            });
        }
    }

    function formatNumber(value) {
        if (value === null || value === undefined || value === '') return 0;
        let num = Number(value);
        return (num % 1 === 0) ? num.toFixed(0) : num;
    }

    /**============================================================= 
     * CARGAR ARTICULOS
    ===============================================================*/

    let currentPage = 1; // Página actual
    const pageSize = 15; // Cantidad de articulos por página

    // Función para cargar articulos
    function loadItemsPOS(search = '', page = 1) {
        const start = (page - 1) * pageSize; // Calcular el índice de inicio

        sendAjaxRequest({
            url: 'services/products.php',
            data: {
                action: 'obtener_articulos_pos',
                draw: currentPage, // Número de solicitud (lo mismo que DataTables)
                start: start, // Índice del primer registro (inicio)
                length: pageSize, // Cantidad de articulos por página
                search: search, // Término de búsqueda
                orderColumn: 0, // Índice de la columna de ordenación (por ejemplo, 0 = nombre)
                orderDir: 'asc' // Dirección de ordenación ('asc' o 'desc')
            },
            successCallback: (response) => {
                try {
                    const data = JSON.parse(response);

                    // Limpiar el grid antes de agregar los nuevos articulos
                    const gridContainer = $('#product-grid');
                    gridContainer.empty();

                    // Agregar los articulos a la cuadrícula
                    data.data.forEach(item => {

                        // Construir imagen si existe, de lo contrario mostrar ícono SVG
                        const productImage = item.imagen && item.imagen !== ""
                            ? `<img src="${SITE_URL}public/uploads/${item.imagen}" 
                                onerror="this.onerror=null; this.src='${SITE_URL}public/imagen/sistem/no-imagen.png';" 
                                alt="Imagen del item">`
                            : `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tags-icon lucide-tags">
                                <path d="M13.172 2a2 2 0 0 1 1.414.586l6.71 6.71a2.4 2.4 0 0 1 0 3.408l-4.592 4.592a2.4 2.4 0 0 1-3.408 0l-6.71-6.71A2 2 0 0 1 6 9.172V3a1 1 0 0 1 1-1z" />
                                <path d="M2 7v6.172a2 2 0 0 0 .586 1.414l6.71 6.71a2.4 2.4 0 0 0 3.191.193" />
                                <circle cx="10.5" cy="6.5" r=".5" fill="currentColor" />
                            </svg>`;

                        const productCard = `

                        <button class="product-card" action="button" data-${item.tipo}="${item.item_id}" data-desc="${item.nombre}">
                        ${item.codigo ? `<div class="item-cod">${item.codigo}</div>` : ''}
                        ${productImage}
                         
                            <div class="product-info">
                                ${item.tipo === 'servicio'
                                ? `<p class="pos-stock servicio">Servicio</p>`
                                : `<p class="pos-stock">inv. ${parseFloat(item.stock)}</p>`
                            }
                                <span>${item.nombre}</span>
                                <p class="pos-price">$${format.format(item.precio)}</p>
                            </div>
                            <input type="hidden" id="price_out" value="${item.precio}">
                                <input type="hidden" id="cost" value="${item.costo}">
                                </button>
                                `;
                        gridContainer.append(productCard);
                    });

                    // Hacer focus en el buscador
                    $('#search-input').trigger('focus');

                } catch (e) {
                    console.error("Error al analizar la respuesta JSON de los items:", e);
                }
            },
            errorCallback: (res) => {
                console.error(res);
            }
        })
    }


    // Manejar el evento de búsqueda 
    $('#search-input')
        .on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                currentPage = 1;
                loadItemsPOS($(this).val().trim(), currentPage);

                // Limpiar para siguiente escaneo
                $(this).val("");
            }
        })
        .on('input', function () {
            currentPage = 1;
            loadItemsPOS($(this).val().trim(), currentPage);
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

                        var cantidad = parseFloat(detail.cantidad) || 0;
                        var precio = parseFloat(detail.precio) || 0;
                        var impuesto = parseFloat(detail.impuesto) || 0; // impuesto por unidad
                        var descuento = parseFloat(detail.descuento) || 0; // descuento por unidad

                        var subtotal = cantidad * precio;
                        var descuentoTotal = cantidad * descuento;
                        var impuestoTotal = cantidad * impuesto;

                        var total = subtotal - descuentoTotal + impuestoTotal;

                        // Construir el contenido del precio con los impuestos y descuentos solo si son mayores que 0
                        var impuestoTexto = impuesto > 0 ? `/ + $${impuesto}` : '';
                        var descuentoTexto = descuento > 0 ? `/ - $${descuento}` : '';

                        const items = `
                        <div class="pos-item-row">
                            <div class="pos-item">
                                <span class="item-name">
                                    ${detail.nombre}
                                    <p>${cantidad} x $${precio} ${impuestoTexto} ${descuentoTexto}</p>
                                </span>
                                ${detail.cant_input}
                                <div class="item-crud">
                                    <span class="item-price">$${format.format(total)}</span>
                                    ${detail.acciones}
                                </div>
                            </div>
                        </div>
                        `;
                        gridContainer.append(items);
                    });


                    // cargar resumen de venta
                    calculateTotalInvoice();
                    loadItemsPOS($('#search-input').val());
                    loadOrdersPOS(); // Cargar ordenes

                    // Mostrar total de items en el detalle
                    var total_items = $('.pos-detail-item .pos-item-row').length;

                    if (total_items > 0) {
                        $('.pos-count-item').css('display', 'flex') // Total Items 
                        $('#cash_received').val('')
                        $('#cash_received').css('display', 'flex') // Dinero recibido
                        $('.pos-count-item p').text(total_items + ' Items');
                        $('.pos-button-cash').attr('disabled', false);
                        $('.pos-button-credit').attr('disabled', false);
                    } else {
                        $('.pos-count-item').hide() // Total Items 
                        $('#cash_received').hide() // Dinero recibido
                        $('.pos-button-cash').attr('disabled', true);
                        $('.pos-button-credit').attr('disabled', true);

                        // Detalle vacio
                        gridContainer.append(` <div class="pos-empty-content">
                        <h3><i class="fas fa-shopping-cart"></i></h3>
                        <p>Tu carrito está vacío</p>
                        <p>Selecciona productos para agregar al carrito</p>
                        </div>`)
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
        var productId = $(this).data('producto') || 0;
        var serviceId = $(this).data('servicio') || 0;
        var productName = $(this).data('desc');
        var priceOut = $(this).find('#price_out').val();
        var cost = $(this).find('#cost').val();

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "agregar_detalle_pos",
                product_id: productId || 0,
                piece_id: 0,
                service_id: serviceId || 0,
                description: productName,
                quantity: 1,
                price: priceOut,
                cost: cost,
                order_id: order_id
            },
            successCallback: (res) => {

                //loadDetailPOS(); // Cargar detalle
                updateToListPrice(price_list, productId); // usar precio de lista

            },
            errorCallback: (res) => {
                console.error(res);
                notifyAlert(res, 'error');

            }, verbose: false
        });
    });

    // eliminar producto
    $('#pos-detail-item').on('click', '#item-delete', function (e) {
        e.preventDefault();

        const id = $(this).data('delete');
        deleteItemPOS(id)
    });

    // eliminar producto en la ventana
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

                if (!wsConnected) {
                    // Fallback: WS no activo
                    loadDetailPOS();
                }
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
            },
            errorCallback: (res) => {
                console.error(res)
                notifyAlert(res, 'error');
            }, verbose: false
        });
    }

    // Guardar lista de precio seleccionada
    const STORAGE_KEY = "pos_list_price";
    const $listPrice = $("#list_price");

    // Restaurar valor guardado al cargar
    const savedListPrice = localStorage.getItem(STORAGE_KEY);
    if (savedListPrice !== null) {
        // Primero, agregar la opción con el nombre de la lista seleccionada antes de "General"
        const savedListName = $("#list_price option[value='" + savedListPrice + "']").text(); // Obtener el nombre de la lista seleccionada
        const selectedOptionHtml = `<option value="${savedListPrice}" selected>${savedListName}</option>`;

        // Insertar la nueva opción antes de "General"
        $listPrice.find('option[value="0"]').before(selectedOptionHtml);

        // Establecer el valor del select con el valor guardado
        $listPrice.val(savedListPrice);
    }

    // Guardar cuando cambia el select
    $listPrice.on("change", function () {
        localStorage.setItem(STORAGE_KEY, $(this).val());
    });

    // Actualizar cantidad
    $(document).on('click', '.qty-plus', function () {
        const input = $(this).siblings('.input-quantity');
        let val = parseInt(input.val()) || 0;

        input.val(val + 1).trigger('change');
        setTimeout(() => { loadDetailPOS(); }, 700);
    });

    $(document).on('click', '.qty-minus', function () {
        const input = $(this).siblings('.input-quantity');
        let val = parseInt(input.val()) || 0;
        const min = parseInt(input.attr('min')) || 0;

        val = val - 1;
        if (val < min) val = min;

        input.val(val).trigger('change');
        setTimeout(() => { loadDetailPOS(); }, 700);

    });

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
        $('.pos-config-window').css('right', '-100%'); // Ventana configuracion de factura
        $('.pos-order-window').css('right', '-100%'); // Ventana de informacion de la orden de compra
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
            $('#discount').val(formatNumber(data.descuento));
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
        const totalDiscount = quantity * base_discount; // descuento por unidad
        let totalTax;

        if (data) {
            totalTax = quantity * base_taxes;
        } else {
            totalTax = subtotalValue * base_taxes / 100;
        }

        const totalValue = subtotalValue - totalDiscount + totalTax;

        // Formatear
        const subtotal = format.format(subtotalValue);
        const discount = format.format(totalDiscount);
        const taxes = format.format(totalTax);
        const total = format.format(totalValue);

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
                    $('.d-flex span').eq(2).text(data.nombre_categoria || 'Sin categoria');

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
                        if (!wsConnected) {
                            // Fallback: WS no activo
                            loadDetailPOS();
                        }
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

        var base_price = parseFloat($('#base_price').val().replace(/,/g, "")) || 0;
        var final_price = parseFloat($('#final_price').val().replace(/,/g, "")) || 0;
        var total_taxes = final_price - base_price;

        // Evitar valores negativos
        if (total_taxes < 0) {
            total_taxes = 0;
        }

        const data = {
            action: "actualizar_detalle_pos",
            quantity: $('#quantity').val(),
            base_price: $('#base_price').val(),
            discount: $('#discount').val() || 0,
            taxes: total_taxes,
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
                //  loadDetailPOS(); // Cargar detalle
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
                notifyAlert(res, 'success')
                hiddenOverlay() // Ocultar ventana

            },
            errorCallback: (res) => {
                notifyAlert(res, 'error')
                console.error(res);
            },
            verbose: false
        })
    }

    // Cargar clientes
    function initClientSelect2(selector, selectedId = null) {
        const $select = $(selector);

        $select.select2({
            placeholder: 'Selecciona un cliente',
            allowClear: true, // Permite limpiar la selección
            ajax: {
                url: SITE_URL + 'services/contacts.php',
                method: 'POST',
                dataType: 'json',
                data: params => ({
                    action: 'obtener_clientes',
                    q: params.term || ''
                }),
                processResults: data => ({
                    results: data.results.map(c => ({
                        id: c.id,
                        text: c.nombre + (c.apellidos ? ' ' + c.apellidos : '')
                    }))
                })
            }
        });

        // Obtener nombre automáticamente por ID
        if (selectedId) {
            sendAjaxRequest({
                url: 'services/contacts.php',
                data: {
                    action: 'obtener_cliente_por_id',
                    id: selectedId
                },
                successCallback: (client) => {
                    const data = JSON.parse(client);
                    if (client) {
                        const option = new Option(data.text, data.id, true, true);
                        $select.append(option).trigger('change');
                    }
                }
            });
        }
    }


    /**============================================================= 
     * FUNCIONES DE LAS ORDENES
    ===============================================================*/

    // Función para cargar las órdenes
    function loadOrdersPOS() {
        var selectedOrderId = $('#order_id').val()

        if (selectedOrderId == 0 || selectedOrderId == '') {
            $('.btn-pos_home').css('border', '1px solid var(--color-primary)')
        } else {
            $('.btn-pos_home').css('border', '1px solid #ccc')
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
                            id="order${element.comanda_id}" data-order="${element.comanda_id}" data-client-id="${element.cliente_id}"
                            ${selectedOrderId == element.comanda_id ? 'checked' : ''}>

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
            }
        });
    }

    //  Cambiar de orden
    $(document).on('click', '.order-item', function () {
        const radio = $(this).find('.order-radio');

        // Marcar el radio
        radio.prop('checked', true).trigger('change');

        // Obtener el data-order
        const orderId = radio.data('order');
        $("#pos-print-order").css('display', 'inline-block'); // Boton imprimir
        $("#pos-order-update").css('display', 'inline-block'); // Boton datos de la orden

        $('#order_id').val(orderId);

        // Obtener el ID del cliente
        const customerId = radio.data('client-id')

        initClientSelect2("#customer_id", customerId)
        loadDetailPOS();

    });


    // Ir a facturacion sin orden
    $('.btn-pos_home').on('click', function () {

        // Quitar datos de las ordenes
        $('#order_id').val('');
        const radio = $('.order-item').find('.order-radio');
        radio.prop('checked', false).trigger('change');

        // Botones del sidebar
        $('#pos-print-order').css('display', 'none')
        $('#pos-order-update').css('display', 'none')

        // Cargar detalle
        loadDetailPOS();
    });


    // Abrir ventan nueva orden
    $('.btn-add_order').on('click', function () {

        // Limpiar los campos de texto
        $('#pos_direction').val('');
        $('#pos_fullname').val('');
        $('#pos_tel').val('');
        $('#pos_comment').val('');

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
                    notifyAlert('Orden creada correctamente', 'success', 1500)
                    if (!wsConnected) {
                        loadOrdersPOS();
                    }

                    hiddenOverlay() // Ocultar ventana
                }

            },
            errorCallback: (res) => {
                console.error(res)
            }
        })
    })

    /**============================================================= 
    *  VENTANA CONFIGURACION DE FACTURACION
    ===============================================================*/

    $('#pos-config').on('click', function () {

        $('.pos-config-window').css('display', 'block').css('right', '0'); // Mostrar ventana deslizante desde la derecha
        $('.overlay').css('display', 'block'); // Mostrar la capa de fondo negro con transparencia
    });

    /**============================================================= 
    *  VENTANA DATOS DE LA ORDEN DE COMPRA
    ===============================================================*/

    $('#pos-order-update').on('click', function () {

        $('.pos-order-window').css('display', 'block').css('right', '0'); // Mostrar ventana deslizante desde la derecha
        $('.overlay').css('display', 'block'); // Mostrar la capa de fondo negro con transparencia

        var orderId = $('#order_id').val() || 0;

        getOrderInfo(orderId);
    });

    // Obtener datos de la orden
    function getOrderInfo(orderId) {

        const data = {
            action: "obtener_orden_info_pos",
            order_id: orderId,
        };

        sendAjaxRequest({
            url: "services/invoices.php",
            data: data,
            successCallback: (order) => {

                try {
                    var data = JSON.parse(order)[0];

                    // Cargar datos
                    initClientSelect2('#pos_edit_customer', data.cliente_id);
                    $('#pos_edit_direction').val(data.direccion_entrega);
                    $('#pos_edit_fullname').val(data.nombre_receptor);
                    $('#pos_edit_tel').val(data.telefono_receptor);
                    $('#pos_edit_comment').val(data.observacion);

                    const select = $('#pos_edit_delivery');

                    let entrega = (data.tipo_entrega || '-')
                        .toString()
                        .trim()
                        .toLowerCase();

                    // 🔎 si no existe, lo crea
                    if (select.find('option[value="' + entrega + '"]').length === 0) {
                        select.append(
                            $('<option>', {
                                value: entrega,
                                text: entrega === '-' ? 'Ninguno' : entrega.charAt(0).toUpperCase() + entrega.slice(1)
                            })
                        );
                    }

                    // ✅ seleccionar
                    select.val(entrega).trigger('change');


                } catch (error) {
                    console.error("Error al obtener datos en la venta: ", error)
                }

            },
            errorCallback: (error) => {
                console.error(error)
            }
        })
    }

    // Actualizar datos

    $('#updateOrderForm').on('submit', function (e) {
        e.preventDefault();

        const data = {
            action: "editar_orden",
            order_id: $('#order_id').val() || 0,
            customer_id: $('#pos_edit_customer').val(),
            name: $('#pos_edit_fullname').val(),
            tel: $('#pos_edit_tel').val(),
            direction: $('#pos_edit_direction').val(),
            observation: $('#pos_edit_comment').val(),
            delivery: $('#pos_edit_delivery').val()
        }

        sendAjaxRequest({
            url: "services/invoices.php",
            data: data,
            successCallback: (res) => {
                if (res === "ready") {

                    if (!wsConnected) {
                        loadOrdersPOS();
                    }

                    hiddenOverlay() // Ocultar ventana
                    notifyAlert("Datos actualizados correctamente", "success", 1500)
                } else {
                    notifyAlert("Ha ocurrido un problema", "error")
                }

            },
            errorCallback: (e) => console.error(e),
            verbose: false
        });


    })

    /**============================================================= 
    * FACTURACION E IMPRESION
    ===============================================================*/

    // Factura al contando
    $('.pos-button-cash').on('click', function () {
        const data = {
            // Datos para la factura
            action: "factura_contado_pos",
            order_id: $('#order_id').val() || 0,
            customer_id: $('#customer_id').val(),
            method_id: $('#method_id').val(),
            total_invoice: parseFloat($('#total_pos').val()),
            cash_received: $('#cash_received').val() || 0
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

                if (res > 0) {

                    // Dinero a devolver
                    if (!isNaN(data.cash_received) && Number(data.cash_received) > 0) {

                        const total = Number(data.total_invoice) || 0;
                        const recibido = Number(data.cash_received) || 0;

                        const cashback = recibido - total;

                        if (cashback > 0) {
                            cashBack(cashback, 15000);
                        }
                    }

                    $('.pos-button-cash').attr('disabled', true); // Desactivar boton
                    notifyAlert("Registro exitoso", "success", 1500)
                    $('#order_id').val('') // quitar orden
                    loadDetailPOS()
                    initMethodSelect2('#method_id', 1)
                    printerInvoicePOS(res) // Imprimir
                } else {
                    notifyAlert("A ocurrido un error", "error");
                }

            },
            errorCallback: (res) => {
                console.error(res)
                notifyAlert(res, 'error')
            }, verbose: false
        });
    })

    // Factura a credito
    $('#invoiceCredit').on('submit', function (e) {
        e.preventDefault();

        const data = {
            // Datos para la factura
            action: "factura_credito_pos",
            order_id: $('#order_id').val() || 0,
            customer_id: $('#modal-customer_id').val(),
            method_id: $('#modal-method_id').val(),
            total_invoice: parseFloat($('#total_pos').val()),
            pay: $('#modal-pay').val() || 0,
            date: $('#modal-date').val()
        };

        sendAjaxRequest({
            url: "services/invoices.php",
            data: data,
            successCallback: (res) => {

                if (res > 0) {

                    $('#pos-credit').modal('hide'); // cerrar modal
                    notifyAlert("Registro exitoso", "success", 1500)
                    $('#order_id').val('') // quitar orden
                    loadDetailPOS()
                    printerInvoicePOS(res) // Imprimir
                } else {
                    notifyAlert("A ocurrido un error", "error");
                }

            },
            errorCallback: (res) => {
                console.error(res)
                notifyAlert(res, 'error')
            }, verbose: false
        });
    })


    /**
     * Envía una factura de venta al servidor de impresión POS
     * Obtiene los datos de la factura vía AJAX y los envía al servicio de impresión
     *
     * @param {number} invoice_id - ID de la factura de venta
     * @returns {void}
     */
    function printerInvoicePOS(invoice_id) {

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                id: invoice_id,
                action: "devolver_datos_impresion"
            },
            successCallback: (response) => {

                var data = typeof response === 'string' ? JSON.parse(response) : response;

                const dataInv = {
                    customer: data.datos.nombre,
                    seller: data.datos.usuario,
                    payment_method: data.datos.nombre_metodo,
                    invoice_id: data.datos.factura_venta_id,
                    subtotal: data.datos.subtotal || 0,
                    discount: data.datos.total_descuento || 0,
                    taxes: data.datos.total_impuesto || 0,
                    total: data.datos.total || 0,
                    received: data.datos.recibido,
                    pending: data.datos.pendiente,
                    date: data.datos.fecha,
                    observation: data.datos.descripcion
                };

                printer(dataInv, JSON.stringify(data.detalle));
            },
            verbose: false
        });

        // Funcion de imprimir
        function printer(dataInv, detail) {
            $.ajax({
                type: "POST",
                url: PRINTER_SERVER + "factura_venta.php",
                data: {
                    detail: detail,
                    data: dataInv
                },
                dataType: "json",
                success: function (res) {
                    if (res.status === "success") {
                        console.log('✅ Impresión completada con exito: ', res.data);
                    } else {
                        console.error('❌ Error:', res.error || 'Desconocido');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('🚨 Error AJAX:', status, error);
                    console.error(xhr.responseText);
                }
            });
        }
    }


    /**
     * Evento para imprimir la orden de venta.
     * Escucha el click en el botón con id "printOrder", obtiene los datos de la orden
     * y envía la información al servidor de impresión.
     */
    $('#pos-print-order').on('click', (e) => {
        e.preventDefault();

        // Solicita los detalles de la orden de venta al servidor
        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "obtener_detalle_orden",
                orderId: $('#order_id').val()
            },
            successCallback: (res) => {
                const detail = JSON.parse(res)[0]; // Detalle de los productos/piezas/servicios
                const data = JSON.parse(res)[1]; // Información general de la orden
                const total = JSON.parse(res)[2]; // Totales

                const totals = {
                    subtotal: total.subtotal,
                    discount: total.total_descuento,
                    taxes: total.total_impuesto,
                    total: total.total,
                    orderId: $('#order_id').val()
                };

                // Envía los datos a la impresora
                printOrder(detail, data, totals);
            }
        });

        /**
         * Envía la orden de venta al servidor de impresión.
         * @param {Object} detail - Lista de ítems de la orden.
         * @param {Object} orderData - Información general de la orden.
         * @param {Object} orderTotal - Totales de la orden (subtotal, descuento, impuestos, total).
         */
        function printOrder(detail, orderData, orderTotal) {

            $.ajax({
                type: "POST",
                url: PRINTER_SERVER + "orden_venta.php",
                data: JSON.stringify({
                    detail: detail,
                    data: orderData,
                    totals: orderTotal
                }),
                contentType: "application/json", // Enviamos JSON
                dataType: "json", // Esperamos JSON de respuesta
                success: function (res) {
                    if (res.status === "success") {
                        console.log("✅ Impresión completada:", res.message);
                        notifyAlert(res.message || "Ticket impreso correctamente.")
                    } else {
                        console.warn("⚠️ Error en impresión:", res.message);
                        notifyAlert(res.message || "Error al imprimir el ticket.", "error");
                    }
                },
                error: function (xhr, status, error) {
                    console.error("❌ Error AJAX:", error);
                    alertify.error("No se pudo conectar con el servidor de impresión.");
                }
            });
        }
    });



    /**============================================================= 
    * INICIAR FUNCIONES
    ===============================================================*/

    initPOSWebSocket(); // Websocket

    loadItemsPOS();  // Cargar productos por primera vez
    loadDetailPOS() // Cargar detalle
    loadOrdersPOS(); // Cargar ordenes

    initClientSelect2('#customer_id', 1);
    initClientSelect2('#pos_customer_id');
    initClientSelect2('#modal-customer_id', 1);

    initMethodSelect2('#method_id', 1)

}); // Ready