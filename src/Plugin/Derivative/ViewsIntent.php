<?php

namespace Drupal\chatbot_api\Plugin\Derivative;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Chatbot\Intent plugin definitions for all Views chatbot_intent displays.
 *
 * @see \Drupal\chatbot_api\Plugin\Chatbot\Intent\ViewsIntent
 */
class ViewsIntent implements ContainerDeriverInterface {

  /**
   * List of derivative definitions.
   *
   * @var array
   */
  protected $derivatives = [];

  /**
   * The base plugin ID.
   *
   * @var string
   */
  protected $basePluginId;

  /**
   * The view storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $viewStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $base_plugin_id,
      $container->get('entity.manager')->getStorage('view')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($base_plugin_id, EntityStorageInterface $view_storage) {
    $this->basePluginId = $base_plugin_id;
    $this->viewStorage = $view_storage;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinition($derivative_id, $base_plugin_definition) {
    if (!empty($this->derivatives) && !empty($this->derivatives[$derivative_id])) {
      return $this->derivatives[$derivative_id];
    }
    $this->getDerivativeDefinitions($base_plugin_definition);
    return $this->derivatives[$derivative_id];
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Check all Views for block displays.
    foreach ($this->viewStorage->loadMultiple() as $view) {
      // Do not return results for disabled views.
      if (!$view->status()) {
        continue;
      }
      $executable = $view->getExecutable();
      $executable->initDisplay();
      foreach ($executable->displayHandlers as $display) {
        /** @var \Drupal\views\Plugin\views\display\DisplayPluginInterface $display */
        // Add a block plugin definition for each block display.
        if (isset($display) && $display->getPluginId() == 'chatbot_intent') {
          if ($intent_name = $display->getOption('intent_name')) {
            $this->derivatives[$intent_name] = [
              'label' => $view->label(),
              'view_name' => $view->id(),
              'display_name' => $display->display['id'],
            ];

            $this->derivatives[$intent_name] += $base_plugin_definition;
          }
        }
      }
    }
    return $this->derivatives;
  }

}
