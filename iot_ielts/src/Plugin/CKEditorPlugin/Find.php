<?php

namespace Drupal\iot_ielts\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "find" plugin.
 * @CKEditorPlugin(
 *   id = "find",
 *   label = @Translation("Find")
 * )
 */
class Find extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getLibraryPath() {
    $path = '/libraries/find';
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      $path = libraries_get_path('find');
    }

    return $path;
  }

  public function getButtons() {
    $path = $this->getLibraryPath();
    return [
      'Find' => [
        'label' => t('Find'),
        'image' => $path . '/icons/find.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return $this->getLibraryPath() . '/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

}
