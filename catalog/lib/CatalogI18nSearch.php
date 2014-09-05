<?php

/**
 * I18n Search functions for Catalog
 */
class CatalogI18nSearch {
  // Constructor
  public function __construct(array $params) {
  }

  // Index all of the catalog items
  public function searchIndex() {
    $options = new CatalogOptions(GSDATAOTHERPATH . $this->id . '/');
    $options = $options->getOptions();
    $fullurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $general = &$options->general;
    $templates = &$options->templates;
    $fields = &$options->fields;
    $catalogurl = $GLOBALS['SITEURL'] . $general->baseurl;
    
    $slugged = (string) $options->general->slugged == 'y' ? true : false;
  
  
    // index categories
    $categories = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $GLOBALS['SITEURL'] . $general->baseurl, $slugged);
    $categories = $categories->getCategories();
    
    /*
    foreach ($categories as $category) {
      i18n_search_index_item('cat:' . $category->getId(), null, time(), time(), null, (string) $category->getTitle(), (string) $category->getDescription());
    }
    */
    // index products
    
    
    $products = new CatalogProducts(GSDATAOTHERPATH . $this->id . '/products/*.xml', $fields, $slugged);
    $prods = $products->getProducts();
    
    foreach ($prods as $product) {
      // content field
      $content = '';
      
      // set categories (and add the titles to the content variable)
      foreach ($product->getField('categories')->category as $cat) {
        //$product->setCategory($categories[(string) $cat]);
        
        $content .= $categories[(string) $cat]->getTitle() . ' ';
      }
      //$product->setUrl($categories[$cat]);
      
      // load each field into the content variable (so they are searchable)
      foreach ($fields as $field) {
        
        if ($field->index == 'y') {
          $content .= $product->getField($field->name) . ' ';
        }
      }
      
      // tags
      $tags = array($general->slug);
      $tags = implode(', ', $tags);
      
      i18n_search_index_item('cat:' . $product->getField('id'), null, time(), time(), null, (string) $product->getField('title'), $content);
    }
  }

  // How to set up a search item
  public function searchItem($id, $language, $creDate, $pubDate, $score) {
    if (substr($id, 0, 4) == 'cat:') {
      return new I18nSearchCatalogResultItem($id, $language, $creDate, $pubDate, $score);
    }
    return null;
  }

  // Displaying a search item
  public function searchDisplay($item, $showLanguage, $showDate, $dateFormat, $numWords) {
    if (substr($item->id, 0, 4) == 'cat:') {
      $options = new CatalogOptions(GSDATAOTHERPATH . $this->id . '/');
      $options = $options->getOptions();
      $templates = &$options->templates;
      $product = $item->CatalogProduct;

      eval('?>' . $templates['i18nsearch-product']);
      
      return true;
    }
    return false;
  }
}

?>
