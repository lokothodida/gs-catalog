<?php

/**
 * Pulls options from /data/catalog/options directory and pulls into one class/object
 * Class is NOT for the admin to use
 */
class CatalogOptions {
  /* properties */
  private $options;

  /* methods */
  // @param $dir {String} directory for options (/data/catalog/options)
  public function __construct($dir) {
    $this->options = (object) array();
    $this->getGeneral($dir);
    $this->getTemplates($dir);
    $this->getFields($dir);
  }

  // return $options
  public function getOptions() {
    return $this->options;
  }

  // load the general.xml file contents
  public function getGeneral($dir) {
    $obj = new CatalogGeneralOptions($dir . 'general.xml');
    $this->options->general = $obj->getGeneralOptions();
  }

  // load templates.xml file contents
  public function getTemplates($dir) {
    $obj = new CatalogTemplates($dir . 'templates.xml');
    $this->options->templates = $obj->getTemplates();
  }

  // load fields.xml file contents
  public function getFields($dir) {
    $obj = new ProductFields($dir . 'fields.xml');
    $this->options->fields = $obj->getFields();
  }
}

?>
