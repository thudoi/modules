<?php

namespace Drupal\iot_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class AnalyticsController.
 */
class AnalyticsController extends ControllerBase {

  /**
   * Dashboard.
   *
   * @return string
   *   Return Hello string.
   */
  public function dashboard() {
    if (\Drupal::currentUser()->id() <= 0) {
      $response = new RedirectResponse('/account/login?destination=/account/analytics');
      $response->send();
    }
    $user = User::load(\Drupal::currentUser()->id());
    $service = \Drupal::service('iot_quiz.userservice');
    $analytics = $service->UserAnalytic();
    return [
      '#theme' => 'iot_user_analytics',
      '#user' => $user,
      '#bandscorechart' => $this->getChartBandScore($user),
      '#accuracy' => $this->getChartAcurracy($user),
      '#timespend' => $this->getChartTimeSpend($user),
      '#perform' => $this->getChartPerform($user),
      '#listening' => $this->getChartListening($user),
      '#reading' => $this->getChartReading($user),
      '#analytics' => $analytics,
      '#dataChart' => $this->getChartData($analytics),
      '#attached' => ['library' => ['iot_user/iot_analytics',],],
    ];
  }

  /**
   * @param $user
   *
   * @return array
   */
  public function getChartBandScore($user) {
    $service = \Drupal::service('iot_quiz.userservice');
    $analytics = $service->UserAnalytic();
    return ['#theme' => 'iot_band_score_chart',];
  }

  /**
   * @param $user
   *
   * @return array
   */
  public function getChartAcurracy($user) {
    return ['#theme' => 'iot_accuracy_chart',];
  }

  /**
   * @param $user
   *
   * @return array
   */
  public function getChartTimeSpend($user) {
    return ['#theme' => 'iot_timespend_chart',];
  }

  /**
   * @param $user
   *
   * @return array
   */
  public function getChartPerform($user) {
    return ['#theme' => 'iot_perform_chart',];
  }

  /**
   * @param $user
   *
   * @return array
   */
  public function getChartListening($user) {
    return ['#theme' => 'iot_listening_chart',];
  }

  /**
   * @param $user
   *
   * @return array
   */
  public function getChartReading($user) {
    return ['#theme' => 'iot_reading_chart',];
  }

  /**
   * @param $data
   *
   * @return array
   */
  public function getChartData($data) {
    return ['#theme' => 'iot_data_chart', '#data' => $data];
  }


}
