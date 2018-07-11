<?php

namespace Drupal\iot_ielts\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "markdown" plugin.
 * @CKEditorPlugin(
 *   id = "markdown",
 *   label = @Translation("Markdown")
 * )
 */
class Markdown extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getLibraryPath() {
    $path = '/libraries/markdown';
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      $path = libraries_get_path('markdown');
    }

    return $path;
  }

  public function getButtons() {
    $path = $this->getLibraryPath();
    return [
      'Markdown' => [
        'label' => t('Markdown'),
        'image' => $path . '/icons/markdown.png',
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
