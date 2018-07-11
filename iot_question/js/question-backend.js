(function ($, Drupal) {
  $(document).ready(function () {
    $('.hp5-checkboxes li.h5p-answer').on('click', function () {
      if ($(this).hasClass('h5p-selected')) {
        $(this).removeClass('h5p-selected');
        $(this).attr('aria-checked', false);
      } else {
        $(this).addClass('h5p-selected');
        $(this).attr('aria-checked', true);
      }
    });
  });
  Drupal.behaviors.backend = {
    attach: function (context, settings) {
      var selector = $('div[id="field-question-add-more-wrapper"] input[type="radio"]');
      selector.each(function () {
        $(this).on('click', function () {
          var parent = $(this).parent().parent().parent().parent().parent().parent().parent();
          parent.find('input[type="radio"]').prop('checked', false);
          $(this).prop('checked', true);
        });
      });
    }
  }
})(jQuery, Drupal);
