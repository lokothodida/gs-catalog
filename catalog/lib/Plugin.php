<?php

/**
 * Register the plugin, initialize it and register its callbacks
 */
class CatalogPlugin {
  /** properties */
  private $id,
          $pluginDir,
          $dataDir,
          $adminUrl,
          $setup,
          $settings;

  /** methods */
  // Constructor
  public function __construct($params) {
    $this->id        = $params['id'];
    $this->pluginDir = $params['pluginDir'];
    $this->dataDir   = GSDATAOTHERPATH . $this->id . '/';
    $this->adminUrl  = 'load.php?id=' . $this->id;

    $this->autoload();
    $this->i18nMerge();

    $this->settings  = new CatalogSettings($this->dataDir);

    $this->register();
    $this->init();
  }

  // Load language files
  public function i18nMerge() {
    i18n_merge($this->id) || i18n_merge($this->id, 'en_US');
  }

  // Queue autoloader
  public function autoload() {
    spl_autoload_register(array($this, 'autoloader'));
  }

  // Autoloader
  private function autoloader($class) {
    // load the class file if it exists
    if (file_exists($file = $this->pluginDir . '/lib/' . $class . '.php')) {
      include($file);
    }
  }

  // Register the plugin
  public function register() {
    // register the plugin
    register_plugin(
      $this->id,                             // plugin id
      i18n_r($this->id . '/PLUGIN_TITLE'),   // title
      '0.3',                                 // version
      'Lawrence Okoth-Odida',                // author
      'http://github.com/lokothodida',       // website url
      i18n_r($this->id . '/PLUGIN_DESC'),    // description
      $this->id,                             // plugin tab
      array($this, 'displayAdminPanel')      // administration panel
    );
  }

  // Plugin initialization
  public function init() {
    // set up the parameters
    $params = array(
      'id' => $this->id,
      'pluginDir' => $this->pluginDir,
    );

    // run the methods from the setup object
    $setup = new CatalogSetup($params);
    $setup->makeDirectories();
    $setup->registerFrontEnd($this);
    $setup->registerBackEnd($this);
    $this->setup = $setup;
  }

  // Catalog front-end
  public function displayCatalog() {
    // set up the router
    $routerParams = array(
      'id'        => $this->id,
      'url'       => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
      'siteUrl'   => $GLOBALS['SITEURL'],
      'settings'  => $this->settings,
      'pluginDir' => $this->pluginDir,
      'dataDir'   => $this->dataDir,
      'setup'     => $this->setup,
    );
    $router = new CatalogRouter($routerParams);

    // set up the renderer
    $renderParams = array(
      'data'     => $GLOBALS['data_index'],
      'settings' => $this->settings,
      'router'   => $router,
    );

    // render the page
    $catalog = new CatalogRender($renderParams);
    $catalog->displayPage();
  }

  // Administration panel
  public function displayAdminPanel() {
    $params = array(
      'id'        => $this->id,
      'settings'  => $this->settings,
      'pluginDir' => $this->pluginDir,
      'dataDir'   => $this->dataDir,
      'adminUrl'  => $this->adminUrl,
      'setup'     => $this->setup,
    );

    $adminPanel = new CatalogAdminPanel($params);
    $adminPanel->displayPage();
  }
}

?>
