(function ($, Drupal) {
  $(document).ready(function () {
    $('.h5p-radio li.h5p-sc-alternative').on('click', function () {
      $('.h5p-radio li.h5p-sc-alternative').removeClass('h5p-sc-selected');
      $(this).addClass('h5p-sc-selected');
    });
  });
  Drupal.behaviors.singlechoice = {
    attach: function (context, settings) {
    }
  }
})(jQuery, Drupal);
