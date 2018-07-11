<?php
/**
 * Created by PhpStorm.
 * User: cadic
 * Date: 11/14/17
 * Time: 14:48
 */

use Drupal\Core\Url;

/**
 * Implement hook_form_alter()
 */
function iot_user_form_alter(&$form, &$form_state, $form_id) {
  switch ($form_id) {
    //profile form
    case 'user_form':
      $user = \Drupal::currentUser();
      if ($user->id() > 1) {
        $form['username'] = [
          '#markup' => $form['account']['name']['#default_value'],
          '#title' => t('Username'),
        ];
        $form['email_address'] = [
          '#markup' => $form['account']['mail']['#default_value'],
          '#title' => t('Email'),
        ];
        $form['#theme'] = ['iot_user_edit_profile'];
      }

      break;
    //register form
    case 'user_register_form':
      $user = \Drupal::currentUser();
      if ($user->id() <= 0) {
        $facebook_icon = base_path() . drupal_get_path('module', 'iot_user') . '/images/facebook_logo.svg';
        $form['facebook_icon'] = ['#markup' => $facebook_icon,];
        $google_icon = base_path() . drupal_get_path('module', 'iot_user') . '/images/google_logo.svg';
        $form['google_icon'] = ['#markup' => $google_icon,];
        $form['#theme'] = ['iot_user_form_register'];
        $form['#attached'] = ['library' => ['iot_user/iot_account',],];
      }
      break;
    case 'user_login_form':
      $form['#theme'] = ['iot_user_login'];
      $form['#attached'] = ['library' => ['iot_user/iot_account',],];
      break;

  }
}

