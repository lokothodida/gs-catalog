<?php

class CatalogFrontEnd {
  // == PROPERTIES ==
  private static $urlPath = array(),
                 $settings,
                 $templates,
                 $categories, $category,
                 $products, $product,
                 $pageTitle, $pageContent;

  // == METHODS ==
  public static function inCatalog() {
    self::$settings = CatalogSettings::getMainSettings();
    self::$urlPath = self::parseUrl();

    $withprettyurls    = strpos(self::getFullUrl(), self::getUrl()) !== false;
    $withoutprettyurls = strpos(self::getFullUrl(), self::getUrl(false)) !== false;
    return $withprettyurls || $withoutprettyurls;
  }

  private static function getFullUrl() {
    return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
  }

  private static function parseUrl() {
    $url = trim(str_replace(array(self::getUrl(), self::getUrl(false)), '', self::getFullUrl()));

    self::$urlPath = explode('/', $url);
    self::$urlPath = array_values(array_filter(self::$urlPath));
  }

  public static function init() {
    self::$templates = CatalogSettings::getThemeSettings();
    self::parseUrl();
  }

  public static function getUrl($prettyUrls = true) {
    global $SITEURL;
    $settings = CatalogSettings::getMainSettings();
    $url = $SITEURL . (!$prettyUrls ? 'index.php?id=' : null ) . $settings['slug'];
    return $url;
  }

  public static function getCategoryUrl($slug) {
    $url = self::getUrl() . '/category/' . $slug . '/';

    return $url;
  }

  public static function getProductUrl($slug) {
    $url = self::getUrl() . '/product/' . $slug . '/';

    return $url;
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
    //var_dump(self::getBreadcrumbsImpl($type, $slug));
    return self::getBreadcrumbsImpl($type, $slug);
    //return array_reverse(self::getBreadcrumbsImpl($type, $slug));
  }

  private static function getBreadcrumbsImpl($type, $slug) {
    if ($type == 'home') {
      return array(
        array(
          'title' => self::$settings['title'],
          'url' => self::getUrl())
      );
    } elseif ($type == 'category' && $slug) {
      self::$categories = isset(self::$categories) ? self::$categories : CatalogCategory::getCategories(array('key' => 'slug'));
      $category = self::$categories[$slug];

      $recursType = !isset(self::$categories[$category->get('parent')]) ? 'home' : 'category';
      $recursSlug = !isset(self::$categories[$category->get('parent')]) ? null : $category->get('parent');

      $crumbs   = self::getBreadcrumbs($recursType, $recursSlug);
      $crumbs[] = array(
        'title' => $category->get('title'),
        'url' => self::getCategoryUrl($category->get('slug')));

      return $crumbs;
    } elseif ($type == 'product' && $slug) {
      $product = CatalogProduct::getProduct($slug);
      $category = $product->get('categories');
      $recursType = isset($category[0]) ? 'category' : 'home';
      $recursSlug = isset($category[0]) ? $category[0] : null;

      $crumbs   = self::getBreadcrumbs($recursType, $recursSlug);
      $crumbs[] = array(
        'title' => $product->get('title'),
        'url' => self::getProductUrl($product->get('slug')));

      return $crumbs;
    } elseif ($type == 'search') {
      $crumbs   = self::getBreadcrumbs('home', null);
      $crumbs[] = array(
        'title' => i18n_r('catalog/SEARCH'),
        'url' => self::getSearchUrl());

      return $crumbs;
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
    if (self::isCategory() && isset(self::$urlPath[1])) {
      $category        = CatalogCategory::getCategory(self::$urlPath[1]);
      $products        = CatalogProduct::getProducts(
        array('category' => $category->get('slug')));
      self::$pageContent = self::evaluateTemplate(array(), 'header') . self::evaluateTemplate(
        array(
          'category' => $category,
          'products' => $products),
        'category') . self::evaluateTemplate(array(), 'footer');
      self::$pageTitle = $category->get('title');
    } elseif (self::isProduct() && isset(self::$urlPath[1])) {
      $product           = CatalogProduct::getProduct(self::$urlPath[1]);
      self::$pageTitle   = $product->get('title');
      self::$pageContent = self::evaluateTemplate(array(), 'header') . self::evaluateTemplate(
        array(
          'product' => $product),
        'product') . self::evaluateTemplate(array(), 'footer');
    } elseif(self::isSearch()) {
      
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
    return isset(self::$urlPath[0]) && self::$urlPath[0] == 'search';
  }

  private static function isHome() {
    return count(self::$urlPath) == 0;
  }

  private static function displayHome($format = null) {
    $categories = CatalogCategory::getCategories();

    $buffer = self::evaluateTemplate(array(), 'indexheader');

    foreach ($categories as $category) {
      $buffer .= self::evaluateTemplate(
        array('category' => $category),
        'indexcategories');
    }

    return $buffer . self::evaluateTemplate(array(), 'indexfooter');
  }

  private static function evaluateTemplate(array $variables = array(), $template) {
    ob_start();

    foreach ($variables as $name => $var) {
      ${$name} = $var;
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
}

?>