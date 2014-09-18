<?php

class CatalogSettingsFields {
  /** properties */
  private $fields;

  /** methods */
  public function __construct($params) {
    $xml = simplexml_load_file($params['file']);
    
    foreach ($xml as $field) {
      $obj = (object) array(
                        'name' => (string) $field['name'],
                        'label' => (string) $field['label'],
                        'type' => (string) $field['type'],
                        'index' => (string) $field['index'],
                        'default' => (string) $field);
      $this->fields[(string) $field['name']] = $obj;
    }
  }

  // Get a particular field
  public function get($field) {
    $field = strtolower($field);

    return isset($this->field[$field]) ? $this->field[$field] : null; 
  }

  public function getFields() {
    return $this->fields;
  }
}

?>
