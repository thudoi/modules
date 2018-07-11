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
 * Provides a 'LatestCollection' Block.
 * @Block(
 *   id = "latestcollection",
 *   admin_label = @Translation("Latest Collection"),
 *   category = @Translation("Latest Collection"),
 * )
 */
class LatestCollection extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'collection')
      ->condition('status', 1)
      ->condition('promote', 1)
      ->sort('field_collection_order', 'ASC')
      ->range(0, 4)
      ->execute();
    $nodes = Node::loadMultiple($nids);
    $services = [];
    foreach ($nodes as $node) {
      $vote_widget_service = \Drupal::service('rate.entity.vote_widget');
      $vote_widget = $vote_widget_service->buildRateVotingWidget($node->id(), $node->getEntityTypeId(), $node->bundle());
      $data['star_rate'] = $vote_widget['votingapi_links'];
      $data['collection'] = $node;
      $services[] = $data;
    }
    return ['#theme' => ['iot_latest_collection'], '#nodes' => $services,];
  }

}
