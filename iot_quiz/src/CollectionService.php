<?php
/**
 * Created by PhpStorm.
 * User: mrcad
 * Date: 12/4/2017
 * Time: 5:16 PM
 */

namespace Drupal\iot_quiz;

use Drupal\Console\Bootstrap\Drupal;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\Core\Database;

class CollectionService {

  /**
   * Get collection detail
   */
  public function getCollectionDetail($collection) {
    $sids = \Drupal::entityQuery('node')
      ->condition('type', 'set')
      ->condition('status', 1)
      ->condition('field_collection', $collection->id())
      ->execute();
    $sets = Node::loadMultiple($sids);
    $arr = [];
    $child = [];
    foreach ($sets as $sid => $set) {
      $arr[] = $set;
      $quiz = [];
      $qids = \Drupal::entityQuery('node')
        ->condition('type', 'quiz')
        ->condition('status', 1)
        ->condition('field_set', $set->id())
        ->execute();
      $quizs = Node::loadMultiple($qids);
      foreach ($quizs as $qid => $q) {
        $current_user = \Drupal::currentUser();
        $data = ['status' => 0,];
        if ($current_user->id()) {
          $ic_ids = \Drupal::entityQuery('node')
            ->condition('type', 'score')
            ->condition('uid', $current_user->id())
            ->condition('field_score_quiz', $q->id())
            ->execute();
          $nodes = Node::loadMultiple($ic_ids);
          foreach ($nodes as $node) {
            if ($node->isPublished()) {
              $data = ['status' => 2,];
            }
            else {
              $dat = $node->get('body')->value;
              $serial = unserialize($dat);
              if ($serial) {
                $ans = array_column($serial, 'ans');
                $ans = count(array_filter($ans));
                $un_ans = 40 - $ans;
                $percent = floor(($ans / 40) * 100);
                $service = \Drupal::service('iot_quiz.questionservice');
                $round = $service->mappArrayPercent($percent);
                if ($ans > 0) {
                  $data = [
                    'status' => 1,
                    'ans' => $ans,
                    'u_ans' => $un_ans,
                    'round' => $round,
                    'percent' => $percent,
                  ];
                }
              }
              break;
            }
          }
        }
        $quiz[] = ['node' => $q, 'data' => $data];
      }
      $child[$set->id()] = $quiz;
    }
    return [
      '#theme' => 'iot_quiz_collection_detail',
      '#sets' => $arr,
      '#quizs' => $child,
      '#otherCollection' => $this->getOtherCollection($collection),
    ];
  }

  /**
   *  Get statistic
   */
  public function getCollectionStatistic($collection) {
    $sids = \Drupal::entityQuery('node')
      ->condition('type', 'set')
      ->condition('status', 1)
      ->condition('field_collection', $collection->id())
      ->execute();
    $sets = Node::loadMultiple($sids);
    $qids = [];
    foreach ($sets as $sid => $set) {
      $ids = \Drupal::entityQuery('node')
        ->condition('type', 'quiz')
        ->condition('status', 1)
        ->condition('field_set', $set->id())
        ->execute();
      foreach ($ids as $id) {
        array_push($qids, $id);
      }
    }
    $connection = \Drupal::database();
    $query = $connection->select('node_counter');
    $query->condition('nid', $qids, 'IN');
    $query->addExpression('sum(totalcount)', 'total');
    $result = $query->execute()->fetchObject();
    $views = $result->total;

    return [
      'views' => $views ? $views : 0,
      'take_test' => $collection->get('field_collection_count') ? $collection->get('field_collection_count')->value : 0,
    ];
  }

  /**
   * @param $collection
   *
   * @return \Drupal\Core\Entity\EntityInterface[]|static[]
   */
  public function getOtherCollection($collection) {
    $data = [];
    $nids1 = \Drupal::entityQuery('node')
      ->condition('type', 'collection')
      ->condition('status', 1)
      ->condition('nid', $collection->id(), '<>')
      ->condition('field_collection_order', $collection->get('field_collection_order')->value, '>')
      ->condition('field_series', $collection->get('field_series')->target_id)
      ->range(0, 2)
      ->execute();
    $collections1 = Node::loadMultiple($nids1);
    $nids2 = \Drupal::entityQuery('node')
      ->condition('type', 'collection')
      ->condition('status', 1)
      ->condition('nid', $collection->id(), '<>')
      ->condition('field_collection_order', $collection->get('field_collection_order')->value, '<')
      ->condition('field_series', $collection->get('field_series')->target_id)
      ->range(0, 2)
      ->execute();
    $collections2 = Node::loadMultiple($nids2);
    if (count($collections1) == 0) {
      $nids2 = \Drupal::entityQuery('node')
        ->condition('type', 'collection')
        ->condition('status', 1)
        ->condition('nid', $collection->id(), '<>')
        ->condition('field_collection_order', $collection->get('field_collection_order')->value, '<')
        ->condition('field_series', $collection->get('field_series')->target_id)
        ->range(0, 4)
        ->execute();
      $collections2 = Node::loadMultiple($nids2);
    }
    if (count($collections1) == 1) {
      $nids2 = \Drupal::entityQuery('node')
        ->condition('type', 'collection')
        ->condition('status', 1)
        ->condition('nid', $collection->id(), '<>')
        ->condition('field_collection_order', $collection->get('field_collection_order')->value, '<')
        ->condition('field_series', $collection->get('field_series')->target_id)
        ->range(0, 3)
        ->execute();
      $collections2 = Node::loadMultiple($nids2);
    }
    if (count($collections2) == 0) {
      $nids1 = \Drupal::entityQuery('node')
        ->condition('type', 'collection')
        ->condition('status', 1)
        ->condition('nid', $collection->id(), '<>')
        ->condition('field_collection_order', $collection->get('field_collection_order')->value, '>')
        ->condition('field_series', $collection->get('field_series')->target_id)
        ->range(0, 4)
        ->execute();
      $collections1 = Node::loadMultiple($nids1);
    }
    if (count($collections2) == 1) {
      $nids1 = \Drupal::entityQuery('node')
        ->condition('type', 'collection')
        ->condition('status', 1)
        ->condition('nid', $collection->id(), '<>')
        ->condition('field_collection_order', $collection->get('field_collection_order')->value, '>')
        ->condition('field_series', $collection->get('field_series')->target_id)
        ->range(0, 3)
        ->execute();
      $collections1 = Node::loadMultiple($nids1);
    }
    foreach ($collections1 as $col1) {
      $data[$col1->id()] = $col1;
    }
    foreach ($collections2 as $col2) {
      $data[$col2->id()] = $col2;
    }
    $result = [];
    foreach ($data as $key => $coll) {
      $vote_widget_service = \Drupal::service('rate.entity.vote_widget');
      $vote_widget = $vote_widget_service->buildRateVotingWidget($coll->id(), $coll->getEntityTypeId(), $coll->bundle());
      $result[$key]['star_rate'] = $vote_widget['votingapi_links'];
      $result[$key]['collection'] = $coll;
    }
    return $result;
  }
}
