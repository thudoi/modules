<?php

namespace Drupal\iot_quiz\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;

/**
 * Provides a resource to get view modes by entity and bundle.
 * @RestResource(
 *   id = "store_result_rest_resource",
 *   label = @Translation("Store result rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/store-result",
 *      "https://www.drupal.org/link-relations/create" = "/api/store-result"
 *   }
 * )
 */
class StoreResultRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new StoreResultRestResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->getParameter('serializer.formats'), $container->get('logger.factory')
      ->get('iot_quiz'), $container->get('current_user'));
  }

  /**
   * Responds to POST requests.
   * Returns a list of bundles for specified entity.
   *
   * @param $data
   *
   * @return \Drupal\rest\ResourceResponse Throws exception expected
   *   Throws exception expected.
   */
  public function post($data) {
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    $node = Node::load((int) $data[3]);
    $ans = array_column($data[2]['answers'], 'ans');
    $ans = count(array_filter($ans));
    $un_ans = $data[0] - $ans;
    $answers = serialize($data[2]['answers']);
    $node->set('body', $answers);
    $status = 0;
    if ($data[5]) {
      $status = 1;
      $node->set('field_score', $data[1] . '/' . $data[0]);
      $node->set('field_time', $data[4]);
      $node->set('field_unanswered_question', $un_ans);
    }
    $node->set('status', $status);
    $node->save();
    $alias = \Drupal::service('path.alias_manager')
      ->getAliasByPath('/node/' . $node->id());
    return new ResourceResponse($alias);
  }

}
