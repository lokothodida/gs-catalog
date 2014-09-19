<?php

/**
 * Individual product
 */

class CatalogProduct extends CatalogItem {
  /** properties */
  private $i18n,
          $url;

  /** methods */
  // Constructor
  public function __construct($params) {
    parent::__construct($params);

    $this->i18n = isset($params['i18n']) ? $params['i18n'] : false;
    $this->setId($params['file']);
    $this->setUrl();
  }

  // Get a field
  public function getField($field, $type = false) {
    $value = parent::getField($field);

    // Format categories into array
    if ($field == 'categories') {
      if (isset($value['category'])) {
        $value = (array) $value['category'];
      } else {
        $value = array();
      }
    } elseif ($field == 'url') {
      $value = $this->getUrl();
    }

    // Parse according to $type
    // ...

    return $value;
  }

  // Use a category's url to set the url
  public function setUrlFromCategory($category) {
    $categories = (array) $category->getField('url');
    $implode = implode('/', $categories);

    //$explode = explode('%product%', $this->generalSettings->get('urlProduct'));

    $this->url = str_replace(
      array('/%parents%', '%product%'),
      array($implode, $this->getField('id')),
      $this->generalSettings->get('urlProduct'));
  }

  // Set the id
  private function setId($filename) {
    $id = basename($filename, '.xml');

    $explode = explode('_', $id);
    $this->item['id'] = $explode[0];

    if ($this->i18n) {
      $this->item['lang'] = isset($explode[1]) ? $explode[1] : return_i18n_default_language();
    }
  }

  // Set the URL
  private function setUrl() {
    $this->url = str_replace(
      array('/%parents%', '%product%'),
      array('', $this->getField('id')),
      $this->generalSettings->get('urlProduct')
    );
  }

  // Get the URL
  private function getUrl() {
    return $this->url;
  }
}

?>
