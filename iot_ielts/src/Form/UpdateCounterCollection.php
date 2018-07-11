<?php

namespace Drupal\iot_ielts\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\statistics\NodeStatisticsDatabaseStorage;

class UpdateCounterCollection extends FormBase {

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
    $form['collection'] = ['#markup' => $node->getTitle(),];
    $form['counter'] = [
      '#type' => 'textfield',
      '#title' => t('Total Views'),
      '#default_value' => $node->get('field_collection_count') ? $node->get('field_collection_count')->value : 0,
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
    $totalViews = $form_state->getValue('counter');
    $node->set('field_collection_count', $totalViews);
    $node->save();
    $response = new RedirectResponse('/admin/collection/count');
    $response->send();
    return [];
  }

}
