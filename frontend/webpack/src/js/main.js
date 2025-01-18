(function () {
  const successPopup = document.querySelector('.success-popup')
  const startPopup = document.querySelector('.start-popup')
  const successPopupClose = document.querySelector('span.pop-cross')
  const mainImg = document.querySelector('img.product__img-main')
  let images = document.querySelectorAll('.product__img-wrap .img-wrap')
  if (startPopup) {
    let buttons = startPopup.querySelectorAll('.main-chatbot__buttons button')
    for (let i = 0; i < buttons.length; i++) {
      buttons[i].addEventListener('click', function () {
          if (this.classList.contains('yes')) {
            startPopup.classList.remove('show')
          } else {
            window.location.replace("https://www.google.com/");
          }
        })
    }
  }
  if (successPopupClose) {
    successPopupClose.addEventListener('click', () => {
      successPopup.classList.toggle('show')
    })
  }

  if (images.length) {
    for (let i = 0; i < images.length; i++) {
      images[i].addEventListener('click', function () {
        mainImg.src = this.querySelector('img').src
      })
    }
  }

}())
