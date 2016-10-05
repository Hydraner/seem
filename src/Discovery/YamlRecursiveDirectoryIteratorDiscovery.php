<?php

namespace Drupal\seem\Discovery;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Drupal\Component\Discovery\YamlDirectoryDiscovery as YamlDirectoryDiscoveryComponent;

/**
 * Discovers multiple YAML files in a set of directories recursively.
 */
class YamlRecursiveDirectoryIteratorDiscovery extends YamlDirectoryDiscoveryComponent {

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
      foreach ($directories as $path) {
        if (is_dir($path)) {
          $directory = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
          $iterator = new RecursiveIteratorIterator($directory);
          foreach ($iterator as $file_info) {
            // Since we don't use RegexDirectoryIterator anymore, make sure the
            // files are yml files.
            if ($file_info->isFile() && preg_match('/\.yml$/i', $file_info->getFilename())) {
              $file_list[$file_info->getPathname()] = $provider;
            }
          }
        }
      }
    }
    return $file_list;
  }

}
