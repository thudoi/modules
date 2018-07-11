<?php

namespace Drupal\iot_ielts\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class ManageQuestion extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'manage_question';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $node = Node::load($node->id());
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'question')
      ->condition('field_section', $node->id())
      ->sort('field_order', 'ASC')
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $form['mytable'] = [
      '#type' => 'table',
      '#caption' => '<a class="btn btn-success" href="/node/add/question?bid=' . $node->id() . '&type=' . $node->get('field_section_type')->value . '&destination=question/' . $node->id() . '/manage">Add Question</a><a class="btn btn-success" href="/quiz/' . $node->get('field_quiz')->target_id . '/manage">Back To Quiz Manager</a>',
      '#header' => [
        t('Question Title'),
        t('Question Count'),
        t('Answer Count'),
        t('Explain Count'),
        t('Front Question Type'),
        t('Question Type'),
        t('Weight'),
        t('Operations'),
      ],
      '#empty' => t('There are no questions yet. <a href="/node/add/question?bid=' . $node->id() . '&type=' . $node->get('field_section_type')->value . '&destination=question/' . $node->id() . '/manage">Add Question.</a>'),
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'mytable-order-weight',
        ],
      ],
    ];


    foreach ($nodes as $id => $entity) {
      $tid = $entity->get('field_qtype_front')->target_id;
      $count = $this->_count_question($entity);
      // TableDrag: Mark the table row as draggable.
      $form['mytable'][$id]['#attributes']['class'][] = 'draggable';
      // TableDrag: Sort the table row according to its existing/configured weight.
      $form['mytable'][$id]['#weight'] = $entity->get('field_order')->value;


      // Some table columns containing raw markup.
      $form['mytable'][$id]['label'] = ['#plain_text' => $entity->get('field_title_ui')->value,];

      $form['mytable'][$id]['question_count'] = ['#plain_text' => $count['count_question'],];
      $form['mytable'][$id]['answer_count'] = ['#plain_text' => $count['count_answer'],];
      $form['mytable'][$id]['explain_count'] = ['#plain_text' => $count['count_explain'],];
      $form['mytable'][$id]['front_type'] = ['#plain_text' => $this->_iot_get_term_name($tid),];
      $form['mytable'][$id]['question_type'] = ['#plain_text' => $entity->get('field_question_type')->value,];

      $form['mytable'][$id]['weight'] = [
        '#type' => 'weight',
        '#title' => t('Weight for @title', ['@title' => $entity->get('field_title_ui')->value]),
        '#title_display' => 'invisible',
        '#default_value' => $entity->get('field_order')->value,
        // Classify the weight element for #tabledrag.
        '#attributes' => ['class' => ['mytable-order-weight']],
      ];

      $form['mytable'][$id]['operations'] = ['#markup' => '<a class="btn btn-success" href="/node/' . $id . '/edit?destination=question/' . $node->id() . '/manage">Edit</a><a class="btn btn-danger" href="/node/' . $id . '/delete?destination=question/' . $node->id() . '/manage">Delete</a>',];

      $form['mytable'][$id]['id'] = [
        '#type' => 'hidden',
        '#value' => $entity->id(),
      ];
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save changes'),
      // TableSelect: Enable the built-in form validation for #tableselect for
      // this form button, so as to ensure that the bulk operations form cannot
      // be submitted without any selected items.
      '#tableselect' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      foreach ($value as $key => $val) {
        $node = Node::load($val['id']);
        $node->set('field_order', $val['weight']);
        $node->save();
      }
    }
  }

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
