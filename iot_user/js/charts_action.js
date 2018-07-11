(function ($, Drupal) {
  Drupal.behaviors.ChartAction = {
    attach: function (context, settings) {

      /**Preload**/
      $(".ab-right a").each(function () {

        var _class = $(this).attr('data');
        $(this).click(function () {
          event.preventDefault();
          $(".ab-right a").removeClass('active');
          $(this).addClass('active');

          //$(".chart-draw .chart-item").removeClass('hidden');
          $(".chart-draw .chart-item").addClass('hidden');
          $(".chart-draw ." + _class).removeClass('hidden');
        })
      });
      var dateFrom = $("#analytics-form input.datefrom").val();
      var dateTo = $("#analytics-form input.dateto").val();

      $("#analytics-form input.dateto").blur(function () {
        if ($(this).val() != dateTo) {
          $("#analytics-form").submit();
        }

      });
      $("#analytics-form input.datefrom").blur(function () {
        if ($(this).val() != dateFrom) {
          $("#analytics-form").submit();
        }
      });


    }
  }
})(jQuery, Drupal);
