<?php
/**
 * Created by PhpStorm.
 * User: mrcad
 * Date: 11/1/2017
 * Time: 5:59 PM
 */

namespace Drupal\iot_quiz\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;

class CollectionController extends ControllerBase {

  /**
   * @return array
   * Get quiz
   */
  public function Collection(NodeInterface $node) {
    //    $node = $this->getNode();
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'quiz')
      ->condition('field_collection', $node->id())
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $sets = [];
    foreach ($nodes as $set) {
      $sets[] = $set;
    }

    return [
      '#theme' => ['iot_manage_collection'],
      '#node' => $node,
      '#sets' => $sets,
    ];
  }

  /**
   * @return array
   * Get collections
   */
  public function Collections() {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'collection')
      ->condition('status', 1)
      ->sort('field_collection_order', 'ASC')
      ->sort('created', 'DESC')
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $collections = [];
    $terms = $this->get_category();
    $category = [];
    //get collection
    foreach ($nodes as $collection) {
      $vote_widget_service = \Drupal::service('rate.entity.vote_widget');
      $vote_widget = $vote_widget_service->buildRateVotingWidget($collection->id(), $collection->getEntityTypeId(), $collection->bundle());
      $collection_data['star_rate'] = $vote_widget['votingapi_links'];
      $collection_data['collection'] = $collection;
      $collections[] = $collection_data;
    }
    //get feature
    $features = [];
    $i = 1;
    foreach ($nodes as $fe) {
      if ($fe->isSticky() == 1) {
        $vote_widget_service = \Drupal::service('rate.entity.vote_widget');
        $vote_widget = $vote_widget_service->buildRateVotingWidget($fe->id(), $fe->getEntityTypeId(), $fe->bundle());
        $collection_data['star_rate'] = $vote_widget['votingapi_links'];
        $collection_data['collection'] = $fe;
        $features[] = $collection_data;
        if ($i == 4) {
          break;
        }
        $i++;
      }
    }
    //get collection by cate
    foreach ($terms as $term) {
      $cate = [];
      foreach ($nodes as $n) {
        if ($term->id() == $n->get('field_category')->target_id) {
          $vote_widget_service = \Drupal::service('rate.entity.vote_widget');
          $vote_widget = $vote_widget_service->buildRateVotingWidget($n->id(), $n->getEntityTypeId(), $n->bundle());
          $collection_data['star_rate'] = $vote_widget['votingapi_links'];
          $collection_data['collection'] = $n;
          $cate[] = $collection_data;
        }
      }
      $category[$term->id()] = $cate;
    }

    return [
      '#theme' => ['iot_collections'],
      '#collections' => $collections,
      '#terms' => $terms,
      '#category' => $category,
      '#features' => $features,
    ];
  }

  public function Sections(NodeInterface $node) {
    if (isset($_GET['field'])) {
      //      $node = $this->getNode();
      $field = $_GET['field'];
      $secs_ids = $node->get($field)->getValue();
      $secs = [];
      foreach ($secs_ids as $secs_id) {
        $sec = Node::load($secs_id['target_id']);
        $secs[] = $sec;
      }
      return [
        '#theme' => ['iot_manage_sections'],
        '#node' => $node,
        '#secs' => $secs,
        '#type' => $field,
      ];
    }
    else {
      return [
        '#type' => 'markup',
        '#markup' => $this->t('invalid field name'),
      ];
    }
  }

  /**
   * @return \Drupal\Core\Entity\EntityInterface|null|static
   */
  //  public function getNode() {
  //    $nid = \Drupal::request()->get('nid');
  //    $node = Node::load($nid);
  //    return $node;
  //  }

  public function get_category() {
    $vocabulary_name = 'quiz_category'; //name of your vocabulary
    $query = \Drupal::entityQuery('taxonomy_term');
    $query->condition('vid', $vocabulary_name);
    $query->sort('weight');
    $tids = $query->execute();
    $terms = Term::loadMultiple($tids);
    return $terms;
  }

}
