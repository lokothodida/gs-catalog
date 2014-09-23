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

  // Get parents
  private function getParents($categories) {
    $tmp = array();

    foreach ($categories as $k => $category) {
      if ($category->getField('category') == '') {
        $tmp[$category->getField('id')] = $category;
      }
    }

    return $tmp;
  }

  // Get children
  private function getChildren($categories) {
    $tmp = array();

    foreach ($categories as $k => $category) {
      if ($category->getField('category') != '') {
        $tmp[$category->getField('category')][] = $category;
      }
    }

    return $tmp;
  }

  // Thread parents and children
  private function threadParentsAndChildren($parents, $children) {
    $tmp = $this->threadParentsAndChildrenRec($parents, $children);

    return $tmp;
  }

  // Continue threading
  private function threadParentsAndChildrenRec($currentChildren, $allChildren) {
    $tmp = array();

    foreach ($currentChildren as $k => $child) {
      $tmp[$k]['category'] = $child;

      if (isset($allChildren[$k])) {
        $tmp[$k]['children'] = $this->threadParentsAndChildrenRec($allChildren[$k], $allChildren);
      }
    }

    return $tmp;
  }

  // Get the categories
  public function getcategories(array $params = array()) {
    $categories = $this->categories;
    $tmp = array();

    // parents only
    if (isset($params['parentsOnly']) && $params['parentsOnly']) {
      foreach ($categories as $k => $category) {
        if ($category->getField('category') == '') {
          $tmp[$k] = $category;
        }
      }

      $categories = $tmp;
      $tmp = array();
    }

    // use slugs as array keys
    if (isset($params['idKeys']) && $params['idKeys']) {
      foreach ($categories as $k => $category) {
        $tmp[$category->getField('id')] = $category;
      }

      $categories = $tmp;
      $tmp = array();
    }

    // sort
    if (isset($params['sort'])) {
      //$categories = $this->sortcategories($categories, $params['sort']);
    } else {
      // by default, sort by the order parameter
      $categories = $this->sortByOrder($categories);
    }

    // hierarchical structure
    if (isset($params['hierarchical']) && $params['hierarchical']) {
      // get parents
      $parents = $this->getParents($categories);

      // get children
      $children = $this->getChildren($categories);

      // thread them
      $categories = $this->threadParentsAndChildren($parents, $children);
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

  // Sort by order
  private function sortByOrder($categories) {
    uasort($categories, array($this, 'sortByOrderImp'));
    return $categories;
  }

  // Implementation of sorting by order
  private function sortByOrderImp(CatalogCategory $a, CatalogCategory $b) {
    $result = $a->getField('order') - $b->getField('order');
    return $result;
  }
}

?>
