<?php

class CatalogProducts {
  private $products = array();
  private $fields;
  private $catfilter = null;
  private $searchfilter = null;
  private $sort = array();
  private $slugged;

  public function __construct($files, $fields = null, $slugged) {
    $this->slugged = $slugged;
    $this->fields = $fields;
    $this->fields[] = (object) array('name' => 'title'); // add 'title' to fields list
    $this->loadProducts(glob($files));
  }
  
  private function loadProducts($files) {
    foreach ($files as $file) {
      $this->products[basename($file, '.xml')] = new CatalogProduct($file, $this->slugged);
    }
  }

  private function filterCategory($product) {
    $categories = (array) $product->getField('categories')->category;

    return (
      in_array($this->catfilter, $categories) ||          // category is in the array
      ($this->catfilter == '' && count($categories) == 0) // no categories
    );
  }

  private function filterSearch($product) {
    foreach ($this->fields as $field) {
      if (strpos((string) $product->getField($field->name), $this->searchfilter) !== false) {
        return true;
      }
    }
    return false;
  }

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

  private function paginate(array $products, $max, $page) {
    return array_slice($products, ($page - 1) * $max, $max);
  }

  public function getProducts($category = false, $search = false, $sort = array(), $max = false) {
    $products = $this->products;

    if ($sort) {
      // sort the products
      $this->sort = $sort;

      uasort($products, array($this, 'sort'));
    }

    if ($category !== false) {
      // filter out by category
      $this->catfilter = trim($category);

      $products = array_filter($products, array($this, 'filterCategory'));
    }

    if ($search) {
      // filter out by search query
      $this->searchfilter = $search;
      $products = array_filter($products, array($this, 'filterSearch'));
    }

    if ($max) {
      // restrict maximum number of entries
      $products = array_slice($products, 0, $max);
    }

    return $products;
  }
}

?>
