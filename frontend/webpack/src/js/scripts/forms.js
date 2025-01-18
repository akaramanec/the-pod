
(function () {

  function sendFormData (form) {
    let request = new XMLHttpRequest();
    request.open('POST', 'send.php', true)
    request.setRequestHeader('accept', 'application/json');

    // Добавляем обработчик на событие `submit`
    form.addEventListener('submit', function(event) {
      event.preventDefault();
      console.log('sendFormData')
      // Это простой способ подготавливить данные для отправки (все браузеры и IE > 9)
      let formData = new FormData(form);
      // Отправляем данные
      request.send(formData);
      console.log(formData);
      // Функция для наблюдения изменения состояния request.readyState обновления statusMessage соответственно
      request.onreadystatechange = function () {
        // <4 =  ожидаем ответ от сервера
        if (request.readyState < 4)
          console.log('Ответ от сервера полностью загружен')
        else if (request.readyState === 4) {
          if (request.status === 200 && request.status < 300)
            console.log('200 - 299 = успешная отправка данных!')
          else
            console.log('что-то пошло не так')
        }
      }
    });

  }
  function setCursorPosition(pos, elem) {
    elem.focus();
    if (elem.setSelectionRange) elem.setSelectionRange(pos, pos);
    else if (elem.createTextRange) {
      let range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd("character", pos);
      range.moveStart("character", pos);
      range.select()
    }
  }
  function mask(event) {
    let matrix = "+38(___)-___-____",
      i = 0,
      def = matrix.replace(/\D/g, ""),
      val = this.value.replace(/\D/g, "");
    if (def.length >= val.length) val = def;
    this.value = matrix.replace(/./g, function (a) {
      return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? "" : a
    });
    if (event.type === "blur") {
      if (this.value.length === 2) this.value = ""
    } else setCursorPosition(this.value.length, this)
  }

  const forms = document.querySelectorAll('form')
  if  (forms.length) {

    for (let i = 0; i < forms.length; i++) {
      let label = forms[i].querySelectorAll('label')
      for (let i = 0; i < label.length; i++) {
        const errorMsg = label[i].querySelector('.error')
        const input = label[i].querySelector('input')
        if (input) {
          input.addEventListener('invalid', function (event) {
            event.preventDefault();
            if (!event.target.validity.valid) {
              errorMsg.style.opacity = '1';
              setTimeout(function () {
                errorMsg.style.opacity = '0';
              }, 3000)
            } else {
              errorMsg.style.opacity = '0';
            }
          })
          if (input.type === 'tel') {
            input.addEventListener("input", mask, false);
            input.addEventListener("focus", mask, false);
            input.addEventListener("blur", mask, false);
          }
        }
      }
      sendFormData(forms[i])
    }
  }
}());



