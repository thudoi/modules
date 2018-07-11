(function ($, Drupal) {
  Drupal.behaviors.ResultFront = {
    attach: function (context, settings) {
      function convert(input) {
        var parts = input.split(':'),
          hours = +parts[0],
          minutes = +parts[1],
          seconds = +parts[2];
        return (minutes * 60 + seconds).toFixed(2);
      }

      if (drupalSettings.test) {
        fill_blank();
        drop_down();
        radio();
        checkbox();
      }
      // declare object for video
      var audio = document.getElementById('audio-player');
      if (audio !== null) {
        var player = new MediaElementPlayer('#audio-player');
        $("a.listen-from-here").each(function () {
          $(this).click(function (e) {
            e.preventDefault();
            var timeToGoAudio = "";
            var data = $(this).attr('data');
            if (data != '') {
              timeToGoAudio = $(this).attr('data');
              timeToGoAudio = convert(timeToGoAudio);
              player.setCurrentTime(timeToGoAudio);
              player.setCurrentRail();
              player.play();
            }

          });
        });
      }
      $("a.facebook-share").each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $("a.a2a_button_facebook").click();
        });
      });
      $("a.twitter-share").each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $("a.a2a_button_twitter").click();
        });
      });
      $("a.google-share").each(function () {
        $(this).click(function (e) {
          e.preventDefault();
          $("a.a2a_button_google_plus").click();
        });
      });

      $("a.share-test-action").mouseover(function (e) {
        $(".share-test").fadeIn("slow");
      });
      $("a.share-test-action").mouseleave(function (e) {
        setTimeout(function () {
          $(".share-test").fadeOut("slow");
        }, 3000)

      });
      $(".share-test").mouseover(function (e) {
        $(this).show();
      });
      $(".share-test").mouseleave(function (e) {
        setTimeout(function () {
          $(".share-test").fadeOut("slow");
        }, 3000)
      });

      function fill_blank() {
        var type_blank = $('.type_blank');
        type_blank.each(function () {
          var textInput = $(this).find('input[type="text"]');
          textInput.each(function () {
            var num = $(this).attr('data-num');
            var ans = drupalSettings.test[num].ans;
            $(this).val(ans);
          });
        })
      }

      function drop_down() {
        var type_drop = $('select[class="iot-question"]');
        type_drop.each(function () {
          var num = $(this).attr('data-num');
          var ans = drupalSettings.test[num].ans;
          $(this).val(ans);
        });
      }

      function radio() {
        var type_radio = $('.type_radio');
        type_radio.each(function () {
          var num = $(this).attr('data-num');
          var ans = drupalSettings.test[num].ans;
          if (ans) {
            $('input[name="q-' + num + '"][value=' + ans + ']').attr('checked', true);
          }
        });
      }

      function checkbox() {
        var type_checkbox = $('.type_checkbox');
        type_checkbox.each(function () {
          var num = $(this).find('.iot-question').attr('data-num');
          var ans = drupalSettings.test[num].ans;
          if (ans) {
            for (var i = 0; i < ans.length; i++) {
              $('input[name="q-' + num + '"][value=' + ans[i] + ']').attr('checked', true);
              $(this).val(ans);
            }
          }
        });
      }
    }
  }
})(jQuery, Drupal);
