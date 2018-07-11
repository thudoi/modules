(function ($, Drupal, drupalSettings) {

  $(document).ready(function () {
    var qid = $("input.get-qid").val();
    if (typeof qid != 'undefined') {
      storeLocal(qid);
      initTimer(qid);
      $(".qp-item").click(function () {
        var id = $(this).attr('data-q');
        var des = $("#" + id);
        $('html, body').animate({
          scrollTop: des.offset().top - 100
        }, 200);
        des.focus();
        if ($('.question-panel').is(':visible')) {
          $('.question-panel').removeClass("show");

        } else {
          $('.question-panel').addClass("show");
          $('.qp-items').getNiceScroll().resize();

        }
      });
    }
    // focus to question ư
    //
    $('.submit-test-block').click(function () {
      localStorage.removeItem(qid);
      localStorage.removeItem('time_' + qid);
      submit(1);
    });
  });

  function submit(status) {
    data = getData();
    time = $('#stopwatch').text();
    solv = drupalSettings.answers;
    result = takeTest(solv, data);
    total = solv.total;
    sid = solv.sec_id;
    score_id = drupalSettings.score;
    package = [
      total, result, data, score_id, time, status
    ];
    getCsrfToken(function (csrfToken) {
      postNode(csrfToken, package, status);
    });
  }

  function takeTest(solv, data) {
    var score = 0;
    for (const key of Object.keys(solv.answers)) {
      switch (solv.answers[key].type) {
        case 'blank':
          for (var i = 0; i < solv.answers[key].answer.length; i++) {
            var str1 = solv.answers[key].answer[i].toString().toLowerCase();
            var str2 = data.answers[key].ans.toString().toLowerCase();
            dostr = str1.replace(/[^a-zA-Z0-9]/g, '');
            ansstr = str2.replace(/[^a-zA-Z0-9]/g, '');
            if (dostr === ansstr) {
              score++;
              data.answers[key].correct = 1;
            }
          }
          break;
        case 'radio':
          if (solv.answers[key].answer == data.answers[key].ans) {
            score++;
            data.answers[key].correct = 1;
          }
          break;
        case 'drop_down':
          if (solv.answers[key].answer == data.answers[key].ans) {
            score++;
            data.answers[key].correct = 1;
          }
          break;
        case 'checkbox':
          for (i = 0; i < solv.answers[key].answer.length; i++) {
            for (j = 0; j < data.answers[key].ans.length; j++) {
              if (data.answers[key].ans[j] == solv.answers[key].answer[i]) {
                score++;
                data.answers[key].correct = 1;
              }
            }
          }
          break;
      }
    }
    return score;
  }

  function getData() {
    var sid = $("input.get-qid").val();
    var data = {'sec_id': sid, 'answers': {}};
    var type_blank = $('.type_blank');
    type_blank.each(function () {
      var textInput = $(this).find('input[type="text"]');
      textInput.each(function () {
        var num = $(this).attr('data-num');
        var type = $(this).attr('data-q_type');
        var ans = $(this).val();
        data.answers[num] = {'num': num, 'type': type, 'ans': ans};
      });
    });
    var type_drop = $('.drop_down');
    type_drop.each(function () {
      var dropInput = $(this).find('select[class="iot-question"]');
      dropInput.each(function () {
        var num = $(this).attr('data-num');
        var type = $(this).attr('data-q_type');
        var ans = $('#q-' + num + ' option:selected').val();
        data.answers[num] = {'num': num, 'type': type, 'ans': ans};
      });
    });
    var type_radio = $('.type_radio');
    type_radio.each(function () {
      var num = $(this).attr('data-num');
      var type = $(this).attr('data-q_type');
      var ans = $(this).find('input[name="q-' + num + '"]:checked').val();
      data.answers[num] = {'num': num, 'type': type, 'ans': ans};
    });
    var type_checkbox = $('.type_checkbox');
    type_checkbox.each(function () {
      var num = $(this).find('.iot-question').attr('data-num');
      var type = $(this).find('.iot-question').attr('data-q_type');
      var ans = $(this).find('input[name="q-' + num + '"]:checked').map(function () {
        return this.value;
      }).get();
      data.answers[num] = {'num': num, 'type': type, 'ans': ans};
    });
    return data;
  }

  function getCsrfToken(callback) {
    $.get(Drupal.url('rest/session/token'))
      .done(function (data) {
        var csrfToken = data;
        callback(csrfToken);
      });
  }

  function postNode(csrfToken, node, status) {
    console.log(node);
    $.ajax({
      url: '/api/store-result?_format=json',
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': csrfToken
      },
      data: JSON.stringify(node),
      success: function (node) {
        if (status == 1) {
          window.location.pathname = '/' + node;
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.log("Status: " + textStatus);
        console.log("Error: " + errorThrown);
      }
    });
  }

  function storeLocal(quiz_id) {
    var uid = drupalSettings.user.uid;
    var history = drupalSettings.storage;
    var store = {
      'id': quiz_id,
      'data': {}
    };
    if (history) {
      for (var i = 0; i < history.length; i++) {
        store.data[history[i]['num']] = {'num': history[i]['num'], 'ans': history[i]['ans']};
        localStorage.setItem(quiz_id + uid, JSON.stringify(store));
      }
    }
    var storage = JSON.parse(localStorage.getItem(quiz_id + uid));
    if (storage !== null) {
      store = storage;
    }
    var uid = drupalSettings.user.uid;
    var texts = $('input[type="text"]');
    texts.each(function () {
      var num = $(this).attr('data-num');
      if (storage !== null && typeof storage.data[num] !== 'undefined') {
        $(this).val(storage.data[num].ans);
        $('.qp-item-' + num).removeClass('qp-item-unanswered').addClass('qp-item-answered');
        $('#txtq' + num).text(storage.data[num].ans);
      }
      $(this).on('change', function () {
        store.data[num] = {'num': num, 'ans': $(this).val()};
        localStorage.setItem(quiz_id + uid, JSON.stringify(store));
        if ($(this).val()) {
          $('.qp-item-' + num).removeClass('qp-item-unanswered').addClass('qp-item-answered');
        } else {
          $('.qp-item-' + num).removeClass('qp-item-answered').addClass('qp-item-unanswered');
        }
        if (uid) {
          submit(0);
        }
        $('#txtq' + num).text($(this).val());
      });
    });
    var type_radio = $('.type_radio');
    type_radio.each(function () {
      var num = $(this).attr('data-num');
      if (storage !== null && typeof storage.data[num] !== 'undefined') {
        $('input[name="q-' + num + '"][value=' + storage.data[num].ans + ']').attr('checked', 'checked');
        $(this).val(storage.data[num].ans);
        $('.qp-item-' + num).removeClass('qp-item-unanswered').addClass('qp-item-answered');
        $('#txtq' + num).text(storage.data[num].ans);
      }
      $('input[name="q-' + num + '"]').on('click change', function (e) {
        store.data[num] = {'num': num, 'ans': $(this).val()};
        localStorage.setItem(quiz_id + uid, JSON.stringify(store));
        if ($(this).val()) {
          $('.qp-item-' + num).removeClass('qp-item-unanswered').addClass('qp-item-answered');
        } else {
          $('.qp-item-' + num).removeClass('qp-item-answered').addClass('qp-item-unanswered');
        }
        $('#txtq' + num).text($(this).val());
        if (uid) {
          submit(0);
        }
      });
    });
    var type_drop = $('select[class="iot-question"]');
    type_drop.each(function () {
      var num = $(this).attr('data-num');
      if (storage !== null && typeof storage.data[num] !== 'undefined') {
        $(this).val(storage.data[num].ans);
        $('.qp-item-' + num).removeClass('qp-item-unanswered').addClass('qp-item-answered');
        $('#txtq' + num).text(storage.data[num].ans);
      }
      $('#q-' + num).on('change', function (e) {
        store.data[num] = {'num': num, 'ans': $(this).val()};
        localStorage.setItem(quiz_id + uid, JSON.stringify(store));
        if ($(this).val()) {
          $('.qp-item-' + num).removeClass('qp-item-unanswered').addClass('qp-item-answered');
        } else {
          $('.qp-item-' + num).removeClass('qp-item-answered').addClass('qp-item-unanswered');
        }
        if (uid) {
          submit(0);
        }
        $('#txtq' + num).text($(this).val());
      });
    });
    var type_checkbox = $('.type_checkbox');
    type_checkbox.each(function () {
      var num = $(this).find('.iot-question').attr('data-num');
      if (storage !== null && typeof storage.data[num] !== 'undefined') {
        for (var i = 0; i < storage.data[num].ans.length; i++) {
          $('input[name="q-' + num + '"][value=' + storage.data[num].ans[i] + ']').attr('checked', true);
          $(this).val(storage.data[num].ans);
          $('.qp-item-' + num).removeClass('qp-item-unanswered').addClass('qp-item-answered');
          if (uid) {
            submit(0);
          }
          $('#txtq' + num).text(storage.data[num].ans.join());
        }
      } else {
        store.data[num] = {'num': num, 'ans': []};
      }
      $('input[name="q-' + num + '"]').on('click change', function (e) {
        if (storage !== null && typeof storage.data[num] !== 'undefined') {
          store.data[num] = storage.data[num];
        }
        var ans = $('#q-' + num).find('input[name="q-' + num + '"]:checked').map(function () {
          return this.value;
        }).get();
        store.data[num] = {'num': num, 'ans': ans};
        localStorage.setItem(quiz_id + uid, JSON.stringify(store));
        if (ans) {
          $('.qp-item-' + num).removeClass('qp-item-unanswered').addClass('qp-item-answered');
        } else {
          $('.qp-item-' + num).removeClass('qp-item-answered').addClass('qp-item-unanswered');
        }
        submit(0);
        $('#txtq' + num).text(store.data[num].ans.join());
      });
    });
  }

  function initTimer(quiz_id) {
    var storage = JSON.parse(localStorage.getItem('time_' + quiz_id));
    var min = $(".innertimmer").val();
    var sec = 0;
    if (storage !== null) {
      min = storage.min;
      sec = storage.sec;
    }
    if (min <= 0) {
      min = 40;
    }
    var countDownDate = new Date().getTime() + ((min * 60 + sec) * 1000) + 1000;
// Update the count down every 1 second
    var x = setInterval(function () {

      // Get todays date and time
      var now = new Date().getTime();

      // Find the distance between now an the count down date
      var distance = countDownDate - now;

      // Time calculations for days, hours, minutes and seconds
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);
      if (seconds < 10) {
        seconds = '0' + seconds;
      }

      // Output the result in an element with id="demo"
      document.getElementById("stopwatch").innerHTML = minutes + ":" + seconds;

      // If the count down is over, write some text
      if (distance < 0) {
        clearInterval(x);
        document.getElementById("stopwatch").innerHTML = "00:00";
        $('#modal-expired').modal('show');
      }
      var uid = drupalSettings.user.uid;
      if (seconds % 5 == 0) {
        if (uid) {
          var store = {'min': minutes, 'sec': seconds};
          localStorage.setItem('time_' + quiz_id, JSON.stringify(store));
        }
      }
    }, 1000);
  }
})(jQuery, Drupal, drupalSettings);
