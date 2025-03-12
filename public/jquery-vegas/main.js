const URL_ =  window.location.protocol + '//' + window.location.host + '/';
$(document).ready(function () {
  $(".sidebar-right").vegas({
    slides: [
      { src: URL_ + "public/imagen/img/img1.webp" },
      { src: URL_ + "public/imagen/img/img2.webp" },
      { src: URL_ + "public/imagen/img/img3.webp" },
    ],
    transition: ["fade", "blur", "fade2", "blur2"],
  });
});
