(function ($, Drupal) {
  Drupal.behaviors.PrintPdf = {
    attach: function (context, settings) {
      $(".printpdf").click(function (e) {
        e.preventDefault();
        HTMLtoPDF();
      });
      // var pdf = new jsPDF('p', 'pt', 'letter');
      // var canvas = pdf.canvas;
      // canvas.height = 72 * 11;
      // canvas.width= 72 * 8.5;;
      // // can also be document.body
      // var html = $("#HTMLtoPDF")[0];
      // html2pdf(html, pdf, function(pdf) {
      //   pdf.output('dataurlnewwindow');
      // });
    }
  }
})(jQuery, Drupal);
