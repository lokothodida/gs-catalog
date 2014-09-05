<?php

/**
 * Collection of categories
 */

class CatalogCategories {
  /** properties */
  private $categories = array(),
          $settings,
          $filter,
          $sort,
          $max;

  /** methods */
  // Constructor
  public function __construct(array $params) {
    $this->settings = $params['settings'];
    $this->loadCategories($params['wildcard']);
  }

  // Get the categories
  public function getcategories(array $params = array()) {
    $categories = $this->categories;

    // category
    if (isset($params['categories'])) {
      $categories = $this->filterCategory($categories, $params['categories']);
    }

    // filter
    if (isset($params['filter'])) {
      $categories = $this->filtercategories($categories, $params['filter']);
    }

    // sort
    if (isset($params['sort'])) {
      $categories = $this->sortcategories($categories, $params['sort']);
    }

    // max
    if (isset($params['max'])) {
      $categories = $this->maxcategories($categories, $params['max']);
    }

    return $categories;
  }

  // Load the categories
  private function loadCategories($wildcard) {
    $categories = glob($wildcard);

    foreach ($categories as $category) {
      $this->categories[] = new CatalogCategory(array(
        'file'     => $category,
        'settings' => $this->settings,
      ));
    }
  }
}

?>
