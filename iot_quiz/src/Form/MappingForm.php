<?php
/**
 * Created by PhpStorm.
 * User: cadic
 * Date: 12/7/2017
 * Time: 11:31 AM
 */

namespace Drupal\iot_quiz\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class MappingForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mapping_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $arr = $this->get_value_form_mapping();
    $form['mapping'] = [
      '#type' => 'vertical_tabs',
      '#title' => t('Mapping Result'),
    ];
    //listening
    $form['listening'] = [
      '#type' => 'details',
      '#title' => t('Listening'),
      '#group' => 'mapping',
    ];
    $form['listening']['listening_1'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 1'),
      '#default_value' => isset($arr['listening']['1']) ? $arr['listening']['1'] : '1',
    ];
    $form['listening']['listening_2_4'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 2-4'),
      '#default_value' => isset($arr['listening']['2-4']) ? $arr['listening']['2-4'] : '2',
    ];
    $form['listening']['listening_5_8'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 5-8'),
      '#default_value' => isset($arr['listening']['5-8']) ? $arr['listening']['5-8'] : '3',
    ];
    $form['listening']['listening_9_10'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 9-10'),
      '#default_value' => isset($arr['listening']['9-10']) ? $arr['listening']['9-10'] : '3.5',
    ];
    $form['listening']['listening_11_12'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 11-12'),
      '#default_value' => isset($arr['listening']['11-12']) ? $arr['listening']['11-12'] : '4',
    ];
    $form['listening']['listening_13_15'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 13-15'),
      '#default_value' => isset($arr['listening']['13-15']) ? $arr['listening']['13-15'] : '4.5',
    ];
    $form['listening']['listening_16_17'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 16-17'),
      '#default_value' => isset($arr['listening']['16-17']) ? $arr['listening']['16-17'] : '5',
    ];
    $form['listening']['listening_18_22'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 18-22'),
      '#default_value' => isset($arr['listening']['18-22']) ? $arr['listening']['18-22'] : '5.5',
    ];
    $form['listening']['listening_23_25'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 23-25'),
      '#default_value' => isset($arr['listening']['23-25']) ? $arr['listening']['23-25'] : '6',
    ];
    $form['listening']['listening_26_29'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 26-29'),
      '#default_value' => isset($arr['listening']['26-29']) ? $arr['listening']['26-29'] : '6.5',
    ];
    $form['listening']['listening_30_31'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 30-31'),
      '#default_value' => isset($arr['listening']['30-31']) ? $arr['listening']['30-31'] : '7',
    ];
    $form['listening']['listening_32_34'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 32-34'),
      '#default_value' => isset($arr['listening']['32-34']) ? $arr['listening']['32-34'] : '7.5',
    ];
    $form['listening']['listening_35-36'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 35-36'),
      '#default_value' => isset($arr['listening']['35-36']) ? $arr['listening']['35-36'] : '8',
    ];
    $form['listening']['listening_37_38'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 37-38'),
      '#default_value' => isset($arr['listening']['37-38']) ? $arr['listening']['37-38'] : '8.5',
    ];
    $form['listening']['listening_39_40'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 39-40'),
      '#default_value' => isset($arr['listening']['39-40']) ? $arr['listening']['39-40'] : '9',
    ];
    ///reading AC
    $form['reading_ac'] = [
      '#type' => 'details',
      '#title' => t('Reading Academy'),
      '#group' => 'mapping',
    ];
    $form['reading_ac']['readingac_1'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 1'),
      '#default_value' => isset($arr['reading_ac']['1']) ? $arr['reading_ac']['1'] : 1,
    ];
    $form['reading_ac']['readingac_2_3'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 2-3'),
      '#default_value' => isset($arr['reading_ac']['2-3']) ? $arr['reading_ac']['2-3'] : 2,
    ];
    $form['reading_ac']['readingac_4_5'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 4-5'),
      '#default_value' => isset($arr['reading_ac']['4-5']) ? $arr['reading_ac']['4-5'] : 2.5,
    ];
    $form['reading_ac']['readingac_6_7'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 6-7'),
      '#default_value' => isset($arr['reading_ac']['6-7']) ? $arr['reading_ac']['6-7'] : 3,
    ];
    $form['reading_ac']['readingac_8_9'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 8-9'),
      '#default_value' => isset($arr['reading_ac']['8-9']) ? $arr['reading_ac']['8-9'] : 3.5,
    ];
    $form['reading_ac']['readingac_10_12'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 10-12'),
      '#default_value' => isset($arr['reading_ac']['10-12']) ? $arr['reading_ac']['10-12'] : 4,
    ];
    $form['reading_ac']['readingac_13_14'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 13-14'),
      '#default_value' => isset($arr['reading_ac']['13-14']) ? $arr['reading_ac']['13-14'] : 4.5,
    ];
    $form['reading_ac']['readingac_15_18'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 15-18'),
      '#default_value' => isset($arr['reading_ac']['15-18']) ? $arr['reading_ac']['15-18'] : 5,
    ];
    $form['reading_ac']['readingac_19_22'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 19-22'),
      '#default_value' => isset($arr['reading_ac']['19-22']) ? $arr['reading_ac']['19-22'] : 5.5,
    ];
    $form['reading_ac']['readingac_23_26'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 23-26'),
      '#default_value' => isset($arr['reading_ac']['23-26']) ? $arr['reading_ac']['23-26'] : 6,
    ];
    $form['reading_ac']['readingac_27_29'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 27-29'),
      '#default_value' => isset($arr['reading_ac']['27-29']) ? $arr['reading_ac']['27-29'] : 6.5,
    ];
    $form['reading_ac']['readingac_30_32'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 30-32'),
      '#default_value' => isset($arr['reading_ac']['30-32']) ? $arr['reading_ac']['30-32'] : 7,
    ];
    $form['reading_ac']['readingac_33_34'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 33-34'),
      '#default_value' => isset($arr['reading_ac']['33-34']) ? $arr['reading_ac']['33-34'] : 7.5,
    ];
    $form['reading_ac']['readingac_35_36'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 35-36'),
      '#default_value' => isset($arr['reading_ac']['35-36']) ? $arr['reading_ac']['35-36'] : 8,
    ];
    $form['reading_ac']['readingac_37_38'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 37-38'),
      '#default_value' => isset($arr['reading_ac']['37-38']) ? $arr['reading_ac']['37-38'] : 8.5,
    ];
    $form['reading_ac']['readingac_39_40'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 39-40'),
      '#default_value' => isset($arr['reading_ac']['39-40']) ? $arr['reading_ac']['39-40'] : 9,
    ];
    //reading gt

    $form['reading_gt'] = [
      '#type' => 'details',
      '#title' => t('Reading General Training'),
      '#group' => 'mapping',
    ];
    $form['reading_gt']['readinggt_1_3'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 1-3'),
      '#default_value' => isset($arr['reading_gt']['1-3']) ? $arr['reading_gt']['1-3'] : 1,
    ];
    $form['reading_gt']['readinggt_4_5'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 4-5'),
      '#default_value' => isset($arr['reading_gt']['4-5']) ? $arr['reading_gt']['4-5'] : 2,
    ];
    $form['reading_gt']['readinggt_6_8'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 6-8'),
      '#default_value' => isset($arr['reading_gt']['6-8']) ? $arr['reading_gt']['6-8'] : 2.5,
    ];
    $form['reading_gt']['readinggt_9_11'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 9-11'),
      '#default_value' => isset($arr['reading_gt']['9-11']) ? $arr['reading_gt']['9-11'] : 3,
    ];
    $form['reading_gt']['readinggt_12_14'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 12-14'),
      '#default_value' => isset($arr['reading_gt']['12-14']) ? $arr['reading_gt']['12-14'] : 3.5,
    ];
    $form['reading_gt']['readinggt_15_18'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 15-18'),
      '#default_value' => isset($arr['reading_gt']['15-18']) ? $arr['reading_gt']['15-18'] : 4,
    ];
    $form['reading_gt']['readinggt_19_22'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 19-22'),
      '#default_value' => isset($arr['reading_gt']['19-22']) ? $arr['reading_gt']['19-22'] : 4.5,
    ];
    $form['reading_gt']['readinggt_23_26'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 23-26'),
      '#default_value' => isset($arr['reading_gt']['23-26']) ? $arr['reading_gt']['23-26'] : 5,
    ];
    $form['reading_gt']['readinggt_27_29'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 27-29'),
      '#default_value' => isset($arr['reading_gt']['27-29']) ? $arr['reading_gt']['27-29'] : 5.5,
    ];
    $form['reading_gt']['readinggt_30_31'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 30-31'),
      '#default_value' => isset($arr['reading_gt']['30-31']) ? $arr['reading_gt']['30-31'] : 6,
    ];
    $form['reading_gt']['readinggt_32_33'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 32-33'),
      '#default_value' => isset($arr['reading_gt']['32-33']) ? $arr['reading_gt']['32-33'] : 6.5,
    ];
    $form['reading_gt']['readinggt_34_35'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 34-35'),
      '#default_value' => isset($arr['reading_gt']['34-35']) ? $arr['reading_gt']['34-35'] : 7,
    ];
    $form['reading_gt']['readinggt_36'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 36'),
      '#default_value' => isset($arr['reading_gt']['36']) ? $arr['reading_gt']['36'] : 7.5,
    ];
    $form['reading_gt']['readinggt_37_38'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 37-38'),
      '#default_value' => isset($arr['reading_gt']['37-38']) ? $arr['reading_gt']['37-38'] : 8,
    ];
    $form['reading_gt']['readinggt_39'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 39'),
      '#default_value' => isset($arr['reading_gt']['39']) ? $arr['reading_gt']['39'] : 8.5,
    ];
    $form['reading_gt']['readinggt_40'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Question 40'),
      '#default_value' => isset($arr['reading_gt']['40']) ? $arr['reading_gt']['40'] : 9,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
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
    // drupal_set_message($this->t('@can_name ,Your application is being submitted!', array('@can_name' => $form_state->getValue('candidate_name'))));
    $listening = [];
    $reading_gt = [];
    $reading_ac = [];
    $i = 0;
    foreach ($form_state->getValues() as $key => $value) {
      if ($i > 0) {
        if (strpos($key, 'listening') !== FALSE) {
          $listening_key = str_replace('listening_', '', $key);
          $listening_key = str_replace('_', '-', $listening_key);
          $listening[$listening_key] = $value;
        }
        if (strpos($key, 'readingac') !== FALSE) {
          $reading_ac_key = str_replace('readingac_', '', $key);
          $reading_ac_key = str_replace('_', '-', $reading_ac_key);
          $reading_ac[$reading_ac_key] = $value;
        }
        if (strpos($key, 'readinggt') !== FALSE) {
          $reading_gt_key = str_replace('readinggt_', '', $key);
          $reading_gt_key = str_replace('_', '-', $reading_gt_key);
          $reading_gt[$reading_gt_key] = $value;
        }
      }
      $i++;
    }
    //save listening
    $listening_data = serialize($listening);
    $result_listening = \Drupal::state()
      ->set('mapping_listening', $listening_data);
    //save reading AC
    $reading_ac_data = serialize($reading_ac);
    $result_reading_ac = \Drupal::state()
      ->set('mapping_reading_ac', $reading_ac_data);
    //save reading GT
    $reading_gt_data = serialize($reading_gt);
    $result_reading_gt = \Drupal::state()
      ->set('mapping_reading_gt', $reading_gt_data);
    return;

  }

  /**
   * Get default value
   */
  private function get_value_form_mapping() {
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
    return $arr;
  }
}
