$(document).ready(function() {
  let windowHeight = $(window).height();

  $(document).on('scroll', function() {
    $('.resources').each(function() {
      let self = $(this),
        height = self.offset().top + self.height();
      if ($(document).scrollTop() + windowHeight >= height) {
        self.addClass('fadeInRight')
      }
    });
  });
  document.querySelectorAll('.animate-btn')
    .forEach(button => button.innerHTML = '<div><span>' + button.textContent
      .trim()
      .split('')
      .join('</span><span>') + '</span></div>');
});
