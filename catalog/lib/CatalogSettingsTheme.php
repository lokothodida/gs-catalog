<?php

/**
 * Theme settings for the catalog
 */
class CatalogSettingsTheme {
  /** properties */
  private $theme;

  /** methods */
  // Constructor
  public function __construct($params) {
    $xml = simplexml_load_file($params['file']);

    $this->loadTheme($params['directory'] . (string) $xml->current . '.xml');
  }

  // Get a particular field
  public function get($field) {
    $field = strtolower($field);

    return isset($this->theme[$field]) ? $this->theme[$field] : null; 
  }

  // Load a theme
  private function loadTheme($file) {
    $xml = simplexml_load_file($file);

    $this->theme['name'] = basename($file, '.xml');

    // format the settings
    foreach ($xml as $name => $template) {
      $this->theme[strtolower($name)] = (string) $template;
    }
  }
}

?>
