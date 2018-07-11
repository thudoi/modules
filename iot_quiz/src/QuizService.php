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
use Drupal\media_entity\Entity\Media;
use Drupal\taxonomy\Entity\Term;

class QuizService {

  /**
   * @param $node
   * @param $type
   * @param string $solution
   *
   * @return array
   */
  public function get_question($node, $type, $solution = '', $print = NULL) {
    switch ($type) {
      case 'writing':
        break;
      case 'speaking':
        break;
      case 'reading':
        $sids = \Drupal::entityQuery('node')
          ->condition('type', 'section')
          ->condition('field_quiz', $node->id())
          ->condition('field_section_type', 'reading')
          ->execute();
        $sections = [];
        $answers = [];
        $total = 0;
        foreach ($sids as $key => $sid) {
          $bids = \Drupal::entityQuery('node')
            ->condition('type', 'question')
            ->condition('field_section', $sid)
            ->sort('field_order', 'ASC')
            ->execute();
          $sids[$key] = ['id' => $sid, 'block' => $bids];
          $q_nodes = Node::loadMultiple($bids);
          $section = Node::load($sid);
          $questions = [];
          foreach ($q_nodes as $kb => $q_node) {
            $title = $q_node->get('field_title_ui')->value;//
            $q_type = $q_node->get('field_question_type')->value;
            $qids = $q_node->get('field_question')->getValue();
            $q_type_f = $q_node->get('field_qtype_front')->target_id;
            $term = Term::load($q_type_f);
            $name = $term->id();
            $content = '<h3>' . $title . '</h3>';
            $des = $q_node->get('field_block_description')->value;
            if ($des) {
              $content .= '<div class="question-title">' . $des . '</div>';
            }
            switch ($q_type) {
              case 'blank':
                $this->process_question_blank($qids, $content, $kb, $questions, $answers, $total, $solution, $type, $name, $print);
                break;
              case 'radio':
                $this->process_question_radio($qids, $content, $kb, $questions, $answers, $total, $solution, $type, $name, $print);
                break;
              case 'checkbox':
                $this->process_question_checkbox($qids, $content, $kb, $questions, $answers, $total, $solution, $type, $name, $print);
                break;
              case 'drop_down':
                $this->process_question_drop($qids, $content, $kb, $questions, $answers, $total, $solution, $q_node, $type, $name, $print);
                break;
              case 'drag_drop':
                $this->process_question_drop($qids, $content, $kb, $questions, $answers, $total, $solution, $q_node, $type, $name, $print);
            }
          }
          $number = $section->get('field_question_range')->value;
          $number = str_replace('Questions ', '', $number);
          $arr = explode('-', $number);
          $sections[$sid] = [
            'title' => $section->getTitle(),
            'questions' => $questions,
            'section' => $section,
            'number' => $arr,
          ];
        }
        $js_data = [
          'total' => $total,
          'sec_id' => $node->id(),
          'answers' => $answers,
        ];
        return ['secs' => $sections, 'answers' => $js_data,];
        break;
        break;
      default:
        $service = \Drupal::service('iot_quiz.questionservice');
        $audio = $node->get('field_audio')->target_id;
        $entity = Media::load($audio);
        $yt_link = '';
        if ($entity) {
          $yt_link = $this->process_media($entity);
        }
        $sids = \Drupal::entityQuery('node')
          ->condition('type', 'section')
          ->condition('field_quiz', $node->id())
          ->condition('field_section_type', 'listening')
          ->execute();
        $sections = [];
        $answers = [];
        $total = 0;
        foreach ($sids as $key => $sid) {
          $bids = \Drupal::entityQuery('node')
            ->condition('type', 'question')
            ->condition('field_section', $sid)
            ->sort('field_order', 'ASC')
            ->execute();
          $sids[$key] = ['id' => $sid, 'block' => $bids];
          $q_nodes = Node::loadMultiple($bids);
          $section = Node::load($sid);
          $questions = [];
          foreach ($q_nodes as $kb => $q_node) {
            $title = $q_node->get('field_title_ui')->value;
            $q_type = $q_node->get('field_question_type')->value;
            $q_type_f = $q_node->get('field_qtype_front')->target_id;
            $term = Term::load($q_type_f);
            $name = $term->id();
            $qids = $q_node->get('field_question')->getValue();

            $listenHere = $service->getTimeStartListening($q_node);
            $qstart = str_replace('Questions ', '', $title);
            $qstart = explode('-', $qstart);
            $listen = [
              '#theme' => 'iot_listen_here',
              '#node' => $q_node,
              '#listen' => $listenHere[$qstart[0]],
            ];
            $content = '<h3>' . $title . '</h3>';
            if ($solution == '') {
              $content .= render($listen);
            }
            $des = $q_node->get('field_block_description')->value;
            if ($des) {
              $content .= '<div class="question-title">' . $des . '</div>';
            }
            switch ($q_type) {
              case 'blank':
                $this->process_question_blank($qids, $content, $kb, $questions, $answers, $total, $solution, $type, $name, $print);
                break;
              case 'radio':
                $this->process_question_radio($qids, $content, $kb, $questions, $answers, $total, $solution, $type, $name, $print);
                break;
              case 'checkbox':
                $this->process_question_checkbox($qids, $content, $kb, $questions, $answers, $total, $solution, $type, $name, $print);
                break;
              case 'drop_down':
                $this->process_question_drop($qids, $content, $kb, $questions, $answers, $total, $solution, $q_node, $type, $name, $print);
                break;
              case 'drag_drop':
                $this->process_question_drop($qids, $content, $kb, $questions, $answers, $total, $solution, $q_node, $type, $name, $print);
            }
          }


          $sections[$sid] = [
            'title' => $section->getTitle(),
            'audio' => $audio,
            'questions' => $questions,
            'section' => $section,
          ];
        }
        $js_data = [
          'total' => $total,
          'sec_id' => $node->id(),
          'answers' => $answers,
        ];
        return [
          'audio' => $yt_link,
          'secs' => $sections,
          'answers' => $js_data,
        ];
        break;
    }
  }

  /**
   * @param $qids
   * @param $content
   * @param $kb
   * @param $questions
   * @param $answers
   * @param $total
   * @param $solution
   * @param null $type
   * @param $name
   */
  private function process_question_radio($qids, &$content, $kb, &$questions, &$answers, &$total, $solution, $type = NULL, $name, $print = NULL) {
    $service = \Drupal::service('iot_quiz.quizservice');
    $questionService = \Drupal::service('iot_quiz.questionservice');
    $node = Node::load($kb);
    foreach ($qids as $q_key => $qid) {
      $qnode = Paragraph::load($qid['target_id']);
      $explain = $qnode->get('field_explain')->value;
      $question = $qnode->get('field_question')->value;
      $number = $qnode->get('field_number')->value;
      $total = $number >= $total ? $number : $total;
      $options = $qnode->get('field_radios')->getValue();
      $o_data = [];
      $correct = '';
      $ext_attr = $solution ? 'disabled' : '';
      $data_template = [];
      foreach ($options as $o_key => $option) {
        $opt = Paragraph::load($option['target_id']);
        $o_data[$o_key]['correct'] = $opt->get('field_correct')->value;
        $o_data[$o_key]['option'] = $opt->get('field_option')->value;
        $o_data[$o_key]['value'] = $opt->get('field_value')->value;
        if ($opt->get('field_correct')->value == 1) {
          $correct = $opt->get('field_value')->value;
        }
        $data_template[] = [
          'id' => $kb . $q_key,
          'ext_attr' => $ext_attr,
          'opt_value' => $opt->get('field_value')->value,
          'opt_option' => $opt->get('field_option')->value,
        ];
      }
      $explain_template = [
        '#theme' => 'iot_explain',
        '#explain' => $this->explanation($node),
        '#node' => $node,
        '#qid' => $number,
        '#type' => $type,
        '#listen' => $questionService->getTimeStartListening($node),
        '#print' => $print,
        '#quiz' => $this->getLocateCondition($node),
        '#nodequiz' => $this->getQuizByQuestion($node),
      ];
      $radio_template = [
        '#theme' => 'iot_radio',
        '#q_num' => $number,
        '#q_type' => $name,
        '#data' => $data_template,
        '#question' => $question,
        '#answer' => $solution ? $correct : '',
        '#explain' => render($explain_template),
        '#print' => $print,
      ];
      $content .= render($radio_template);
      $answers[$number] = [
        'type' => 'radio',
        'number' => $number,
        'answer' => $correct,
      ];
      $data[$kb][$number] = [
        'q_number' => $number,
        'explain' => $explain,
        'question' => $question,
        'options' => $o_data,
      ];
      $pallete[$kb][$number] = ['q_number' => $number,];
    }
    $questions[$kb] = [
      'type' => 'radio',
      'content' => $content,
      'question' => $pallete[$kb],
    ];
  }

  /**
   * @param $qids
   * @param $content
   * @param $kb
   * @param $questions
   * @param $answers
   * @param $total
   * @param $solution
   * @param $q_node
   * @param null $type
   * @param $name
   */
  private function process_question_drop($qids, &$content, $kb, &$questions, &$answers, &$total, $solution, $q_node, $type = NULL, $name, $print = NULL) {
    $q_data = [];
    foreach ($qids as $q_key => $qid) {
      $qnode = Paragraph::load($qid['target_id']);
      $explain = $qnode->get('field_explain')->value;
      $question = $qnode->get('field_question')->value;
      $number = $qnode->get('field_number')->value;
      $options = $qnode->get('field_dropdown')->getValue();
      $o_data = [];
      foreach ($options as $o_key => $option) {
        $opt = Paragraph::load($option['target_id']);
        $o_data[$o_key]['option'] = $opt->get('field_option')->value;
        $o_data[$o_key]['value'] = $opt->get('field_value')->value;
      }
      if ($question) {
        $process = $this->iot_preprocess_dropdown($question, $o_data, $answers, $total, $solution, $q_node, $qnode, $type, $name, $print);
      }
      $data[$qid['target_id']] = [
        'q_num' => $number,
        'explain' => $explain,
        'question' => $question,
        'options' => $o_data,
      ];
      $content .= $process['content'];
      $q_data = $process['question'];
    }
    $questions[$kb] = [
      'type' => 'drop_down',
      'content' => $content,
      'question' => $q_data,
    ];
  }

  /**
   * @param $qids
   * @param $content
   * @param $kb
   * @param $questions
   * @param $answers
   * @param $total
   * @param $solution
   * @param null $type
   * @param $name
   */
  private function process_question_checkbox($qids, &$content, $kb, &$questions, &$answers, &$total, $solution, $type = NULL, $name, $print = NULL) {
    $node = Node::load($kb);
    $service = \Drupal::service('iot_quiz.quizservice');
    $questionService = \Drupal::service('iot_quiz.questionservice');
    foreach ($qids as $q_key => $qid) {
      $qnode = Paragraph::load($qid['target_id']);
      $explain = $qnode->get('field_explain')->value;
      $question = $qnode->get('field_question')->value;
      $number = $qnode->get('field_number')->value;
      $prefix_number = '';
      if (strpos($number, '-') !== FALSE) {
        $last = explode('-', $number);
        $total = $last[1] >= $total ? $last[1] : $total;
      }
      else {
        $total = $number >= $total ? $number : $total;
        $prefix_number = '<b>' . $number . '</b>';
      }
      $options = $qnode->get('field_checkbox')->getValue();
      $o_data = [];
      $content .= '<div class="type_checkbox sl-item"><div class="iot-question" id="q-' . $number . '" data-num="' . $number . '">
                    <div class="question-title">' . $prefix_number . '<span>' . $question . '</span></div>
                    <ul class="list-question">';
      $correct = [];
      $ext_attr = $solution ? 'disabled' : '';
      $ans = '';
      foreach ($options as $o_key => $option) {
        $opt = Paragraph::load($option['target_id']);
        $o_data[$o_key]['correct'] = $opt->get('field_correct_checkbox')->value;
        $o_data[$o_key]['option'] = $opt->get('field_option')->value;
        $o_data[$o_key]['value'] = $opt->get('field_value')->value;
        if ($opt->get('field_correct_checkbox')->value == 1) {
          $correct[] = $opt->get('field_value')->value;
        }
        $checkboxes_template = [
          '#theme' => 'iot_checkboxes',
          '#q_num' => $number,
          '#q_type' => $name,
          '#ext_attr' => $ext_attr,
          '#id' => $kb . $q_key,
          '#opt_value' => $opt->get('field_value')->value,
          '#opt_option' => $opt->get('field_option')->value,
        ];
        $content .= render($checkboxes_template);
      }
      $content .= '</ul>';
      $c_solution = implode(',', $correct);

      $explain_template = [
        '#theme' => 'iot_explain',
        '#explain' => $service->explanation($node),
        '#node' => $node,
        '#qid' => $number,
        '#type' => $type,
        '#listen' => $questionService->getTimeStartListening($node),
        '#print' => $print,
        '#quiz' => $this->getLocateCondition($node),
        '#nodequiz' => $this->getQuizByQuestion($node),
      ];
      if ($print == NULL) {
        $ans .= $solution ? '<li class="answer"><span>' . $number . '</span> Answer: <span class="b-r">' . $c_solution . '</span></li>' . render($explain_template) : '';
      }

      $content .= '<ul>' . $ans . '</ul>';
      $content .= '</div></div>';
      $answers[$number] = [
        'type' => 'checkbox',
        'number' => $number,
        'answer' => $correct,
      ];
      $data[$qid['target_id']] = [
        'q_number' => $number,
        'explain' => $explain,
        'question' => $question,
        'options' => $o_data,
      ];
      $pallete[$qid['target_id']] = ['q_number' => $number,];
    }
    $questions[$kb] = [
      'type' => 'checkbox',
      'content' => $content,
      'question' => $data,
    ];
  }

  /**
   * @param $qids
   * @param $content
   * @param $kb
   * @param $questions
   * @param $answers
   * @param $total
   * @param $solution
   * @param null $type
   * @param $name
   */
  private function process_question_blank($qids, &$content, $kb, &$questions, &$answers, &$total, $solution, $type = NULL, $name, $print = NULL) {
    $node = Node::load($kb);
    foreach ($qids as $q_key => $qid) {
      $qnode = Paragraph::load($qid['target_id']);
      $question = $qnode->get('field_question')->value;
      $question = $this->iot_preprocess_question($question, $answers, $total, $solution, $node, $type, $name, $print);
      $content .= '<div class="sl-item">';
      $content .= $question['content'];
      $content .= '</div>';
    }
    $questions[$kb] = [
      'type' => 'blank',
      'content' => $content,
      'question' => $question['question'],
    ];
  }

  /**
   * @param $text
   * @param $options
   * @param $answers
   * @param $total
   * @param $solution
   * @param $q_node
   * @param null $qnode
   * @param null $type
   * @param $name
   *
   * @return array
   */
  private function iot_preprocess_dropdown($text, $options, &$answers, &$total, $solution, $q_node, $qnode = NULL, $type = NULL, $name, $print = NULL) {
    $service = \Drupal::service('iot_quiz.quizservice');
    $questionService = \Drupal::service('iot_quiz.questionservice');
    $content = $text;
    preg_match_all('/\[(.+?)\:(.+?)\]/', $text, $matches);
    $questions = [];
    $opts = '<option value=""> </option>';
    foreach ($options as $option) {
      $opts .= '<option value="' . $option['value'] . '">' . $option['value'] . '</option>';
    }
    $as = '';
    foreach ($matches[1] as $key => $match) {
      $end = explode(':', $match);
      $questions[$key] = [
        'q_number' => $match,
        'correct' => $matches[2][$key],
      ];
      $total = $match >= $total ? $match : $total;
      $answers[$end[0]] = [
        'type' => 'drop_down',
        'number' => $match,
        'answer' => $matches[2][$key],
      ];
      $explain_template = [
        '#theme' => 'iot_explain',
        '#explain' => $service->explanation($q_node),
        '#node' => $q_node,
        '#qid' => $match,
        '#type' => $type,
        '#listen' => $questionService->getTimeStartListening($q_node),
        '#print' => $print,
        '#quiz' => $this->getLocateCondition($q_node),
        '#nodequiz' => $this->getQuizByQuestion($q_node),
      ];
      $ext_attr = $solution ? 'disabled="disabled"' : '';
      if ($print == NULL) {
        $as .= $solution ? '<li class="answer"><span>' . $match . '</span> Answer: <span class="b-r">' . $matches[2][$key] . '</span></li>' . render($explain_template) : '';
      }

      //check TFNG and YNNG.
      if ($q_node->get('field_qtype_front')->target_id == 11 || $q_node->get('field_qtype_front')->target_id == 12) {
        $surfix = $this->get_option_value_dropdown($q_node->get('field_qtype_front')->target_id);
      }
      else {
        $surfix = $opts;
      }
      $blank_template = [
        '#theme' => 'iot_dropdown',
        '#q_num' => $match,
        '#q_type' => $name,
        '#ext_attr' => $ext_attr,
        '#opt' => $surfix,
        '#as' => '',
      ];
      $replace = render($blank_template);
      $content = str_replace($matches[0][$key], $replace, $content);
    }
    if ($q_node->get('field_qtype_front')->target_id == 11 || $q_node->get('field_qtype_front')->target_id == 12) {
      $table_dropdown = $this->_set_table_dropdown($options, $qnode, TRUE, $q_node->get('field_qtype_front')->target_id);
    }
    else {
      $table_dropdown = $this->_set_table_dropdown($options, $qnode, FALSE, $q_node->get('field_qtype_front')->target_id);
    }
    $content = str_replace('{OPTION}', $table_dropdown, $content);
    $answer = '<ul>' . $as . '</ul>';
    $content = '<div class="drop_down sl-item">' . $content . $answer . '</div>';
    return ['question' => $questions, 'content' => $content,];
  }

  /**
   * @param $text
   * @param $answers
   * @param $total
   * @param $solution
   * @param null $node
   * @param null $type
   * @param $name
   *
   * @return array
   */
  private function iot_preprocess_question($text, &$answers, &$total, $solution, $node = NULL, $type = NULL, $name, $print = NULL) {
    $service = \Drupal::service('iot_quiz.quizservice');
    $questionService = \Drupal::service('iot_quiz.questionservice');
    $content = $text;
    preg_match_all('/\[(.+?)\:(.+?)\]/', $text, $matches);
    $questions = [];
    $ext = '';
    foreach ($matches[1] as $key => $match) {
      $pattern = '/[0-9]{1,2}/';
      preg_match($pattern, $match, $q_num);
      $prefix = explode($q_num[0], $match);
      $questions[$key] = [
        'q_number' => $q_num[0],
        'id' => 'q-' . $q_num[0],
        'prefix' => $prefix[0],
        'Capital' => $prefix[1],
      ];
      $as = explode('@', $matches[2][$key]);
      $arr = explode('|', $as[0]);
      $ans = [];
      if (isset($arr[1])) {
        foreach ($arr as $a) {
          //          $p = '/\s/';
          //          $ans[] = strtolower(preg_replace($p, '', $a));
          $ans[] = $a;
        }
        if (isset($as[1])) {
          $pre_a = $as[1];
        }
        else {
          $pre_a = implode(',', $ans);
        }
      }
      else {
        $pre_a = $matches[2][$key];
        $ans[] = $matches[2][$key];
      }
      $total = $q_num[0] >= $total ? $q_num[0] : $total;
      $answers[$q_num[0]] = [
        'type' => 'blank',
        'number' => $q_num[0],
        'answer' => $ans,
        'prefix' => $pre_a,
      ];

      $explain_template = [
        '#theme' => 'iot_explain',
        '#explain' => $service->explanation($node),
        '#node' => $node,
        '#qid' => $q_num[0],
        '#type' => $type,
        '#listen' => $questionService->getTimeStartListening($node),
        '#print' => $print,
        '#quiz' => $this->getLocateCondition($node),
        '#nodequiz' => $this->getQuizByQuestion($node),
      ];
      if ($print == NULL) {
        $ext .= $solution ? '<li class="answer"><b>' . $q_num[0] . '</b> Answer: <span class="b-r">' . $pre_a . '</span></li>' . render($explain_template) : '';
      }

      $ext_attr = $solution ? 'readonly' : '';
      $blank_template = [
        '#theme' => 'iot_blank',
        '#q_num' => $q_num[0],
        '#q_type' => $name,
        '#ext_attr' => $ext_attr,
      ];
      $replace = render($blank_template);
      $content = str_replace($matches[0][$key], $replace, $content);
    }
    $class = 'type_blank';
    if (strpos($content, '<table') !== FALSE) {
      $class = 'type_blank table-scroll';
    }
    $content = '<div class="' . $class . '">' . $content . '<ul>' . $ext . '</ul></div>';
    return ['question' => $questions, 'content' => $content,];
  }

  /**
   * get dropdown list
   *
   * @return string
   */
  private function get_option_value_dropdown($type) {
    $value = '';
    if ($type == '11') {
      $value = 'TFNG';
    }
    if ($type == '12') {
      $value = 'YNNG';
    }
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'option')
      ->condition('field_option_type', $value)
      ->condition('status', 1)
      ->execute();
    $nodes = Node::loadMultiple($nids);
    $option = '<option value=""></option>';
    foreach ($nodes as $node) {
      $option .= '<option value="' . $node->get('field_value')->value . '">' . $node->get('field_value')->value . '</option>';
    }
    return $option;
  }

  /**
   * Get table option dropdown
   *
   * @param $cell
   * @param $node
   *
   * @return mixed|null
   */
  private function _set_table_dropdown($cell, $node, $type = FALSE, $dtype) {
    $arr = [];
    //        $caption = '';
    if (!empty($node->get('field_dropdown_title')->value)) {
      $arr[] = ['', $node->get('field_dropdown_title')->value];
      //            $caption = t('The table caption / Title');
    }
    $value = '';
    if ($dtype == '11') {
      $value = 'TFNG';
    }
    if ($dtype == '12') {
      $value = 'YNNG';
    }
    if ($type) {
      $nids = \Drupal::entityQuery('node')
        ->condition('type', 'option')
        ->condition('field_option_type', $value)
        ->condition('status', 1)
        ->execute();
      $nodes = Node::loadMultiple($nids);
      foreach ($nodes as $node) {
        $arr[] = [
          $node->get('field_value')->value,
          $node->get('field_option')->value,
        ];
      }
    }
    else {
      foreach ($cell as $data) {
        $arr[] = [$data['value'], $data['option']];
      }
    }
    $header = [];
    $table = [
      '#theme' => 'table', //'#cache' => ['disabled' => TRUE],
      //            '#caption' => $caption,
      '#header' => $header,
      '#rows' => $arr,
    ];
    return render($table);
  }

  /**
   * Explanation on result page for reading question
   *
   * @param $sections
   */
  public function explanation($node) {
    $explain = [];
    // $section = $sec['section'];
    $questions = $node->get('field_question')->getValue();
    foreach ($questions as $question) {
      $para = Paragraph::load($question['target_id']);
      //kint($question->get('field_question_type')->value);
      switch ($node->get('field_question_type')->value) {
        case 'checkbox':
          $qnumber = $para->get('field_number')->value;
          $exp = $para->get('field_explain')->getValue();
          $exp_checkbox = Paragraph::load($exp[0]['target_id']);
          $explain[$qnumber] = $exp_checkbox->get('field_explanation')->value;
          break;
        case 'radio':
          $qnumber = $para->get('field_number')->value;
          $exp = $para->get('field_explanation')->value;
          $explain[$qnumber] = $exp;
          break;
        case 'drop_down':
        case 'drag_drop':
        case 'blank':
          $exp = $para->get('field_explain')->getValue();
          foreach ($exp as $pex) {
            $expl = Paragraph::load($pex['target_id']);
            $explain[$expl->get('field_number')->value] = $expl->get('field_explanation')->value;
          }
          break;
      }
    }
    return $explain;
  }

  /**
   * Mapping explian to token
   *
   * @param $explain
   */
  public function explain_reading_mapping($qnode) {
    $return = '';
    $questions = $qnode->get('field_question')->getValue();
    foreach ($questions as $question) {
      $para = Paragraph::load($question['target_id']);
      //kint($question->get('field_question_type')->value);
      switch ($qnode->get('field_question_type')->value) {
        case 'checkbox':
          $qnumber = $para->get('field_number')->value;
          $exp = $para->get('field_explain')->getValue();
          $exp_checkbox = Paragraph::load($exp[0]['target_id']);
          $return .= '<button class="btn btn-primary" data-toggle="collapse" data-target="#col-' . $qnumber . '">Explain for question ' . $qnumber . '</button><div id="col-' . $qnumber . '" class="collapse">' . $exp_checkbox->get('field_explanation')->value . '</div>';
          break;
        case 'radio':
          $qnumber = $para->get('field_number')->value;
          $exp = $para->get('field_explanation')->value;
          $return .= '<button class="btn btn-primary" data-toggle="collapse" data-target="#col-' . $qnumber . '">Explain for question ' . $qnumber . '</button><div id="col-' . $qnumber . '" class="collapse">' . $exp . '</div>';
          break;
        case 'drop_down':
        case 'drag_drop':
        case 'blank':
          $exp = $para->get('field_explain')->getValue();
          foreach ($exp as $pex) {
            $expl = Paragraph::load($pex['target_id']);
            //$explain[$expl->get('field_number')->value] = $expl->get('field_explanation')->value;
            $return .= '<button class="btn btn-primary" data-toggle="collapse" data-target="#col-' . $expl->get('field_number')->value . '">Explain for question ' . $expl->get('field_number')->value . '</button><div id="col-' . $expl->get('field_number')->value . '" class="collapse">' . $expl->get('field_explanation')->value . '</div>';
          }
          break;
      }
    }
    //  $return .= '<button class="btn btn-primary" data-toggle="collapse" data-target="#col-' . $number . '">Explain for question ' . $number . '</button><div id="col-' . $number . '" class="collapse">' . $explain[$number] . '</div>';
    return $return;
  }

  /**
   * @param $link
   *
   * @return mixed
   */
  private function process_media($entity) {
    $audio_link = FALSE;
    //    $country = ip2country_get_country(\Drupal::request()->getClientIp());
    $country = 'VN';
    if ($country == 'CN') {
      $cdn = 'http://cdn.intergreat.com';
      $oss = 'http://ieltsonlinetests.oss-ap-southeast-1.aliyuncs.com';
      $link = $entity->get('field_audio_direct_link')->value;
      $audio_link = str_replace($oss, $cdn, $link);
      if ($audio_link) {
        return $audio_link;
      }
    }
    else {
      if (!empty($entity->get('field_youtube_link')->value)) {
        $link = $entity->get('field_youtube_link')->value;
        $api = 'http://audioapi.ieltsonlinetests.com?v=' . $link;
        $return = json_decode($this->curl_get($api));
        $audio_link = $return[1]->url;
      }
      if ($audio_link) {
        return $audio_link;
      }
      else {
        if (!empty($entity->get('field_soundcloud_link')->value)) {
          $link = $entity->get('field_soundcloud_link')->value;
          $link = explode("//", $link);
          $api_key = '95f22ed54a5c297b1c41f72d713623ef';
          $audio_link = 'https://' . $link[1] . '/stream?client_id=' . $api_key;
        }
        if ($audio_link) {
          return $audio_link;
        }
      }

    }
    return $audio_link;


  }

  /**
   * @param $url
   * @param array $get
   * @param array $options
   *
   * @return mixed
   */
  public function curl_get($url, array $get = [], array $options = []) {
    $defaults = [
      CURLOPT_URL => $url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($get),
      CURLOPT_HEADER => 0,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_TIMEOUT => 4,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if (!$result = curl_exec($ch)) {
      trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
  }

  /**
   * Implement check locate button
   */
  public function getLocateCondition($question) {
    $setion = Node::load($question->get('field_section')->target_id);
    $quiz = Node::load($setion->get('field_quiz')->target_id);
    $section_ids = \Drupal::entityQuery('node')
      ->condition('type', 'section')
      ->condition('field_quiz', $quiz->id())
      ->condition('status', 1)
      ->execute();
    $sections = \Drupal\node\Entity\Node::loadMultiple($section_ids);
    $ex = [];
    foreach ($sections as $section) {
      if ($quiz->get('field_quiz_type')->value == 'listening') {
        preg_match_all('/explainq(.+?)\">/', $section->get('field_audio_sc')->value, $matches);
        foreach ($matches[1] as $q) {
          $q = str_replace(' explain', '', $q);
          $ex[$q] = $q;
        }
      }
      if ($quiz->get('field_quiz_type')->value == 'reading') {
        preg_match_all('/explainq(.+?)\">/', $section->get('field_passage_explain')->value, $matches);
        foreach ($matches[1] as $q) {
          $q = str_replace(' explain', '', $q);
          $ex[$q] = $q;
        }

      }
    }
    //kint($ex);
    return $ex;
  }

  public function getQuizByQuestion($question) {
    $setion = Node::load($question->get('field_section')->target_id);
    $quiz = Node::load($setion->get('field_quiz')->target_id);
    return $quiz;
  }
}
