<?php

class CatalogRouter {
  private $siteurl;
  private $uri;
  private $params;
  
  public function __construct($uri, $siteurl, $slugged) {
    $this->uri = $uri;
    $this->siteurl = $siteurl;
    $this->parse($slugged);
  }
  
  public function parse($slugged = true) {
    // remove all of the ? onwards
    $url = $this->uri;
    $url = strpos($url, '?') ? substr($url, 0, strpos($url, '?')) : $url;
    $url = trim(str_replace($this->siteurl, '', $url));
    
    // slugged version
    if ($slugged) {
      $params = explode('/', $url);
      $params = array_map('trim', $params);
      $params = array_filter($params); // remove empty values
      $params = array_values($params); // reindexc array from 0
    }
    // non-slugged version
    else {
      // id
      $params[] = $_GET['id'];
      // category
      if (isset($_GET['category']) && ($v = glob($file = GSDATAOTHERPATH . 'catalog/categories/' . $_GET['category'] . '-*.xml'))) { 
        // recursively get each category
        $params = $this->recGetCategory(basename($v[0], '.xml'), $params);
      }
      // product
      if (isset($_GET['product']) && ($v = glob($file = GSDATAOTHERPATH . 'catalog/products/' . $_GET['product'] . '-*.xml'))) { 
        $params[] = basename($v[0], '.xml');
      }
    }
    
    $this->params = $params;
  }
  
  private function recGetCategory($category, $params) {
    $file = GSDATAOTHERPATH . 'catalog/categories/' . $category . '.xml';
    if (file_exists($file) && ($xml = simplexml_load_file($file)) && $xml->category) {
      $params = $this->recGetCategory((string) $xml->category, $params);
    }
    $params[] = $category;
    return $params;
  }
  
  public function getParams() {
    return $this->params;
  }
}

?>
