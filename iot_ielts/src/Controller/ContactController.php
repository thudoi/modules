<?php

namespace Drupal\iot_ielts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\simplenews\Entity\Subscriber;
use Drupal\simplenews\SubscriberInterface;

/**
 * Class SetController.
 */
class ContactController extends ControllerBase {

  /**
   * Section Manager.
   *
   * @return array
   *   Return template.
   */
  public function Contact() {
    $mailManager = \Drupal::service('plugin.manager.mail');
    $params = [];
    if (isset($_POST['name'])) {
      $params['name'] = $_POST['name'];
    }
    if (isset($_POST['email'])) {
      $params['email'] = $_POST['email'];
    }
    if (isset($_POST['title'])) {
      $params['subject'] = $_POST['title'];
    }
    if (isset($_POST['message'])) {
      $params['message'] = $_POST['message'];
    }

    $to = 'hi@ieltsonlinetests.com';
    $sendEmail = FALSE;
    if ($params) {
      $body = "<p>" . $params['message'] . "</p><p>Name: " . $params['name'] . "</p><p> Email: " . $params['email'] . "</p>";
      $params['body'] = [\Drupal\Core\Mail\MailFormatHelper::htmlToText($body)];
      $sendEmail = \Drupal::service('plugin.manager.mail')
        ->mail('smtp', 'smtp-test', $to, 'en', $params, $params['email']);
    }
    if ($sendEmail) {
      print 'ok';
      exit();
    }
    else {
      print $sendEmail;
      exit();
    }

  }

  /**
   * @return array
   */
  public function ContactUs() {
    return [
      '#theme' => ['iot_contact_page'],
      '#attached' => ['library' => ['iot_ielts/contact_form',],],
    ];
  }

  /**
   * @return array
   */
  public function subscriberCallback() {
    if (isset($_POST['email'])) {
      $email = $_POST['email'];
      $subscriber = simplenews_subscriber_load_by_mail($email);
      if (!$subscriber) {
        $subscriber = Subscriber::create([]);
        $subscriber->setMail($email);
        $subscriber->setLangcode('');
        $subscriber->setStatus(SubscriberInterface::ACTIVE);
        $subscriber->save();
      }
      print 'ok';
      exit();
      return [];
    }
  }


}


