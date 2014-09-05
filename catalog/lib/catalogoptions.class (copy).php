<?php

class CatalogOptions {
  private $options;
  
  public function __construct($dir) {
    $this->options = (object) array();
    $this->getGeneral($dir);
    $this->getTemplates($dir);
    $this->getFields($dir);
  }
  
  public function getOptions() {
    return $this->options;
  }
  
  public function getGeneral($dir) {
    $obj = new CatalogGeneralOptions($dir . 'general.xml');
    $this->options->general = $obj->getGeneralOptions();
  }
  
  public function getTemplates($dir) {
    $obj = new CatalogTemplates($dir . 'templates.xml', $dir . '/templates/*.xml');
    $this->options->templates = $obj->getTemplates();
  }
  
  public function getFields($dir) {
    $obj = new ProductFields($dir . 'fields.xml');
    $this->options->fields = $obj->getFields();
  }
}

?>
