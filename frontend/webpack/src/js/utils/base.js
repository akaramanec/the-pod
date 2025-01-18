// Smoth paga scroll
const $page = $('html, body');
$('a[href*="#"]').click(function () {
  $page.animate({
    scrollTop: $($.attr(this, 'href')).offset().top,
  }, 500);
  return false;
});



// Video auto height
const iframe = $('.main-video-popup iframe');
const width = iframe.width();
iframe.css('height', width / 1.7777 + 'px');


