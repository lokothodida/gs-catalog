<?php

/**
 * Router to determine what actions need to be done in the catalog front-end
 */
class CatalogRouter {
  /** properties */
  private $url,
          $path,
          $siteUrl,
          $settings,
          $pluginDir,
          $inCatalog = false,
          $pageType = 'index';

  /** methods */
  // Constructor
  public function __construct($params) {
    $this->url             = $params['url'];
    $this->siteUrl         = $params['siteUrl'];
    $this->settings        = $params['settings'];
    $this->pluginDir       = $params['pluginDir'];
    $this->dataDir         = $params['dataDir'];
    $this->setup           = $params['setup'];
    $this->generalSettings = $this->settings->get('general');

    $this->parseUrl();
  }

  // Are we inside the catalog?
  public function inCatalog() {
    return $this->inCatalog;
  }

  // Get the page type
  public function getPageType() {
    return $this->pageType;
  }

  // Get the url path
  public function getPath() {
    return $this->path;
  }

  // Get the parameters for the current action
  public function getParams() {
    $params = array();

    if ($this->pageType == 'category') {
      // category
      // Remove extension
      $id = end($this->path);
      $id = explode('.', $id);
      $id = reset($id);

      if (is_numeric($id)) {
        // non-slugged version
        $categoryFile = glob($this->dataDir . 'categories/' . $id . '*.xml');
        $categoryFile = reset($categoryFile);
      } else {
        // slugged version
        $categoryFile = $this->dataDir . 'categories/' . $id . '.xml';
      }

      $categoryParams = array(
        'file' => $categoryFile,
        'settings' => $this->settings,
      );

      $productsParams = array(
        'wildcard' => $this->dataDir . 'products/*.xml',
        'settings' => $this->settings,
        'i18n' => $this->setup->i18nExists(),
      );

      $params['category'] = new CatalogCategory($categoryParams);
      $params['products'] = new CatalogProducts($productsParams);

      $getProductsParams = array();
      $getProductsParams['categories'] = array($params['category']->getField('id'));

      // language
      if ($this->setup->i18nExists()) {
        $lang = return_i18n_languages();
        $lang = $lang[0];
        $getProductsParams['languages'] = $lang;
      }

      $params['products'] = $params['products']->getProducts($getProductsParams);

      // pagination
      $paginate = new ArrayPaginate($params['products']);

      $_GET['p'] = isset($_GET['p']) ? $_GET['p'] : 1;

      // set up the pagination url
      $url = $this->removeQueryString($this->url) . '?p=%page%';

      $results = $paginate->paginate(array(
        'itemsPerPage' => $this->generalSettings->get('productsperpage'),
        'currentPage'  => $_GET['p'],
        'url'          => $url,
      ));

      $params['products'] = $results['results'];
      $params['navigation'] = $results['navigation'];
    } elseif ($this->pageType == 'product') {
      // product
      // Remove extension
      $id = end($this->path);
      $id = explode('.', $id);
      $id = reset($id);

      if (is_numeric($id)) {
        // non-slugged version
        $productFile = glob($this->dataDir . 'products/' . $id . '*.xml');
        $productFile = reset($productFile);
      } else {
        // slugged version
        $productFile = $this->dataDir . 'products/' . $id . '.xml';
      }

      $productParams = array(
        'file' => $productFile,
        'settings' => $this->settings,
      );

      $params['product'] = new CatalogProduct($productParams);

      $productsParams = array(
        'wildcard' => $this->dataDir . 'products/*.xml',
        'settings' => $this->settings,
        'i18n' => $this->setup->i18nExists(),
      );

      $getProductsParams = array('filter' => array('id' => $id));

      if ($this->setup->i18nExists()) {
        $getProductsParams['languages'] = true;
      }

      $products = new CatalogProducts($productsParams);
      $products = $products->getProducts($getProductsParams);

      // select the correct language
      if ($this->setup->i18nExists()) {
        $lang = return_i18n_languages();
        $lang = isset($products[$id]['language'][$lang[0]]) ? $lang[0]: return_i18n_default_language();
        $params['product'] = $products[$id]['language'][$lang];
      }

      $params['categories'] = new CatalogCategories(array(
        'wildcard' => $this->dataDir . 'categories/*.xml',
        'settings' => $this->settings,
      ));
    } else {
      // index
      $params['categories'] = new CatalogCategories(array(
        'wildcard' => $this->dataDir . 'categories/*.xml',
        'settings' => $this->settings,
      ));

      $getCategoriesParams = array();
      $categoryView = $this->generalSettings->get('categoryview');

      if ($categoryView == 'hierarchical') {
        $getCategoriesParams['hierarchical'] = true;
      } elseif ($categoryView == 'parents') {
        $getCategoriesParams['parentsOnly'] = true;
      } else {
        
      }

      $params['categories'] = $params['categories']->getCategories($getCategoriesParams);
    }

    return $params;
  }

  // function for removing empty strings from array
  private function filterEmpty($string) {
    return !(trim($string) == '');
  }

  // Parse the URL into its components and determine the action
  public function parseUrl() {
    // remove the site url (and 'index.php?')
    $url = str_replace(array($this->siteUrl, 'index.php?id='), '', $this->url);
    $this->path = explode('/', $url);
    $this->path = array_map('trim', $this->path);
    $this->path = array_filter($this->path, array($this, 'filterEmpty')); // remove empty values
    $this->path = array_values($this->path);                              // reindex array from 0

    // ensure that the rest of the query isn't included at the end of the path
    $this->path[count($this->path) - 1] = $this->removeQueryString($this->path[count($this->path) - 1]);

    // if the first element of the path is the catalog slug, we are in the catalog
    if ($this->generalSettings->get('slug') == $this->path[0]) {
      // plus, we don't need it as the first element of the path array
      $this->inCatalog = true;
      array_shift($this->path);

      // set the page type
      $this->pageType = count($this->path) ? $this->path[0] : $this->pageType;
    }
  }

  // Remove the query string from the end of the URL
  private function removeQueryString($string) {
    $explode = explode('?', $string);
    $string = $explode[0];

    $explode = explode('&', $string);
    $string = $explode[0];

    return $string;
  }
}

?>
