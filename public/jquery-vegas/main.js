let SITE_URL;

if (window.location.hostname !== "localhost") {
    SITE_URL = window.location.protocol + '//' + window.location.host + '/';
} else {
    SITE_URL = window.location.protocol + '//' + window.location.host + '/' + 'proyecto/';
}

$(document).ready(function () {
  $(".sidebar-right").vegas({
    slides: [
      { src: SITE_URL + "public/imagen/img/img1.webp" },
      { src: SITE_URL + "public/imagen/img/img2.webp" },
      { src: SITE_URL + "public/imagen/img/img3.webp" },
    ],
    transition: ["fade", "blur", "fade2", "blur2"],
  });
});
