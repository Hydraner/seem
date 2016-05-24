<?php

namespace Drupal\seem\Plugin\Discovery;

use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use Drupal\seem\Discovery\SuggestionYamlDiscovery as ComponentSuggestionYamlDiscovery;


/**
 * Allows YAML files to define plugin definitions.
 *
 * If the value of a key (like title) in the definition is translatable then
 * the addTranslatableProperty() method can be used to mark it as such and also
 * to add translation context. Then
 * \Drupal\Core\StringTranslation\TranslatableMarkup will be used to translate
 * the string and also to mark it safe. Only strings written in the YAML files
 * should be marked as safe, strings coming from dynamic plugin definitions
 * potentially containing user input should not.
 */
class SuggestionYamlDiscovery extends YamlDiscovery {
  
  public function __construct(array $directories, $file_cache_key_suffix, $key = 'id') {
    // Intentionally does not call parent constructor as this class uses a
    // different YAML discovery.
    $this->discovery = new ComponentSuggestionYamlDiscovery($directories, $file_cache_key_suffix, $key);
  }

}
