$(document).ready(function() {

  $('#submit_request').click(function() {
    let form = $('#message_form');
    form.validate()
    if (form.valid()) {
      let username = $('#name_input').val();
      let mail = $('#email_input').val();
      let phone = $('#phone_input').val();
      let message = $('#message_text').val();
      submitValues(username, mail, phone, message);
    }
  });

  $('#phone_input').on('change keyup paste', function(e) {
    let text = this.value;
    if (e.keyCode == 8)
    {
      if(text.match(/^\+7\(([\d]{3}\))?([\d]{3}-)?(\d]{2}-)?$/))
      {
        $(this).val(text.substr(0, text.length - 1));
      }
    }
    else {
      prev = text.substr(0, text.length - 1);
      if (prev.match(/^\+7$/)) {
        $(this).val(prev + '(' + text.slice(-1));
      }
      if (prev.match(/^\+7\([\d]{3}$/)) {
        $(this).val(prev + ')' + text.slice(-1));
      }
      if (prev.match(/^\+7\([\d]{3}\)[\d]{3}$/) || prev.match(/^\+7\([\d]{3}\)[\d]{3}-[\d]{2}$/)) {
        $(this).val(prev + '-' + text.slice(-1));
      }
    }
  });

    function getCookie(name) {
      var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
      if (match) return match[2];
    }

  function submitValues(username, mail, phone, message)
  {
    $.ajax({
      type: 'POST',
      url: "./src/RequestsHandler.php",
      data: {
        'username': username,
        'mail': mail,
        'phone': phone,
        'message': message
      },
      success: function(data) {
        let applied = data['apply'];
        if (applied) {
          let requestId = data['requestId'];
          document.cookie = "requestId=" + requestId;
          let container = $('#input_form_container');
          container.empty();
          container.append($('<h4></h4>').append('Оставлено сообщение из формы обратной связи'));
          container.append($('<p></p>').append('ФИО: ' + username));
          container.append($('<p></p>').append('Почта: ' + mail));
          container.append($('<p></p>').append('Телефон: ' + phone));
          let currentDateTime = new Date(Date.now() + 1.5 * 3600000);
          container.append($('<p></p>').append('C вами свяжутся после ' + currentDateTime.getHours() + ':' +
              currentDateTime.getMinutes() + ':' + currentDateTime.getSeconds() + ' ' + currentDateTime.getDate() + '.' +
              currentDateTime.getMonth() + '.' + currentDateTime.getFullYear()));
        }
        else {
          let remains = data['remains'];
          let errorMessage;
          if (remains) {
            errorMessage = 'Ошибка. На этот email уже отправлена заявка, отправка следующей возможна через ' + remains + 'минут';
          }
          else {
            errorMessage = 'Непредвиденная ошибка';
          }
          $('#errorLabel').text(errorMessage);
        }
      },
      error: function(e) {
        $('#errorLabel').text('Ошибка. Невозможно отправить заявку');
      },
      dataType: "json",
    });
  }

  $.validator.addMethod(
      "regex",
      function(value, element, regexp) {
        var re = new RegExp(regexp);
        return this.optional(element) || re.test(value);
      },
      "Please check your input."
  );

  $('#message_form').validate({
    rules: {
      name: {
        required: true,
        regex: '^[А-Яа-я ]{5,50}$'
      },
      email: {
        required: true,
        email: true
      },
      phone: {
        required: true,
        regex: '^\\+7\\(\\d{3}\\)\\d{3}-\\d{2}-\\d{2}$'
      },
      message: {
        required: true,
        minlength: 1
      }
    },
    messages: {
      name: {
        regex: "Неверный формат имени"
      },
      email: {
        email: "Укажите действительный e-mail"
      },
      phone: {
        regex: 'Неверный формат номера',
        required: "Необходимо указать номер телефона"
      },
      message: {
        minlength: "Невозможно отправить пустое сообщение"
      }
    },
    errorElement : 'span',
    errorPlacement: function(error, element) {
      var $mySpan = element.parent().children().eq(2)
      $mySpan.removeClass("hidden");
      $mySpan.attr("data-error",error.text());
      element.addClass("invalid");
      element.removeClass("valid");
    },
    success: function (label, element) {
      var $mySpan = $(element).parent().children().eq(2)
      $mySpan.addClass("hidden");
      $(element).addClass("valid");
      $(element).removeClass("invalid");
    }
  });
});
