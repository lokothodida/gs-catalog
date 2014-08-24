<?php

class CatalogTemplates {
  private $templates;

  public function __construct($file) {
    $xml = simplexml_load_file($file);
    
    foreach ($xml as $k => $template) {
      $this->templates[(string) $template['name']] = (string) $template;
    }
  }
  
  public function getTemplates() {
    return $this->templates;
  }
}

?>
