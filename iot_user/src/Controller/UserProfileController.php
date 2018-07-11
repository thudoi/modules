<?php

namespace Drupal\iot_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\user\Entity\User;
use Masterminds\HTML5\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DashboardController.
 */
class UserProfileController extends ControllerBase {

  /**
   * Dashboard.
   *
   * @return string
   *   Return Hello string.
   */
  public function login() {
    $user = \Drupal::currentUser();
    if ($user->id() > 0) {
      $response = new RedirectResponse('/account/profile');
      $response->send();
    }
    $return = '/';
    if (isset($_GET['destination'])) {
      $return = $_GET['destination'];
    }
    if (isset($_GET['submit'])) {
      $return = $return . '?submit=' . $_GET['submit'];
    }
    //var_dump($return);die;

    $submit = '';
    if (isset($_GET['submit'])) {
      $submit = $_GET['submit'];
    }
    return [
      '#theme' => 'iot_user_login',
      '#destination' => $return,
      '#submit' => $submit,
      '#attached' => ['library' => ['iot_user/iot_account',],],
    ];
  }

  /**
   * Implement Register
   *
   * @return array
   */
  public function register() {
    $user = \Drupal::currentUser();
    if ($user->id() > 0) {
      $response = new RedirectResponse('/account/profile');
      $response->send();
    }
    $return = '/';
    if (isset($_GET['destination'])) {
      $return = $_GET['destination'];
    }
    return [
      '#theme' => 'iot_user_register',
      '#destination' => $return,
      '#attached' => ['library' => ['iot_user/iot_account',],],
    ];
  }

  /**
   * Implement Register
   *
   * @return array
   */
  public function buildProfile() {
    $user = \Drupal::currentUser();

    if ($user->id() <= 0) {
      $response = new RedirectResponse('/account/login');
      $response->send();
    }
    $return = '/';
    if (isset($_GET['destination'])) {
      $return = $_GET['destination'];
    }
    $fields = [];
    $fields['field_target_score'] = $this->getFieldNameSetting('field_target_score');
    $fields['field_previous_score'] = $this->getFieldNameSetting('field_previous_score');
    $fields['field_practicing'] = $this->getFieldNameSetting('field_practicing');
    $fields['field_destination'] = $this->getFieldNameSetting('field_destination');
    return [
      '#theme' => 'iot_user_build_profile',
      '#destination' => $return,
      '#fields' => $fields,
      '#attached' => ['library' => ['iot_user/iot_account',],],
    ];
  }

  /**
   * Callback to login
   */
  public function loginCallback() {
    $request = Request::createFromGlobals();
    $service = \Drupal::service('user.auth');
    $email = FALSE;
    $password = FALSE;
    $username = FALSE;
    $data = 0;
    if (isset($_POST['account_email'])) {
      $email = $_POST['account_email'];
      if (valid_email_address($email)) {
        $account_search = \Drupal::entityTypeManager()
          ->getStorage('user')
          ->loadByProperties(['mail' => $email]);
      }
      else {
        $account_search = \Drupal::entityTypeManager()
          ->getStorage('user')
          ->loadByProperties(['name' => $email]);
      }
      $account = reset($account_search);
      if ($account) {
        $username = $account->get('name')->value;
      }
    }
    if (isset($_POST['account_password'])) {
      $password = $_POST['account_password'];
    }
    if ($username) {
      if ($uid = $service->authenticate($username, $password)) {
        /** @var \Drupal\user\UserInterface $user */
        $user = User::load($uid);
        user_login_finalize($user);
        $data = 1;
        print $data;
        exit();
      }
      else {
        print $data;
        exit();
      }
    }
    else {
      print $data;
      exit();
    }
  }

  /**
   * Register call back
   */
  public function registerCallback() {
    $username = FALSE;
    $email = FALSE;
    $password = FALSE;
    $data = 1;
    if (isset($_POST['account_username'])) {
      $username = $_POST['account_username'];
      $account_search = \Drupal::entityTypeManager()
        ->getStorage('user')
        ->loadByProperties(['name' => $username]);
      if ($account_search) {
        $data = $this->t('Your username is ready exist. Please try to <a class="use-ajax" data-dialog-type="modal" href="/account/login"><b>login</b></a> or <a href="/user/password"><b>forgot password</b></a>.');
        print $data;
        exit();
      }
    }
    if (isset($_POST['account_email'])) {
      $email = $_POST['account_email'];
      $account_search = \Drupal::entityTypeManager()
        ->getStorage('user')
        ->loadByProperties(['mail' => $email]);
      if ($account_search) {
        $data = $this->t('Your email is ready exist. Please try to <a class="use-ajax" data-dialog-type="modal" href="/account/login"><b>login</b></a> or <a href="/user/password"><b>forgot password</b></a>.');
        print $data;
        exit();
      }
    }
    if (isset($_POST['account_password'])) {
      $password = $_POST['account_password'];
    }
    if ($username && $email && $password) {
      try {
        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $user = \Drupal\user\Entity\User::create();

        // Mandatory.
        $user->setPassword($password);
        $user->enforceIsNew();
        $user->setEmail($email);
        $user->setUsername($username);

        // Optional.
        $user->set('init', $email);
        $user->set('langcode', $language);
        $user->set('preferred_langcode', $language);
        $user->set('preferred_admin_langcode', $language);
        $user->activate();

        // Save user account.
        $result = $user->save();
        $account_search = \Drupal::entityTypeManager()
          ->getStorage('user')
          ->loadByProperties(['name' => $username]);
        $account = reset($account_search);
        user_login_finalize($account);
        $data = 1;
        print $data;
        exit();
      } catch (\Exception $ex) {
        print $ex->getMessage();
        exit();
      }
    }
  }

  /**
   * @param $field_name
   *
   * @return mixed
   * Get Field Name
   */
  public function getFieldNameSetting($field_name) {
    $fields = \Drupal::entityManager()
      ->getStorage('field_storage_config')
      ->loadByProperties(['field_name' => $field_name]);
    $field_storage = reset($fields);
    $storage = $field_storage->toArray();
    return $storage['settings']['allowed_values'];
  }

  public function buildProfileCallback() {
    // var_dump($_POST);die;
    $user = \Drupal::currentUser();
    $account = User::load($user->id());
    $field_ielts = FALSE;
    if (isset($_POST['field_ima'])) {
      $field_ima = $_POST['field_ima'];
      $account->set('field_ima', $field_ima);
    }
    if (isset($_POST['field_ielts'])) {
      $field_ielts = $_POST['field_ielts'];
    }
    if ($field_ielts && $field_ielts == 'I have an IELTS score') {
      $account->set('field_ielts', $field_ielts);
      if (isset($_POST['field_previous_score'])) {
        $field_previous_score = $_POST['field_previous_score'];
        $account->set('field_previous_score', $field_previous_score);
      }
      if (isset($_POST['field_date'])) {
        $account->set('field_month_year_exam', $_POST['field_date']);
      }
    }
    else {
      $account->set('field_ielts', $field_ielts);
    }

    if (isset($_POST['field_target_score'])) {
      $field_target_score = $_POST['field_target_score'];
      $account->set('field_target_score', $field_target_score);
    }
    if (isset($_POST['field_practicing'])) {
      $field_practicing = $_POST['field_practicing'];
      $account->set('field_practicing', $field_practicing);
    }
    if (isset($_POST['field_destination'])) {
      $destination = [];
      $rate = [];
      foreach ($_POST['field_destination'] as $des) {
        $destination[] = $des['key'];
        $rate[] = $des['key'] . '-' . $des['rate'];
      }

      //      ddl($field_data_rating);
      $account->set('field_destination', $destination);
      $account->set('field_country_rate', $rate);
    }
    try {
      $account->save();
      print 1;
      exit();
    } catch (Exception $e) {
      print $e->getMessage();
    }


  }

  /**
   * @return array
   */
  public function loginVerify() {
    if (isset($_POST['des'])) {
      \Drupal::state()->set('destination_social', $_POST['des']);
    }
    print TRUE;
    exit();
    return [];
  }

}
