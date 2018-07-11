<?php

namespace Drupal\iot_ielts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class SetController.
 */
class ReportController extends ControllerBase {

  public function reportMistake($node) {
    $user = \Drupal::currentUser();
    $account = User::load($user->id());
    $set = Node::load($node->get('field_set')->target_id);
    $collection = Node::load($set->get('field_collection')->target_id);
    $name = FALSE;
    if ($account->get('field_first_name')->value) {
      $name = $account->get('field_first_name')->value;
    }
    if ($account->get('field_last_name')->value) {
      $name = $account->get('field_last_name')->value . ' ' . $name;
    }
    if (!$name) {
      $name = $account->getAccountName();
    }
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $url = \Drupal::service('path.alias_manager')
      ->getAliasByPath('/node/' . $node->id());
    $url = $host . $url;
    if (isset($_GET['url'])) {
      $url = $_GET['url'];
    }
    $number = [];
    for ($i = 1; $i < 41; $i++) {
      $number[$i] = t('Question ') . $i;
    }
    $number['other'] = t('Other');
    if ($user->id() > 0) {
      $name = $name;
    }
    else {
      $name = FALSE;
    }
    $qid = FALSE;
    if (isset($_GET['qid'])) {
      $qid = $_GET['qid'];
    }

    return [
      '#theme' => ['iot_report_mistake'],
      '#node' => $node,
      '#collection' => $collection,
      '#name' => $name,
      '#user' => $account,
      '#number' => $number,
      '#url' => $url,
      '#qid' => $qid,
      '#attached' => ['library' => ['iot_ielts/report_mistake',],],
    ];

  }

  /**
   *
   */
  public function reportMistakeCallback() {
    $node = Node::create(['type' => 'report_mistake']);
    $quiz = Node::load($_POST['quiz']);
    $collection_id = $_POST['collection'];
    $node->set('title', $quiz->get('field_title_ui')->value);
    $node->set('field_description', $_POST['message']);
    if (isset($_POST['name'])) {
      $node->set('field_report_name', $_POST['name']);
    }
    if (isset($_POST['email'])) {
      $node->set('field_report_email', $_POST['email']);
    }
    $country = ip2country_get_country(\Drupal::request()->getClientIp());
    $node->set('uid', 1);
    $node->set('field_quiz', $_POST['quiz']);
    $node->set('field_collection', $_POST['collection']);
    $node->set('field_url', $_POST['url']);
    $node->set('field_question_number', $_POST['question']);
    $node->set('status', 0);
    $node->set('field_location', $country);
    $node->set('field_browser', get_browser_name($_SERVER['HTTP_USER_AGENT']));
    $node->enforceIsNew();
    try {
      $node->save();
      print 'ok';
      exit();
    } catch (\Exception $e) {
      print $e->getMessage();
      exit();
    }

  }

  public function reportMistakeView($node) {
    $quiz = Node::load($node->get('field_quiz')->target_id);
    $set = Node::load($quiz->get('field_set')->target_id);
    $collection = Node::load($set->get('field_collection')->target_id);
    $name = FALSE;
    if ($node->get('field_report_name')) {
      $name = $node->get('field_report_name')->value;
    }
    $url = $node->get('field_url')->value;
    $number = $node->get('field_question_number')->value;
    $email = $node->get('field_report_email')->value;
    return [
      '#theme' => ['iot_report_view'],
      '#node' => $node,
      '#quiz' => $quiz,
      '#collection' => $collection,
      '#name' => $name,
      '#email' => $email,
      '#number' => $number,
      '#url' => $url,
      '#attached' => ['library' => ['iot_ielts/report_mistake_view',],],
    ];
  }

  public function reportMistakeResolved($node) {
    $node->set('status', 1);
    $node->save();
    drupal_set_message(t('The report has been resolved.'));
    $response = new RedirectResponse('/admin/report/mistake');
    $response->send();

  }

  public function reportViewCallback() {
    $mailManager = \Drupal::service('plugin.manager.mail');
    $params = [];
    if (isset($_POST['subject'])) {
      $params['subject'] = $_POST['subject'];
    }
    if (isset($_POST['message'])) {
      $body = $_POST['message'];
    }
    $to = $_POST['email'];
    $reply = 'hi@ieltsonlinetests.com';
    $params['body'] = [\Drupal\Core\Mail\MailFormatHelper::htmlToText($body)];
    $sendEmail = \Drupal::service('plugin.manager.mail')
      ->mail('smtp', 'smtp-test', $to, 'en', $params, $reply);
    if ($sendEmail) {
      print 'ok';
      exit();
    }
    else {
      print $sendEmail;
      exit();
    }

  }

}


