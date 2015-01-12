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
    $status[] = self::copyDefault(CATALOGDATAPATH . 'category_index.txt', CATALOGPLUGINPATH . 'defaults/category_index.txt');
    $status[] = self::copyDefault(CATALOGDATAPATH . 'product_index.txt', CATALOGPLUGINPATH . 'defaults/product_index.txt');

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
      if (!is_dir($dir) && !is_dir($defaultDir)) {
        $succ[] = copy($defaultDir, $dir);
      } else {
        mkdir($dir, 0755, true);

        foreach (glob($defaultDir . '*.xml') as $file) {
          $succ[] = copy($file, $dir . '/' . basename($file));
        }
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

    return !in_array(false, $success, true);
  }

  public static function createCategoriesIndex() {
    return (bool) CatalogCategory::createCategoriesIndex();
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

  public static function createProductsIndex() {
    return (bool) CatalogProduct::createProductsIndex();
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
  
  public static function changeTheme($theme) {
    return CatalogSettings::changeTheme($theme);
  }

  // == DISPLAY PAGES ==
  public static function displayOverviewPage() {
    $categories = count(CatalogCategory::getCategories());
    $products = count(CatalogProduct::getProducts());
    include(CATALOGPLUGINPATH . 'overview.php');
  }

  public static function displayViewCategoriesPage() {
    $categories = CatalogCategory::getCategories(array('order' => 'custom', 'view' => 'nested'));
    $totalCategories = isset($categories['parents']) ? count($categories['parents']) + count($categories['children']) : count($categories);
    $action = CATALOGADMINURL . '&categories';
    include(CATALOGPLUGINPATH . 'viewcategories.php');
  }

  public static function displayCategoriesOnAdminPanel($categories) {
    if (isset($categories['parents']) && isset($categories['children'])) {
      self::displayNestedCategories(0, $categories['parents'], $categories['children']);
    } else {
      foreach ($categories as $category): ?>
      <tr>
        <td style="padding-left:4px">
          <input type="hidden" name="order[]" value="<?php echo $category->get('slug'); ?>"/>
          <a href="<?php echo CATALOGADMINURL . '&categories&edit=' . $category->get('slug'); ?>"><?php echo $category->get('title'); ?></a>
          <?php
            $availablelangs = $category->getAvailableLanguages();
            if ($availablelangs) {
          ?>
          (
            <?php foreach ($availablelangs as $language) : ?>
              <input type="hidden" name="order[]" value="<?php echo $category->get('slug'); ?>_<?php echo $language; ?>"/>
              <a href="<?php echo CATALOGADMINURL . '&categories&edit=' . $category->get('slug'); ?>_<?php echo $language; ?>"><?php echo $language; ?></a>
            <?php endforeach; ?>
          )
          <?php } ?>
        </td>
        <td class="secondarylink">
          <a href="<?php echo catalog_get_category_url($category->get('slug')); ?>" target="_blank">
            #
          </a>
        </td>
        <td class="delete">
          <a href="<?php echo CATALOGADMINURL . '&categories&delete=' . $category->get('slug'); ?>">
            x
          </a>
        </td>
      </tr>
      <?php endforeach;
    }
  }

  private static function displayNestedCategories($level, $parents, $children) {
    foreach ($parents as $category) : ?>
      <tr>
        <td style="padding-left:4px">
          <?php echo str_repeat('&nbsp;', $level*5); ?>
          <input type="hidden" name="order[]" value="<?php echo $category->get('slug'); ?>"/>
          <a href="<?php echo CATALOGADMINURL . '&categories&edit=' . $category->get('slug'); ?>"><?php echo $category->get('title'); ?></a>
          <?php
            $availablelangs = $category->getAvailableLanguages();
            if ($availablelangs) {
          ?>
          (
            <?php foreach ($availablelangs as $language) : ?>
              <input type="hidden" name="order[]" value="<?php echo $category->get('slug'); ?>_<?php echo $language; ?>"/>
              <a href="<?php echo CATALOGADMINURL . '&categories&edit=' . $category->get('slug'); ?>_<?php echo $language; ?>"><?php echo $language; ?></a>
            <?php endforeach; ?>
          )
          <?php } ?>
        </td>
        <td class="secondarylink">
          <a href="<?php echo catalog_get_category_url($category->get('slug')); ?>" target="_blank">
            #
          </a>
        </td>
        <td class="delete">
          <a href="<?php echo CATALOGADMINURL . '&categories&delete=' . $category->get('slug'); ?>">
            x
          </a>
        </td>
      </tr>
      <?php 
        if (isset($children[$category->get('slug')])) {
          self::displayNestedCategories($level + 1, $children[$category->get('slug')], $children);
        }
      ?>
    <?php endforeach;
  }

  public static function displayCreateCategoryPage() {
    self::displayEditCategoryPage(null);
  }

  public static function displayEditCategoryPage($id) {
    $settings   = CatalogSettings::getMainSettings();
    $category   = CatalogCategory::getCategory($id);
    $categories = CatalogCategory::getCategories(array('order' => 'custom'));
    $action     = ($id == null) ? CATALOGADMINURL . '&categories' : '';
    $title      = ($id == null) ? i18n_r('catalog/CREATE_CATEGORY') : i18n_r('catalog/EDIT_CATEGORY');
    $postName   = ($id == null) ? 'createCategory' : 'editCategory';
    $cancelUrl  = CATALOGADMINURL . '&categories';
    $languages  = $settings['languages'];
    $mode       = ($id == null) ? 'create' : 'edit';
    $deleteUrl  = $cancelUrl . '&delete=' . (isset($_GET['edit']) ? $_GET['edit'] : null);
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
    $settings   = CatalogSettings::getMainSettings();
    $product    = CatalogProduct::getProduct($id);
    $categories = CatalogCategory::getCategories(array('order' => 'custom'));
    $fields     = CatalogSettings::getFieldsSettings();
    $action     = ($id == null) ? CATALOGADMINURL . '&products' : '';
    $title      = ($id == null) ? i18n_r('catalog/CREATE_PRODUCT') : i18n_r('catalog/EDIT_PRODUCT');
    $postName   = ($id == null) ? 'createProduct' : 'editProduct';
    $cancelUrl  = CATALOGADMINURL . '&products';
    $languages  = $settings['languages'];
    $mode       = ($id == null) ? 'create' : 'edit';
    $deleteUrl  = $cancelUrl . '&delete=' . (isset($_GET['edit']) ? $_GET['edit'] : null);
    include(CATALOGPLUGINPATH . 'editproduct.php');
  }

  public static function displaySettingsMainPage() {
    $settings = CatalogSettings::getMainSettings();
    include(CATALOGPLUGINPATH . 'settingsmain.php');
  }

  public static function displaySettingsThemePage() {
    $settings = CatalogSettings::getThemeSettings();
    $themes = array();

    foreach (glob(CATALOGDATAPATH . 'theme/*.xml') as $file) {
      $themes[] = basename($file, '.xml');
    }

    include(CATALOGPLUGINPATH . 'settingstheme.php');
  }

  public static function displaySettingsFieldsPage() {
    $settings = $fields = CatalogSettings::getFieldsSettings();
    include(CATALOGPLUGINPATH . 'settingsfields.php');
  }

  public static function displayErrorMessage($msg, $succ, $undo = false) {
    if ($undo) $msg .= ' <a href="' . $undo . '">' . i18n_r('UNDO') . '</a>';
    ?>
    <script type="text/javascript">
      $(function() {
        $('div.bodycontent').before('<div class="<?php echo $succ ? 'updated' : 'error'; ?>" style="display:block;">'+
          <?php echo json_encode($msg); ?>+'</div>');
        $(".updated, .error").fadeOut(500).fadeIn(500);
      });
    </script>
    <?php 
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