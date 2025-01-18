function addOffsetAnimation(targetBlock,addClass) {
  let target = $(targetBlock);
  let winHeight = $(window).height();
  let targetPos = target.offset().top;
  let scrollToElem = targetPos -(winHeight + 50);

  $(window).scroll(function(){
    let winScrollTop = $(this).scrollTop();
    if(winScrollTop > scrollToElem){
      $(target).addClass(addClass);

    } else {
      $(target).removeClass(addClass);
    }
  });
}


