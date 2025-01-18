$(document).ready(function () {
  $('.reviews__content').owlCarousel({
    loop: true,
    margin: 20,
    dotsEach: true,
    center: true,
    responsive: {
      0: {
        items: 1
      },
      1200: {
        items: 7
      }
    }
  });
});
