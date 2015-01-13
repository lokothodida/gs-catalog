<?php

require_once(CATALOGPLUGINPATH . 'data.class.php');

class CatalogProduct extends CatalogData {
  // == PROPERTIES ==
  protected static $classname = __CLASS__,
                   $noExist = 'PRODUCT_DOES_NOT_EXIST',
                   $folder = 'products',
                   $type = 'product',
                   $arrayFilterCategory;

  // == STATIC (SINGLETON) METHODS ==
  public static function getProduct($id, $lang = false) {
    return parent::getItem($id, $lang);
  }

  public static function getProducts(array $params = array()) {
    $products = parent::getItems($params);

    if (isset($params['lang'])) {
      foreach ($products as $i => $product) {
        $langs = $product->getAvailableLanguages();
        if (in_array($params['lang'], $langs)) {
          $pro = self::getProduct($product->getId(), $params['lang']);
          $products[$i] = $pro;
        }
      }
    }

    if (isset($params['category'])) {
      self::$arrayFilterCategory = $params['category'];
      $products = array_filter($products, 'self::filterByCategory');
    }

    return $products;
  }

  public static function getProductsPaginated($productParams, $paginateParams) {
    $products = self::getProducts($productParams);
    $paginated = parent::getItemsPaginated($products, $paginateParams);

    return $paginated;
  }

  public static function searchProducts(array $params = array(), array $paginate = array()) {
    require_once(CATALOGPLUGINPATH . 'paginate.class.php');

    $products = static::getProducts();
    $results = array();

    foreach ($products as $i => $product) {
      $contents = '';

      foreach ($product->getFields() as $field) {
        $content = $product->get($field);

        if (is_array($content)) {
          $content = implode(' ', $content);
        }

        $contents .= $content . ' ';
      }

      $contents = strtolower($contents);
      
      if (isset($params['tags'])) {
        $tags = explode(",", $params['tags']);
        $tags = array_map('trim', $tags);
        $in = true;
        $i = 0;

        while ($in && ($i < count($tags))) {
          $in = $in && (strpos($contents, $tags[$i]) !== false);
          $i++;
        }

        if ($in) {
          $results[] = $product;
        }
      } elseif (isset($params['words'])) {
        $words = strtolower($params['words']);

        if (!empty($words) && strpos($contents, $words) !== false) {
          $results[] = $product;
        }
      }
    }

    if ($paginate) {
      $ap = new ArrayPaginate($results);
      $results = $ap->paginate($paginate);
    } else {
      $results = array('results' => $results);
    }

    return $results;
  }

  protected static function filterByCategory($product) {
    return in_array(self::$arrayFilterCategory, $product->get('categories'));
  }

  public static function createProductsIndex() {
    return parent::createItemIndex();
  }

  protected static function updateProductIndex($slug) {
    return parent::updateItemIndex($slug);
  }

  public static function getProductIndex($refresh = false) {
    return parent::getItemIndex($refresh);
  }

  public static function isInIndex($slug) {
    $key = false;

    foreach (self::getProductIndex() as $index) {
      $tmp = explode(' ', $index);
      if (isset($tmp[1]) && trim($tmp[1]) == $slug) {
        $key = (int) $tmp[0];
      }
    }

    return $key;
  }

  protected static function modifyXml($xml, $data) {
    $data['categories'] = isset($data['categories']) ? implode("\n", $data['categories']) : null;
    return parent::modifyXml($xml, $data);
  }

  public static function getI18nSearchResultItem($id, $language, $creDate, $pubDate, $score) {
    require_once(CATALOGPLUGINPATH . 'searchresult.class.php');
    return new I18nSearchCatalogResultItem($id, $language, $creDate, $pubDate, $score);
  }

  // == DYNAMIC (OBJECT) METHODS ==
  protected function getData($filename) {
    parent::getData($filename);

    $this->data['categories'] = isset($this->data['categories']) ? explode("\n", $this->data['categories']) : array();
    $this->data['categories'] = array_filter(array_map('trim', $this->data['categories']));
    $this->data['slug'] = basename($filename, '.xml');
  }
}

?>