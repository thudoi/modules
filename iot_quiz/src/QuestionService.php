<?php
/**
 * Created by PhpStorm.
 * User: mrcad
 * Date: 12/4/2017
 * Time: 5:16 PM
 */

namespace Drupal\iot_quiz;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;

class QuestionService {

  /**
   * @param $node
   * @param $score
   * @param $collection
   * @param $author
   * @param $set
   * @param $collection_date
   *
   * @return array
   */
  public function getResultQuestion($node, $score, $collection, $author, $set) {
    $type = $node->get('field_quiz_type')->value;
    $title = $node->get('field_title_ui')->value;
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
    switch ($type) {
      case 'listening':
        $service = \Drupal::service('iot_quiz.quizservice');
        $content = $service->get_question($node, 'listening', 'solution');
        $result = $score->get('body')->value;
        $result = unserialize($result);
        $correct = $score->get('field_score')->value;
        $arg = explode('/', $correct);
        $map_score = 0;
        $mapping = [];
        $mapp = [];
        foreach ($arr['listening'] as $k => $val) {
          $ar_k = explode('-', $k);
          if ($arg[0] >= $ar_k[0] && $arg[0] <= $ar_k[1]) {
            $map_score = $val;
            $mapp[$k] = $val;
          }
        }
        $time = explode(':', $score->get('field_time')->value);
        $time = ($time[0] * 60) + ($time[1]);
        $percent_correct = intval(($arg[0] * 100) / $arg[1]);
        $total_second = $node->get('field_duration')->value;
        $total_second = $total_second * 60;
        $real_time = $total_second - $time;
        $time_spend = intval(($real_time * 100) / $total_second);
        $t_spend = $this->mappArrayPercent($time_spend);
        if ($real_time > 0) {
          $spend = gmdate('i:s', $real_time);
        }
        elseif ($real_time == 0) {
          $spend = gmdate('i:s', $total_second);
          $t_spend = 100;
        }
        else {
          $spend = '00:00';
        }
        $mapping['mapp'] = array_reverse($arr['listening']);
        $mapping['score'] = $mapp;
        $test_result = [
          'score' => $map_score,
          'correct' => $correct,
          'time' => $spend,
          'correct_percent' => $this->mappArrayPercent($percent_correct),
          'time_spend' => $t_spend,
        ];
        $data = [];
        foreach ($content['answers']['answers'] as $key => $answer) {
          switch ($answer['type']) {
            case 'blank':
              $data[$key] = [
                'num' => $result[$key]['num'],
                'ans' => isset($result[$key]['ans']) ? $result[$key]['ans'] : '',
                'correct' => isset($result[$key]['correct']) ? $result[$key]['correct'] : 0,
                'correct_ans' => $answer['prefix'],
              ];
              break;
            case 'radio':
              $data[$key] = [
                'num' => $result[$key]['num'],
                'ans' => isset($result[$key]['ans']) ? $result[$key]['ans'] : '',
                'correct' => isset($result[$key]['correct']) ? $result[$key]['correct'] : 0,
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'drop_down':
              $data[$key] = [
                'num' => $result[$key]['num'],
                'ans' => isset($result[$key]['ans']) ? $result[$key]['ans'] : '',
                'correct' => isset($result[$key]['correct']) ? $result[$key]['correct'] : 0,
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'checkbox':
              $c_correct = isset($result[$key]['correct']) ? $result[$key]['correct'] : 0;
              $c_ans = isset($result[$key]['ans']) ? implode(',', $result[$key]['ans']) : '';
              if ($c_correct) {
                $i = 0;
                foreach ($answer['answer'] as $as) {
                  foreach ($result[$key]['ans'] as $r) {
                    if ($r == $as) {
                      $i++;
                    }
                  }
                }
                $c_ans .= ' (Correct ' . $i . '/' . count($answer['answer']) . ')';
              }
              $data[$key] = [
                'num' => $result[$key]['num'],
                'ans' => $c_ans,
                'correct' => isset($result[$key]['correct']) ? $result[$key]['correct'] : 0,
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
          '#type' => 'result',
        ];
        $score_table = render($score_table_theme);
        return [
          '#theme' => 'iot_result_listening',
          '#node' => $node,
          '#result' => $data,
          '#score' => $test_result,
          '#secs' => $content['secs'],
          '#audio' => $content['audio'],
          '#answers' => $content['answers'],
          '#collection_header' => $collection_header,
          '#author' => $author,
          '#set' => $set,
          '#mapping' => $mapping,
          '#score_table' => $score_table,
          '#leader_board' => $this->getLeaderBoard($node->id()),
          '#attached' => [
            'library' => [
              'iot_quiz/iot_result',
              'iot_quiz/iot_frontend',
            ],
            'drupalSettings' => ['test' => $data, 'qid' => $node->id()],
          ],
        ];
        break;
      case 'reading':
        $set_id = $node->get('field_set')->target_id;
        $set = Node::load($set_id);
        $collection_id = $set->get('field_collection')->target_id;
        $collection = Node::load($collection_id);
        $collection_type = $collection->get('field_category')->target_id;
        $mapping = [];
        switch ($collection_type) {
          case 3:
            $arr_map = $arr['reading_ac'];
            break;
          default:
            $arr_map = $arr['reading_gt'];
            break;
        }
        $mapping['mapp'] = array_reverse($arr_map);
        $service = \Drupal::service('iot_quiz.quizservice');
        $content = $service->get_question($node, 'reading', 'solution');
        $result = $score->get('body')->value;
        $result = unserialize($result);
        $correct = $score->get('field_score')->value;
        $arg = explode('/', $correct);
        $map_score = 0;
        $mapp = [];
        foreach ($arr_map as $k => $val) {
          $ar_k = explode('-', $k);
          if ($arg[0] >= $ar_k[0] && $arg[0] <= $ar_k[1]) {
            $map_score = $val;
            $mapp[$k] = $val;
          }
        }
        $time = explode(':', $score->get('field_time')->value);
        $time = ($time[0] * 60) + ($time[1]);
        $percent_correct = intval(($arg[0] * 100) / $arg[1]);
        $total_second = $node->get('field_duration')->value;
        $total_second = $total_second * 60;
        $real_time = $total_second - $time;
        $time_spend = intval(($real_time * 100) / $total_second);
        $t_spend = $this->mappArrayPercent($time_spend);
        if ($real_time > 0) {
          $spend = gmdate('i:s', $real_time);
        }
        elseif ($real_time == 0) {
          $spend = gmdate('i:s', $total_second);
          $t_spend = 100;
        }
        else {
          $spend = '00:00';
        }
        $mapping['score'] = $mapp;
        $test_result = [
          'score' => $map_score,
          'correct' => $correct,
          'time' => $spend,
          'correct_percent' => $this->mappArrayPercent($percent_correct),
          'time_spend' => $t_spend,
        ];
        //$sections = $service->explanation_reading($content['secs']);
        $explain = [];
        foreach ($content['secs'] as $section) {
          foreach ($section['questions'] as $qid => $question) {
            $qnode = Node::load($qid);
            $explain[$qid] = $service->explain_reading_mapping($qnode);
          }

        }
        $data = [];
        foreach ($content['answers']['answers'] as $key => $answer) {
          switch ($answer['type']) {
            case 'blank':
              $data[$key] = [
                'num' => $result[$key]['num'],
                'ans' => isset($result[$key]['ans']) ? $result[$key]['ans'] : '',
                'correct' => isset($result[$key]['correct']) ? $result[$key]['correct'] : 0,
                'correct_ans' => $answer['prefix'],
              ];
              break;
            case 'radio':
              $data[$key] = [
                'num' => $result[$key]['num'],
                'ans' => isset($result[$key]['ans']) ? $result[$key]['ans'] : '',
                'correct' => isset($result[$key]['correct']) ? $result[$key]['correct'] : 0,
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'drop_down':
              $data[$key] = [
                'num' => $result[$key]['num'],
                'ans' => isset($result[$key]['ans']) ? $result[$key]['ans'] : '',
                'correct' => isset($result[$key]['correct']) ? $result[$key]['correct'] : 0,
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'checkbox':
              $c_correct = isset($result[$key]['correct']) ? $result[$key]['correct'] : 0;
              $c_ans = isset($result[$key]['ans']) ? implode(',', $result[$key]['ans']) : '';
              if ($c_correct) {
                $i = 0;
                foreach ($answer['answer'] as $as) {
                  foreach ($result[$key]['ans'] as $r) {
                    if ($r == $as) {
                      $i++;
                    }
                  }
                }
                $c_ans .= ' (Correct ' . $i . '/' . count($answer['answer']) . ')';
              }
              $data[$key] = [
                'num' => $result[$key]['num'],
                'ans' => $c_ans,
                'correct' => isset($result[$key]['correct']) ? $result[$key]['correct'] : 0,
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
          '#type' => 'result',
          '#class' => 'green',
        ];
        $score_table = render($score_table_theme);
        return [
          '#theme' => ['iot_result_reading'],
          '#node' => $node,
          '#result' => $data,
          '#score' => $test_result,
          '#secs' => $content['secs'],
          '#answers' => $content['answers'],
          '#explain' => $explain,
          '#collection_header' => $collection_header,
          '#author' => $author,
          '#set' => $set,
          '#mapping' => $mapping,
          '#score_table' => $score_table,
          '#leader_board' => $this->getLeaderBoard($node->id()),
          '#attached' => [
            'library' => ['iot_quiz/iot_result',],
            'drupalSettings' => ['test' => $data, 'qid' => $qid],
          ],
        ];
        break;
    }
  }

  /**
   * @param $node
   *
   * @return array
   */
  public function getQuestionDetail($node) {
    $score_service = \Drupal::service('iot_quiz.scoreservice');
    $score_id = $score_service->InitScore($node);
    $storage = [];
    if ($score_id) {
      $score = Node::load($score_id);
      $data = $score->get('body')->value;
      if ($data) {
        $data_arr = unserialize($data);
        foreach ($data_arr as $dat) {
          if (isset($dat['ans']) && $dat['ans']) {
            $storage[] = ['num' => $dat['num'], 'ans' => $dat['ans']];
          }
        }
      }
    }
    $type = $node->get('field_quiz_type')->value;
    $set = Node::load($node->get('field_set')->target_id);
    $collection = Node::load($set->get('field_collection')->target_id);
    $user = \Drupal::currentUser();
    $logged_in = $user->isAuthenticated();
    switch ($type) {
      case 'writing':
        return [
          '#theme' => ['iot_writing_speaking'],
          '#node' => $node,
          '#attached' => [
            'library' => [
              'iot_quiz/iot_frontend',
              'iot_quiz/reading_front',
            ],
          ],
        ];
        break;
      case 'speaking':
        return [
          '#theme' => ['iot_writing_speaking'],
          '#node' => $node,
          '#attached' => [
            'library' => [
              'iot_quiz/iot_frontend',
              'iot_quiz/reading_front',
            ],
          ],
        ];
        break;
      case 'reading':
        $service = \Drupal::service('iot_quiz.quizservice');
        $content = $service->get_question($node, $type);
        $solution_popup_data = [
          '#theme' => ['iot_popup_solution'],
          '#secs' => $content['secs'],
        ];
        $popup_solution = render($solution_popup_data);
        $popup_expired_data = [
          '#theme' => ['iot_popup_expired'],
          '#node' => $collection,
        ];
        $popup_expired = render($popup_expired_data);
        $popup_submit_data = ['#theme' => ['iot_popup_submit'],];
        $popup_submit = render($popup_submit_data);
        return [
          '#theme' => ['iot_reading'],
          '#node' => $node,
          '#logged_in' => $logged_in,
          '#secs' => $content['secs'],
          '#popup_solution' => $popup_solution,
          '#popup_submit' => $popup_submit,
          '#popup_expired' => $popup_expired,
          '#attached' => [
            'library' => [
              'iot_quiz/iot_frontend',
              'iot_quiz/reading_front',
            ],
            'drupalSettings' => [
              'answers' => $content['answers'],
              'score' => $score_id,
              'storage' => $storage,
            ],
          ],
        ];
        break;
      //default
      default:
        $service = \Drupal::service('iot_quiz.quizservice');
        $content = $service->get_question($node, $type);
        $solution_popup_data = [
          '#theme' => ['iot_popup_solution'],
          '#secs' => $content['secs'],
        ];
        $popup_solution = render($solution_popup_data);
        $popup_expired_data = [
          '#theme' => ['iot_popup_expired'],
          '#node' => $collection,
        ];
        $popup_expired = render($popup_expired_data);
        $popup_submit_data = ['#theme' => ['iot_popup_submit'],];
        $popup_submit = render($popup_submit_data);
        return [
          '#theme' => ['iot_listening'],
          '#node' => $node,
          '#popup_solution' => $popup_solution,
          '#popup_submit' => $popup_submit,
          '#popup_expired' => $popup_expired,
          '#logged_in' => $logged_in,
          '#secs' => $content['secs'],
          '#audio' => $content['audio'],
          '#attached' => [
            'library' => ['iot_quiz/iot_frontend',],
            'drupalSettings' => [
              'answers' => $content['answers'],
              'score' => $score_id,
              'storage' => $storage,
            ],
          ],
        ];
        break;
    }
  }

  /**
   * @param $node
   *
   * @return array
   */
  public function getOtherTestWS($node) {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'quiz')
      ->condition('status', 1)
      ->condition('nid', $node->id(), '<>')
      ->condition('field_set', $node->get('field_set')->target_id)
      ->execute();
    $nodes = Node::loadMultiple($nids);
    return ['#theme' => 'iot_other_test_ws', '#nodes' => $nodes,];
  }

  /**
   * @param $node
   *
   * @return array
   */
  public function getOtherCollectionWS($node) {
    $collectionService = \Drupal::service('iot_quiz.collectionservice');
    $set = Node::load($node->get('field_set')->target_id);
    $col = Node::load($set->get('field_collection')->target_id);
    $collections = $collectionService->getOtherCollection($col);
    return [
      '#theme' => 'iot_other_collection_ws',
      '#collections' => $collections,
    ];
  }

  /**
   * @param $score
   *
   * @return array
   */
  public function getOtherTestResult($score) {
    $quiz = Node::load($score->get('field_score_quiz')->target_id);
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'quiz')
      ->condition('status', 1)
      ->condition('nid', $quiz->id(), '<>')
      ->condition('field_set', $quiz->get('field_set')->target_id)
      ->execute();
    $nodes = Node::loadMultiple($nids);
    return ['#theme' => 'iot_other_result', '#nodes' => $nodes,];
  }

  /**
   * @param $score
   *
   * @return array
   */
  public function getLeaderBoard($quiz_id) {
    //    $score_service = \Drupal::service('iot_quiz.scoreservice');
    //    $tops = $score_service->get_top_score($quiz_id);
    $date = strtotime('now');
    $last_week = $date + (60 * 60 * 24 * -7);
    $sids = \Drupal::entityQuery('node')
      ->condition('type', 'score')
      ->condition('created', $last_week, '>=')
      ->condition('field_score_quiz', $quiz_id)
      ->condition('status', 1)
      ->sort('created', 'ASC')
      ->execute();
    $quiz = Node::load($quiz_id);
    $nodes = Node::loadMultiple($sids);
    $data = [];
    foreach ($nodes as $key => $node) {
      $time = explode(':', $node->get('field_time')->value);
      $total_second = $quiz->get('field_duration')->value;
      if (($total_second - $time[0]) > 15) {
        $time = ($time[0] * 60) + ($time[1]);
        $uid = $node->getOwnerId();
        $account = \Drupal\user\Entity\User::load($uid); // pass your uid
        $name = $account->getUsername();
        $score = $node->get('field_score')->value;
        $score = explode('/', $score);
        $total_second = $total_second * 60;
        $real_time = $total_second - $time;
        if ($real_time > 0) {
          $spend = gmdate('i:s', $real_time);
        }
        elseif ($real_time == 0) {
          $spend = gmdate('i:s', $total_second);
        }
        else {
          $spend = '00:00';
        }
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
        $map_score = 0;
        foreach ($arr['listening'] as $k => $val) {
          $ar_k = explode('-', $k);//in_array($score[0], $ar_k
          if ($score[0] >= $ar_k[0] && $score[0] <= $ar_k[1]) {
            $map_score = $val;
          }
        }
        if (!$data[$uid]) {
          $data[$uid] = [
            'score' => $map_score,
            'time' => $spend,
            'user_name' => $name,
          ];
        }
      }
    }

    foreach ($data as $key => $row) {
      $score_sort[$key] = $row['score'];
      $time_sort[$key] = $row['time'];
    }
    if ($data) {
      array_multisort($score_sort, SORT_DESC, $time_sort, SORT_ASC, $data);
    }
    $sliced_array = array_slice($data, 0, 10);
    return ['#theme' => 'iot_leader_board', '#node' => $sliced_array,];
  }

  /**
   * @param $value
   *
   * @return int|mixed
   */
  public function mappArrayPercent($value) {
    $arrs = [
      0,
      5,
      10,
      15,
      20,
      25,
      30,
      35,
      40,
      45,
      50,
      55,
      60,
      65,
      70,
      75,
      80,
      85,
      90,
      95,
      100,
    ];
    $number = 0;
    foreach ($arrs as $arr) {
      if ($value - $arr > 0 && $value - $arr < 5) {
        $number = $arr;
      }
      if ($value - $arr == 0) {
        $number = $arr;
      }
    }
    return $number;
  }

  /**
   * Get time start fir listening
   *
   * @param $section
   *
   * @return array
   */
  public function getTimeStartListening($question) {
    $time = [];
    $sec = Node::load($question->get('field_section')->target_id);
    $quiz = Node::load($sec->get('field_quiz')->target_id);

    $section_ids = \Drupal::entityQuery('node')
      ->condition('type', 'section')
      ->condition('field_quiz', $quiz->id())
      ->condition('status', 1)
      ->execute();
    $sections = \Drupal\node\Entity\Node::loadMultiple($section_ids);
    foreach ($sections as $section) {
      if (!empty($section->get('field_subtitle')->value)) {
        $script = $section->get('field_subtitle')->value;
        preg_match_all('/<p>([^`]*?)<\/p>/', $script, $matches);

        foreach ($matches[0] as $match) {
          if (preg_match_all('/\[(.+?)\]/', $match, $m)) {
            $replace = ['[', ']', 'question'];
            $question = str_replace($replace, '', $m[0]);
            if (preg_match_all('/0([^`]*?)\>/', $match, $t)) {
              if (isset($t[0][0])) {
                $start = explode(",", $t[0][0]);
                $t = str_replace(',', '', $start[0]);
                $t = str_replace('.', '', $t);
                $time[$question[0]] = $t;
              }
            }
            if (preg_match_all('/0([^`]*?)\,/', $match, $t)) {
              if (isset($t[0][0])) {
                $start = explode(".", $t[0][0]);
                $t = str_replace(',', '', $start[0]);
                $t = str_replace('.', '', $t);
                $time[$question[0]] = $t;
              }
            }

          }
        }
      }
    }
    return $time;

  }

  /**
   * Get time start fir listening
   *
   * @param $section
   *
   * @return array
   */
  public function getTimeStartListeningbyQuiz($quiz) {
    $time = [];

    $section_ids = \Drupal::entityQuery('node')
      ->condition('type', 'section')
      ->condition('field_quiz', $quiz->id())
      ->condition('status', 1)
      ->execute();
    $sections = \Drupal\node\Entity\Node::loadMultiple($section_ids);
    foreach ($sections as $section) {
      if (!empty($section->get('field_subtitle')->value)) {
        $script = $section->get('field_subtitle')->value;
        preg_match_all('/<p>([^`]*?)<\/p>/', $script, $matches);

        foreach ($matches[0] as $match) {
          if (preg_match_all('/\[(.+?)\]/', $match, $m)) {
            $replace = ['[', ']', 'question'];
            $question = str_replace($replace, '', $m[0]);
            if (preg_match_all('/0([^`]*?)\>/', $match, $t)) {
              if (isset($t[0][0])) {
                $start = explode(",", $t[0][0]);
                $time[$question[0]] = $start[0];
              }
            }
            if (preg_match_all('/0([^`]*?)\,/', $match, $t)) {
              if (isset($t[0][0])) {
                $start = explode(".", $t[0][0]);
                $time[$question[0]] = $start[0];
              }
            }

          }
        }
      }
    }
    return $time;

  }
}

