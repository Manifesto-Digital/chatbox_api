<?php

namespace Drupal\chatbot_api\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Chatbot Plugin plugin manager.
 */
class ChatbotPluginManager extends DefaultPluginManager {

  /**
   * Constructs a new ChatbotPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Chatbot', $namespaces, $module_handler, 'Drupal\chatbot_api\Plugin\ChatbotPluginInterface', 'Drupal\chatbot_api\Annotation\Chatbot');

    $this->alterInfo('chatbot_info');
    $this->setCacheBackend($cache_backend, 'chatbot_info_plugins');
  }

}
