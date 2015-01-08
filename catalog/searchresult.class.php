<?php

class I18nSearchCatalogResultItem extends I18nSearchResultItem {
  protected $data = null;
  protected $product = false;
  
  protected function get($name) {
    $slug = substr($this->id, 8);

    if (!$this->product) {
      $this->product = CatalogProduct::getProduct($slug);
    }

    switch ($name) {
      case 'CatalogProduct': return @$this->product;
      case 'title':          return @$this->product->get('title');
      case 'description':    return @$this->product->get('description');
      case 'content':        return @$this->product->getField('description');
      case 'link':           return catalog_get_product_url(@$this->product->get('slug'));
      default:               return @$this->product->get($name);
    }
  }
}
    
?>