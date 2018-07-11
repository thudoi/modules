(function ($, Drupal) {
  Drupal.behaviors.radio_options = {
    attach: function (context, settings) {
      for (i = 0; i < 20; i++) {

        var selector = $('div[data-drupal-selector="edit-field-question-' + i + '-subform-field-radios-wrapper"] input[type="radio"]');
        selector.each(function () {
          $(this).on('click', function () {
            //console.log($(this));
            $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find('input[type="radio"]').prop('checked', false);
            // selector.prop('checked', false);
            $(this).prop('checked', true);
          });
        });

      }
      $(document).ajaxComplete(function (event, xhr, settings) {
        if (settings.data.indexOf("field_question") != -1) {
          var val = $(".field--name-field-question-type select").val();
          $(".field--name-field-question-type select").val('_none').change();
          $(".field--name-field-question-type select").val(val).change();
          $(".field--name-field-question-type select").trigger('change');
          //console.log(val);
          var valfront = $(".field--name-field-question-type-front select").val();
          $(".field--name-field-question-type-front select").val('_none');
          $(".field--name-field-question-type-front select").val(valfront).change();
          $(".field--name-field-question-type-front select").trigger('change');
        }
      });

      $(".hidden-editing").hide();

      function eraseCookie(c_name) {
        createCookie(cookie_name, "", -1);
      }

      function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
      }

      $(".bulk-adding").each(function (e) {
        if (e == 0) {
          $(this).show();
        }
        else {
          $(this).remove();
        }
      });
      $(".field--name-field-explain button.btn-danger").each(function () {
        $(this).html('<span class="icon glyphicon glyphicon-trash" aria-hidden="true"></span>Remove Explanation');
      });
      //TFNG
      if ($("#edit-field-qtype-front").length > -1) {
        //console.log('nhan');
        var drop = $("#edit-field-question-type").val();
        var front = $("#edit-field-qtype-front").val();
        // console.log(front);
        // console.log(drop);
        if (front == '11' && drop == 'drop_down' || front == '12' && drop == 'drop_down') {
          $(".field--name-field-dropdown").hide();
        } else {
          // $(".field--name-field-dropdown").show();
        }

        $("#edit-field-question-type").change(function () {
          var front = $("#edit-field-qtype-front").val();
          // console.log(front + '2222');

          if (front == '11' && $(this).val() == 'drop_down' || front == '12' && $(this).val() == 'drop_down') {
            $(".field--name-field-dropdown").hide();
          } else {
            // $(".field--name-field-dropdown").show();
          }
        });

        $("#edit-field-qtype-front").change(function () {

          var drop = $("#edit-field-question-type").val();
          // console.log(drop + '3333');
          if (drop == 'drop_down' && $(this).val() == '11' || drop == 'drop_down' && $(this).val() == '12') {
            $(".field--name-field-dropdown").hide();
          } else {
            //   $(".field--name-field-dropdown").show();
          }
        });
      }
      if ($("#edit-field-question-type").length > -1) {
        var type = $("#edit-field-question-type").val();
        if (type == 'checkbox') {
          $(".field--name-field-explain .field--name-field-number").each(function () {
            $(this).hide();
          });
        }
        $("#edit-field-question-type").change(function () {
          if ($(this).val() == 'checkbox') {
            $(".field--name-field-explain .field--name-field-number").each(function () {
              $(this).hide();
            });
          }
        });

      }


    }
  }
})(jQuery, Drupal);
