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
            padding: 0px;
        }
        `;
        // Agrega el estilo al head del documento
        document.head.appendChild(style);
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
            url: 'services/products',
            data: {
                action: 'pos',
                draw: currentPage,         // Número de solicitud (lo mismo que DataTables)
                start: start,              // Índice del primer registro (inicio)
                length: pageSize,          // Cantidad de productos por página
                search: search,            // Término de búsqueda
                orderColumn: 0,            // Índice de la columna de ordenación (por ejemplo, 0 = nombre)
                orderDir: 'asc'            // Dirección de ordenación ('asc' o 'desc')
            },
            successCallback: (response) => {
                try {
                    const data = JSON.parse(response);

                    // Limpiar el grid antes de agregar los nuevos productos
                    const gridContainer = $('#product-grid');
                    gridContainer.empty();

                    // Agregar los productos a la cuadrícula
                    data.data.forEach(product => {
                        const productCard = `
                        <button class="product-card" action="button" data-product="${product.producto_id}" data-desc="${product.nombre_producto}">
                            <img src="${SITE_URL}public/imagen/"  onerror="this.onerror=null; this.src='${SITE_URL}public/imagen/sistem/no-imagen.png';" alt="">
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

                } catch (e) {
                    console.error("Error al analizar la respuesta JSON:", e);
                }
            },
            errorCallback: (res) => {
                console.error(res);
            }
        })
    }

    // Cargar productos por primera vez
    loadProductsPOS();

    // Manejar el evento de búsqueda 
    $('#search-input').on('input', function () {
        const searchValue = $(this).val().trim();
        currentPage = 1; // Volver a la primera página con el nuevo término de búsqueda
        loadProductsPOS(searchValue, currentPage); // Recargar la página 1 con el término de búsqueda
    });

    /**============================================================= 
     * CARGAR DETALLES
    ===============================================================*/

    // Función para cargar productos
    function loadDetailPOS() {
        sendAjaxRequest({
            url: 'services/products',
            data: {
                action: 'detalle_punto_de_venta'
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
                                <span class="item-name">${detail.descripcion}</span>
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

                } catch (e) {
                    console.error("Error al analizar la respuesta JSON:", e);
                }
            },
            errorCallback: (res) => {
                console.error(res);
                notifyAlert(res, 'error');
            },
            verbose: false
        })
    }

    // Cargar detalle por primera vez
    loadDetailPOS();


    /**============================================================= 
         * FUNCIONES DEL DETALLE POS
    ===============================================================*/

    // Agregar detalle
    $('#product-grid').on('click', '.product-card', function () {

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
                description: productName,
                quantity: 1,
                price: priceOut,
                cost: cost
            },
            successCallback: (res) => {

                calculateTotalInvoice(); // cargar total
                loadDetailPOS(); // Cargar detalle
                updateToListPrice(price_list, productId); // usar precio de lista
                loadProductsPOS()
            },
            errorCallback: (res) => {
                console.error(res);
                notifyAlert(res, 'error');

            },
            verbose: true
        });
    });

    // eliminar producto
    $('#pos-detail-item').on('click', '#item-delete', function (e) {
        e.preventDefault();

        const detalleId = $(this).data('delete');

        sendAjaxRequest({
            url: "services/invoices.php",
            data: {
                action: "eliminar_detalle_temporal",
                id: detalleId
            },
            successCallback: () => {
                loadDetailPOS();  // Recargar los detalles
                loadProductsPOS()
                calculateTotalInvoice();  // Recalcular el total de la factura
            },
            errorCallback: (res) => {
                console.error('Error al eliminar detalle:', res);
                notifyAlert(res, 'error');
            }
        });
    });

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
                product_id: productId
            },
            successCallback: (res) => {

                loadDetailPOS();  // Recargar los detalles
                calculateTotalInvoice();  // Recalcular el total de la factura
            },
            errorCallback: (res) => {
                console.error(res)
                notifyAlert(res, 'error');
            }
        });
    }

    // Editar item

    $('#pos-detail-item').on('click', '#item-edit', function (e) {
        e.preventDefault();

        // Mostrar ventana
        $('.pos-product-edit').css('display', 'block').css('right', '0');

        // Mostrar la ventana con el fondo oscuro
        $('.pos-product-edit').css('display', 'block').css('right', '0');  // Mostrar ventana deslizante desde la derecha
        $('.overlay').css('display', 'block');  // Mostrar la capa de fondo negro con transparencia
    });

    // Cerrar la ventana y la capa de fondo
    $('.overlay').click(function () {
        $('.pos-product-edit').css('right', '-100%');  // Ocultar la ventana deslizante
        $('.overlay').fadeOut(300);  // Ocultar la capa de fondo negro
    });

}); // Ready