<?php

namespace Drupal\iot_ielts\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "token_insert" plugin.
 * @CKEditorPlugin(
 *   id = "token_insert",
 *   label = @Translation("Token Insert")
 * )
 */
class TokenInsert extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getLibraryPath() {
    $path = '/libraries/token_insert';
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      $path = libraries_get_path('token_insert');
    }

    return $path;
  }

  public function getButtons() {
    $path = $this->getLibraryPath();
    return [
      'TokenInsert' => [
        'label' => t('Token Insert'),
        'image' => $path . '/icons/token_insert.png',
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
