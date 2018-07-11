<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 11/22/17
 * Time: 2:43 PM
 */

namespace Drupal\iot_ielts\Breadcrumb;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

class IotIeltsBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $attributes) {
    $parameters = $attributes->getParameters()->all();

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();

    $breadcrumb->addLink(Link::fromTextAndUrl('Home', Url::fromUserInput('/')));
    $current_path = \Drupal::service('path.current')->getPath();
    $path_args = explode('/', $current_path);
    $nid = $route_match->getParameter('nid');
    $node = $route_match->getParameter('node');
    $term = $route_match->getParameter('taxonomy_term');
    $breadcrumb = new Breadcrumb();
    $breadcrumb->addLink(Link::fromTextAndUrl('Home', Url::fromUserInput('/')));
    //var_dump($path_args);
    if (isset($path_args[1]) && $path_args[1] == 'collection') {
      $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
    }
    if (isset($path_args[1]) && $path_args[1] == 'set') {
      $node = Node::load($nid);

      $id = $node->get('field_collection')->getValue();
      $col = Node::load($id[0]['target_id']);
      $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
      $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/collection/' . $id[0]['target_id'] . '/manage')));
    }
    if (isset($path_args[1]) && $path_args[1] == 'quiz') {
      $node = Node::load($nid);
      $setID = $node->get('field_set')->getValue();
      $set = Node::load($setID[0]['target_id']);
      $title = $set->get('field_title_ui')->value;
      $id = $set->get('field_collection')->getValue();
      $col = Node::load($id[0]['target_id']);
      $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
      $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/collection/' . $id[0]['target_id'] . '/manage')));
      $breadcrumb->addLink(Link::fromTextAndUrl($title, Url::fromUserInput('/set/' . $setID[0]['target_id'] . '/manage')));
    }
    if (isset($path_args[1]) && $path_args[1] == 'node' && isset($path_args[3]) && $path_args[3] == 'solution' || isset($path_args[1]) && $path_args[1] == 'node' && isset($path_args[3]) && $path_args[3] == 'result') {
      $node = Node::load($path_args[2]);
      $setID = $node->get('field_set')->getValue();
      $set = Node::load($setID[0]['target_id']);
      $title = $set->get('field_title_ui')->value;
      $id = $set->get('field_collection')->getValue();
      $col = Node::load($id[0]['target_id']);
      $breadcrumb->addLink(Link::fromTextAndUrl('Collection', Url::fromUserInput('/ielts-exam-library')));
      $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/node/' . $col->id())));
      $breadcrumb->addLink(\Drupal\Core\Link::createFromRoute($node->get('field_title_ui')->value, '<none>'));
    }
    if (isset($path_args[1]) && $path_args[1] == 'question') {
      $node = Node::load($path_args[2]);
      $quizId = $node->get('field_quiz')->getValue();
      $quiz = Node::load($quizId[0]['target_id']);
      $setId = $quiz->get('field_set')->getValue();
      $set = Node::load($setId[0]['target_id']);
      $title = $quiz->get('field_title_ui')->value;
      $id = $set->get('field_collection')->getValue();
      $setTitle = $set->get('field_title_ui')->value;
      $col = Node::load($id[0]['target_id']);
      $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
      $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/collection/' . $id[0]['target_id'] . '/manage')));
      $breadcrumb->addLink(Link::fromTextAndUrl($setTitle, Url::fromUserInput('/set/' . $setId[0]['target_id'] . '/manage')));
      $breadcrumb->addLink(Link::fromTextAndUrl($title, Url::fromUserInput('/quiz/' . $quizId[0]['target_id'] . '/manage')));
    }
    if (isset($path_args[1]) && $path_args[1] == 'node' && isset($path_args[2]) && $path_args[2] == 'add' && isset($_GET['destination']) || isset($path_args[1]) && $path_args[1] == 'node' && isset($path_args[3]) && $path_args[3] == 'edit' && isset($_GET['destination'])) {
      $des = $_GET['destination'];
      if ($des == 'user/collections') {
        $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
      }
      $des_arr = explode('/', $des);
      if ($des_arr[1] == 'collection' && $des_arr[3] == 'manage') {
        $col = Node::load($des_arr[2]);
        $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
        $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/collection/' . $des_arr[2] . '/manage')));
      }
      if ($des_arr[1] == 'set' && $des_arr[3] == 'manage') {
        $set = Node::load($des_arr[2]);
        $setTitle = $set->get('field_title_ui')->value;
        $colId = $set->get('field_collection')->getValue();
        $col = Node::load($colId[0]['target_id']);
        $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
        $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/collection/' . $des_arr[2] . '/manage')));
        $breadcrumb->addLink(Link::fromTextAndUrl($setTitle, Url::fromUserInput($des)));
      }
      if ($des_arr[1] == 'quiz' && $des_arr[3] == 'manage') {
        $quiz = Node::load($des_arr[2]);
        $quizTitle = $quiz->get('field_title_ui')->value;
        $setId = $quiz->get('field_set')->getValue();
        $set = Node::load($setId[0]['target_id']);
        $setTitle = $set->get('field_title_ui')->value;
        $colId = $set->get('field_collection')->getValue();
        $col = Node::load($colId[0]['target_id']);
        $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
        $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/collection/' . $des_arr[2] . '/manage')));
        $breadcrumb->addLink(Link::fromTextAndUrl($setTitle, Url::fromUserInput('/set/' . $set->id() . '/manage')));
        $breadcrumb->addLink(Link::fromTextAndUrl($quizTitle, Url::fromUserInput($des)));
      }
      if ($des_arr[1] == 'question' && $des_arr[3] == 'manage') {
        $section = Node::load($des_arr[2]);
        $sectionTitle = $section->get('field_title_ui')->value;
        $quizId = $section->get('field_quiz')->getValue();
        $quiz = Node::load($quizId[0]['target_id']);
        $quizTitle = $quiz->get('field_title_ui')->value;
        $setId = $quiz->get('field_set')->getValue();
        $set = Node::load($setId[0]['target_id']);
        $setTitle = $set->get('field_title_ui')->value;
        $colId = $set->get('field_collection')->getValue();
        $col = Node::load($colId[0]['target_id']);
        $breadcrumb->addLink(Link::fromTextAndUrl('Manage Collection', Url::fromUserInput('/manage/collections')));
        $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/collection/' . $des_arr[2] . '/manage')));
        $breadcrumb->addLink(Link::fromTextAndUrl($setTitle, Url::fromUserInput('/set/' . $set->id() . '/manage')));
        $breadcrumb->addLink(Link::fromTextAndUrl($quizTitle, Url::fromUserInput('/quiz/' . $quiz->id() . '/manage')));
        $breadcrumb->addLink(Link::fromTextAndUrl($sectionTitle, Url::fromUserInput($des)));
      }
    }

    if (isset($node) && $node->getType() == 'quiz') {
      if ($node->get('field_quiz_type') == 'writing' || $node->get('field_quiz_type') == 'speaking') {
        $setID = $node->get('field_set')->getValue();
        $set = Node::load($setID[0]['target_id']);
        $id = $set->get('field_collection')->getValue();
        $col = Node::load($id[0]['target_id']);
        $breadcrumb->addLink(Link::fromTextAndUrl('Collection', Url::fromUserInput('/collections')));
        $breadcrumb->addLink(Link::fromTextAndUrl($col->getTitle(), Url::fromUserInput('/node/' . $col->id())));
        $breadcrumb->addLink(\Drupal\Core\Link::createFromRoute($node->get('field_title_ui')->value, '<none>'));
      }
    }
    if (isset($node) && $node->getType() == 'tips') {
      $term = FALSE;
      $breadcrumb->addLink(Link::fromTextAndUrl('IELTS Tips', Url::fromUserInput('/ielts-tips')));
      if ($node->get('field_category')) {
        $term = Term::load($node->get('field_category')->target_id);
      }
      if ($term) {
        $breadcrumb->addLink(Link::fromTextAndUrl($term->getName(), Url::fromUserInput('/taxonomy/term/' . $term->id())));
      }
      // $breadcrumb->addLink(\Drupal\Core\Link::createFromRoute($node->getTitle(), '<none>'));
    }
    return $breadcrumb;


  }

}
