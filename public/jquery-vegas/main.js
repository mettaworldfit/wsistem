$(document).ready(function () {

    let basePath = '/';

    if (window.location.hostname === 'localhost') {
        const pathParts = window.location.pathname.split('/');
        basePath = '/' + pathParts[1] + '/'; // Detecta el nombre
    }

    const URL = window.location.protocol + '//' + window.location.host + basePath;


    $(".sidebar-right").vegas({
        slides: [
            { src: URL + "public/imagen/img/img1.jpg" },
            { src: URL + "public/imagen/img/img2.jpg" },
            { src: URL + "public/imagen/img/img3.jpg" },
        ],
        transition: ["fade", "blur", "fade2", "blur2"],
    });
});