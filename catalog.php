<?php

/* immediately invoked function to initialize and register the plugin */
function gsCatalogInit() {
  // get plugin id and construct a root path
  $id   = basename(__FILE__, ".php");
  $root = GSPLUGINPATH . $id . '/';

  // load the main plugin class and get its name
  include($root . 'lib/catalogplugin.class.php');
  $classes = get_declared_classes();
  $class = end($classes);

  // first instantiate the plugin object
  $plugin = new $class($id, $root);

  // autoload the classes, then load the language files
  $plugin->autoload();
  $plugin->i18nMerge();

  // set the registration properties then register the plugin
  $plugin->register();
  $plugin->init();
} gsCatalogInit();

/* public functions */
// get categories
function catalog_get_categories() {
  // ...
}

// get category
function catalog_get_category() {
  // ...
}

// get products
function catalog_get_products() {
  // ...
}

// get product
function catalog_get_product() {
  // ...
}
// show shopping cart
function catalog_show_cart() {
  $cart = new CatalogCart(GSDATAOTHERPATH . basename(__FILE__, '.php') . '/cart.xml');
  if ($cart->getEnabled() == 'y') {
    eval('?>' . $cart->getCartTemplate());
  }
}

?>
