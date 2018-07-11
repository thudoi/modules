<?php

namespace Drupal\iot_quiz;

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class ExplainService {

  public function Import($sid, $data) {
    $sids = \Drupal::entityQuery('node')
      ->condition('type', 'question')
      ->condition('field_section', $sid)
      ->execute();
    $nodes = Node::loadMultiple($sids);
    foreach ($nodes as $node) {
      $blocks = $node->get('field_question')->getValue();
      $type = $node->get('field_question_type')->value;
      foreach ($blocks as $block) {
        $question = Paragraph::load($block['target_id']);
        if ($type == 'radio' || $type == 'checkbox') {
          $number = $question->get('field_number')->value;
          $question->set('field_explanation', $data[$number]);
          $question->save();
        }
        else {
          $range = $node->get('field_title_ui')->value;
          $range = str_replace('Questions', '', $range);
          $range = str_replace('Question', '', $range);
          $range = str_replace(' ', '', $range);
          $exp = explode('-', $range);
          $data_paragraph = [];
          for ($i = $exp[0]; $i <= $exp[1]; $i++) {
            $paragraph = Paragraph::create([
              'type' => 'explanation',
              'field_explanation' => [
                "value" => $data[$i],
                "format" => "full_html",
              ],
              'field_number' => ["value" => $i,],
            ]);
            $paragraph->save();
            $data_paragraph[] = [
              'target_id' => $paragraph->id(),
              'target_revision_id' => $paragraph->getRevisionId(),
            ];
          }
          $question->set('field_explain', $data_paragraph);
          $question->save();
        }
      }
    }
  }

  public function Get($block) {
  }
}
