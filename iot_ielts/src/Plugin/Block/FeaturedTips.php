<?php
/**
 * Created by PhpStorm.
 * User: cadic
 * Date: 12/18/2017
 * Time: 9:46 PM
 */

namespace Drupal\iot_ielts\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'Featured Tips' Block.
 * @Block(
 *   id = "featured_tips",
 *   admin_label = @Translation("Featured Tips"),
 *   category = @Translation("Featured Tips"),
 * )
 */
class FeaturedTips extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    if (isset($_GET['page']) && $_GET['page'] != 0) {
      return ['#markup' => ''];
    }
    else {
      $vote_widget_service = \Drupal::service('rate.entity.vote_widget');
      $nids = \Drupal::entityQuery('node')
        ->condition('type', 'tips')
        ->condition('field_featured', 1)
        ->condition('status', 1)
        ->sort('created', 'DESC')
        ->range(0, 1)
        ->execute();
      $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
      $node = FALSE;
      if ($nodes) {
        $node = reset($nodes);
      }
      $views = 0;
      $vote_widget = FALSE;
      $cate = FALSE;
      $term_uri = FALSE;
      $desc = '';
      if ($node) {
        $term = Term::load($node->get('field_category')->target_id);
        $vote_widget = $vote_widget_service->buildRateVotingWidget($node->id(), $node->getEntityTypeId(), $node->bundle());
        $views = counterNode($node);
        if ($term) {
          $cate = $term;
          $term_uri = taxonomy_term_uri($term);
        }
        $desc = shortContent($node->get('body')->value, 200);
      }

      return [
        '#theme' => ['iot_tips_featured'],
        '#node' => $node,
        '#rate' => $vote_widget['votingapi_links'],
        '#views' => $views,
        '#term' => $cate,
        '#term_uri' => $term_uri,
        '#desc' => $desc,
      ];
    }
  }

}
