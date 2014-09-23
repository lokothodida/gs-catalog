<?php

/**
 * Theme settings for the catalog
 */
class CatalogSettingsTheme {
  /** properties */
  private $themes, $theme, $current;

  /** methods */
  // Constructor
  public function __construct($params) {
    $xml = simplexml_load_file($params['file']);

    $this->current = (string) $xml->current;

    $dir = dirname($params['file']) . '/themes/*.xml';

    $this->loadThemeNames($dir);
    $this->loadTheme($params['directory'] . (string) $xml->current . '.xml');
  }

  // Get a particular field
  public function get($field) {
    $field = strtolower($field);

    return isset($this->theme[$field]) ? $this->theme[$field] : null; 
  }

  // Get themes
  public function getThemes() {
    return $this->themes;
  }

  // Get current theme name
  public function getCurrentTheme() {
    return $this->current;
  }

  // Get templates for current theme
  public function getTemplates() {
    return $this->theme;
  }

  // Load theme names
  private function loadThemeNames($dir) {
    $themesDir = glob($dir);
    $themes = array();

    foreach ($themesDir as $theme) {
      $name = basename($theme, '.xml');
      $themes[] = $name;
    }

    $this->themes = $themes;
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
