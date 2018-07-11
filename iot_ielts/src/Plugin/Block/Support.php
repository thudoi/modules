<?php
/**
 * Created by PhpStorm.
 * User: cadic
 * Date: 12/18/2017
 * Time: 9:46 PM
 */

namespace Drupal\iot_ielts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'Support' Block.
 * @Block(
 *   id = "support",
 *   admin_label = @Translation("Support"),
 *   category = @Translation("Support"),
 * )
 */
class Support extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'service')
      ->condition('status', 1)
      ->condition('field_service_type', 'support')
      ->sort('created', 'ASC')
      ->execute();
    $nodes = Node::loadMultiple($nids);
    $services = [];
    foreach ($nodes as $node) {
      $services[] = $node;
    }
    return ['#theme' => ['iot_support'], '#nodes' => $services,];
  }

}
