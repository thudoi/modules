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
 * Provides a 'Featured Tips' Block.
 * @Block(
 *   id = "related_featured_tips",
 *   admin_label = @Translation("Related Featured Tips"),
 *   category = @Translation("Related Featured Tips"),
 * )
 */
class RelatedFeaturedTips extends BlockBase {

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
        ->condition('sticky', 1)
        ->condition('status', 1)
        ->sort('created', 'DESC')
        ->execute();
      $nodes = Node::loadMultiple($nids);
      $tips = [];
      $views = [];
      $rate = [];
      $cate = [];
      $desc = [];
      if ($nodes) {
        foreach ($nodes as $nid => $node) {
          $tips[$nid] = $node;
          $term = Term::load($node->get('field_category')->target_id);
          $vote_widget = $vote_widget_service->buildRateVotingWidget($node->id(), $node->getEntityTypeId(), $node->bundle());
          $rate[$nid] = $vote_widget['votingapi_links'];
          $views[$nid] = counterNode($node);
          if ($term) {
            $cate[$nid]['term'] = $term->getName();
            $cate[$nid]['term_uri'] = taxonomy_term_uri($term);
          }
          $desc[$nid] = shortContent($node->get('body')->value, 150);
        }
      }

      return [
        '#theme' => ['iot_tips_featured_related'],
        '#nodes' => $tips,
        '#rate' => $rate,
        '#views' => $views,
        '#term' => $cate,
        '#desc' => $desc,
      ];
    }
  }

}
