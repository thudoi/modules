<?php

namespace Drupal\iot_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Masterminds\HTML5\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\taxonomy\Entity\Term;

/**
 * Class DashboardController.
 */
class DashboardController extends ControllerBase {

  /**
   * Dashboard.
   *
   * @return string
   *   Return Hello string.
   */
  public function profile() {
    $user = \Drupal::currentUser();
    if ($user->id() <= 0) {
      $response = new RedirectResponse('/account/login?destination=/account/profile');
      $response->send();
    }
    $account = User::load($user->id());
    $data = [];
    $data['user'] = $account;
    $data['dob'] = date('d/m/Y', strtotime($account->get('field_dob')->value));

    return ['#theme' => 'iot_user_profile', '#user' => $data,];
  }

  public function profileCallback() {
    $request = Request::createFromGlobals();
    $service = \Drupal::service('user.auth');
    $user = \Drupal::currentUser();
    $account = User::load($user->id());
    if ($user->id() <= 0) {
      $response = new RedirectResponse('/account/login');
      $response->send();
    }
    if (!isset($_POST)) {
      $response = new RedirectResponse('/account/login');
      $response->send();
    }
    if (isset($_POST['firstname']) && !empty($_POST['firstname'])) {
      $account->set('field_first_name', $_POST['firstname']);
    }
    if (isset($_POST['lastname']) && !empty($_POST['lastname'])) {
      $account->set('field_last_name', $_POST['lastname']);
    }

    $account->set('field_gender', $_POST['gender']);

    if (isset($_POST['dob']) && !empty($_POST['dob'])) {
      $dob = explode('/', $_POST['dob']);
      $date = $dob[2] . '-' . $dob[1] . '-' . $dob[0];
      // var_dump($date);die;
      $account->set('field_dob', $date);
    }
    if (isset($_POST['facebook_id']) && !empty($_POST['facebook_id'])) {
      $account->set('field_facebook_id', $_POST['facebook_id']);
    }
    if (isset($_POST['wechat_id']) && !empty($_POST['wechat_id'])) {
      $account->set('field_wechat_id', $_POST['wechat_id']);
    }
    if (isset($_POST['current_pass']) && !empty($_POST['current_pass'])) {
      if ($uid = $service->authenticate($account->getAccountName(), $_POST['current_pass'])) {
        /** @var \Drupal\user\UserInterface $user */
      }
      else {
        drupal_set_message('Current password does not match.', 'error');
        $response = new RedirectResponse('/account/profile');
        $response->send();
      }
    }
    if (isset($_POST['password']) && !empty($_POST['password'])) {
      if ($_POST['password'] == $_POST['confirm_pass']) {
        $account->setPassword($_POST['password']);
      }
      else {
        drupal_set_message('The password does not match.', 'error');
        $response = new RedirectResponse('/account/profile');
        $response->send();
      }
    }
    $account->set('field_subscribe', $_POST['subscriber']);
    if (isset($_FILES['picture']) && !empty($_FILES['picture']['name'])) {
      $data = file_get_contents($_FILES["picture"]["tmp_name"]);
      $file = file_save_data($data, "public://pictures/" . date('Y-m') . "/" . $_FILES["picture"]["name"], FILE_EXISTS_REPLACE);
      if ($file) {
        $account->set('user_picture', [
          'target_id' => $file->id(),
          'alt' => $_FILES["picture"]["name"],
          'title' => $_FILES["picture"]["name"],
        ]);
      }

    }
    $account->save();
    drupal_set_message('Update your profile success.');
    $response = new RedirectResponse('/account/profile');
    $response->send();
  }

  /**
   * Dashboard.
   *
   * @return string
   *   Return Hello string.
   */
  public function history() {
    $service = \Drupal::service('iot_quiz.userservice');
    $user = \Drupal::currentUser();
    if ($user->id() <= 0) {
      $response = new RedirectResponse('/account/login?destination=/account/history');
      $response->send();
    }
    $col = '';
    $st = 'All';
    $status = 2;
    if (isset($_GET['collection']) && !empty($_GET['collection'])) {
      $col = $_GET['collection'];
    }
    if (isset($_GET['status']) && !empty($_GET['status'])) {
      $st = $_GET['status'];
    }
    if ($st == 'true') {
      $status = 1;
    }
    if ($st == 'false') {
      $status = 0;
    }
    $header = [// We make it sortable by name.
      [
        'data' => $this->t('Date taken'),
        'field' => 'created',
        'sort' => 'desc',
      ],
      ['data' => $this->t('Type')],
      ['data' => $this->t('Series')],
      ['data' => $this->t('Test name'), 'field' => 'title', 'sort' => 'asc'],
      ['data' => $this->t('Score')],
      ['data' => $this->t('Accuracy')],
      ['data' => $this->t('Tme spent')],
      ['data' => $this->t('Progress')],
      ['data' => $this->t('Action')],

    ];

    $db = \Drupal::database();
    $query = $db->select('node_field_data', 'n');
    $query->fields('n', ['nid']);
    $query->leftJoin('node__field_score', 'sc', 'n.nid=sc.entity_id');
    $query->leftJoin('node__field_score_quiz', 'sq', 'n.nid=sq.entity_id');
    $query->condition('n.type', 'score');
    $query->condition('n.uid', $user->id());
    if (isset($_GET['collection']) && $_GET['collection'] != 'All') {
      $query->condition('sq.field_score_quiz_target_id', $this->getQuizByCollection($_GET['collection']), 'IN');
    }
    $or = db_or();

    if ($status == 0) {
      $query->condition('n.status', $status, '=');
    }
    elseif ($status == 1) {
      $query->condition('n.status', $status, '=');
      $query->condition('sc.field_score_value', '0/%', 'NOT LIKE');
    }
    else {
      $or->condition('sc.field_score_value', '0/%', 'NOT LIKE');
      $or->condition('n.status', 0, '=');
      $query->condition($or);
    }


    // The actual action of sorting the rows is here.
    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($header);
    // Limit the rows to 20 for each page.
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(10);
    $result = $pager->execute();

    // Populate the rows.
    $rows = [];
    $timeTotal = 0;
    foreach ($result as $row) {
      $node = Node::load($row->nid);
      $quiz = Node::load($node->get('field_score_quiz')->target_id);
      $scoreCorrect = $node->get('field_score')->value;// return 9/40
      $scoreArr = explode('/', $scoreCorrect);
      $durationSecond = intval($quiz->get('field_duration')->value) * 60;
      $time = $node->get('field_time')->value;// return 39:54
      $timeArr = explode(':', $time);
      if ($durationSecond > 0 && $timeArr[0] > 0 && $timeArr[1] > 0) {
        $left = $durationSecond - (($timeArr[0] * 60) + $timeArr[1]);
      }
      else {
        $left = 0;
      }

      $timeSpend = gmdate("i:s", $left);
      $collection = $this->getCollectionByQuiz($quiz);
      $alias_node = \Drupal::service('path.alias_manager')
        ->getAliasByPath('/node/' . $node->Id());
      $alias_quiz = \Drupal::service('path.alias_manager')
        ->getAliasByPath('/node/' . $quiz->Id());
      if ($node->get('status')->value == 1) {
        $progress = '<div class="progress"><div data="100%" class="progress-state" style="width: 100%"></div></div>';
      }
      else {
        $progress = '<div class="progress"><div data="' . intval($scoreArr[0] * 100 / $scoreArr[1]) . '%" class="progress-state" style="width: ' . $service->formatNumber($scoreArr[0] * 100 / $scoreArr[1], TRUE) . '%"></div></div>';
      }
      $progress = ['#markup' => $progress,];
      $action = ['#markup' => $node->get('status')->value == 1 ? '<a href="' . $alias_node . '" class="btn-table"><span></span> Review</a>' : '<a href="' . $alias_quiz . '" class="btn-table"><span class="icon-resume"></span> Resume </a>',];
      $accu = '-';
      $time = '-';
      $score = '-';
      if ($node->get('status')->value == 1) {
        $accu = $scoreArr[0] > 0 ? intval($service->formatNumber($scoreArr[0] * 100 / $scoreArr[1], TRUE)) . '%' : '0%';
        $time = $timeSpend;
        $score = $service->getScore($node);
      }

      $rows[] = [
        'data' => [
          'created' => date('d/m/Y', $node->get('created')->value),
          'type' => $collection['category'],
          'series' => $collection['series'],
          'title' => $node->get('title')->value,
          'score' => $score,
          'accuracy' => $accu,
          'time' => $time,
          'progress' => render($progress),
          'action' => render($action),
        ],
      ];
    }

    // The table description.
    $build = ['#markup' => t('<h2 class="page-caption">Tests History</h2><div class="table-history">')];

    $filter = [
      '#theme' => 'iot_user_history_filter',
      '#collections' => $this->getAllCollections(),
      '#st' => $st,
      '#col' => $col,
    ];
    $build['filter'] = ['#markup' => render($filter),];

    // Generate the table.
    $build['my_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    // Finally add the pager.
    $build['pager'] = ['#type' => 'pager'];
    $build['close'] = ['#markup' => t('</div>')];

    return $build;
  }

  /**
   * @param $quiz
   *
   * @return array
   */
  public function getCollectionByQuiz($quiz) {
    $set = Node::load($quiz->get('field_set')->target_id);
    $collection = Node::load($set->get('field_collection')->target_id);
    $category = Term::load($collection->get('field_category')->target_id);
    $series = Term::load($collection->get('field_series')->target_id);
    return [
      'category' => $category->getName(),
      'series' => $collection->getTitle(),
    ];
  }

  /*
   * Implement get All Collection
   */
  public function getAllCollections() {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'collection')
      ->condition('status', 1)
      ->sort('field_collection_order', 'ASC')
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $secs = [];
    foreach ($nodes as $sec) {
      $secs[$sec->id()] = $sec->getTitle();
    }
    return $secs;
  }

  /**
   * Get all quiz id by collection
   */
  public function getQuizByCollection($col) {
    $quiz = [];
    $sets = \Drupal::entityQuery('node')
      ->condition('type', 'set')
      ->condition('status', 1)
      ->condition('field_collection', $col)
      ->execute();
    $sets = \Drupal\node\Entity\Node::loadMultiple($sets);
    foreach ($sets as $set) {
      $qs = \Drupal::entityQuery('node')
        ->condition('type', 'quiz')
        ->condition('status', 1)
        ->condition('field_set', $set->id())
        ->execute();
      $qzs = \Drupal\node\Entity\Node::loadMultiple($qs);
      foreach ($qzs as $quz) {
        $quiz[] = $quz->id();
      }

    }
    return $quiz;
  }

}
