<?php
/**
 * Created by PhpStorm.
 * User: cadic
 * Date: 12/18/2017
 * Time: 9:46 PM
 */

namespace Drupal\iot_ielts\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Menu obile' Block.
 * @Block(
 *   id = "menu_mobile",
 *   admin_label = @Translation("Menu Mobile"),
 *   category = @Translation("Menu Mobile"),
 * )
 */
class MenuMobile extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    $user = \Drupal::currentUser();
    if ($user->id() <= 0) {
      $name = FALSE;
    }
    else {
      $name = $user->getAccountName();
    }
    return [
      '#theme' => ['iot_menu_mobile'],
      '#menus' => $this->_get_menu_items('main'),
      '#user' => $name,
    ];
  }

  /**
   * @param $menu_name
   *
   * @return array
   */
  public function _get_menu($menu_name) {

    $menu_tree = \Drupal::menuTree();
    // Build the typical default set of menu tree parameters.
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
    // Load the tree based on this set of parameters.
    $tree = $menu_tree->load($menu_name, $parameters);
    // Transform the tree using the manipulators you want.
    $manipulators = [// Only show links that are accessible for the current user.
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      // Use the default sorting of menu links.
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree->transform($tree, $manipulators);
    // Finally, build a renderable array from the transformed tree.
    $menu = $menu_tree->build($tree);

    return $menu;
  }

  /**
   * @param $menu_name
   *
   * @return array
   */
  public function _get_menu_items($menu_name) {

    $menu_data = $this->_get_menu($menu_name);
    $i = 1;
    foreach ($menu_data['#items'] as $item) {
      if (isset($item['url']) && $item['url']->getRouteName() == '') {
        $menu[$i]['links'] = [
          'title' => $item['title'],
          'link' => $item['url']->getRouteName(),
        ];
      }
      else {
        $menu[$i]['links'] = [
          'title' => $item['title'],
          'link' => $item['url']->getInternalPath(),
        ];
      }
      $menu[$i]['belows'] = $this->_get_child($item['below']);
      $i++;
    }
    return $menu;
  }

  /**
   * @param $items
   *
   * @return array
   */
  public function _get_child($items) {
    $belows = [];

    $i = 1;
    foreach ($items as $below) {
      $belowsChild = [];
      if ($below['below']) {
        $t = 1;
        foreach ($below['below'] as $child) {
          if (isset($below['url']) && $below['url']->getRouteName() == '') {
            $belowsChild[$t]['child'] = [
              'title' => $child['title'],
              'link' => $child['url']->getRouteName(),
            ];
          }
          else {
            $belowsChild[$t]['child'] = [
              'title' => $child['title'],
              'link' => $child['url']->getInternalPath(),
            ];
          }
          $t++;
        }
      }
      if (isset($below['url']) && $below['url']->getRouteName() == '') {
        $belows[$i]['belows'] = [
          'title' => $below['title'],
          'link' => $below['url']->getRouteName(),
        ];
      }
      else {
        $belows[$i]['belows'] = [
          'title' => $below['title'],
          'link' => $below['url']->getInternalPath(),
        ];
      }
      if ($belowsChild) {
        $belows[$i]['belowChild'] = $belowsChild;
      }

      $i++;
    }
    return $belows;
  }

}
