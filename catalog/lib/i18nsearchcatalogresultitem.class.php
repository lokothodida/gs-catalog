<?php

class I18nSearchCatalogResultItem extends I18nSearchResultItem {
  protected $data = null;
  protected $categories = false;
  protected $product = false;
  
  protected function get($name) {
    // get the entry
    $id = substr($this->id, 4);
    $product = GSDATAOTHERPATH . 'catalog/products/' . $id . '.xml';
    
    if (file_exists($product)) {
      /*
      if (!$this->categories) {
        $this->categories = new CatalogCategories(GSDATAOTHERPATH . 'catalog/categories/*.xml', $GLOBALS['SITEURL'] . $general->baseurl, ((string) $general->slugged == 'y' ? true : false));
        $this->categories = $this->categories->getCategories();
      }
      */
      
      $this->data = new CatalogProduct(GSDATAOTHERPATH . 'catalog/products/' . $id .'.xml', null, false);
      foreach ($this->data->getField('categories')->category as $cat) {
        //$this->data->setCategory($categories[(string) $cat]);
      }
    
      //$this->data = (array) simplexml_load_file($product);
    }
    else return null;
  
    switch ($name) {
      case 'title':       return @$this->data->getField('title');
      case 'description': return @$this->data->getField('description');
      case 'content':     return '<p>' . htmlspecialchars($this->data['categories']) . '</p>';
      case 'link':        return null; 
      default:            return @$this->data->getField($name); 
    }
  }
}
    
?>
