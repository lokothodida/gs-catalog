<?php

class I18nSearchCatalogResultItem extends I18nSearchResultItem {
  protected $data = null;
  protected $categories = false;
  protected $product = false;
  
  protected function get($name) {
    // get the entry
    $productId = substr($this->id, 4);
    $catalogId = basename(dirname(dirname(__DIR__)));

    $general = new CatalogGeneralOptions(GSDATAOTHERPATH . $catalogId . '/general.xml');
    $this->product = new CatalogCategory(GSDATAOTHERPATH . $catalogId . '/products/' . $productId . '.xml', ((string) $general->getSlugged() == 'y'));
    $this->product->setParents();

    switch ($name) {
      case 'CatalogProduct':  return @$this->product;
      case 'title':           return @$this->product->getField('title');
      case 'description':     return @$this->product->getField('description');
      case 'content':         return @$this->product->getField('description');
      case 'link':            return @$this->product->getField('url');
      default:                return @$this->product->getField($name);
    }
  }
}
    
?>
