<?php

class CatalogFrontEnd {
  // == PROPERTIES ==
  private static $urlPath = array(),
                 $_get = false, $_post = false,
                 $settings,
                 $templates,
                 $categories, $category,
                 $products, $product,
                 $languages, $language,
                 $pageTitle, $pageContent, $pageTemplate;

  // == METHODS ==
  public static function inCatalog() {
    self::$settings = CatalogSettings::getMainSettings();
    self::parseUrl();

    $withprettyurls    = strpos(self::getFullUrl(), self::getUrl()) !== false;
    $withoutprettyurls = strpos(self::getFullUrl(), self::getUrl(false)) !== false;

    return $withprettyurls || $withoutprettyurls;
  }

  private static function getFullUrl() {
    return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  }

  private static function parseUrl() {
    $url = trim(str_replace(array(self::getUrl(), self::getUrl(false)), '', self::getFullUrl()));

    if (!self::$urlPath) {
      self::$urlPath = explode('/', $url);
      self::$urlPath = array_values(array_filter(self::$urlPath, 'self::removeEmptyElement'));

      if (isset(self::$urlPath[count(self::$urlPath) - 1])) {
        $tmp = explode('?', self::$urlPath[count(self::$urlPath) - 1]);
        $tmp = (count($tmp) == 1) ? explode('&', self::$urlPath[count(self::$urlPath) - 1], 2) : $tmp;

        self::$urlPath[count(self::$urlPath) - 1] = $tmp[0];

        if (isset($tmp[1])) {
          // remove everything past the '?' from the urlPath variable
          //unset(self::$urlPath[count(self::$urlPath) - 1]);
        }
      }

      self::$urlPath = array_values(array_filter(self::$urlPath, 'self::removeEmptyElement'));
    }

    if (!self::$_get) {
      self::$_get = isset($tmp[1]) ? self::parseGet($tmp[1]) : $_GET;
    }
  }

  private static function removeEmptyElement($elem) {
    return trim($elem) != '';
  }

  private static function parseGet($string) {
    $get = explode('&', $string);
    $_get = array();

    foreach ($get as $i => $g) {
      $tmp = explode('=', $g);
      $_get[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : null;
    }

    // setting the language
    if (isset($_get['setlang']) && function_exists('return_i18n_setlang_url')) {
      return_i18n_setlang_url($_get['setlang']);
    }

    return $_get;
  }
  
  public static function init() {
    self::$templates = CatalogSettings::getThemeSettings();
    self::$settings  = CatalogSettings::getMainSettings();
    self::$languages = self::$settings['languages'];
    self::$languages = self::getLanguage();
    self::$pageTemplate = self::$settings['template'];
  }

  private static function getLanguage() {
    if (function_exists('return_i18n_languages')) {
      return return_i18n_languages();
    } else {
      return false;
    }
  }

  public static function getDefaultLanguage() {
    if (function_exists('return_i18n_default_language')) {
      return return_i18n_default_language();
    } else {
      return false;
    }
  }

  public static function getCurrentLanguage() {
    $languages = self::getLanguage();

    return $languages ? $languages[0] : false;
  }

  public static function getUrl($prettyUrls = true) {
    global $SITEURL;
    $settings = CatalogSettings::getMainSettings();
    $url = $SITEURL . (!$prettyUrls ? 'index.php?id=' : null ) . $settings['slug'];
    return $url;
  }

  public static function getCategoryUrl($slug) {
    $settings = CatalogSettings::getMainSettings();
    $url = self::getUrl();

    if ($settings['slugged'] == 'y') {
      $url .= '/category/' . $slug . '/';
    } else {
      $isInIndex = CatalogCategory::isInIndex($slug);

      $url .= '/category/' . (($isInIndex !== false) ? $isInIndex : $slug)  . '/';
    }

    return $url;
  }

  public static function getProductUrl($slug) {
    $settings = CatalogSettings::getMainSettings();
    $url = self::getUrl();

    if ($settings['slugged'] == 'y') {
      $url .= '/product/' . $slug . '/';
    } else {
      $isInIndex = CatalogProduct::isInIndex($slug);

      $url .= '/product/' . (($isInIndex !== false) ? $isInIndex : $slug)  . '/';
    }

    return $url;
  }

  public static function getSearchUrl() {
    return self::getUrl() . '/search/';
  }

  public static function getPageType() {
    if (self::isSearch()) {
      return 'search';
    } elseif(self::isCategory()) {
      return 'category';
    } elseif (self::isProduct()) {
      return 'product';
    } elseif (self::isHome()) {
      return 'home';
    } else {
      return 'error';
    }
  }

  public static function getPageSlug() {
    if (!self::isHome() && isset(self::$urlPath[1])) {
      return self::$urlPath[1];
    } else {
      return null;
    }
  }

  public static function getBreadcrumbs($type, $slug) {
    return self::getBreadcrumbsImpl($type, $slug);
  }

  private static function getBreadcrumbsImpl($type, $slug) {
    if ($type == 'home') {
      return array(
        array(
          'title' => self::$settings['title'],
          'url' => self::getUrl())
      );
    } elseif ($type == 'category' && $slug && CatalogCategory::exists($slug)) {
      $lang = self::getCurrentLanguage();
      self::$categories = isset(self::$categories) ? self::$categories : CatalogCategory::getCategories(array('key' => 'slug'));
      $category = CatalogCategory::getCategory($slug, $lang);

      $recursType = !isset(self::$categories[$category->get('parent')]) ? 'home' : 'category';
      $recursSlug = !isset(self::$categories[$category->get('parent')]) ? null : $category->get('parent');

      $crumbs   = self::getBreadcrumbsImpl($recursType, $recursSlug);
      $crumbs[] = array(
        'title' => $category->get('title'),
        'url' => self::getCategoryUrl($category->get('slug')));

      return $crumbs;
    } elseif ($type == 'product' && $slug && CatalogProduct::exists($slug)) {
      $lang = self::getCurrentLanguage();
      $product = CatalogProduct::getProduct($slug, $lang);
      $category = $product->get('categories');
      $recursType = isset($category[0]) ? 'category' : 'home';
      $recursSlug = isset($category[0]) ? $category[0] : null;

      $crumbs   = self::getBreadcrumbsImpl($recursType, $recursSlug);
      $crumbs[] = array(
        'title' => $product->get('title'),
        'url' => self::getProductUrl($product->get('slug')));

      return $crumbs;
    } elseif ($type == 'search') {
      $crumbs   = self::getBreadcrumbsImpl('home', null);
      $crumbs[] = array(
        'title' => i18n_r('catalog/SEARCH'),
        'url' => self::getSearchUrl());

      return $crumbs;
    } else {
      return self::getBreadcrumbsImpl('home', null);
    }
  }

  public static function outputBreadcrumbs($delimiter, $type, $slug) {
    $crumbs = self::getBreadcrumbs($type, $slug);

    foreach ($crumbs as $i => $crumb) {
      ?>
      <a href="<?php echo $crumb['url']; ?>">
        <?php echo $crumb['title']; ?>
      </a>
      <?php if (($i + 1) != count($crumbs)) echo $delimiter; ?>
      <?php
    }
  }

  public static function setPageInformation() {
    $cart = CatalogSettings::getCartSettings();
    
    if (self::isCategory() && isset(self::$urlPath[1])) {
      $page = isset(self::$_get['p']) ? self::$_get['p'] : 1;
      $lang = self::getCurrentLanguage();
      $slug = self::$urlPath[1];

      if (CatalogCategory::exists($slug)) {
        $category        = CatalogCategory::getCategory($slug, $lang);
        $slug            = $category->get('slug');
        $products        = CatalogProduct::getProductsPaginated(
          array('category' => $slug, 'lang' => $lang),
          array(
            'itemsPerPage' => self::$settings['productsperpage'],
            'currentPage' => $page,
            'url' => self::getCategoryUrl($slug) . '?p=%page%'));
        self::$pageContent = self::evaluateTemplate(array(), 'header') . self::evaluateTemplate(
          array(
            'category' => $category,
            'products' => $products['results'],
            'pagination' => $products['results'] ? $products['navigation'] : null),
          'category') . self::evaluateTemplate(array(), 'footer');
        self::$pageTitle = $category->get('title');
      } else {
        self::$pageTitle = self::$settings['title'];
        self::$pageContent = self::$settings['categoryerror'];
      }
    } elseif (self::isProduct() && isset(self::$urlPath[1])) {
      $slug = self::$urlPath[1];

      if (CatalogProduct::exists($slug)) {
        $lang              = self::getCurrentLanguage();
        $product           = CatalogProduct::getProduct($slug, $lang);
        self::$pageTitle   = $product->get('title');
        self::$pageContent = self::evaluateTemplate(array(), 'header') . self::evaluateTemplate(
          array(
            'product' => $product,
            'cart' => $cart['template']),
          'product') . self::evaluateTemplate(array(), 'footer');
      } else {
        self::$pageTitle = self::$settings['title'];
        self::$pageContent = self::$settings['producterror'];
      }
    } elseif(self::isSearch()) {
      $page = isset(self::$_get['p']) ? self::$_get['p'] : 1;
      $params = array();

      if (isset(self::$_get['words'])) {
        $params['words'] = self::$_get['words'];
      } else {
        $params['words'] = '';
      }

      $results = CatalogProduct::searchProducts($params,
        array(
          'itemsPerPage' => self::$settings['productsperpage'],
          'currentPage' => $page,
          'url' => self::getSearchUrl() . '?words=' . $params['words'] . '&p=%page%'));
      self::$pageTitle   = i18n_r('catalog/SEARCH');
      self::$pageContent = self::evaluateTemplate(array(), 'header') . self::displaySearchResults($results) . self::evaluateTemplate(array(), 'footer');
    } elseif(self::isHome()) {
      self::$pageContent = self::displayHome();
      self::$pageTitle   = self::$settings['title'];
    } else {
      self::$pageTitle   = i18n_r('catalog/ERROR');
      self::$pageContent = self::evaluateTemplate(array(), 'header') . self::$settings['pageerror'] . self::evaluateTemplate(array(), 'footer');
    }
  }

  private static function isCategory() {
    return isset(self::$urlPath[0]) && self::$urlPath[0] == 'category';
  }

  private static function isProduct() {
    return isset(self::$urlPath[0]) && self::$urlPath[0] == 'product';
  }

  private static function isSearch() {
    return isset(self::$urlPath[0]) && (self::$urlPath[0] == 'search') && (self::$settings['internalsearch'] == 'y');
  }

  private static function isHome() {
    return count(self::$urlPath) == 0;
  }

  private static function displayHome($format = null) {
    $lang = self::getCurrentLanguage();
    $categories = CatalogCategory::getCategories(
      array('order' => 'custom', 'view' => self::$settings['categoryview'], 'lang' => $lang));

    $buffer = self::evaluateTemplate(array(), 'header') . self::evaluateTemplate(array(), 'indexheader');

    if (self::$settings['categoryview'] == 'nested') {
      $buffer .= self::displayNestedCategories($categories['parents'], $categories['children']);
    } else {
      foreach ($categories as $category) {
        $buffer .= self::evaluateTemplate(
          array('category' => $category),
          'indexcategories');
      }
    }

    return $buffer . self::evaluateTemplate(array(), 'indexfooter') . self::evaluateTemplate(array(), 'footer');
  }

  private static function displayNestedCategories($parents, $children) {
    $buffer = '<ul>';

    foreach ($parents as $parent) {
      $buffer .= '<li class="category">' . self::evaluateTemplate(
          array('category' => $parent),
          'indexcategories');
      
      if (isset($children[$parent->get('slug')])) {
        $buffer .= self::displayNestedCategories($children[$parent->get('slug')], $children);
      }

      $buffer .= '</li>';
    }

    return $buffer . '</ul>';
  }

  private static function showCart() {
    ob_start();

    global $SITEURL;
    $settings = CatalogSettings::getCartSettings();
    $simplecart = array(
      'cartColumns' => array(
        array('attr' => 'name', 'label' => $settings['labelname']),
        array('attr' => 'price', 'label' => $settings['labelprice']),
        array('attr' => 'quantity', 'label' => $settings['labelquantity']),
        array('attr' => 'total', 'label' => $settings['labeltotal']),
      ),
      'cartStyle' => $settings['cartstyle'],
      'checkout' => array(
        'type' => $settings['checkouttype'],
        'email' => $settings['checkoutemail']
      ),
      'currency' => $settings['currency'],
      'language' => $settings['language'],
      'shippingFlatRate' => $settings['shippingflatrate'],
      'shippingQuantityRate' => $settings['shippingquantityrate'],
      'shippingTotalRate' => $settings['shippingtotalrate'],
      'taxRate' => $settings['taxrate'],
      'taxShipping' => $settings['taxshipping']
    );
    ?>
    
    <script src="<?php echo $SITEURL; ?>/plugins/catalog/js/simpleCart.min.js"></script>
    <script>
      simpleCart(<?php echo json_encode($simplecart); ?>);
    </script>
    <?php
    
    $contents = ob_get_clean();

    return $contents;
  }

  private static function displaySearchResults(array $results = array()) {
    ob_start();
    $settings = CatalogSettings::getMainSettings();
    ?>
    <form method="get">
      <input type="text" name="words" />
      <input type="submit" value="<?php i18n('catalog/SEARCH'); ?>"/>
    </form>
    <div class="navigation">
      <?php if (isset($results['navigation'])) echo $results['navigation']; ?>
    </div>
    <div class="search-results">
      <?php
      if (!count($results['results'])) {
        echo $settings['noresults'];
      }

      foreach ($results['results'] as $result) {
        $item = (object) array();
        foreach ($result->getFields() as $field) {
          $item->{$field} = $result->get($field);
        }

        // i18n defaults
        $item->link = self::getProductUrl($result->get('slug'));

        echo self::evaluateTemplate(array('item' => $item), 'searchresult');
      }
      ?>
    </div>
    <div class="navigation">
      <?php if (isset($results['navigation'])) echo $results['navigation']; ?>
    </div>
    <?php
    $contents = ob_get_clean();

    return $contents;
  }

  private static function evaluateTemplate(array $variables = array(), $template) {
    ob_start();

    foreach ($variables as $name => $var) {
      ${$name} = $var;
    }

    if ($template == 'header') {
      echo self::showCart();
    }

    eval("?>" . self::$templates[$template]);

    $contents = ob_get_clean();

    return $contents;
  }

  public static function getPageTitle() {
    return self::$pageTitle;
  }

  public static function getPageContent() {
    return self::$pageContent;
  }

  public static function getPageTemplate() {
    return self::$pageTemplate;
  }
}

?>