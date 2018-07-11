<?php
/**
 * Created by PhpStorm.
 * User: cadic
 * Date: 12/18/2017
 * Time: 9:46 PM
 */

namespace Drupal\iot_ielts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Facts' Block.
 * @Block(
 *   id = "facts",
 *   admin_label = @Translation("Facts"),
 *   category = @Translation("Facts"),
 * )
 */
class Facts extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'service')
      ->condition('status', 1)
      ->condition('field_service_type', 'facts')
      ->sort('created', 'ASC')
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $services = [];
    foreach ($nodes as $node) {
      $services[] = $node;
    }
    return ['#theme' => ['iot_facts'], '#nodes' => $services,];
  }
}
