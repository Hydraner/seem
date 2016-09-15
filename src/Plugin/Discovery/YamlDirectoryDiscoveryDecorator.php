<?php

namespace Drupal\seem\Plugin\Discovery;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Core\Plugin\Discovery\YamlDirectoryDiscovery;

/**
 * Enables YAML discovery in directories for plugin definitions.
 *
 * You should normally extend this class to add validation for the values in the
 * YAML data or to restrict use of the class or derivatives keys.
 *
 * @see Drupal\Core\Plugin\Discovery\YamlDiscoveryDecorator.
 */
class YamlDirectoryDiscoveryDecorator extends YamlDirectoryDiscovery {

  /**
   * The Discovery object being decorated.
   *
   * @var \Drupal\Component\Plugin\Discovery\DiscoveryInterface
   */
  protected $decorated;

  /**
   * Constructs a YamlDirectoryDiscoveryDecorator object.
   *
   * @param \Drupal\Component\Plugin\Discovery\DiscoveryInterface $decorated
   *   The discovery object that is being decorated.
   * @param array $directories
   *   An array of directories to scan, keyed by the provider. The value can
   *   either be a string or an array of strings. The string values should be
   *   the path of a directory to scan.
   * @param string $file_cache_key_suffix
   *   The file cache key suffix. This should be unique for each type of
   *   discovery.
   * @param string $key
   *   (optional) The key contained in the discovered data that identifies it.
   *   Defaults to 'id'.
   */
  public function __construct(DiscoveryInterface $decorated, array $directories, $file_cache_key_suffix, $key = 'id') {
    parent::__construct($directories, $file_cache_key_suffix, $key);

    $this->decorated = $decorated;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    return parent::getDefinitions() + $this->decorated->getDefinitions();
  }

  /**
   * Passes through all unknown calls onto the decorated object.
   */
  public function __call($method, $args) {
    return call_user_func_array(array($this->decorated, $method), $args);
  }
}
