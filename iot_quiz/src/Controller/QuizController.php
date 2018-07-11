<?php
/**
 * Created by PhpStorm.
 * User: mrcad
 * Date: 11/23/2017
 * Time: 10:23 AM
 */

namespace Drupal\iot_quiz\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;

class QuizController extends ControllerBase {

  public function ShowSolution(NodeInterface $node) {
    $type = $node->get('field_quiz_type')->value;
    $title = $node->get('field_title_ui')->value;
    $service_question = \Drupal::service('iot_quiz.questionservice');
    $return = [];
    switch ($type) {
      case 'listening':
        $service = \Drupal::service('iot_quiz.quizservice');

        $content = $service->get_question($node, 'listening', 'solution');

        $data = [];
        $set = Node::load($node->get('field_set')->target_id);
        $collection = Node::load($set->get('field_collection')->target_id);
        $variables['node'] = $node;
        foreach ($content['answers']['answers'] as $key => $answer) {
          switch ($answer['type']) {
            case 'blank':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['prefix'],
              ];
              break;
            case 'radio':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'drop_down':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'checkbox':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => implode(',', $answer['answer']),
              ];
              break;
          }
        }
        $collection_theme = [
          '#theme' => 'iot_collection_header',
          '#collection' => $collection,
          '#type' => 'listening',
          '#title' => $title,
        ];
        $collection_header = render($collection_theme);
        $score_table_theme = [
          '#theme' => 'iot_result_question',
          '#result' => $data,
        ];
        $leader = $service_question->getLeaderBoard($node->id());
        $score_table = render($score_table_theme);
        $return = [
          '#theme' => ['iot_solution_listening'],
          '#node' => $node,
          '#result' => $data,
          '#secs' => $content['secs'],
          '#audio' => $content['audio'],
          '#collection_header' => $collection_header,
          '#answers' => $content['answers'],
          '#score_table' => $score_table,
          '#leader_board' => $leader,
          '#attached' => [
            'library' => [
              'iot_quiz/iot_result',
              'iot_quiz/iot_frontend',
            ],
          ],
        ];
        break;
      case 'reading':
        $service = \Drupal::service('iot_quiz.quizservice');
        $content = $service->get_question($node, 'reading', 'solution');
        $set = Node::load($node->get('field_set')->target_id);
        $collection = Node::load($set->get('field_collection')->target_id);
        $data = [];
        foreach ($content['answers']['answers'] as $key => $answer) {
          switch ($answer['type']) {
            case 'blank':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['prefix'],
              ];
              break;
            case 'radio':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'drop_down':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'checkbox':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => implode(',', $answer['answer']),
              ];
              break;
          }
        }
        $collection_theme = [
          '#theme' => 'iot_collection_header',
          '#collection' => $collection,
          '#type' => 'reading',
          '#title' => $title,
        ];
        $collection_header = render($collection_theme);
        $score_table_theme = [
          '#theme' => 'iot_result_question',
          '#result' => $data,
          '#class' => 'green',
        ];
        $score_table = render($score_table_theme);
        $return = [
          '#theme' => ['iot_solution_reading'],
          '#node' => $node,
          '#result' => $data,
          '#collection_header' => $collection_header,
          '#secs' => $content['secs'],
          '#answers' => $content['answers'],
          '#score_table' => $score_table,
          '#leader_board' => $service_question->getLeaderBoard($node->id()),
          '#attached' => ['library' => ['iot_quiz/iot_result',],],
        ];
        break;
    }
    return $return;
  }

  public function printDownload(NodeInterface $node) {
    $type = $node->get('field_quiz_type')->value;
    $title = $node->get('field_title_ui')->value;
    $service_question = \Drupal::service('iot_quiz.questionservice');
    $return = [];
    switch ($type) {
      case 'listening':
        $service = \Drupal::service('iot_quiz.quizservice');

        $content = $service->get_question($node, 'listening', 'solution');

        $data = [];
        $set = Node::load($node->get('field_set')->target_id);
        $collection = Node::load($set->get('field_collection')->target_id);
        $variables['node'] = $node;
        foreach ($content['answers']['answers'] as $key => $answer) {
          switch ($answer['type']) {
            case 'blank':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['prefix'],
              ];
              break;
            case 'radio':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'drop_down':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'checkbox':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => implode(',', $answer['answer']),
              ];
              break;
          }
        }
        $collection_theme = [
          '#theme' => 'iot_collection_header',
          '#collection' => $collection,
          '#type' => 'listening',
          '#title' => $title,
        ];
        $collection_header = render($collection_theme);
        $score_table_theme = [
          '#theme' => 'iot_result_question',
          '#result' => $data,
        ];

        $leader = $service_question->getLeaderBoard($node->id());
        $score_table = render($score_table_theme);
        $return = [
          '#theme' => ['iot_print_listening'],
          '#node' => $node,
          '#result' => $data,
          '#secs' => $content['secs'],
          '#audio' => $content['audio'],
          '#collection_header' => $collection_header,
          '#answers' => $content['answers'],
          '#score_table' => $score_table,
          '#leader_board' => $leader,
          '#attached' => ['library' => ['iot_quiz/iot_printpdf',],],
        ];
        break;
      case 'reading':
        $service = \Drupal::service('iot_quiz.quizservice');
        $content = $service->get_question($node, 'reading', 'solution');
        $set = Node::load($node->get('field_set')->target_id);
        $collection = Node::load($set->get('field_collection')->target_id);
        $data = [];
        foreach ($content['answers']['answers'] as $key => $answer) {
          switch ($answer['type']) {
            case 'blank':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['prefix'],
              ];
              break;
            case 'radio':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'drop_down':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => $answer['answer'],
              ];
              break;
            case 'checkbox':
              $data[$key] = [
                'num' => $answer['number'],
                'correct_ans' => implode(',', $answer['answer']),
              ];
              break;
          }
        }
        $collection_theme = [
          '#theme' => 'iot_collection_header',
          '#collection' => $collection,
          '#type' => 'reading',
          '#title' => $title,
        ];
        $collection_header = render($collection_theme);
        $score_table_theme = [
          '#theme' => 'iot_result_question',
          '#result' => $data,
          '#class' => 'green',
        ];
        $score_table = render($score_table_theme);
        $return = [
          '#theme' => ['iot_solution_reading'],
          '#node' => $node,
          '#result' => $data,
          '#collection_header' => $collection_header,
          '#secs' => $content['secs'],
          '#answers' => $content['answers'],
          '#score_table' => $score_table,
          '#leader_board' => $service_question->getLeaderBoard($node->id()),
          '#attached' => ['library' => ['iot_quiz/iot_printpdf',],],
        ];
        break;
    }
    return $return;
  }

  /**
   * Implement explain question
   *
   * @param $node
   *
   * @return mixed
   */
  public function ExplainQuestion($node) {
    $service = \Drupal::service('iot_quiz.quizservice');
    $content = $service->explain_reading_mapping($node);
    return ['#theme' => ['iot_explain'], '#content' => $content,];
  }
}
