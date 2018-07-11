(function ($, Drupal) {
  Drupal.behaviors.ContactForm = {
    attach: function (context, settings) {
      $("form#contact-form").submit(function () {

        var email = $("#contact-form input.contact-email").val();
        var name = $("#contact-form input.contact-name").val();
        var title = $("#contact-form input.contact-title").val();
        var message = $("#contact-form .contact-message").val();
        if (email == '') {
          $("#contact-form .error-message.contact-email").text('Email is required.');
          return false;
        } else {
          $("#contact-form .error-message.contact-email").text('');
        }
        if (!validateEmail(email)) {
          $("#contact-form .error-message.contact-email").text('Email is invalid.');
          return false;
        } else {
          $("#contact-form .error-message.contact-email").text('');
        }
        if (name == '') {
          $("#contact-form .error-message.contact-name").text('Name is required.');
          return false;
        } else {
          $("#contact-form .error-message.contact-name").text('');
        }
        if (title == '') {
          $("#contact-form .error-message.contact-title").text('Title is required.');
          return false;
        } else {
          $("#contact-form .error-message.contact-title").text('');
        }
        if (message == '') {
          $("#contact-form .error-message.contact-message").text('Message is required.');
          return false;
        } else {
          $("#contact-form .error-message.contact-message").text('');
        }
        var des = $(".destination").val();
        if (des == '') {
          des = '/account/profile'
        }
        $("#contact-form button").attr('disabled', 'disabled');
        $("#contact-form .preload").show();
        $.post('/contact/form', {name: name, email: email, title: title, message: message})

          .done(function (data) {
            if (data == 'ok') {
              $("#contact-form .preload").hide();
              $("#contact-form input.contact-email").val('');
              $("#contact-form input.contact-name").val('');
              $("#contact-form input.contact-title").val('');
              $("#contact-form .contact-message").val('');
              $('#modal-contact').modal('show');
              $("#contact-form button").attr('disabled', false);

            } else {
              $(".preload").hide();
              $(".error-message.main").html(data);
              $("#contact-form button").attr('disabled', false);

            }
          });
        return false;
      });


      $("form#contact-form-page").submit(function () {

        var email = $("#contact-form-page input.contact-email").val();
        var name = $("#contact-form-page input.contact-name").val();
        var title = $("#contact-form-page input.contact-title").val();
        var message = $("#contact-form-page .contact-message").val();
        if (email == '') {
          $("#contact-form-page .error-message.contact-email").text('Email is required.');
          return false;
        } else {
          $("#contact-form-page .error-message.contact-email").text('');
        }
        if (!validateEmail(email)) {
          $("#contact-form-page .error-message.contact-email").text('Email is invalid.');
          return false;
        } else {
          $("#contact-form-page .error-message.contact-email").text('');
        }
        if (name == '') {
          $("#contact-form-page .error-message.contact-name").text('Name is required.');
          return false;
        } else {
          $("#contact-form-page .error-message.contact-name").text('');
        }
        if (title == '') {
          $("#contact-form-page .error-message.contact-title").text('Title is required.');
          return false;
        } else {
          $("#contact-form-page .error-message.contact-title").text('');
        }
        if (message == '') {
          $("#contact-form-page .error-message.contact-message").text('Message is required.');
          return false;
        } else {
          $("#contact-form-page .error-message.contact-message").text('');
        }
        var des = $(".destination").val();
        if (des == '') {
          des = '/account/profile'
        }
        $("#contact-form-page button").attr('disabled', 'disabled');
        $("#contact-form-page .preload").show();
        $.post('/contact/form', {name: name, email: email, title: title, message: message})

          .done(function (data) {
            if (data == 'ok') {
              $("#contact-form .preload").hide();
              $(".modal-header .close").click();
              $("#contact-form-page input.contact-email").val('');
              $("#contact-form-page input.contact-name").val('');
              $("#contact-form-page input.contact-title").val('');
              $("#contact-form-page .contact-message").val('');
              $('#modal-contact').modal('show');
              $("#contact-form-page button").attr('disabled', false);

            } else {
              $(".preload").hide();
              $(".error-message.main").html(data);
              $("#contact-form-page button").attr('disabled', false);

            }
          });
        return false;
      });

      function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email.toLowerCase());
      }

    }
  }
})(jQuery, Drupal);
