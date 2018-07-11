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
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'Tips' Block.
 * @Block(
 *   id = "tips",
 *   admin_label = @Translation("Tips"),
 *   category = @Translation("Tips"),
 * )
 */
class Tips extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    $nid = \Drupal::state()->get('iot_random_tips');
    if ($nid) {
      $node = Node::load($nid);
    }
    else {
      $nids = \Drupal::entityQuery('node')
        ->condition('type', 'tips')
        ->condition('promote', 1)
        ->condition('status', 1)
        ->sort('created', 'DESC')
        ->range(0, 1)
        ->execute();
      $nodes = Node::loadMultiple($nids);
      $node = FALSE;
      if ($nodes) {
        $node = reset($nodes);
      }
    }
    $vote_widget_service = \Drupal::service('rate.entity.vote_widget');
    $views = 0;
    $vote_widget = FALSE;
    $cate = FALSE;
    $term_uri = FALSE;
    if ($node) {
      $term = Term::load($node->get('field_category')->target_id);
      $vote_widget = $vote_widget_service->buildRateVotingWidget($node->id(), $node->getEntityTypeId(), $node->bundle());
      $views = counterNode($node);
      if ($term) {
        $cate = $term;
        $term_uri = taxonomy_term_uri($term);
      }
      if ($node->get('field_standfirst')->value) {
        $desc = shortContent($node->get('field_standfirst')->value, 150);
      }
      else {
        $desc = shortContent($node->get('body')->value, 150);
      }
    }

    return [
      '#theme' => ['iot_tips'],
      '#node' => $node,
      '#rate' => $vote_widget['votingapi_links'],
      '#views' => $views,
      '#term' => $cate,
      '#term_uri' => $term_uri,
      '#desc' => $desc,
    ];
  }

}
