
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
