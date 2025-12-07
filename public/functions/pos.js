$(document).ready(function() {

    // Verifica la URL
    if (window.location.href.includes('invoices/pos')) {
        console.log('modo POS activo');


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


    var elem = document.querySelector('.products-wrap');
    var msnry = new Masonry(elem, {
        itemSelector: '.product-item',
        columnWidth: '.product-item',
        percentPosition: true
    });











})