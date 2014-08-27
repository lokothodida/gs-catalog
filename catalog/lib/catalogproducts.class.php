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
    if (in_array($this->catfilter, (array) $product->getField('categories')->category)) {
      return true;
    }
    else return false;
  }

  private function filterSearch($product) {
    foreach ($this->fields as $field) {
      //echo $product->getField($field->name) . ' ' . $this->searchfilter . '<br>';
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
      $this->sort = $sort;

      uasort($products, array($this, 'sort'));
    }

    if ($category) {
      $this->catfilter = $category;
      $products = array_filter($products, array($this, 'filterCategory'));
    }

    if ($search) {
      $this->searchfilter = $search;
      $products = array_filter($products, array($this, 'filterSearch'));
    }

    if ($max) {
      $products = array_slice($products, 0, $max);
    }

    return $products;
  }
}

?>
