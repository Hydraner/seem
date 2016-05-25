<?php

namespace Drupal\seem\Discovery;

use Drupal\Component\Discovery\DiscoveryException;
use Drupal\Component\Discovery\YamlDirectoryDiscovery;
use Drupal\Component\FileCache\FileCacheFactory;
use Drupal\Component\Serialization\Exception\InvalidDataTypeException;
use Drupal\Component\Serialization\Yaml;

/**
 * Provides discovery for YAML files within a given set of directories.
 */
class SuggestionYamlDiscovery extends YamlDirectoryDiscovery {

  /**
   * {@inheritdoc}
   */
  public function findAll() {
    $all = array();

    $files = $this->findFiles();

    $file_cache = FileCacheFactory::get('yaml_discovery:' . $this->fileCacheKeySuffix);

    // Try to load from the file cache first.
    foreach ($file_cache->getMultiple(array_keys($files)) as $file => $data) {
      $all[$files[$file]][$this->getIdentifier($file, $data)] = $data;
      unset($files[$file]);
    }

    // If there are files left that were not returned from the cache, load and
    // parse them now. This list was flipped above and is keyed by filename.
    if ($files) {
      foreach ($files as $file => $provider) {
        // If a file is empty or its contents are commented out, return an empty
        // array instead of NULL for type consistency.
        try {
          $data = Yaml::decode(file_get_contents($file)) ?: [];
        } catch (InvalidDataTypeException $e) {
          throw new DiscoveryException("The $file contains invalid YAML", 0, $e);
        }
        $data[static::FILE_KEY] = $file;
        // Inject the context.
        // @todo: The context should be used as ID istead of 'context'.
        $basename = basename($file);
        $data['context'] = explode('.', $basename)[0];
        $all[$provider][$this->getIdentifier($file, $data)] = $data;
        $file_cache->set($file, $data);
      }
    }

    return $all;
  }

  /**
   * Returns an array of providers keyed by file path.
   *
   * @return array
   *   An array of providers keyed by file path.
   */
  protected function findFiles() {
    $file_list = [];
    foreach ($this->directories as $provider => $directories) {
      $directories = (array) $directories;
      foreach ($directories as $directory) {
        // Make the discovery search in /layout directory.
        // @todo: Maybe we should do this already in the PluginManager.
        $directory = $directory . '/display';
        if (is_dir($directory)) {
          /** @var \SplFileInfo $fileInfo */
          foreach ($this->getDirectoryIterator($directory) as $fileInfo) {
            $file_list[$fileInfo->getPathname()] = $provider;
          }
        }
      }
    }
    return $file_list;
  }

  /**
   * Returns an array of file paths, keyed by provider.
   *
   * @return array
   */
//  protected function findFiles() {
//    $plugin_manager = \Drupal::service('plugin.manager.seem_element_type_plugin.processor');
//    $suggestions = array();
//    foreach ($plugin_manager->getDefinitions() as $plugin_id => $definition) {
//      $suggestions += $plugin_manager->createInstance($plugin_id)->getSuggestions();
//    }
//
//    $files = array();
//    foreach ($this->directories as $provider => $directory) {
//      $file = $directory . '/layout/' . $provider . '.' . $this->name . '.yml';
//      if (file_exists($file)) {
//        $files[$provider] = $file;
//      }
//    }
//    return $files;
//  }

}
