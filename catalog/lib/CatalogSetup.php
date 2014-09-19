<?php

/**
 * Set up/initialize the plugin
 */
class CatalogSetup {
  /** properties */
  private $id,
          $pluginDir;

  /** methods */
  public function __construct(array $params) {
    $this->id        = $params['id'];
    $this->pluginDir = $params['pluginDir'];
  }

  // Make all of the directories in the /data/other folder with correct permissions
  public function makeDirectories() {
    $dirs = array('', '/products', 'categories', 'themes', 'tmp');
    $defaults = array('general', 'fields', 'themes');
    
    // make all the directories
    foreach ($dirs as $dir) {
      $file = GSDATAOTHERPATH . $this->id . '/' . $dir;
      if (!file_exists($file)) {
        mkdir($file, 0755, true);
      }
    }

    // data/other/catalog/.htaccess (deny all)
    if (!file_exists(GSDATAOTHERPATH . $this->id . '/.htaccess')) {
      file_put_contents(GSDATAOTHERPATH . $this->id . '/.htaccess', 'Deny from all');
    }

    // data/other/catalog/tmp/.htaccess (allow all)
    if (!file_exists(GSDATAOTHERPATH . $this->id . '/tmp/.htaccess')) {
      file_put_contents(GSDATAOTHERPATH . $this->id . '/tmp/.htaccess', 'Allow from all');
    }

    // copy defaults
    $this->copyDefaultFiles();
  }

  // Copy the default files to /data/other from the catalog/assets folder
  private function copyDefaultFiles($overwrite = false) {
    $defaults = array('general', 'fields', 'themes', 'identifiers', 'cart');
    $success = array();
    foreach ($defaults as $default) {
      $def = $this->pluginDir . '/assets/defaults/' . $default . '.xml';
      $f = GSDATAOTHERPATH . $this->id . '/' . $default . '.xml';

      if (!file_exists($f) || $overwrite) {
        $success[] = copy($def, $f);
      }

      if ($default == 'themes') {
        // copy the basic themes available
        $themes = glob($this->pluginDir . '/assets/defaults/themes/*.xml');

        foreach ($themes as $theme) {
          $themef = GSDATAOTHERPATH . $this->id . '/themes/' . basename($theme);
          if (!file_exists($themef) || $overwrite) {
            $success[] = copy($theme, $themef);
          }
        }
      }
    }

    return $success;
  }

  // Adding filters
  public function addFilter($name, $method) {
    add_filter($name, array($this, $method));
  }
  
  // Adding actions
  public function addAction($hook, $method, $args = array()) {
    add_action($hook, array($this, $method), $args);
  }
  
  // Adding sidebars
  public function addSidebar($name, $action = false, $page = null, $flag = true) {
    if ($page == null) $page = $this->id;
    add_action($page . '-sidebar','createSideMenu', array($this->id, i18n_r($name), $action, $flag));
  }

  public function registerFrontEnd($plugin) {
    // register the catalog
    add_action('index-post-dataindex', array($plugin, 'displayCatalog'));

    // i18n search actions
    $i18nParams = array();
    $i18n = new CatalogI18nSearch($i18nParams);

    //add_action('search-index', array($i18n, 'searchIndex'));
    //add_filter('search-item', array($i18n, 'searchItem'));
    //add_filter('search-display', array($i18n, 'searchDisplay'));

    // register and queue the javascript files
    register_script('simpleCart', $GLOBALS['SITEURL'] . '/plugins/' . $this->id . '/assets/js/simpleCart.min.js', '1.0', FALSE);
    queue_script('jquery', GSFRONT); 
    queue_script('simpleCart', GSFRONT);
  }

  public function registerBackEnd($plugin) {
    add_action('nav-tab', 'createNavTab', array($this->id, $this->id, i18n_r($this->id . '/PLUGIN_TITLE'), 'home'));

    // sidebar links
    $this->addSideBar($this->id . '/HOME', 'home');
    $this->addSideBar($this->id . '/CATEGORIES', 'categories');

    if (isset($_GET['id']) && ($_GET['id'] == $this->id)) {
      if (!empty($_GET['categories']) && $_GET['categories'] != 'create') {
        $this->addSidebar($this->id . '/EDIT_CATEGORY', 'categories=' . $_GET['categories']);
        $this->addSideBar($this->id . '/CREATE_CATEGORY', 'categories=create');
      } elseif (!empty($_GET['categories'])) {
        
      }
    }

    $this->addSideBar($this->id . '/PRODUCTS', 'products');
    $this->addSideBar('SETTINGS', 'settings');

    // the below registers the codemirror css and javascript files
    register_script('codemirror', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
    register_style('codemirror-css', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/lib/codemirror.css','screen',FALSE);
    register_style('codemirror-theme', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/theme/default.css','screen',FALSE);

    // queue the scripts and styles
    queue_script('codemirror', GSBACK);
    queue_style('codemirror-css', GSBACK);
    queue_style('codemirror-theme', GSBACK);
  }

  public function registerI18nSearch() {
  }

  public function registerScripts() {
  }

  public function registerStyles() {
  }

  // Slugification
  // http://gilbert.pellegrom.me/php-quick-convert-string-to-slug/
  public function toSlug($string) {
    return rtrim(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->transliterate($string)))), '-');
  }
  
  // Transliteration
  public function transliterate($string) {
    if (isset($GLOBALS['i18n']['TRANSLITERATION']) && is_array($translit = $GLOBALS['i18n']['TRANSLITERATION']) && count($translit > 0)) {
      $string =  str_replace(array_keys($translit), array_values($translit), $string);
    }
    return $string;
  }

  // Check i18n exists
  public function i18nExists() {
    return function_exists('return_i18n_default_language');
  }

  // Reset the i18n search index
  public function deleteI18nSearchIndex() {
    if (function_exists('delete_i18n_search_index')) delete_i18n_search_index();
  }

  // Creation/editing functions
  
}

?>
