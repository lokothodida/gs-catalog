<?php

/**
 * General settings for the catalog
 */
class CatalogSettingsGeneral {
  /** properties */
  private $data;

  /** methods */
  // Constructor
  public function __construct($file) {
    $xml = simplexml_load_file($file);

    $this->formatSettings($xml);
  }

  // Format the settings
  private function formatSettings($xml) {
    foreach ($xml as $field => $setting) {
      $field = (string) $field;
      $setting = (string) $setting;

      if (!$setting) {
        $this->data[$field] = false;
      } elseif ($setting == 'y') {
        $this->data[$field] = true;
      } elseif ($field == 'languages') {
        // Turn langauges into an array
        $langauges = explode("\n",  $setting);
        $this->data[$field] = array_map('trim', $langauges);
      } else {
        $this->data[$field] = $setting;
      }
    }

    // Dynamic settings
    // URLs
    $this->data['url'] = $GLOBALS['SITEURL'] . (((int) $GLOBALS['PRETTYURLS']) ? '' : 'index.php?id=') . $this->get('slug') . '/';
    $this->set('urlProduct', $this->get('url') . 'product/%parents%/%product%' . $this->get('urlSuffix'));
    $this->set('urlCategory', $this->get('url') . 'category/%parents%/%category%' . $this->get('urlSuffix'));
  }

  // Set a particular field
  private function set($field, $value) {
    $this->data[strtolower($field)] = $value;
  }

  // Get a particular field
  public function get($field) {
    $field = strtolower($field);

    return isset($this->data[$field]) ? $this->data[$field] : null; 
  }
}

?>
