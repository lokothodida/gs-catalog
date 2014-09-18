<?php

/**
 * Parse an array of categories (CatalogCategory) into a hierarcical trail
 */

class CatalogCategoriesTrail {
  /** properties */
  private $categories,
          $trail = array();

  /** public methods */
  // Constructor
  public function __construct(array $categories) {
    $this->categories = $categories;

    foreach ($this->categories as $k => $category) {
      $this->setupTrail($category);
    }
  }

  // Return the trail
  public function getTrail() {
    return $this->trail;
  }

  /** private methods */
  // Set up the trail for a particular category
  private function setupTrail(CatalogCategory $category) {
    // find the parent
    $parent = $category->getField('category');

    // this is a recursive function, so we need a base case
    // i.e. if the parent doesn't exist, we've hit a root category
    if (!$parent || !isset($this->categories[$parent])) {
      // root, so only breadcrumb is the current category
      $this->categories[$category] = array($category);

      return null;
    }

    // check if the parent trail has been built up yet
    if (!isset($this->trail[$parent])) {
      $this->setupTrail($this->categories[$parent]);
    }

    // now the trail exists, use it for the existing trail
    $this->trail[$category] = $this->trail[$parent];

    // add the current category on
    $this->trail[$category][] = $this->category;
  }
}

?>
