$(document).ready(function() {


    $(".sidebar-right").vegas({
        slides: [
            { src: window.location.protocol + '//' + window.location.host + '/' + "public/imagen/img/img1.webp" },
            { src: window.location.protocol + '//' + window.location.host + '/' + "public/imagen/img/img2.webp" },
            { src: window.location.protocol + '//' + window.location.host + '/' + "public/imagen/img/img3.webp" },
        ],
        transition: ["fade", "blur", "fade2", "blur2"],
    });
});