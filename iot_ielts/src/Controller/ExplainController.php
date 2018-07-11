<?php

namespace Drupal\iot_ielts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class SetController.
 */
class ExplainController extends ControllerBase {

  /**
   * Explain Manager.
   *
   * @return array
   *   Return template.
   */
  public function ExplainSection(NodeInterface $node) {
    //      $node = Node::load($node);
    $range = $node->get('field_question_range')->value;
    $range = str_replace('Questions', '', $range);
    $range = str_replace('Question', '', $range);
    $range = str_replace(' ', '', $range);
    $data = explode('-', $range);
    $qids = \Drupal::entityQuery('node')
      ->condition('type', 'question')
      ->condition('field_section', $node->id())
      ->execute();
    $questions = Node::loadMultiple($qids);
    $data_c = [];
    foreach ($questions as $question) {
      $blocks = $question->get('field_question')->getValue();
      $type = $question->get('field_question_type')->value;
      foreach ($blocks as $block) {
        if ($type == 'checkbox') {
          $block_c = Paragraph::load($block['target_id']);
          $data_c[] = [
            'data' => $block_c->get('field_number')->value,
            'check' => FALSE,
          ];
        }
      }
    }
    $sids = \Drupal::entityQuery('node')
      ->condition('type', 'volunteer_explain')
      ->condition('field_v_section', $node->id())
      ->execute();
    if ($sids) {
      $id = reset($sids);
    }
    else {
      $volunteer = Node::create([
        'type' => 'volunteer_explain',
        'title' => $node->get('title')->value,
        'status' => 1,
      ]);
      $volunteer->set('field_v_section', $node->id());
      $volunteer->set('field_status', 0);
      $data_paragraph = [];
      for ($i = $data[0]; $i <= $data[1]; $i++) {
        if ($data_c) {
          foreach ($data_c as $key => $range_c) {
            $check = $this->check_q_num($i, $range_c['data']);
            if ($check) {
              if (!$data_c[$key]['check']) {
                $paragraph = Paragraph::create([
                  'type' => 'explain',
                  'field_v_explain' => ["value" => '', "format" => "full_html"],
                  'field_question_number' => ["value" => $range_c['data'],],
                ]);
                $paragraph->save();
                $data_paragraph[] = [
                  'target_id' => $paragraph->id(),
                  'target_revision_id' => $paragraph->getRevisionId(),
                ];
                $data_c[$key]['check'] = TRUE;
              }
            }
            else {
              $paragraph = Paragraph::create([
                'type' => 'explain',
                'field_v_explain' => ["value" => '', "format" => "full_html"],
                'field_question_number' => ["value" => $i,],
              ]);
              $paragraph->save();
              $data_paragraph[] = [
                'target_id' => $paragraph->id(),
                'target_revision_id' => $paragraph->getRevisionId(),
              ];

            }
          }
        }
        else {
          $paragraph = Paragraph::create([
            'type' => 'explain',
            'field_v_explain' => ["value" => '', "format" => "full_html"],
            'field_question_number' => ["value" => $i,],
          ]);
          $paragraph->save();
          $data_paragraph[] = [
            'target_id' => $paragraph->id(),
            'target_revision_id' => $paragraph->getRevisionId(),
          ];
        }
      }
      $volunteer->set('field_explains', $data_paragraph);
      $volunteer->save();
      $id = $volunteer->id();
    }

    $node_data = Node::load($id);
    $form = \Drupal::entityTypeManager()
      ->getFormObject('node', 'default')
      ->setEntity($node_data);
    $form = \Drupal::formBuilder()->getForm($form);
    return [
      '#theme' => ['iot_manage_explain'],
      '#form' => $form,
      '#sets' => '',

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

  public function check_q_num($i, $range) {
    $data = explode('-', $range);
    if ($i <= $data[1] && $i >= $data[0]) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
}


