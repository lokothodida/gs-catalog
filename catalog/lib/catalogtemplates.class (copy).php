<?php

class CatalogTemplates {
  private $templates;
  private $themes = array();
  private $current = false;
  private $currentFile = false;

  public function __construct($file, $themes) {
    $xml = simplexml_load_file($file);

    // detect the current theme
    $this->current = (string) $xml->current;

    // load the available theme names
    if ($themes) {
      $this->themes = $this->getThemeNames($themes);
    }

    // open the current theme
    if ($this->currentFile) {
      $xml = simplexml_load_file($this->currentFile);

      foreach ($xml as $k => $template) {
        $this->templates[(string) $template['name']] = (string) $template;
      }
    }
  }

  private function getThemeNames($dir) {
    $themesDir = glob($dir);
    $themes = array();

    foreach ($themesDir as $theme) {
      $name = basename($theme, '.xml');
      $themes[] = $name;

      if ($name == $this->current) {
        $this->currentFile = $theme;
      }
    }

    return $themes;
  }

  public function getThemes() {
    return $this->themes;
  }

  public function getCurrentTheme() {
    return $this->current;
  }

  public function getTemplates() {
    return $this->templates;
  }
}

?>
