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
 * Provides a 'Facts' Block.
 * @Block(
 *   id = "headerquestion",
 *   admin_label = @Translation("Header Question"),
 *   category = @Translation("Header Question"),
 * )
 */
class HeaderQuestion extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    $node = \Drupal::request()->get('node');
    $set = Node::load($node->get('field_set')->target_id);
    $collection = Node::load($set->get('field_collection')->target_id);
    $collection_date = date('d M Y', strtotime($collection->get('field_publication_date')->value));
    $vote_widget_service = \Drupal::service('rate.entity.vote_widget');
    $vote_widget = $vote_widget_service->buildRateVotingWidget($collection->id(), $collection->getEntityTypeId(), $collection->bundle());
    $collection_service = \Drupal::service('iot_quiz.collectionservice');
    $statistic = $collection_service->getCollectionStatistic($collection);
    $views = $statistic['views'];
    $take_test = $statistic['take_test'];
    $star_rate = $vote_widget['votingapi_links'];
    return [
      '#theme' => ['iot_header_question'],
      '#node' => $node,
      '#collection' => $collection,
      '#views' => $views,
      '#take_test' => $take_test,
      '#star_rate' => $star_rate,
      '#collection_date' => $collection_date,
    ];
  }

}
