<?php

namespace Drupal\iot_quiz;

use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

define('READING', 1);
define('LISTENING', 0);

class UserService {

  /**
   * @return array
   */
  public function UserAnalytic() {
    $current_user = \Drupal::currentUser();
    $now = date('d/m/Y', time());
    $nowstamp = time();
    $laststamp = $nowstamp - (84600 * 28);
    $last = date('d/m/Y', $laststamp);
    if (isset($_GET['dateFrom'])) {
      $dateArr = explode('/', $_GET['dateFrom']);
      $date = date($dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0], ' 00:00:00');
      $laststamp = strtotime($date);
    }
    if (isset($_GET['dateTo'])) {
      $dateArr = explode('/', $_GET['dateTo']);
      $date = date($dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0], ' 23:59:59');
      $nowstamp = strtotime($date);
    }
    $days_between = ceil(abs($nowstamp - $laststamp) / 86400);
    $date = [];
    for ($i = 0; $i <= $days_between; $i++) {
      $date[date('d/m/Y', $laststamp + ($i * 86400))] = date('d/m/Y', $laststamp + ($i * 86400));
    }

    $sids = \Drupal::entityQuery('node')
      ->condition('type', 'score')
      ->condition('uid', $current_user->id())
      ->condition('status', 1)
      ->condition('created', [$laststamp, $nowstamp], 'BETWEEN')
      ->execute();
    $nodes = Node::loadMultiple($sids);
    $unanswered = 0;
    $totalQuestion = 0;
    $totalCorrect = 0;
    $totalInCorrect = 0;
    $totalsecond = 0;
    $totalAnswer = 0;
    $totalAccuracy = 0;
    $listArr = [];
    $totalScore = 0;
    $i = 1;
    $num = [];
    $test_taken = 0;
    foreach ($nodes as $node) {
      $scoreCorrect = $node->get('field_score')->value;// return 9/40
      $scoreArr = explode('/', $scoreCorrect);
      $score = $this->getScore($node);
      if ($score > 0) {
        if (!empty($node->get('body')->value)) {
          $seialize = unserialize($node->get('body')->value);
        }
        else {
          $seialize = FALSE;
        }
        $quiz = Node::load($node->get('field_score_quiz')->target_id);
        $durationSecond = intval($quiz->get('field_duration')->value) * 60;
        $time = $node->get('field_time')->value;// return 39:54
        $timeArr = explode(':', $time);
        $left = $durationSecond - (($timeArr[0] * 60) + $timeArr[1]);
        $totalsecond += $left;

        $totalCorrect += $scoreArr[0];
        $unanswered += $node->get('field_unanswered_question')->value;// return 21
        $totalInCorrect += $scoreArr[1] - ($node->get('field_unanswered_question')->value + $scoreArr[0]);
        $totalAccuracy += $scoreArr[0] * 100 / $scoreArr[1];

        $totalScore += $score;
        $totalQuestion += $scoreArr[1];
        $totalAnswer += $scoreArr[1] - $node->get('field_unanswered_question')->value;
        $num[] = $i;
        if ($quiz->get('field_quiz_type')->value == 'listening') {
          $listArr[$node->id()] = [
            'listening' => [
              'score' => $score,
              'test' => $i,
              'accuracy' => $scoreArr[0] * 100 / $scoreArr[1],
              'time' => $left,
              'date' => date('d/m/Y', $node->get('created')->value),
            ],
          ];
        }
        if ($quiz->get('field_quiz_type')->value == 'reading') {
          $listArr[$node->id()] = [
            'reading' => [
              'score' => $score,
              'test' => $i,
              'accuracy' => $scoreArr[0] * 100 / $scoreArr[1],
              'time' => $left,
              'date' => date('d/m/Y', $node->get('created')->value),
            ],
          ];
        }
        $test_taken += 1;
      }

      $i++;
    }
    //    $test_taken = count($nodes);
    $avarageTime = $totalsecond / $test_taken;
    $avarageAnserSecond = $totalsecond / $totalAnswer;
    $listening = $this->getListeningAnalytics();
    $reading = $this->getReadingAnalytics();
    $max_score = ($listening['max_score'] + $reading['max_score']) / 2;
    $scoreL = [];
    $scoreR = [];
    $accL = [];
    $accR = [];
    $timeR = [];
    $timeL = [];
    foreach ($listArr as $nid => $list) {
      if (isset($list['listening']) && $list['listening']['score'] != NULL) {
        $scoreL[$list['listening']['date']][$nid] = $list['listening']['score'];
      }
      if (isset($list['reading']) && $list['reading']['score'] != NULL) {
        $scoreR[$list['reading']['date']][$nid] = $list['reading']['score'];
      }
      if (isset($list['listening']) && $list['listening']['accuracy'] != NULL) {
        $accL[$list['listening']['date']][$nid] = $list['listening']['accuracy'];
      }
      if (isset($list['reading']) && $list['reading']['accuracy'] != NULL) {
        $accR[$list['reading']['date']][$nid] = $list['reading']['accuracy'];
      }
      if (isset($list['listening']) && $list['listening']['time'] != NULL) {
        $timeL[$list['listening']['date']][$nid] = $list['listening']['time'];
      }
      if (isset($list['reading']) && $list['reading']['time'] != NULL) {
        $timeR[$list['reading']['date']][$nid] = $list['reading']['time'];
      }
    }
    // $score_listening = implode(',', $scoreL);
    // $score_reading = implode(',', $scoreR);
    // $acc_listening = implode(',', $accL);
    // $acc_reading = implode(',', $accR);
    //  $time_listening = implode(',', $timeL);
    //  $time_reading = implode(',', $timeR);

    $totalPie = $totalCorrect + $totalInCorrect + $unanswered;
    $inQpercent = $totalInCorrect * 100 / $totalPie;
    $coQpercent = $totalCorrect * 100 / $totalPie;
    $unQpercent = $unanswered * 100 / $totalPie;
    $pie = [
      'total_unanswer' => $unQpercent,
      'total_incorrect' => $inQpercent,
      'total_correct' => $coQpercent,
    ];
    $chartLine = [
      'test' => implode(',', $num),
      'scoreL' => $scoreL,
      'scoreR' => $scoreR,
      'accL' => $accL,
      'accR' => $accR,
      'timeL' => $timeL,
      'timeR' => $timeR,
    ];
    $dateRange = implode(',', $date);
    return [
      'taken' => $test_taken,
      'avarage_score' => $this->formatNumber($totalScore / $test_taken, TRUE),
      'avarage_accuracy' => $this->formatNumber($totalAccuracy / $test_taken),
      'avarage_time' => gmdate("i:s", $avarageTime),
      'total_question' => $totalQuestion,
      'total_unanswer' => $unanswered,
      'total_incorrect' => $totalInCorrect,
      'total_correct' => $totalCorrect,
      'list' => $listArr,
      'listening' => $listening,
      'reading' => $reading,
      'dateTo' => date('d/m/Y', $nowstamp),
      'dateFrom' => date('d/m/Y', $laststamp),
      'time_per_question' => gmdate("i:s", $avarageAnserSecond),
      'max_score' => $this->formatNumber($max_score, TRUE),
      'chartLine' => $chartLine,
      'pei' => $pie,
      'dateRange' => $dateRange,
      'dataRange' => $date,

    ];
  }

  /**
   * @return array
   */
  public function getListeningAnalytics() {
    $current_user = \Drupal::currentUser();
    $now = date('d/m/Y', time());
    $nowstamp = time();
    $laststamp = $nowstamp - (84600 * 28);
    $last = date('d/m/Y', $laststamp);
    if (isset($_GET['dateFrom'])) {
      $dateArr = explode('/', $_GET['dateFrom']);
      $date = date($dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0], ' 00:00:00');
      $laststamp = strtotime($date);
    }
    if (isset($_GET['dateTo'])) {
      $dateArr = explode('/', $_GET['dateTo']);
      $date = date($dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0], ' 23:59:59');
      $nowstamp = strtotime($date);
    }

    $sids = \Drupal::entityQuery('node')
      ->condition('type', 'score')
      ->condition('uid', $current_user->id())
      ->condition('status', 1)
      ->condition('created', [$laststamp, $nowstamp], 'BETWEEN')
      ->execute();
    $nodes = Node::loadMultiple($sids);
    $unanswered = 0;
    $totalQuestion = 0;
    $totalCorrect = 0;
    $totalInCorrect = 0;
    $totalsecond = 0;
    $totalAccuracy = 0;
    $totalAnswer = 0;
    $totalScore = 0;
    $i = 1;
    $test_taken = 0;
    $arr_score_list = [];
    $performData = [];
    foreach ($nodes as $node) {
      $quiz = Node::load($node->get('field_score_quiz')->target_id);
      $scoreCorrect = $node->get('field_score')->value;// return 9/40
      $scoreArr = explode('/', $scoreCorrect);
      $score = $this->getScore($node);
      if ($quiz->get('field_quiz_type')->value == 'listening') {
        if ($score > 0) {
          if ($node->get('body')->value) {
            $seialize = unserialize(strip_tags($node->get('body')->value));
            if ($seialize) {
              foreach ($seialize as $sr) {
                $performData[] = $sr;
              }
            }

          }

          $durationSecond = intval($quiz->get('field_duration')->value) * 60;
          $time = $node->get('field_time')->value;// return 39:54
          $timeArr = explode(':', $time);
          $left = $durationSecond - (($timeArr[0] * 60) + $timeArr[1]);
          $totalsecond += $left;

          $totalCorrect += $scoreArr[0];
          $unanswered += $node->get('field_unanswered_question')->value;// return 21
          $totalInCorrect += $scoreArr[1] - ($node->get('field_unanswered_question')->value + $scoreArr[0]);
          $totalAccuracy += $scoreArr[0] * 100 / $scoreArr[1];

          $totalScore += $score;
          $totalQuestion += $scoreArr[1];
          $test_taken += 1;
          $totalAnswer += $scoreArr[1] - $node->get('field_unanswered_question')->value;
          $arr_score_list[] = $this->getScore($node);
        }
      }
      $i++;
    }
    if ($performData) {
      $perform = $this->getPerformTest($performData, LISTENING);
    }
    else {
      $perform = FALSE;
    }


    $avarageTime = $totalsecond / $test_taken;
    $avarageAnserSecond = $totalsecond / $totalAnswer;
    return [
      'taken' => $test_taken,
      'avarage_score' => $this->formatNumber($totalScore / $test_taken, TRUE),
      'avarage_accuracy' => $this->formatNumber($totalAccuracy / $test_taken),
      'avarage_time' => gmdate("i:s", $avarageTime),
      'total_question' => $totalQuestion,
      'total_unanswer' => $unanswered,
      'total_incorrect' => $totalInCorrect,
      'total_correct' => $totalCorrect,
      'time_per_question' => gmdate("i:s", $avarageAnserSecond),
      'max_score' => max($arr_score_list),
      'perform' => $perform,
    ];
  }

  /**
   * @return array
   */
  public function getReadingAnalytics() {
    $current_user = \Drupal::currentUser();
    $now = date('d/m/Y', time());
    $nowstamp = time();
    $laststamp = $nowstamp - (84600 * 28);
    $last = date('d/m/Y', $laststamp);
    if (isset($_GET['dateFrom'])) {
      $dateArr = explode('/', $_GET['dateFrom']);
      $date = date($dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0], ' 00:00:00');
      $laststamp = strtotime($date);
    }
    if (isset($_GET['dateTo'])) {
      $dateArr = explode('/', $_GET['dateTo']);
      $date = date($dateArr[2] . '-' . $dateArr[1] . '-' . $dateArr[0], ' 23:59:59');
      $nowstamp = strtotime($date);
    }

    $sids = \Drupal::entityQuery('node')
      ->condition('type', 'score')
      ->condition('uid', $current_user->id())
      ->condition('status', 1)
      ->condition('created', [$laststamp, $nowstamp], 'BETWEEN')
      ->execute();
    $nodes = Node::loadMultiple($sids);
    $unanswered = 0;
    $totalQuestion = 0;
    $totalCorrect = 0;
    $totalInCorrect = 0;
    $totalsecond = 0;
    $totalAccuracy = 0;
    $totalAnswer = 0;
    $totalScore = 0;
    $i = 1;
    $test_taken = 0;
    $arr_score_list = [];
    $performData = [];
    foreach ($nodes as $node) {
      $quiz = Node::load($node->get('field_score_quiz')->target_id);
      $scoreCorrect = $node->get('field_score')->value;// return 9/40
      $scoreArr = explode('/', $scoreCorrect);
      $score = $this->getScore($node);
      if ($quiz->get('field_quiz_type')->value == 'reading') {
        if ($score > 0) {
          if (!empty($node->get('body')->value)) {
            $seialize = unserialize($node->get('body')->value);
            if ($seialize) {
              foreach ($seialize as $sr) {
                $performData[] = $sr;
              }
            }
          }
          $durationSecond = intval($quiz->get('field_duration')->value) * 60;
          $time = $node->get('field_time')->value;// return 39:54
          $timeArr = explode(':', $time);
          $left = $durationSecond - (($timeArr[0] * 60) + $timeArr[1]);
          $totalsecond += $left;

          $totalCorrect += $scoreArr[0];
          $unanswered += $node->get('field_unanswered_question')->value;// return 21
          $totalInCorrect += $scoreArr[1] - ($node->get('field_unanswered_question')->value + $scoreArr[0]);
          $totalAccuracy += $scoreArr[0] * 100 / $scoreArr[1];

          $totalScore += $score;
          $totalQuestion += $scoreArr[1];
          $test_taken += 1;
          $totalAnswer += $scoreArr[1] - $node->get('field_unanswered_question')->value;
          $arr_score_list[] = $this->getScore($node);
        }
      }
      $i++;
    }
    if ($performData) {
      $perform = $this->getPerformTest($performData, READING);
    }
    else {
      $perform = FALSE;
    }

    $avarageTime = $totalsecond / $test_taken;
    $avarageAnserSecond = $totalsecond / $totalAnswer;
    return [
      'taken' => $test_taken,
      'avarage_score' => $this->formatNumber($totalScore / $test_taken, TRUE),
      'avarage_accuracy' => $this->formatNumber($totalAccuracy / $test_taken),
      'avarage_time' => gmdate("i:s", $avarageTime),
      'total_question' => $totalQuestion,
      'total_unanswer' => $unanswered,
      'total_incorrect' => $totalInCorrect,
      'total_correct' => $totalCorrect,
      'time_per_question' => gmdate("i:s", $avarageAnserSecond),
      'max_score' => max($arr_score_list),
      'perform' => $perform,
    ];
  }

  /**
   * Get Score
   *
   * @param $node
   *
   * @return int
   */
  public function getScore($node) {
    $arr = [];
    $result_listening = \Drupal::state()->get('mapping_listening');
    if ($result_listening) {
      $arr['listening'] = unserialize($result_listening);
    }
    $result_reading_ac = \Drupal::state()->get('mapping_reading_ac');
    if ($result_reading_ac) {
      $arr['reading_ac'] = unserialize($result_reading_ac);
    }
    $result_reading_gt = \Drupal::state()->get('mapping_reading_gt');
    if ($result_reading_gt) {
      $arr['reading_gt'] = unserialize($result_reading_gt);
    }

    $correct = $node->get('field_score')->value;
    $arg = explode('/', $correct);

    $quiz = Node::load($node->get('field_score_quiz')->target_id);
    $score = 0;
    if ($quiz->get('field_quiz_type')->value == 'listening') {
      foreach ($arr['listening'] as $k => $val) {
        $ar_k = explode('-', $k);
        if (in_array($arg[0], $ar_k)) {
          $score = $val;
        }
      }
    }
    if ($quiz->get('field_quiz_type')->value == 'reading') {
      $set_id = $quiz->get('field_set')->target_id;
      $set = Node::load($set_id);
      $collection_id = $set->get('field_collection')->target_id;
      $collection = Node::load($collection_id);
      $collection_type = $collection->get('field_category')->target_id;
      switch ($collection_type) {
        case 3:
          $arr_map = $arr['reading_ac'];
          break;
        default:
          $arr_map = $arr['reading_gt'];
          break;
      }
      foreach ($arr_map as $k => $val) {
        $ar_k = explode('-', $k);
        if (in_array($arg[0], $ar_k)) {
          $score = $val;
        }
      }
    }
    return $score;
  }

  /**
   * Formart Number
   *
   * @param $number , $score
   *
   * @return mixed
   */
  public function formatNumber($number, $score = NULL) {
    if ($score) {
      $number = number_format($number, 1);
      $nArr = explode('.', $number);
      if (isset($nArr[1])) {
        if ($nArr[1 > 3 && $nArr[1] < 5]) {
          if ($nArr[0] < 9) {
            $number = $nArr[0] . '.5';
          }
          else {
            $number = $nArr[0];
          }

        }
        elseif ($nArr[1] > 5) {
          if ($nArr[0] < 9) {
            $number = $nArr[0] + 1;
          }
          else {
            $number = $nArr[0];
          }

        }
      }
    }
    else {
      $number = number_format($number, 2);
    }
    return $number;
  }

  /**
   * @param $score
   * @param $type
   *
   * @return array
   */
  public function getPerformTest($score, $type) {
    $terms = $this->getQuestionTypeFront($type);
    $data = [];
    if ($score) {
      foreach ($terms as $tid => $name) {
        $total_question = 0;
        $total_correct = 0;
        foreach ($score as $sc) {
          if (isset($sc['type']) && $tid == $sc['type']) {
            $total_question += 1;
            if (isset($sc['correct']) && $sc['correct'] == 1) {
              $total_correct += 1;
            }
          }
        }
        $data[] = [
          'name' => $name . '(' . $this->mappingName($name) . ')',
          'total_question' => $total_question,
          'total_correct' => $total_correct,
          'accuracy' => $total_correct > 0 ? $this->formatNumber($total_correct * 100 / $total_question, FALSE) : 0,
          'shortname' => $this->mappingName($name),
        ];

      }
      return $data;
    }

  }

  /**
   * @param $type
   *
   * @return array
   */
  public function getQuestionTypeFront($type) {
    $vid = 'question_type';
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    $data = [];
    foreach ($terms as $t) {
      $term = Term::load($t->tid);
      $arr = $term->get('field_type')->getValue();
      foreach ($arr as $ar) {
        if ($ar['value'] == $type) {
          $data[$t->tid] = $term->getName();
        }
      }

    }
    return $data;
  }

  /**
   * @param $name
   *
   * @return array
   */
  public function mappingName($name) {
    $data = [
      'Matching' => 'M',
      'Matching Headings' => 'MH',
      'Multiple Choice' => 'MCH',
      'Multiple Choices with multiple answers' => 'MCA',
      'Plan, map, diagram labelling' => 'PMD',
      'Sentence Completion' => 'SEC',
      'Summary, form completion' => 'SFC',
      'TRUE-FALSE-NOT GIVEN' => 'TFNG',
      'YES-NO-NOT GIVEN' => 'YNNG',

    ];
    return $data[$name];
  }
}

