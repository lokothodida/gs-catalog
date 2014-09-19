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
    $params['i18n'] = isset($params['i18n']) ? $params['i18n'] : false;
    $this->settings = $params['settings'];
    $this->loadProducts($params['wildcard'], $params['i18n']);
  }

  // Get the products
  public function getProducts(array $params = array()) {
    $products = $this->products;

    // perform the query on the products
    $sq = new CatalogItemsQuery($products);

    $query = array();

    // category
    if (isset($params['categories'])) {
      if (empty($params['categories'])) {
        // no categories
        $query['categories']['$eq='] = array();
      } else {
        // searching for particular categories
        if (!is_array($params['categories'])) {
          $params['categories'] = array($params['categories']);
        }

        $query['categories']['$has'] = $params['categories'];
        $query['categories']['$cs'] = false;
      }
    }

    $sort = array();

    $results = $sq->query($query, $sort);

    // languages
    if (!empty($params['languages'])) {
      $results = $this->sortProductsByLanguages($results);
    }

    // get a particular language
    if (!empty($params['languages']) && $params['languages'] !== true) {
      $results = $this->filterProductsByLanguages($results, $params['languages']);
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
      $slug    = $product->getField('id');
      $lang = $product->getField('lang');

      $tmp[$slug]['language'][$lang] = $product;
    }

    return $tmp;
  }

  // Filter products into a particular language
  private function filterProductsByLanguages($products, $lang) {
    $tmp = array();

    foreach ($products as $k => $product) {
      foreach ($product['language'] as $l => $p) {
        if ($l == $lang) {
          $tmp[$k] = $p;
          break;
        } elseif ($l == return_i18n_default_language()) {
          $tmp[$k] = $p;
          break;
        }
      }
    }

    return $tmp;
  }

  // Load the products
  private function loadProducts($wildcard, $i18n = false) {
    $products = glob($wildcard);

    foreach ($products as $product) {
      $this->products[] = new CatalogProduct(array(
        'file'     => $product,
        'settings' => $this->settings,
        'i18n'     => $i18n,
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
