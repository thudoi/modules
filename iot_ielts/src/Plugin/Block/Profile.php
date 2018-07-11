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
 * Provides a 'Facts' Block.
 * @Block(
 *   id = "profile",
 *   admin_label = @Translation("Profile"),
 *   category = @Translation("Profile"),
 * )
 */
class Profile extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    return [
      '#markup' => '<a class="use-ajax click-profile-build" data-dialog-type="modal" href="/account/build/profile"></a>',
      '#attached' => ['library' => ['iot_user/iot_account',],],
    ];
  }

}
