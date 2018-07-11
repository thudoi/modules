(function ($, Drupal) {
  Drupal.behaviors.ContactForm = {
    attach: function (context, settings) {
      $("form#report-mistake").submit(function () {

        var email = $("#report-mistake input.email").val();
        var name = $("#report-mistake input.yourname").val();
        var question = $("#report-mistake select.question").val();
        var message = $("#report-mistake .area-wrong").val();
        var quiz = $("#report-mistake .quiz").val();
        var collection = $("#report-mistake .collection").val();
        var url = $("#report-mistake .url").val();
        if (email == '') {
          $("#report-mistake .error-message.report-email").text('Email is required.');
          return false;
        } else {
          $("#report-mistake .error-message.report-email").text('');
        }
        if (!validateEmail(email)) {
          $("#report-mistake .error-message.report-email").text('Email is invalid.');
          return false;
        } else {
          $("#report-mistake .error-message.report-email").text('');
        }

        if (message == '') {
          $("#report-mistake .error-message.report-message").text('Description is required.');
          return false;
        } else {
          $("#report-mistake .error-message.report-message").text('');
        }
        $("#report-mistake button").attr('disabled', 'disabled');
        $("#report-mistake .preload").show();
        $.post('/report/mistake/callback', {
          name: name,
          email: email,
          question: question,
          message: message,
          quiz: quiz,
          collection: collection,
          url: url
        })

          .done(function (data) {
            if (data == 'ok') {
              $("#report-mistake .preload").hide();
              $("#report-mistake input.email").val('');
              $("#report-mistake input.yourname").val('');
              $("#report-mistake .area-wrong").val('');
              $('#modal-report').modal('show');
              $("#report-mistake button").attr('disabled', false);
              $(".modal-header .close").click();


            } else {
              $(".preload").hide();
              // $(".error-message.main").html(data);
              $("#report-mistake button").attr('disabled', false);


            }
          });
        return false;
      });

      $("form#report-view").submit(function () {

        var email = $("#report-view input.email").val();
        var subject = $("#report-view input.subject").val();
        var message = $("#report-view .message").val();


        if (message == '') {
          $("#report-view .error-message.report-message").text('Message is required.');
          return false;
        } else {
          $("#report-view .error-message.report-message").text('');
        }
        $("#report-view button").attr('disabled', 'disabled');
        $("#report-view .preload").show();
        $.post('/report/view/callback', {
          subject: subject,
          email: email,
          message: message
        })
          .done(function (data) {
            if (data == 'ok') {
              $("#report-view .preload").hide();
              $("#report-view input.subject").val('');
              $("#report-view .message").val('');
              $("#report-view button").attr('disabled', false);
              $(".right-message").html('<div class="success">Message has been sent.</div>');
              // $("button.ui-dialog-titlebar-close").click();

            } else {
              $(".preload").hide();
              // $(".error-message.main").html(data);
              $("#report-view button").attr('disabled', false);
              $(".right-message").html('<div class="error">' + data + '</div>');

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
