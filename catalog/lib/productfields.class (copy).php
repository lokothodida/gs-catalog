<?php

class ProductFields {
  private $fields;

  public function __construct($file) {
    $xml = simplexml_load_file($file);
    
    foreach ($xml as $field) {
      $obj = (object) array(
                        'name' => (string) $field['name'],
                        'label' => (string) $field['label'],  
                        'type' => (string) $field['type'],  
                        'index' => (string) $field['index'], 
                        'default' => (string) $field);
      $this->fields[] = $obj;
    }
  }

  public function getFields() {
    return $this->fields;
  }
}

?>
