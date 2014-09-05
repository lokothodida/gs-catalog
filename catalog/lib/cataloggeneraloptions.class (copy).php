<?php

class CatalogGeneralOptions {
  private $general;

  public function __construct($file) {
    $this->general = array();
    $xml = simplexml_load_file($file);
    
    foreach ($xml as $k => $option) {
      $this->general[(string) $k] = (string) $option;
    }
    
    // base url (depends on whether slugs are enabled)
    $this->general['baseurl'] = $this->general['slugged'] == 'y' ? $this->general['slug'] . '/' : 'index.php?id=' . $this->general['slug']; 
  }
  
  public function getGeneralOptions() {
    return (object) $this->general;
  }
  
  public function __call($name, $args) {
    if (substr($name, 0, 3) == 'get') {
      $field = strtolower(substr($name, 3));
      if (isset($this->general[$field])) {
        return $this->general[$field];
      }
      else return null;
    }
  }
}

?>
