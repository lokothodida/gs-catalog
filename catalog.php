<?php

/* immediately invoked function to initialize and register the plugin */
function gsCatalogInit() {
  // get plugin id and construct a root path
  $id   = basename(__FILE__, ".php");
  $root = GSPLUGINPATH . $id . '/';

  // load the main plugin class and get its name
  include($root . 'lib/Plugin.php');
  $classes = get_declared_classes();
  $class = end($classes);
  $params = array(
    'id' => $id,
    'pluginDir' => $root,
  );

  // first instantiate the plugin object
  $plugin = new $class($params);

  // autoload the classes, then load the language files
  $plugin->autoload();
  $plugin->i18nMerge();

  // set the registration properties then register the plugin
  $plugin->register();
  $plugin->init();
} gsCatalogInit();

/* public functions */
// get categories
function catalog_get_categories($sortBy = false, $ascDesc = false) {
  $id = basename(__FILE__, '.php');
  $general = new CatalogGeneralOptions(GSDATAOTHERPATH . $id . '/general.xml');
  $categories = new CatalogCategories(GSDATAOTHERPATH . $id . '/categories/*.xml', $GLOBALS['SITEURL'] . $general->getBaseurl(), ((string) $general->getSlugged() == 'y'));

  return $categories->getCategories(false, array('sortBy' => $sortBy, 'ascDesc' => $ascDesc));
}

// get category
function catalog_get_category($categoryId) {
  $id = basename(__FILE__, '.php');
  $general = new CatalogGeneralOptions(GSDATAOTHERPATH . $id . '/general.xml');
  $category = new CatalogCategory(GSDATAOTHERPATH . $id . '/categories/' . $categoryId . '.xml', ((string) $general->getSlugged() == 'y'));

  return $category;
}

// get products
function catalog_get_products($options) {
  $id = basename(__FILE__, '.php');
  $general = new CatalogGeneralOptions(GSDATAOTHERPATH . $id . '/general.xml');
  $fields = new ProductFields(GSDATAOTHERPATH . $id . '/fields.xml');

  $products = new CatalogProducts(GSDATAOTHERPATH . $id . '/products/*.xml', ((string) $general->getSlugged() == 'y'));

  $category = isset($options['category']) ? $options['category'] : false;
  $search   = isset($options['search']) ? $options['search'] : array();
  $sort     = isset($options['sort']) ? $options['sort'] : array();
  $max      = isset($options['max']) ? $options['max'] : false;

  return $products->getProducts($category, $search, $sort, $max);
}

// get product
function catalog_get_product($productId) {
  $id = basename(__FILE__, '.php');
  $general = new CatalogGeneralOptions(GSDATAOTHERPATH . $id . '/general.xml');
  $product = new CatalogProduct(GSDATAOTHERPATH . $id . '/products/' . $productId . '.xml', ((string) $general->getSlugged() == 'y'));

  return $product;
}

// show shopping cart
function catalog_show_cart() {
  $cart = new CatalogCart(GSDATAOTHERPATH . basename(__FILE__, '.php') . '/cart.xml');
  if ($cart->getEnabled() == 'y') {
    eval('?>' . $cart->getCartTemplate());
  }
}

?>
