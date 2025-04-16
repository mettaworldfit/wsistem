$(document).ready(function() {


    $(".sidebar-right").vegas({
        slides: [
            { src: window.location.protocol + '//' + window.location.host + '/' + "public/imagen/img/img1.jpg" },
            { src: window.location.protocol + '//' + window.location.host + '/' + "public/imagen/img/img2.jpg" },
            { src: window.location.protocol + '//' + window.location.host + '/' + "public/imagen/img/img3.jpg" },
        ],
        transition: ["fade", "blur", "fade2", "blur2"],
    });
});