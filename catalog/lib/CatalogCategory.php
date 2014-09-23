<?php

/**
 * Encapsulate category data type
 */

class CatalogCategory extends CatalogItem {
  /** methods */
  protected $urlCategory;

  // Constructor
  public function __construct(array $params) {
    parent::__construct($params);
    $this->item['id'] = basename($params['file'], '.xml');
    $this->item['exists'] = (boolean) file_exists($params['file']);
    $this->setCategoryUrl();
  }

  public function getField($field, $option = false) {
    $value = parent::getField($field);

    if ($field == 'id' && $option) {
      // Parsing the ID
      $value = $this->parseId($value);
    } elseif($field == 'url') {
      // Returning a URL
      $value = $this->urlCategory;
    }

    return $value;
  }

  protected function setCategoryUrl() {
    $categories = (array) $this->getField('categories');
    $implode = implode('/', $categories);

    $parsedId = $this->parseId($this->getField('id'));
    $id = $this->generalSettings->get('slugged') ? $this->getField('id') : $parsedId['numeric'];

    $this->urlCategory = str_replace(
      array('/%parents%', '%category%'),
      array($implode, $id),
      $this->generalSettings->get('urlCategory'));
  }
}

?>
