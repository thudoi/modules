<?php

namespace Drupal\iot_ielts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class SetController.
 */
class SetController extends ControllerBase {

  /**
   * Section Manager.
   *
   * @return array
   *   Return template.
   */
  public function Set($nid) {
    $node = Node::load($nid);

    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'quiz')
      ->condition('field_set', $node->id())
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $secs = [];
    foreach ($nodes as $sec) {
      $secs[] = $sec;
    }
    return [
      '#theme' => ['iot_manage_set'],
      '#node' => $node,
      '#secs' => $secs,
    ];


  }

  /**
   * Question Manager.
   *
   * @return array
   *   Return template.
   */
  public function Question($nid) {
    $node = Node::load($nid);
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'question')
      ->condition('field_section', $node->id())
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $questions = [];
    $term = [];
    $count = [];
    foreach ($nodes as $sec) {
      $tid = $sec->get('field_qtype_front')->getValue();
      $questions[] = $sec;
      $term[$sec->id()] = $this->_iot_get_term_name($tid[0]['target_id']);
      $count[$sec->id()] = $this->_count_question($sec);
    }
    // kint($questions[0]);
    return [
      '#theme' => ['iot_manage_question'],
      '#node' => $node,
      '#questions' => $questions,
      '#term' => $term,
      '#count' => $count,
    ];


  }


  /**
   * Question Manager.
   *
   * @return array
   *   Return template.
   */
  public function Quiz($nid) {
    $node = Node::load($nid);
    $type = $node->get('field_quiz_type')->value;
    $questions = [];
    if ($type == 'listening' || $type == 'reading') {
      $section_ids = \Drupal::entityQuery('node')
        ->condition('type', 'section')
        ->condition('field_quiz', $node->id())
        ->execute();
      $sections = \Drupal\node\Entity\Node::loadMultiple($section_ids);
      foreach ($sections as $section) {
        $questions[] = $section;
      }
    }

    return [
      '#theme' => ['iot_manage_quiz'],
      '#node' => $node,
      '#questions' => $questions,
    ];
  }

  /**
   * collection Manager.
   *
   * @return array
   *   Return template.
   */
  public function Collection() {
    $node = $this->getNode();
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'set')
      ->condition('field_collection', $node->id())
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $sets = [];

    foreach ($nodes as $set) {
      $sets[] = $set;
    }
    return [
      '#theme' => ['iot_manage_collections'],
      '#node' => $node,
      '#sets' => $sets,

    ];
  }


  /**
   * @return \Drupal\Core\Entity\EntityInterface|null|static
   * Implement getNode
   */
  public function getNode() {
    $nid = \Drupal::request()->get('nid');
    $node = Node::load($nid);
    return $node;
  }

  /**
   * Get term name
   */
  public function _iot_get_term_name($tid) {
    $term = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->load($tid);
    return $term->name->value;
  }

  /**
   * get count question
   */
  public function _count_question($node) {
    $type = $node->get('field_question_type')->value;
    $count_question = 0;
    $count_answer = 0;
    $count_explain = 0;
    $total = 0;
    //radios
    if ($type == 'radio') {
      $questions = $node->get('field_question')->getValue();
      foreach ($questions as $q) {
        $para = \Drupal\paragraphs\Entity\Paragraph::load($q['target_id']);
        $explain = $para->get('field_explanation')->value;
        if (!empty($explain)) {
          $count_explain += 1;
        }
        $number = $para->get('field_number')->value;
        $para_child = $para->get('field_radios')->getValue();
        if ($number > 0) {
          $count_question += 1;
        }

        foreach ($para_child as $pachild) {
          $para_an = \Drupal\paragraphs\Entity\Paragraph::load($pachild['target_id']);
          $correct = $para_an->get('field_correct');
          if (isset($correct)) {
            $correct_an = $correct->getValue();
            foreach ($correct_an as $asn) {
              if (!is_null($asn['value']) && $asn['value'] == 1) {
                $count_answer += 1;
              }
            }

          }
        }
      }
    }
    //checkbox
    if ($type == 'checkbox') {
      $questions = $node->get('field_question')->getValue();
      foreach ($questions as $q) {
        $para = \Drupal\paragraphs\Entity\Paragraph::load($q['target_id']);
        $para_child = $para->get('field_checkbox')->getValue();
        $expain_child = $para->get('field_explain')->getValue();
        foreach ($expain_child as $ex) {
          $ex_c = \Drupal\paragraphs\Entity\Paragraph::load($ex['target_id']);
          $expl = $ex_c->get('field_explanation')->value;
          if (isset($expl) && !empty($expl)) {
            $count_explain += 1;
          }
        }
        $number = $para->get('field_number')->value;
        if (!empty($number)) {
          $arr = explode('-', $number);
          $total = ($arr[1] - $arr[0]) + 1;
        }
        $count_question = $total;
        foreach ($para_child as $pachild) {
          $para_an = \Drupal\paragraphs\Entity\Paragraph::load($pachild['target_id']);
          if (isset($para_an)) {
            $correct = $para_an->get('field_correct_checkbox')->value;
            if (isset($correct) && $correct == 1) {
              $count_answer += 1;
            }
          }

        }
      }
    }
    //other type
    if ($type == 'drop_down' || $type == 'blank' || $type == 'drag_drop') {
      $questions = $node->get('field_question')->getValue();
      foreach ($questions as $q) {
        $para = \Drupal\paragraphs\Entity\Paragraph::load($q['target_id']);
        $question = $para->get('field_question')->value;
        if (preg_match_all('/(\[*\:.*?\])/i', $question, $regs)) {
          $count_answer += count($regs[0]);
        }
        if (preg_match_all('/(\[[0-9].*?\])/i', $question, $regs)) {
          $count_question += count($regs[0]);
        }

        $expain = $para->get('field_explain')->getValue();
        foreach ($expain as $ex) {
          $ex_c = \Drupal\paragraphs\Entity\Paragraph::load($ex['target_id']);
          $expl = $ex_c->get('field_explanation')->value;
          if (isset($expl) && !empty($expl)) {
            $count_explain += 1;
          }
        }
      }
    }
    ///////////
    $result = [
      'count_question' => $count_question,
      'count_answer' => $count_answer,
      'count_explain' => $count_explain,
    ];
    return $result;
  }

}


