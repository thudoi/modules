(function ($, Drupal) {
  Drupal.behaviors.AccountAction = {
    attach: function (context, settings) {

      /**Preload**/
      //login accountl
      $("form#user-login").submit(function () {

        var email = $("#user-login input.email").val();
        var pass = $("#user-login input.password").val();
        if (email == '') {
          $("#user-login .error-message.email").text('Username or Email is required.');
          return false;
        } else {
          $("#user-login .error-message.email").text('');
        }
        if (pass == '') {
          $("#user-login .error-message.password").text('Password is required.');
          return false;
        } else {
          $("#user-login .error-message.password").text('');
        }
        var des = $(".destination").val();
        var submit = $(".submit-test").val();
        if (des == '') {
          des = '/account/profile'
        }
        if (submit != '') {
          des = des + '?submit=true'
        }
        $.post('/account/login/callback', {account_email: email, account_password: pass})

          .done(function (data) {

            if (data == 1) {
              setTimeout(function () {
                window.location.href = des;
              }, 200);
            } else {
              $(".error-message.main").html('Your username or password is incorrect. <a href="/user/password"><b>Forgot your password?</b></a>');
            }
          });
        return false;
      });
      $("form#user-login-form").submit(function () {

        var email = $("#user-login-form input.email").val();
        var pass = $("#user-login-form input.password").val();
        if (email == '') {
          $("#user-login-form .error-message.email").text('Username or Email is required.');
          return false;
        } else {
          $("#user-login-form .error-message.email").text('');
        }
        if (pass == '') {
          $("#user-login-form .error-message.password").text('Password is required.');
          return false;
        } else {
          $("#user-login-form .error-message.password").text('');
        }
        var des = $(".destination").val();
        var submit = $(".submit-test").val();
        if (des == '') {
          des = '/account/profile'
        }
        if (submit != '') {
          des = des + '?submit=true'
        }
        $.post('/account/login/callback', {account_email: email, account_password: pass})

          .done(function (data) {

            if (data == 1) {
              setTimeout(function () {
                window.location.href = des;
              }, 200);
            } else {
              $(".error-message.main").html('Your username or password is incorrect. <a href="/user/password"><b>Forgot your password?</b></a>');
            }
          });
        return false;
      });

//register user account
      $("#user-register").submit(function () {
        var username = $("#user-register input.username").val();
        var email = $("#user-register input.email").val();
        var pass = $("#user-register input.password").val();
        var confirm_pass = $("#user-register input.confirm_password").val();
        if (username == '') {
          $("#user-register .error-message.username").text('Username is required.');
          return false;
        } else {
          $("#user-register .error-message.username").text('');
        }
        if (email == '') {
          $("#user-register .error-message.email").text('Email is required.');
          return false;
        } else {
          $("#user-register .error-message.email").text('');
        }
        if (!validateEmail(email)) {
          $("#user-register .error-message.email").text('Email is invalid.');
          return false;
        } else {
          $("#user-register .error-message.email").text('');
        }
        if (pass == '') {
          $("#user-register .error-message.password").text('Password is required.');
          return false;
        } else {
          $("#user-register .error-message.password").text('');
        }
        if (confirm_pass == '') {
          $("#user-register .error-message.confirm_password").text('Confirm Password is required.');
          return false;
        } else {
          $("#user-register .error-message.confirm_password").text('');
        }
        if (pass != confirm_pass) {
          $("#user-register .error-message.password").text('Password is not match.');
          return false;
        } else {
          $("#user-register .error-message.password").text('');
        }
        var des = $(".destination").val();
        $.post('/account/register/callback', {
          account_username: username,
          account_email: email,
          account_password: pass
        })

          .done(function (data) {

            if (data == 1) {
              setTimeout(function () {
                window.location.href = des + '?build_profile=true';
              }, 500);
            } else {
              $(".error-message.main").html(data);
            }
          });
        return false;
      });
      $("#user-register-form").submit(function () {
        var username = $("#user-register input.username").val();
        var email = $("#user-register input.email").val();
        var pass = $("#user-register input.password").val();
        var confirm_pass = $("#user-register input.confirm_password").val();
        if (username == '') {
          $("#user-register .error-message.username").text('Username is required.');
          return false;
        } else {
          $("#user-register .error-message.username").text('');
        }
        if (email == '') {
          $("#user-register .error-message.email").text('Email is required.');
          return false;
        } else {
          $("#user-register .error-message.email").text('');
        }
        if (!validateEmail(email)) {
          $("#user-register .error-message.email").text('Email is invalid.');
          return false;
        } else {
          $("#user-register .error-message.email").text('');
        }
        if (pass == '') {
          $("#user-register .error-message.password").text('Password is required.');
          return false;
        } else {
          $("#user-register .error-message.password").text('');
        }
        if (confirm_pass == '') {
          $("#user-register .error-message.confirm_password").text('Confirm Password is required.');
          return false;
        } else {
          $("#user-register .error-message.confirm_password").text('');
        }
        if (pass != confirm_pass) {
          $("#user-register .error-message.password").text('Password is not match.');
          return false;
        } else {
          $("#user-register .error-message.password").text('');
        }
        var des = $(".destination").val();
        $.post('/account/register/callback', {
          account_username: username,
          account_email: email,
          account_password: pass
        })

          .done(function (data) {

            if (data == 1) {
              setTimeout(function () {
                window.location.href = des + '?build_profile=true';
              }, 500);
            } else {
              $(".error-message.main").html(data);
            }
          });
        return false;
      });

      ///build profile


      $(".bp-control .cancel").each(function () {
        var id = $(this).attr('href');
        $(this).click(function (event) {
          $(".build-profile").addClass('hidden');
          $(id).removeClass('hidden');
          event.preventDefault();
        });
      });

      $('.str-item').click(function () {
        $(this).parent().find('.str-item').removeClass("active");
        $(this).addClass("active");
      });

      // $('.starbox-item a').hover(function () {
      //   $(this).addClass('hover');
      //   $(this).prevAll().addClass("hover");
      //   $(this).prevAll().removeClass("hover-fa-star-o");
      //   $(this).nextAll().removeClass("hover");
      //   $(this).nextAll().addClass("hover-fa-star-o");
      // });
      $('.starbox-item a').each(function () {
        $(this).hover(function () {
          $(this).addClass('hover');
          $(this).removeClass("hover-fa-star-o");
          $(this).prevAll().addClass("hover");
          $(this).prevAll().removeClass("hover-fa-star-o");
          $(this).nextAll().removeClass("hover");
          $(this).nextAll().addClass("hover-fa-star-o");
        })

      });

      $('.starbox-item').mouseleave(function () {
        $(this).find(".hover").removeClass("hover");
        $(this).find(".hover-fa-star-o").removeClass("hover-fa-star-o");
      });

      $('.starbox-item a').click(function () {
        $(this).addClass('click');
        var star = $(this).attr('data-rate');
        $(this).parent().find('.star-data').val(star);
        $(this).prevAll().addClass("click");
        $(this).removeClass("hover-fa-star-o");
        $(this).prevAll().removeClass("hover-fa-star-o");
        $(this).nextAll().removeClass("click");
        $(this).nextAll().removeClass("hover");
        $(this).nextAll().addClass("hover-fa-star-o");
      });

      if (findGetParameter('build_profile')) {
        $("a.click-profile-build").trigger("click");
      }


      function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email.toLowerCase());
      }

      function findGetParameter(parameterName) {
        var result = null,
          tmp = [];
        location.search
          .substr(1)
          .split("&")
          .forEach(function (item) {
            tmp = item.split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
          });
        return result;
      }

      //validate step 2
      var step2 = 0;
      // $(".step2-action").addClass('hidden');
      $(".step2-choose").each(function () {
        if ($(this).hasClass('active')) {
          if ($(".target_score").val() != '' && $(this).attr('data') == 'I have an IELTS score') {
            //    $(".step2-action").removeClass('hidden');
            step2 = 1;
          } else if ($(".target_score2").val() != '' && $(this).attr('data') == "I don't have an IELTS score") {
            //  $(".step2-action").removeClass('hidden');
            step2 = 1;
          } else {
            // $(".step2-action").addClass('hidden');
            step2 = 0;
          }
        }
      });
      $(".step2-choose").each(function () {
        $(this).click(function () {
          if ($(this).hasClass('active')) {
            if ($(".target_score").val() != '' && $(this).attr('data') == 'I have an IELTS score') {
              //  $(".step2-action").removeClass('hidden');
              step2 = 1;
            } else if ($(".target_score2").val() != '' && $(this).attr('data') == "I don't have an IELTS score") {
              // $(".step2-action").removeClass('hidden');
              step2 = 1;
            } else {
              // $(".step2-action").addClass('hidden');
              step2 = 0;
            }
          }
        });

      });
      $(".target_score").change(function () {
        if ($(this).val() != '') {
          //  $(".step2-action").removeClass('hidden');
          step2 = 1;
        } else {
          //  $(".step2-action").addClass('hidden');
          step2 = 0;
        }
      });
      $(".target_score2").change(function () {
        if ($(this).val() != '') {
          // $(".step2-action").removeClass('hidden');
          step2 = 1;
        } else {
          // $(".step2-action").addClass('hidden');
          step2 = 0;
        }
      });

      //$("#submit-proced").addClass('hidden');
      var status = 0;
      var rate = 0;
      $(".practicing .str-item").each(function () {
        if ($(this).hasClass('active')) {
          status = 1;
        }
        $(this).click(function () {
          if ($(this).hasClass('active')) {
            status = 1;
          }
          if (status == 1 && rate == 1) {
            //   $("#submit-proced").removeClass('hidden');
          }
        });
      });
      $(".star-data").each(function () {
        if ($(this).val() != '') {
          rate = 1;
        }
      });
      $(".starbox-item a").each(function () {
        $(this).click(function () {
          rate = 1;
          if (rate == 1 && status == 1) {
            //  $("#submit-proced").removeClass('hidden');
          }
        });
      });
      if (status == 1 && rate == 1) {
        // $("#submit-proced").removeClass('hidden');
      }

      $(".bp-control .next").each(function () {
        var id = $(this).attr('href');
        $(this).click(function (event) {
          if (id == '#step3') {
            if (step2 == 1) {
              $(".build-profile").addClass('hidden');
              $(id).removeClass('hidden');
              $(".error-message2").text('');
            } else {
              $(".error-message2").text('Please choose an option to continue.');
              return false;
            }
          } else {
            $(".build-profile").addClass('hidden');
            $(id).removeClass('hidden');
            $(".error-message2").text('');

          }

          event.preventDefault();
        });
      });
      //save profil

      $("a#submit-proced").click(function (e) {
        if (status == 0 || rate == 0) {
          $(".error-message3").text('Please choose an option to continue.');
          return false;
        }
        $(".error-message2").text('');

        e.preventDefault();
        var field_ima = '';
        $(".pb-choose span").each(function () {
          if ($(this).hasClass('active')) {
            field_ima = $(this).attr('data');
          }
        });
        var field_ielts = '';
        $(".step2-choose").each(function () {
          if ($(this).hasClass('active')) {
            field_ielts = $(this).attr('data');
          }
        });
        var field_previous_score = $(".previous_score").val();
        if (field_ielts == 'I have an IELTS score') {
          var field_target_score = $(".target_score").val();
        } else {
          var field_target_score = $(".target_score2").val();
        }
        var field_practicing = '';
        $(".st3-radio .str-item").each(function () {
          if ($(this).hasClass('active')) {
            field_practicing = $(this).attr('data');
          }
        });
        var field_destination = [];
        $(".st3-item .starbox-item").each(function () {
          var rate = $(this).find('.star-data').val();
          if (rate) {
            var key = $(this).find('.star-data').attr('data-key');
            field_destination.push({'rate': rate, 'key': key});
          }
        });
        var field_date = $('.step2-content .datetimepicker').val();
        $.post('/account/profile/callback', {
          field_ima: field_ima,
          field_ielts: field_ielts,
          field_previous_score: field_previous_score,
          field_date: field_date,
          field_target_score: field_target_score,
          field_practicing: field_practicing,
          field_destination: field_destination
        })

          .done(function (data) {

            if (data == 1) {
              setTimeout(function () {
                window.location.href = '/';
              }, 500);
            } else {
              $(".error-message.main").html(data);
            }
          });
        return false;

      });

      //login verify
      $(".login-right a").each(function () {
        $(this).click(function () {
          var submit = $(this).attr('href');
          var at = submit.split('destination=');
          var des = at[1];
          $.post('/account/login/verify', {
            des: des
          });
          //return false;
        });
      });


    }
  }
})(jQuery, Drupal);
