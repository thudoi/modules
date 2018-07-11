<?php

namespace Drupal\iot_quiz;

use Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;

class ScoreService {

  public function InitScore(Node $quiz) {
    $current_user = \Drupal::currentUser();
    if ($current_user->id()) {
      $sids = \Drupal::entityQuery('node')
        ->condition('type', 'score')
        ->condition('uid', $current_user->id())
        ->condition('status', 0)
        ->condition('field_score_quiz', $quiz->id())
        ->execute();
      if ($sids) {
        $id = reset($sids);
      }
      else {
        $node = Node::create([
          'type' => 'score',
          'title' => $quiz->get('title')->value,
          'status' => 0,
        ]);
        $node->set('field_score_quiz', $quiz->id());
        $node->save();
        $id = $node->id();
      }
    }
    else {
      $id = 0;
    }
    return $id;
  }

  public function check_user_in_top($quiz_id) {
    $current_user = \Drupal::currentUser();
    $connection = Database::getConnection();
    $query = $connection->select('top_score', 'ts')
      ->fields('ts', ['tid'])
      ->condition('ts.uid', $current_user->id())
      ->condition('ts.qid', $quiz_id);
    $result = $query->execute()->fetchfield();
    return $result;
  }

  public function insert_user_to_top($quiz_id, $data) {
    $current_user = \Drupal::currentUser();
    $database = \Drupal::database();
    $fields = [
      'uid' => $current_user->id(),
      'qid' => $quiz_id,
      'score' => $data['score'],
      'time' => $data['time'],
      'total_question' => $data['total_question'],
    ];
    $database->insert('top_score')->fields($fields)->execute();
  }

  public function get_top_score($quiz_id) {
    $connection = Database::getConnection();
    $query = $connection->select('top_score', 'ts')
      ->fields('ts', [
        'uid',
        'score',
        'time',
      ])
      ->condition('ts.qid', $quiz_id)
      ->orderBy('ts.score', 'DESC')
      ->range(0, 10);
    $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    return $result;
  }
}
