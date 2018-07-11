<?php

namespace Drupal\iot_ielts\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateCounter extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'update_counter';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $form['nid'] = ['#type' => 'hidden', '#value' => $node->id(),];
    $form['quiz'] = ['#markup' => $node->getTitle()];
    $form['counter'] = [
      '#type' => 'textfield',
      '#title' => t('Total Views'),
      '#default_value' => counterNode($node),
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save changes'),
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
    $node = Node::load($form_state->getValue('nid'));
    $connection = \Drupal::database();
    $totalViews = $form_state->getValue('counter');
    $query = $connection->select('node_counter', 'a');
    $query->fields('a');
    $query->condition('nid', $node->id());
    $result = $query->execute()->fetchObject();
    if ($result) {
      $connection->update('node_counter')
        ->condition('nid', $node->id())
        ->fields([
          'totalcount' => $totalViews,
          // FIELD_1 NEW value./ FIELD_3 NEW value.
        ])
        ->execute();
      drupal_flush_all_caches();
    }
    else {
      $connection->insert('node_counter')->fields([
        'nid',
        'totalcount',
        'daycount',
        'timestamp',
      ])->values([$node->id(), $totalViews, 0, time(),])->execute();
      drupal_flush_all_caches();
    }
    $response = new RedirectResponse('/admin/quiz');
    $response->send();
    return [];
  }

}
