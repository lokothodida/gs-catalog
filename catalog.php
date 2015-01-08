<?php

// get correct id for plugin
$thisfile = basename(__FILE__, '.php');

// register the plugin
register_plugin(
  $thisfile,
  'Catalog',
  '0.4',
  'Lawrence Okoth-Odida',
  'https://github.com/lokothodida',
  i18n_r('catalog/PLUGIN_DESCRIPTION'),
  'catalog',
  'catalog_admin'
);

// define constants
define('CATALOGPLUGINPATH', GSPLUGINPATH . 'catalog/');
define('CATALOGDATAPATH', GSDATAOTHERPATH . 'catalog/');
define('CATALOGADMINURL', 'load.php?id=catalog');

// language file
i18n_merge($thisfile) || i18n_merge($thisfile, 'en_US');

// includes
require_once(CATALOGPLUGINPATH . 'backend.class.php');
require_once(CATALOGPLUGINPATH . 'category.class.php');
require_once(CATALOGPLUGINPATH . 'product.class.php');
require_once(CATALOGPLUGINPATH . 'settings.class.php');

// activate filter
add_action('nav-tab', 'createNavTab', array('catalog', $thisfile, i18n_r('catalog/TAB'), 'overview'));
add_action('catalog-sidebar', 'createSideMenu', array($thisfile, i18n_r('catalog/OVERVIEW'), 'overview'));

if (defined('IN_GS')) {
  if (isset($_GET['categories'])) {
    add_action('catalog-sidebar', 'createSideMenu', array($thisfile, i18n_r('catalog/CREATE_CATEGORY'), 'categories&create'));
  }
}
add_action('catalog-sidebar', 'createSideMenu', array($thisfile, i18n_r('catalog/CATEGORIES'), 'categories'));
add_action('catalog-sidebar', 'createSideMenu', array($thisfile, i18n_r('catalog/PRODUCTS'), 'products'));
add_action('catalog-sidebar', 'createSideMenu', array($thisfile, i18n_r('catalog/SETTINGS'), 'settings'));

add_action('error-404', 'catalog_main');
add_action('index-post-dataindex', 'catalog_main');

add_action('search-index', 'CatalogBackEnd::searchIndex');
add_filter('search-item', 'CatalogBackEnd::searchItem');
add_filter('search-display', 'CatalogBackEnd::searchDisplay');

// scripts and styles
register_script('codemirror', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
register_style('codemirror-css', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/lib/codemirror.css','screen',FALSE);
register_style('codemirror-theme', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/theme/default.css','screen',FALSE);

// queue the scripts and styles
queue_script('codemirror', GSBACK);
queue_style('codemirror-css', GSBACK);
queue_style('codemirror-theme', GSBACK);

// == FUNCTIONS ==
function get_catalog_settings() {
}

// ==  BACKEND HOOKS ==


// == FRONTEND HOOKS ==
// get a category
function catalog_get_category($id) {
  return CatalogCategory::getCategory($id);
}

// get a product
function catalog_get_product($id) {
  return CatalogProduct::getProduct($id);
}

// get url of the main catalog
function catalog_get_url() {
  require_once(GSPLUGINPATH . 'catalog/frontend.class.php');
  return CatalogFrontEnd::getUrl();
}

// get url of a category
// @params $slug    slug of the category
function catalog_get_category_url($slug) {
  require_once(GSPLUGINPATH . 'catalog/frontend.class.php');
  return CatalogFrontEnd::getCategoryUrl($slug);
}

// get url of a product
// @params $slug    slug of the product
function catalog_get_product_url($slug) {
  require_once(GSPLUGINPATH . 'catalog/frontend.class.php');
  return CatalogFrontEnd::getProductUrl($slug);
}

// get slug of the current catalog page
function catalog_get_page_slug() {
  require_once(GSPLUGINPATH . 'catalog/frontend.class.php');
  return CatalogFrontEnd::getPageSlug();
}

// get type of the current catalog page
function catalog_get_page_type() {
  require_once(GSPLUGINPATH . 'catalog/frontend.class.php');
  return CatalogFrontEnd::getPageType();
}

// get breadcrumb trail of given page type and slug
// @params $type    page type
//         $slug    page slug
// @return array of breadcrumb trail, with breadcrumb title and url
function catalog_get_breadcrumbs($type = null, $slug = null) {
  require_once(GSPLUGINPATH . 'catalog/frontend.class.php');
  return CatalogFrontEnd::getBreadcrumbs($type, $slug);
}

// output html of breadcrumbs
function catalog_output_breadcrumbs($delimiter = '>', $type = null, $slug = null) {
  require_once(GSPLUGINPATH . 'catalog/frontend.class.php');
  $type = $type == null ? catalog_get_page_type() : $type;
  $slug = $slug == null ? catalog_get_page_slug() : $slug;
  return CatalogFrontEnd::outputBreadcrumbs($delimiter, $type, $slug);
}

// == BACKEND PAGES ==

function catalog_admin() {
  CatalogBackEnd::init();

  if (isset($_GET['categories'])) {
    if (isset($_GET['create'])) {
      CatalogBackEnd::displayCreateCategoryPage($_GET['create']);
    } elseif (isset($_GET['edit'])) {
      if (isset($_POST['editCategory'])) {
        CatalogBackEnd::editCategory($_GET['edit'], $_POST);
      }
      CatalogBackEnd::displayEditCategoryPage($_GET['edit']);
    } else {
      if (isset($_POST['createCategory'])) {
        CatalogBackEnd::createCategory($_POST);
      } elseif (isset($_POST['orderCategories'])) {
        CatalogBackEnd::orderCategories($_POST);
      } elseif (isset($_GET['delete'])) {
        CatalogBackEnd::deleteCategory($_GET['delete']);
      }

      CatalogBackEnd::displayViewCategoriesPage();
    }
  } elseif (isset($_GET['products'])) {
    if (isset($_GET['create'])) {
      CatalogBackEnd::displayCreateProductPage($_GET['create']);
    } elseif (isset($_GET['edit'])) {
      if (isset($_POST['editProduct'])) {
        CatalogBackEnd::editProduct($_GET['edit'], $_POST);
      }
      CatalogBackEnd::displayEditProductPage($_GET['edit']);
    } elseif (isset($_GET['delete'])) {
      CatalogBackEnd::displayDeleteProductPage($_GET['delete']);
    } else {
      if (isset($_POST['createProduct'])) {
        CatalogBackEnd::createProduct($_POST);
      }

      CatalogBackEnd::displayViewProductsPage();
    }
  } elseif (isset($_GET['settings'])) {
    if ($_GET['settings'] == 'theme') {
      if (isset($_POST['editTheme'])) {
        CatalogBackEnd::editSettingsTheme($_POST);
      }
      CatalogBackEnd::displaySettingsThemePage();
    } elseif ($_GET['settings'] == 'fields') {
      if (isset($_POST['editFields'])) {
        CatalogBackEnd::editSettingsFields($_POST);
      }
      CatalogBackEnd::displaySettingsFieldsPage();
    } elseif ($_GET['settings'] == 'cart') {
      if (isset($_POST['editCart'])) {
        CatalogBackEnd::editSettingsCart($_POST);
      }
      CatalogBackEnd::displaySettingsCartPage();
    } else {
      if (isset($_POST['editMain'])) {
        CatalogBackEnd::editSettingsMain($_POST);
      }
      CatalogBackEnd::displaySettingsMainPage();
    }
  } else {
    CatalogBackEnd::displayOverviewPage();
  }
}

// == FRONTEND PAGES ==

function catalog_main() {
  global $data_index;
  require_once(GSPLUGINPATH . 'catalog/frontend.class.php');

  // check if we are in the catalog then display the correct page
  $settings = CatalogSettings::getMainSettings();
  $slug = $settings['slug'];

  if (CatalogFrontEnd::inCatalog()) {
    CatalogFrontEnd::init();
    CatalogFrontEnd::setPageInformation();
    $data_index->title   = CatalogFrontEnd::getPageTitle();
    $data_index->content = CatalogFrontEnd::getPageContent();
  }

  return $data_index;
}

?>