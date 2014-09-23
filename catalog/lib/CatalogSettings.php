<?php

/**
 * Encapsulate all settings for the catalog
 */
class CatalogSettings {
  /** properties */
  private $settings = array();

  /** methods */
  // Constructor - load the settings
  public function __construct($directory) {
    $this->settings['general'] = new CatalogSettingsGeneral($directory . 'general.xml');
    $this->settings['fields']  = new CatalogSettingsFields(array('file' => $directory . '/fields.xml'));
    $this->settings['theme']   = new CatalogSettingsTheme(array(
      'directory' => $directory . 'themes/',
      'file'      => $directory . 'themes.xml',
    ));
  }

  // Get a particular setting
  public function get($setting) {
    return isset($this->settings[$setting]) ? $this->settings[$setting] : null;
  }
}

?>
