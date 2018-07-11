<?php

namespace Drupal\iot_ielts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class SetController.
 */
class TipsController extends ControllerBase {

  /**
   * Section Manager.
   *
   * @return array
   *   Return template.
   */
  public function import() {
    $connection = \Drupal::database();
    $query = $connection->select('articles', 'a');
    $query->fields('a');
    $result = $query->execute()->fetchAll();
    foreach ($result as $article) {
      $file = FALSE;
      if ($article->Avatar) {
        $image = str_replace('_thumbpad', '', $article->Avatar);
        $data = file_get_contents($image);
        $link = explode('/', $image);
        $num = count($link);
        $file = file_save_data($data, 'public://' . $link[$num - 1], FILE_EXISTS_REPLACE);
      }


      // Create node object with attached file.
      $tips = Node::create(['type' => 'tips']);
      $tips->set('title', $article->Title);
      $tips->set('body', $article->Content);
      if ($file) {
        $tips->set('field_image', $file->id());
      }
      $tips->set('uid', 1);
      if ($article->CategoryId != 'tips') {
        $tips->set('field_category', $this->getCatId($article->CategoryId));
      }
      $tags = [];
      if ($tagIds = $this->getTags($article->ArticleId)) {
        foreach ($tagIds as $t) {
          $tags[] = $this->getTagId($t);
        }
      }
      $tips->set('field_tags', $tags);
      if ($article->Source) {
        $tips->set('field_source', $article->Source);
      }
      if ($article->Standfirst) {
        $tips->set('field_standfirst', $article->Standfirst);
      }
      $tips->set('status', 1);
      $tips->enforceIsNew();
      $tips->save();
    }
    $response = new RedirectResponse('/admin/content');
    $response->send();

  }

  public function getTagId($tagid) {
    $vid = 'tags';
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    $tid = NULL;
    foreach ($terms as $t) {
      $term = Term::load($t->tid);
      if ($term->get('field_tagid')->value == $tagid) {
        $tid = $t->tid;
      }
    }
    return $tid;
  }

  public function getCatId($catid) {
    $vid = 'tips';
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    $tid = NULL;
    foreach ($terms as $t) {
      $term = Term::load($t->tid);
      if ($term->get('field_catid')->value == $catid) {
        $tid = $t->tid;
      }
    }
    return $tid;
  }

  public function getTags($articleId) {
    $connection = \Drupal::database();
    $query = $connection->select('articletags', 'a');
    $query->fields('a');
    $query->condition('ArticleId', $articleId);
    $result = $query->execute()->fetchAll();
    $tagids = [];
    foreach ($result as $r) {
      $tagids[] = $r->TagId;
    }
    return $tagids;
  }

  public function updateCount() {
    $connection = \Drupal::database();
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'tips')
      ->condition('status', 1)
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    foreach ($nodes as $node) {
      $query = $connection->select('node_counter', 'a');
      $query->fields('a');
      $query->condition('nid', $node->id());
      $result = $query->execute()->fetchObject();
      if ($result) {
        $connection->update('node_counter')
          ->condition('nid', $node->id())
          ->fields([
            'totalcount' => counterTips(),
            // FIELD_1 NEW value./ FIELD_3 NEW value.
          ])
          ->execute();
      }
      else {
        $connection->insert('node_counter')->fields([
          'nid',
          'totalcount',
          'daycount',
          'timestamp',
        ])->values([$node->id(), counterTips(), 0, time(),])->execute();
      }

    }
    $response = new RedirectResponse('/admin/content');
    $response->send();
    return [];
  }

}


