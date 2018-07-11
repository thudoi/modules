<?php
/**
 * Created by PhpStorm.
 * User: cadic
 * Date: 11/14/17
 * Time: 14:48
 */

use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Implement hook_form_alter()
 */
function iot_ielts_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'node_question_form' || $form_id == 'node_question_edit_form') {

    $delta = 0;
    $max_delta = $form['field_question']['widget']['#max_delta'];
    //  kint($form);
    while ($delta <= $max_delta) {
      $form['field_question']['widget'][$delta]['subform']['field_radios']['#states'] = ['visible' => [':input[name="field_question_type"]' => ['value' => 'radio'],],];

      $form['field_question']['widget'][$delta]['subform']['field_checkbox']['#states'] = ['visible' => [':input[name="field_question_type"]' => ['value' => 'checkbox'],],];
      $form['field_question']['widget'][$delta]['subform']['field_dropdown_title']['#states'] = ['visible' => [':input[name="field_question_type"]' => ['value' => 'drop_down'],],];
      $form['field_question']['widget'][$delta]['subform']['field_dropdown']['#states'] = [
        'visible' => [
          [':input[name="field_question_type"]' => ['value' => 'drop_down']],
          'or',
          [':input[name="field_question_type"]' => ['value' => 'drag_drop']],
        ],
      ];

      $form['field_question']['widget'][$delta]['subform']['field_explain']['#states'] = [
        'visible' => [
          [':input[name="field_question_type"]' => ['value' => 'blank']],
          'or',
          [':input[name="field_question_type"]' => ['value' => 'checkbox']],
          'or',
          [':input[name="field_question_type"]' => ['value' => 'drop_down']],
          'or',
          [':input[name="field_question_type"]' => ['value' => 'drag_drop']],
        ],
      ];
      $form['field_question']['widget'][$delta]['subform']['field_explanation']['#states'] = [
        'invisible' => [
          [':input[name="field_question_type"]' => ['value' => 'blank']],
          'or',
          [':input[name="field_question_type"]' => ['value' => 'checkbox']],
          'or',
          [':input[name="field_question_type"]' => ['value' => 'drop_down']],
          'or',
          [':input[name="field_question_type"]' => ['value' => 'drag_drop']],
        ],
      ];
      $form['field_question']['widget'][$delta]['subform']['field_number']['#states'] = [
        'visible' => [
          [':input[name="field_question_type"]' => ['value' => 'radio']],
          'or',
          [':input[name="field_question_type"]' => ['value' => 'checkbox']],
        ],
      ];

      $delta_child = 0;
      $max_child_delta = $form['field_question']['widget'][$delta]['subform']['field_radios']['widget']['#max_delta'];
      while ($delta_child <= $max_child_delta) {
        unset($form['field_question']['widget'][$delta]['subform']['field_radios']['widget'][$delta_child]['subform']['field_correct']['widget']['#options']['_none']);
        unset($form['field_question']['widget'][$delta]['subform']['field_checkbox']['widget'][$delta_child]['subform']['field_correct_checkbox']['widget']['#options']['_none']);
        //                $form['field_question']['widget'][$delta]['subform']['field_explain']['widget'][$delta_child]['subform']['field_number']['#states'] = [
        //                    'invisible' => [
        //                        ':input[name="field_question_type"]' => ['value' => 'checkbox'],
        //                    ],
        //                ];
        $delta_child++;
      }

      $delta++;

    }
    $form['field_block_description']['#states'] = ['visible' => [':input[name="field_question_type"]' => ['value' => 'radio'],],];
    $form['#attached']['library'][] = 'iot_ielts/radios_option';
    if (isset($_GET['bid'])) {
      $section = Node::load($_GET['bid']);
      $form['title']['widget'][0]['value']['#default_value'] = $section->getTitle();
      hide($form['field_section']);
      hide($form['title']);
    }
    if ($form_id == 'node_question_edit_form') {
      hide($form['field_section']);
      hide($form['title']);
      $form['field_question_type']['#attributes']['class'][] = 'hidden-editing';
    }
    if (isset($_GET['destination'])) {
      $form['actions']['back'] = [
        '#markup' => '<a class="btn btn-primary" href="' . $_GET['destination'] . '">' . t('Back') . '</a>',
        '#weight' => 2000,
      ];
    }

  }
  //section
  if ($form_id == 'node_section_form' || $form_id == 'node_section_edit_form') {
    $form['field_passage']['#states'] = ['visible' => [':input[name="field_section_type"]' => ['value' => 'reading'],],];
    $form['field_passage_explain']['#states'] = ['visible' => [':input[name="field_section_type"]' => ['value' => 'reading'],],];
    $form['field_passage_desc']['#states'] = ['visible' => [':input[name="field_section_type"]' => ['value' => 'reading'],],];
    $form['field_audio_sc']['#states'] = ['invisible' => [':input[name="field_section_type"]' => ['value' => 'reading'],],];
    $form['field_subtitle']['#states'] = ['invisible' => [':input[name="field_section_type"]' => ['value' => 'reading'],],];
    if (isset($_GET['sid']) && isset($_GET['type'])) {
      $set = Node::load($_GET['sid']);
      $form['title']['widget'][0]['value']['#default_value'] = $set->getTitle();
      $form['field_section_type']['widget']['#default_value'] = $_GET['type'];
      hide($form['field_quiz']);
      hide($form['title']);
      // hide($form['field_section_type']);
    }
    if ($form_id == 'node_section_edit_form') {
      hide($form['field_set']);
      $form['field_section_type']['#attributes']['class'][] = 'hidden-editing';
      hide($form['title']);
    }
    if (isset($_GET['destination'])) {
      $form['actions']['back'] = [
        '#markup' => '<a class="btn btn-primary" href="' . $_GET['destination'] . '">' . t('Back') . '</a>',
        '#weight' => 2000,
      ];
    }


  }
  //quiz

  if ($form_id == 'node_quiz_form' || $form_id == 'node_quiz_edit_form') {
    $form['field_audio']['#states'] = ['visible' => [':input[name="field_quiz_type"]' => ['value' => 'listening'],],];
    $form['field_audio_url']['#states'] = ['visible' => [':input[name="field_quiz_type"]' => ['value' => 'listening'],],];
    $form['field_show_explanation']['#states'] = [
      'visible' => [
        [':input[name="field_quiz_type"]' => ['value' => 'listening']],
        'or',
        [':input[name="field_quiz_type"]' => ['value' => 'reading']],
      ],
    ];
    $form['field_question_simple']['#states'] = [
      'visible' => [
        [':input[name="field_quiz_type"]' => ['value' => 'writing']],
        'or',
        [':input[name="field_quiz_type"]' => ['value' => 'speaking']],
      ],
    ];

    if (isset($_GET['sid']) && isset($_GET['type'])) {
      $set = Node::load($_GET['sid']);
      $form['title']['widget'][0]['value']['#default_value'] = $set->getTitle();
      hide($form['field_set']);
      hide($form['title']);
      // hide($form['field_section_type']);
    }
    if ($form_id == 'node_quiz_edit_form') {
      hide($form['field_set']);
      hide($form['title']);
    }
    if (isset($_GET['destination'])) {
      $form['actions']['back'] = [
        '#markup' => '<a class="btn btn-primary" href="' . $_GET['destination'] . '">' . t('Back') . '</a>',
        '#weight' => 2000,
      ];
    }


  }

  //block

  if ($form_id == 'node_set_form' || $form_id == 'node_set_edit_form') {
    if (isset($_GET['cid'])) {
      $collection = Node::load($_GET['cid']);
      $form['title']['widget'][0]['value']['#default_value'] = $collection->getTitle();
      hide($form['field_collection']);
      hide($form['title']);
    }
    if ($form_id == 'node_set_edit_form') {
      hide($form['field_collection']);
      hide($form['title']);
    }
    if (isset($_GET['destination'])) {
      $form['actions']['back'] = [
        '#markup' => '<a class="btn btn-primary" href="' . $_GET['destination'] . '">' . t('Back') . '</a>',
        '#weight' => 2000,
      ];
    }
  }

  if ($form_id == 'node_question_simple_form' || $form_id == 'node_question_simple_edit_form') {
    if (isset($_GET['bid'])) {
      $set = Node::load($_GET['bid']);
      $form['title']['widget'][0]['value']['#default_value'] = $set->getTitle();
      hide($form['field_set']);
      hide($form['title']);
      hide($form['field_question_type']);
    }
    if ($form_id == 'node_question_simple_edit_form') {
      hide($form['title']);
      hide($form['field_set']);
      hide($form['field_question_type']);
    }
    if (isset($_GET['destination'])) {
      $form['actions']['back'] = [
        '#markup' => '<a class="btn btn-primary" href="' . $_GET['destination'] . '">' . t('Back') . '</a>',
        '#weight' => 2000,
      ];
    }
  }
  if ($form_id == 'node_collection_form' || $form_id == 'node_collection_edit_form') {
    if (isset($_GET['destination'])) {
      $form['actions']['back'] = [
        '#markup' => '<a class="btn btn-primary" href="' . $_GET['destination'] . '">' . t('Back') . '</a>',
        '#weight' => 2000,
      ];
    }
  }
  if ($form['#id'] == 'views-exposed-form-manage-report-mistake-page-1') {
    //       $form['field_quiz_target_id']['#type'] = 'select';
    //      $form['field_quiz_target_id']['#options'] = getQuizList();
    //    unset($form['field_quiz_target_id']);
    //    $form['field_quiz_target_id'] = [
    //        '#type' => 'select',
    //        '#options' => getQuizList(),
    //        '#weight' => 2
    //    ];
    unset($form['field_collection_target_id']);
    $form['field_collection_target_id'] = [
      '#type' => 'select',
      '#options' => getCollectionList(),
      '#weight' => 1,
    ];
  }
  if ($form_id == 'node_volunteer_explain_form') {
    $form['title']['#disabled'] = 'disabled';
    $form['field_v_section']['#disabled'] = 'disabled';
    $form['field_status']['#disabled'] = 'disabled';
    foreach ($form['field_explains']['widget'] as $key => $val) {
      if (is_numeric($key)) {
        $form['field_explains']['widget'][$key]['subform']['field_question_number']['#disabled'] = 'disabled';
      }
    }
    unset($form['actions']['preview']);
    if (isset($_GET['destination'])) {
      $form['actions']['back'] = [
        '#markup' => '<a class="btn btn-primary" href="' . $_GET['destination'] . '">' . t('Back') . '</a>',
        '#weight' => 2000,
      ];
    }
    $form['actions']['import'] = [
      '#type' => 'submit',
      '#value' => t('Import'),
      '#submit' => ['_iot_import_explain'],
    ];
  }
}

function _iot_import_explain($form, \Drupal\Core\Form\FormStateInterface $form_state) {
  if ($entity = $form_state->getFormObject()->getEntity()) {
    $entity->set('field_status', 2);
    $entity->save();
    $explains = $form_state->getValue('field_explains');
    foreach ($explains as $explain) {
      $explain['subform']['field_question_number'];
    }
    $data = [];
    foreach ($explains as $explain) {
      if ($explain['subform']['field_question_number'][0]['value']) {
        $data[$explain['subform']['field_question_number'][0]['value']] = $explain['subform']['field_v_explain'][0]['value'];
      }
    }
    $sid = $entity->get('field_v_section')->target_id;
    $service = \Drupal::service('iot_quiz.explainservice');

    $service->Import($sid, $data);
    drupal_set_message('Import successful');
  }
}

function getQuizList() {

  $nids = \Drupal::entityQuery('node')
    ->condition('type', 'report_mistake')
    ->execute();
  $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
  $secs = [];
  $secs[''] = t('All');
  foreach ($nodes as $sec) {
    $quiz = Node::load($sec->get('field_quiz')->target_id);
    $secs[$quiz->id()] = str_replace('_', ' ', $quiz->getTitle());
  }
  return $secs;
}

function getCollectionList() {

  $nids = \Drupal::entityQuery('node')
    ->condition('type', 'report_mistake')
    ->execute();
  $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
  $secs = [];
  $secs[''] = t('All');
  foreach ($nodes as $sec) {
    $quiz = Node::load($sec->get('field_collection')->target_id);
    $secs[$quiz->id()] = str_replace('_', ' ', $quiz->getTitle());
  }
  return $secs;
}

