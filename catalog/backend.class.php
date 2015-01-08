<?php

class CatalogBackEnd {
  // == PROPERTIES ==
  // == ADMIN FUNCTIONS ==
  public static function init() {
    $status = array();

    // create the correct folder structure and copy defaults
    $status[] = self::copyDefault(CATALOGDATAPATH . 'categories/', CATALOGPLUGINPATH . 'defaults/categories/');
    $status[] = self::copyDefault(CATALOGDATAPATH . 'products/', CATALOGPLUGINPATH . 'defaults/products/');
    $status[] = self::copyDefault(CATALOGDATAPATH . 'settings/', CATALOGPLUGINPATH . 'defaults/settings/');
    $status[] = self::copyDefault(CATALOGDATAPATH . 'theme/', CATALOGPLUGINPATH . 'defaults/theme/');

    return $status;
  }

  public static function i18nExists() {
    return function_exists('i18n_init');
  }

  // http://gilbert.pellegrom.me/php-quick-convert-string-to-slug/
  public static function strtoslug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
  }

  private static function copyDefault($dir, $defaultDir) {
    $succ = array();

    if (!file_exists($dir) && file_exists($defaultDir)) {
      mkdir($dir, 0755, true);

      foreach (glob($defaultDir . '*.xml') as $file) {
        $succ[] = copy($file, $dir . '/' . basename($file));
      }

      return $succ;
    } else {
      return false;
    }
  }

  private static function isDirEmpty($dir) {
    return empty(glob($dir));
  }

  private static function getCategories($order = 'asc', $key = null) {
    $categories = CatalogCategory::getCategories(array('order' => $order, 'key' => $key));
    return $categories;
  }

  public static function createCategory(array $data) {
    return CatalogCategory::create($data);
  }

  public static function editCategory($id, $data) {
    return CatalogCategory::edit($id, $data);
  }

  public static function deleteCategory($id) {
    return CatalogCategory::delete($id);
  }

  public static function orderCategories(array $data) {
    $success = array();

    foreach ($data['order'] as $i => $slug) {
      $file = CATALOGDATAPATH . 'categories/' . $slug . '.xml';
      $xml = new SimpleXMLExtended($file, 0, true);
      $xml->order = $i;
      $success[$file] = $xml->saveXML($file);
    }
  }

  private static function getProducts() {
    $products = CatalogProduct::getProducts();
    return $products;
  }

  public static function createProduct(array $data) {
    return CatalogProduct::create($data);
  }

  public static function editProduct($id, array $data) {
    return CatalogProduct::edit($id, $data);
  }

  public static function deleteProduct($id) {
    return CatalogProduct::delete($id);
  }

  public static function editSettingsMain(array $data) {
    return CatalogSettings::editSettingsMain($data);
  }

  public static function editSettingsFields(array $data) {
    return CatalogSettings::editSettingsFields($data);
  }

  public static function editSettingsTheme(array $data) {
    return CatalogSettings::editSettingsTheme($data);
  }

  // == DISPLAY PAGES ==
  public static function displayOverviewPage() {
    include(CATALOGPLUGINPATH . 'overview.php');
  }

  public static function displayViewCategoriesPage() {
    $categories = self::getCategories($order = 'custom');
    include(CATALOGPLUGINPATH . 'viewcategories.php');
  }

  public static function displayCreateCategoryPage() {
    self::displayEditCategoryPage(null);
  }

  public static function displayEditCategoryPage($id) {
    $category   = CatalogCategory::getCategory($id);
    $categories = CatalogCategory::getCategories($order = 'custom');
    $action     = ($id == null) ? CATALOGADMINURL . '&categories' : '';
    $title      = ($id == null) ? i18n_r('catalog/CREATE_CATEGORY') : i18n_r('catalog/EDIT_CATEGORY');
    $postName   = ($id == null) ? 'createCategory' : 'editCategory';
    $cancelUrl  = CATALOGADMINURL . '&categories';
    include(CATALOGPLUGINPATH . 'editcategory.php');
  }

  public static function displayViewProductsPage() {
    $products   = self::getProducts();
    $categories = self::getCategories(null, 'slug');
    include(CATALOGPLUGINPATH . 'viewproducts.php');
  }

  public static function displayCreateProductPage() {
    self::displayEditProductPage(null);
  }

  public static function displayEditProductPage($id) {
    $product    = CatalogProduct::getProduct($id);
    $categories = CatalogCategory::getCategories($order = 'custom');
    $fields     = CatalogSettings::getFieldsSettings();
    $action     = ($id == null) ? CATALOGADMINURL . '&products' : '';
    $title      = ($id == null) ? i18n_r('catalog/CREATE_PRODUCT') : i18n_r('catalog/EDIT_PRODUCT');
    $postName   = ($id == null) ? 'createProduct' : 'editProduct';
    $cancelUrl  = CATALOGADMINURL . '&products';
    include(CATALOGPLUGINPATH . 'editproduct.php');
  }

  public static function displaySettingsMainPage() {
    $settings = CatalogSettings::getMainSettings();
    include(CATALOGPLUGINPATH . 'settingsmain.php');
  }

  public static function displaySettingsThemePage() {
    $settings = CatalogSettings::getThemeSettings();
    include(CATALOGPLUGINPATH . 'settingstheme.php');
  }

  public static function displaySettingsFieldsPage() {
    $settings = $fields = CatalogSettings::getFieldsSettings();
    include(CATALOGPLUGINPATH . 'settingsfields.php');
  }

  public static function searchIndex() {
    $products = CatalogProduct::getProducts();
    $fields = CatalogSettings::getFieldsSettings();

    foreach ($products as $product) {
      $content = '';

      foreach ($fields as $field) {
        $content .= $product->get($field['name']) . ' ';
      }

      i18n_search_index_item(
        'catalog:' . $product->get('slug'),
        null,
        time(), time(),
        null,
        $product->get('title'),
        $content);
    }
  }
  
  public static function searchItem($id, $language, $creDate, $pubDate, $score) {
    if (substr($id, 0, 8) == 'catalog:') {
      return CatalogProduct::getI18nSearchResultItem($id, $language, $creDate, $pubDate, $score);
    } else {
      return null;
    }
  }
  
  public static function searchDisplay($item, $showLanguage, $showDate, $dateFormat, $numWords) {
    if (substr($item->id, 0, 8) == 'catalog:') {
      $theme = CatalogSettings::getThemeSettings();
      $product = $item->CatalogProduct;

      eval('?>' . $theme['searchresult']);
      return true;
    } else {
      return false;
    }
  }
}

?>