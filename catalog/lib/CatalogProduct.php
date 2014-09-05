<?php

/**
 * Individual product
 */

class CatalogProduct extends CatalogItem {
  /** properties */
  private $url;

  /** methods */
  // Constructor
  public function __construct($params) {
    parent::__construct($params);

    $this->item['id'] = basename($params['file'], '.xml');
    $this->url = null;
  }

  // Get a field
  public function getField($field, $type = false) {
    $value = parent::getField($field);

    // Format categories into array
    if ($field == 'categories') {
      if (isset($value['category'])) {
        $value = $value['category'];
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

  // Get the URL
  private function getUrl() {
    return $this->url;
  }
}

?>
