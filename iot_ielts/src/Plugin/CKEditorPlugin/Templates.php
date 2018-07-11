<?php
/**
 * @file
 * Contains \Drupal\wysiwyg_template\Plugin\CKEditorPlugin\Templates.
 */

namespace Drupal\iot_ielts\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the CKEditor Templates plugin.
 * No buttons are exposed for this plugin, it is only here so it gets properly
 * loaded by the Drupal-specific TemplateSelector plugin.
 * @CKEditorPlugin(
 *   id = "templates",
 *   label = @Translation("Template selector"),
 *   module = "wysiwyg_template"
 * )
 */
class Templates extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface {

  /**
   * Get path to library folder.
   */
  public function getLibraryPath() {
    $path = '/libraries/templates';
    if (\Drupal::moduleHandler()->moduleExists('libraries')) {
      $path = libraries_get_path('templates');
    }

    return $path;
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
  public function getButtons() {
    $path = $this->getLibraryPath();
    return [
      'Templates' => [
        'label' => $this->t('Templates'),
        'image' => $path . '/icons/templates.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    // @todo This location is hard-coded and should be more flexible.
    // @see https://www.drupal.org/node/2693151
    return $this->getLibraryPath() . '/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    return [];
  }

}
