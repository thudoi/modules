<?php
/**
 * Created by PhpStorm.
 * User: mrcad
 * Date: 11/23/2017
 * Time: 10:23 AM
 */

namespace Drupal\iot_quiz\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;
use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PrintController extends ControllerBase {

  public function printDownload(NodeInterface $node) {

    $host = \Drupal::request()->getSchemeAndHttpHost();
    $alias = \Drupal::service('path.alias_manager')
      ->getAliasByPath('/node/' . $node->id());
    $url = $host . $alias;
    $short_url = $this->send($url);
    $type = $node->get('field_quiz_type')->value;
    $title = $node->get('field_title_ui')->value;
    $service_question = \Drupal::service('iot_quiz.questionservice');
    $return = [];
    switch ($type) {
      case 'listening':
        $service = \Drupal::service('iot_quiz.quizservice');

        $content = $service->get_question($node, 'listening', 'solution', 'print');

        $data = [];
        $set = Node::load($node->get('field_set')->target_id);
        $collection = Node::load($set->get('field_collection')->target_id);
        $variables['node'] = $node;
        foreach ($content['answers']['answers'] as $key => $answer) {
          switch ($answer['type']) {
            case 'blank':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['prefix'],
              ];
              break;
            case 'radio':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'drop_down':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'checkbox':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => implode(',', $answer['answer']),
              ];
              break;
          }
        }
        $collection_theme = [
          '#theme' => 'iot_collection_header',
          '#collection' => $collection,
          '#type' => 'listening',
          '#title' => $title,
        ];
        $collection_header = render($collection_theme);
        $score_table_theme = [
          '#theme' => 'iot_result_question',
          '#result' => $data,
        ];


        $score_table = render($score_table_theme);
        $return = [
          '#theme' => ['iot_print_listening'],
          '#node' => $node,
          '#result' => $data,
          '#secs' => $content['secs'],
          '#audio' => $content['audio'],
          '#collection_header' => $collection_header,
          '#answers' => $content['answers'],
          '#score_table' => $score_table,
          '#url' => $short_url,
          '#attached' => [
            'library' => [
              'iot_quiz/iot_printpdf',
              'iot_quiz/iot_result',
              'iot_quiz/iot_frontend',
            ],
          ],
        ];
        // return $this->downloadPdf($return,'listening');
        break;
      case 'reading':
        $service = \Drupal::service('iot_quiz.quizservice');
        $content = $service->get_question($node, 'reading', 'solution');
        $set = Node::load($node->get('field_set')->target_id);
        $collection = Node::load($set->get('field_collection')->target_id);
        $data = [];
        foreach ($content['answers']['answers'] as $key => $answer) {
          switch ($answer['type']) {
            case 'blank':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['prefix'],
              ];
              break;
            case 'radio':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'drop_down':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'checkbox':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => implode(',', $answer['answer']),
              ];
              break;
          }
        }
        $collection_theme = [
          '#theme' => 'iot_collection_header',
          '#collection' => $collection,
          '#type' => 'reading',
          '#title' => $title,
        ];
        $collection_header = render($collection_theme);
        $score_table_theme = [
          '#theme' => 'iot_result_question',
          '#result' => $data,
          '#class' => 'green',
        ];
        $score_table = render($score_table_theme);
        $return = [
          '#theme' => ['iot_print_reading'],
          '#node' => $node,
          '#result' => $data,
          '#collection_header' => $collection_header,
          '#secs' => $content['secs'],
          '#answers' => $content['answers'],
          '#score_table' => $score_table,
          '#url' => $short_url,
          '#attached' => [
            'library' => [
              'iot_quiz/iot_printpdf',
              'iot_quiz/iot_result',
              'iot_quiz/iot_frontend',
            ],
          ],
        ];
        //  return $this->downloadPdf($return);
        break;
    }
    return $return;
  }


  public function downloadPdf($html, $name = 'mytest') {
    $pdf = $this->generateSimplePdf($html);
    // Tell the browser that this is not an HTML file to show, but a pdf file to
    // download.
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdf));
    header('Content-Disposition: attachment; filename="' . $name . '.pdf"');
    print $pdf;
    return [];
  }


  /**
   * Generates a pdf file using TCPDF module.
   *
   * @return string Binary string of the generated pdf.
   */
  protected function generateSimplePdf($htmlData) {
    // Get the content we want to convert into pdf.
    $html = '';
    $host = \Drupal::request()->getSchemeAndHttpHost();
    //  var_dump($host);die;
    //$html .='<style>'.file_get_contents($host.'/themes/iot/css/main.css').'</style>';
    //  $html .='<style>'.file_get_contents('http://iot.local/themes/iot/css/custom.css').'</style>';
    //  $html .='<style>'.file_get_contents('http://iot.local/modules/custom/iot_quiz/css/frontend.css').'</style>';
    //  $html .='<style>'.file_get_contents('http://iot.local/modules/custom/iot_quiz/css/result.css').'</style>';
    //  $html .='<style>'.file_get_contents($host.'/modules/custom/iot_quiz/css/print.css').'</style>';
    $html .= \Drupal::service('renderer')->render($htmlData);
    $pattern = '/<input (.+?) \>\]/';
    $html = preg_replace('/<input (.*?) \>/', '_______{it}$1{/it}', $html);
    var_dump($html);
    die;
    // Never make an instance of TCPDF or TCPDFDrupal classes manually.
    // Use tcpdf_get_instance() instead.
    $tcpdf = tcpdf_get_instance();
    /* DrupalInitialize() is an extra method added to TCPDFDrupal that initializes
    *  some TCPDF variables (like font types), and makes possible to change the
    *  default header or footer without creating a new class.
    */
    $tcpdf->DrupalInitialize(['footer' => ['html' => 'Access http://ieltsonlinetests.com for more practices',]]);
    // Insert the content. Note that DrupalInitialize automatically adds the first
    // page to the pdf document.
    $tcpdf->writeHTML($html);

    return $tcpdf->Output('', 'S');
  }

  /**
   * @param $node
   */
  public function printDownloadAction($node) {
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $filepath = getcwd() . '/sites/default/files/';
    $filename = str_replace(' ', '', $node->get('field_title_ui')->value) . '-' . $node->id() . '.pdf';
    $url = $host . '/sites/default/files/' . $filename;
    $command = "xvfb-run -a --server-args=\"-screen 0, 1024x768x24\" /var/wkhtmltopdf --use-xserver --no-outline --margin-top '20' --margin-right '20' --margin-bottom '20' --margin-left '20' --disable-smart-shrinking --footer-html 'http://localhost:8000/themes/iot/templates/footer.html' --footer-right 'page [page]' 'http://localhost:8000/node/" . $node->id() . "/print/action' '" . $filepath . $filename . "' 
";
    //var_dump($command);die;
    if (!file_exists($filepath . $filename)) {
      shell_exec($command);
    }
    header("Content-Disposition: attachment; filename=" . urlencode($filename));
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Description: File Transfer");
    header("Content-Length: " . filesize($filename));
    flush(); // this doesn't really matter.
    $fp = fopen($url, "r");
    while (!feof($fp)) {
      echo fread($fp, 65536);
      flush(); // this is essential for large downloads
    }
    fclose($fp);

    return [];
  }

  function GoogleURLAPI() {
    // Keep the API Url
    return 'https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDoIJQFwOnZjJHIIlTI1G_ll8li8Y_08P0';
  }

  // Shorten a URL
  function shorten($url) {
    // Send information along
    $response = $this->send($url);
    // Return the result
    return isset($response['id']) ? $response['id'] : FALSE;
  }

  // Expand a URL
  function expand($url) {
    // Send information along
    $response = $this->send($url, FALSE);
    // Return the result
    return isset($response['longUrl']) ? $response['longUrl'] : FALSE;
  }

  // Send information to Google
  function send($url, $shorten = TRUE) {
    // Create cURL
    $ch = curl_init();
    // If we're shortening a URL...
    if ($shorten) {
      curl_setopt($ch, CURLOPT_URL, $this->GoogleURLAPI());
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["longUrl" => $url]));
      curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    }
    else {
      curl_setopt($ch, CURLOPT_URL, $this->GoogleURLAPI() . '&shortUrl=' . $url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // Execute the post
    $result = curl_exec($ch);
    // Close the connection
    curl_close($ch);
    // Return the result
    $data = json_decode($result, TRUE);
    return $data['id'];
  }


}
