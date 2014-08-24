<?php

class CatalogProduct {
  private $product;
  private $categories = array();
  private $file;
  private $url;
  private $slugged;
  
  // initializes product object using a valid filename
  // pre:   $file is a path to a valid catalog product
  public function __construct($file = null, $slugged) {
    if (file_exists($file)) {
      $this->product = simplexml_load_file($file);
      $this->product->id = basename($file, '.xml');
    }
    $this->slugged = $slugged;
  }
  
  public function getField($name) {
    if (isset($this->product->{$name})) {
      return $this->product->{$name};
    }
    else {
      return null;
    }
  }
  
  public function setCategory(CatalogCategory $category) {
    $key = array_search($category->getId(), (array) $this->product->categories->category);
    if ($key !== false) {
      $this->categories[$key] = $category;
    }
  }
  
  public function getCategories() {
    return $this->categories;
  }
  
  public function setUrl(CatalogCategory $category) {
    if ($this->slugged) {
      $this->url = $category->getUrl() . $this->product->id . '.htm';
    }
    else {
      $id = substr($this->product->id, 0, strpos($this->product->id, '-'));
      $this->url = $category->getUrl() . '&product='. $id;
    }
  }
  
  public function getUrl() {
    return $this->url;
  }
}

?>
