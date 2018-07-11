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
 *   id = "footerbar",
 *   admin_label = @Translation("Footer Bar"),
 *   category = @Translation("Footer Bar"),
 * )
 */
class FooterBar extends BlockBase {

  /**
   * {@inheritdoc}
   * @return array
   */
  public function build() {
    return ['#theme' => ['iot_footer_bar'],];
  }

}
