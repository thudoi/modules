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
 * Provides a 'ContactHome' Block.
 * @Block(
 *   id = "contactmessage",
 *   admin_label = @Translation("Contact Message"),
 *   category = @Translation("Contact Message"),
 * )
 */
class ContactMessage extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {

    return [
      '#theme' => ['iot_contact_message'],
      '#attached' => ['library' => ['iot_ielts/contact_form',],],
    ];
  }

}
