<?php

// include main class
include(basename(__FILE__, '.php') . '/lib/catalogplugin.class.php');

// first instantiate the plugin object
$catalogplugin = new CatalogPlugin(basename(__FILE__, '.php'));

// autoload the classes, then load the language files
$catalogplugin->autoload();
$catalogplugin->i18nMerge();

// set the registration properties then register the plugin
$catalogplugin->title    = 'PLUGIN_TITLE';
$catalogplugin->version  = '0.1';
$catalogplugin->author   = 'Lawrence Okoth-Odida';
$catalogplugin->website  = 'http://www.lokida.co.uk/';
$catalogplugin->desc     = 'PLUGIN_DESC';
$catalogplugin->admin    = 'back';
$catalogplugin->register();
$catalogplugin->init();


function catalog_show_cart() {
  $cart = new CatalogCart(GSDATAOTHERPATH . basename(__FILE__, '.php') . '/cart.xml');
  if ($cart->getEnabled() == 'y') {
    eval('?>' . $cart->getCartTemplate());
  }
}

?>
