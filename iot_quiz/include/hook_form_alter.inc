<?php

/**
 * Implement hook_form_alter
 */

use Drupal\Core\Url;

function iot_quiz_form_alter(&$form, &$form_state, $form_id) {
  //    drupal_set_message($form_id);
  switch ($form_id) {
    //    case 'node_collection_form':
    //      unset($form['field_collection_set']);
    //      break;
    //    case 'node_collection_edit_form':
    //      unset($form['field_collection_set']);
    //      $form['#theme'] = ['custom_edit_node_collection'];
    //      break;
    case 'node_section_listening_form':
      unset($form['field_listening_questions']);
      $form['#theme'] = ['custom_edit_node_listening_questions'];
      break;
    case 'node_section_reading_form':
      unset($form['field_reading_questions']);
      $form['#theme'] = ['custom_edit_node_reading_questions'];
      break;
    case 'node_section_listening_edit_form':
      if (isset($_GET['action'])) {
        switch ($_GET['action']) {
          case 'edit':
            unset($form['field_listening_questions']);
            break;
          case 'manage':
            $form['title']['#access'] = FALSE;
            $form['field_audio']['#access'] = FALSE;
            $form['field_audio_script']['#access'] = FALSE;
            $form['field_subtitle']['#access'] = FALSE;
            unset($form['title']);
            break;
          default:
            $form['field_listening_questions']['#access'] = FALSE;
            break;
        }
      }
      $form['#theme'] = ['custom_edit_node_listening_questions'];
      break;
    case 'node_section_reading_edit_form':
      if (isset($_GET['action'])) {
        switch ($_GET['action']) {
          case 'edit':
            unset($form['field_reading_questions']);
            break;
          case 'manage':
            unset($form['title']);
            break;
          default:
            unset($form['field_reading_questions']);
            break;
        }
      }
      $form['#theme'] = ['custom_edit_node_reading_questions'];
      break;
    case 'node_quiz_edit_form':
      if (isset($_GET['edit'])) {
        switch ($type = $_GET['edit']) {
          case 'writing':
            unset($form['field_reading_sections']);
            unset($form['title']);
            unset($form['field_quiz_category']);
            unset($form['advanced']);
            unset($form['status']);
            unset($form['changed']);
            unset($form['revision_log']);
            unset($form['revision']);
            unset($form['meta']);
            unset($form['field_quiz_duration']);
            unset($form['field_listening_sections']);
            unset($form['field_speaking']);
            break;
          case 'speaking':
            unset($form['field_reading_sections']);
            unset($form['title']);
            unset($form['field_quiz_category']);
            unset($form['advanced']);
            unset($form['status']);
            unset($form['changed']);
            unset($form['revision_log']);
            unset($form['revision']);
            unset($form['meta']);
            unset($form['field_quiz_duration']);
            unset($form['field_listening_sections']);
            unset($form['field_writing']);
            break;
          default:
            unset($form['field_reading_sections']);
            unset($form['field_listening_sections']);
            unset($form['field_speaking']);
            unset($form['field_writing']);
            break;
        }
      }
      break;
    case 'node_quiz_form':
      unset($form['field_reading_sections']);
      unset($form['field_listening_sections']);
      unset($form['field_speaking']);
      unset($form['field_writing']);
      break;
  }
}

function iot_alter_quiz_form_submit() {

}

function iot_quiz_inline_entity_form_entity_form_alter(&$entity_form, &$form_state) {
  //  drupal_set_message($entity_form['#ief_labels']);
  if ($entity_form['#entity_type'] == 'node' && $entity_form['#bundle'] == 'quiz') {
    unset($entity_form['field_reading_sections']);
    unset($entity_form['field_listening_sections']);
    unset($entity_form['field_speaking']);
    unset($entity_form['field_writing']);
  }
}

function iot_quiz_inline_entity_form_reference_form_alter(&$reference_form, &$form_state) {
  $reference_form['entity_id']['#description'] = t('New autocomplete description');
}
