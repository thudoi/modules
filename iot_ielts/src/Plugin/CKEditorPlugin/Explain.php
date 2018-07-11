<?php

namespace Drupal\iot_ielts\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "markdown" plugin.
 * @CKEditorPlugin(
 *   id = "explain",
 *   label = @Translation("Explain")
 * )
 */
class Explain extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getLibraryPath() {
    $path = '/libraries/explain';
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      $path = libraries_get_path('explain');
    }

    return $path;
  }

  public function getButtons() {
    $path = $this->getLibraryPath();
    return [
      'Explain' => [
        'label' => t('Explain'),
        'image' => $path . '/icons/explain.png',
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
