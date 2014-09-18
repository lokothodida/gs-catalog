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

    // perform the query on the products
    $sq = new CatalogItemsQuery($products);

    $query = array();

    // category
    if (isset($params['categories'])) {
      if (!is_array($params['categories'])) {
        $params['categories'] = array($params['categories']);
      }

      $query['categories']['$has'] = $params['categories'];
      $query['categories']['$cs'] = false;
    }

    $sort = array();

    $results = $sq->query($query, $sort);


    // languages
    if (!empty($params['languages'])) {
      $results = $this->sortProductsByLanguages($results);
    }

    return $results;
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

  // Sort products into langauges
  private function sortProductsByLanguages($products) {
    $tmp = array();

    foreach ($products as $product) {
      $id      = $product->getField('id');
      $explode = explode('_', $id);
      $slug    = $explode[0];
      $lang    = isset($explode[1]) ? $explode[1] : return_i18n_default_language();

      $tmp[$slug]['language'][$lang] = $product;
    }

    return $tmp;
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
}

?>
