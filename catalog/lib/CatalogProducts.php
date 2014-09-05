<?php

/**
 * Collection of products
 */

class CatalogProducts {
  /** properties */
  private $products = array(),
          $settings,
          $filter,
          $categories,
          $sort,
          $max;

  /** methods */
  // Constructor
  public function __construct(array $params) {
    $this->settings = $params['settings'];
    $this->loadProducts($params['wildcard']);
  }

  // Get the products
  public function getProducts(array $params = array()) {
    $products = $this->products;
    /*
    // category
    if (isset($params['categories'])) {
      $products = $this->filterCategory($products, $params['categories']);
    }

    // filter
    if (isset($params['filter'])) {
      $products = $this->filterProducts($products, $params['filter']);
    }

    // sort
    if (isset($params['sort'])) {
      $products = $this->sortProducts($products, $params['sort']);
    }

    // max
    if (isset($params['max'])) {
      $products = $this->maxProducts($products, $params['max']);
    }
    */
    return $products;
  }

  // Load the products
  private function loadProducts($wildcard) {
    $products = glob($wildcard);

    foreach ($products as $product) {
      $this->products[] = new CatalogProduct(array(
        'file'     => $product,
        'settings' => $this->settings,
      ));
    }
  }

  // Sort the products
  private function sortProducts($products, $sort) {
    $this->sort = $sort;
    return uasort($products, array($this, 'sort'));
  }

  // Sorting algorithm
  private function sort(CatalogProduct $a, CatalogProduct $b) {
    $score = 0;

    // run through each sorting field, aggregating the score
    foreach ($this->sort as $field => $ascDesc) {
      // check field values
      $af = $a->getField($field);
      $bf = $b->getField($field);

      // compare numbers if the values are numeric; strings otherwise
      $comparison = (is_numeric($af) && is_numeric($bf)) ? cmp($af, $bf) : strcmp($af, $bf);
      $score += $ascDesc == 'asc' ? $comparison : -$comparison;
    }

    return $score;
  }

  // Filter the products
  private function filterProducts($products, $filter) {
    $this->filter = $filter;
    return array_filter($products, array($this, 'filter'));
  }

  // Filtering algorithm
  private function filter($product) {
    foreach ($this->filter as $field) {
      if ($product->getField($field['name']) == $field['value']) {
        return true;
      }
    }

    return false;
  }

  // Filtering algorithm for categories
  private function filterCategory($products, $categories) {
    $this->categories = array();

    // get the numeric id for category (so either it or the full slug can be used)
    foreach ($categories as $category) {
      $numericId = explode('-', $category);
      $numericId = reset($numericId);


      if (is_numeric($numericId)) {
        $this->categories[$numericId] = $category;
      } else {
        $this->categories[$category] = $category;
      }
    }

    return array_filter($products, array($this, 'filterC'));
  }

  // Parse category slug into id and id-less slug
  private function parseCategorySlug($slug) {
    $explode = explode('-', $slug);
    $id = reset($explode);

    if (isset($explode)) {
      array_shift($explode);
    }

    $slug = implode('-', $explode);

    return array(
      'id' => $id,
      'slug' => $slug,
    );
  }

  // Filter by category
  private function filterC($product) {
    return true;
    $categories = $product->getField('categories');

    foreach ($this->categories as $id => $category) {
      $parsedCategory = $this->parseCategorySlug($category);

var_dump($category, $categories);
echo '<br>';
var_dump($inCategoriesArray = in_array($category, $categories));
echo '<br><br>';

      if ($inCategoriesArray) {
        //return true;
      }
    }
    return false;
  }

  // Search the products
  private function searchProducts($products, $search) {
    $this->search = $search;
    return $products;
  }
}

?>
