<?php

class CatalogProduct {
  // == PROPERTIES ==
  private $data = array();
  private static $arrayFilterCategory;

  // == METHODS ==
  public function __construct($filename) {
    if (file_exists($filename)) {
      $this->getData($filename);
    } elseif ($filename != null) {
      throw new Exception('PRODUCTDOESNTEXIST');
    }
  }

  private function getData($filename) {
    $xml = new SimpleXMLExtended($filename, 0, true);

    foreach ($xml as $field => $value) {
      $this->data[$field] = $this->getXmlDataField($xml, $field);
    }

    $this->data['categories'] = isset($this->data['categories']) ? explode("\n", $this->data['categories']) : array();
    $this->data['categories'] = array_filter(array_map('trim', $this->data['categories']));
    $this->data['slug'] = basename($filename, '.xml');
  }

  private function getXmlDataField($xml, $field) {
    return (string) ($xml->{$field});
  }

  public static function getProducts(array $params = array()) {
    $products = array();

    foreach (glob(CATALOGDATAPATH . 'products/*.xml') as $file) {
      $products[] = new CatalogProduct($file);
    }

    if (isset($params['category'])) {
      self::$arrayFilterCategory = $params['category'];
      $products = array_filter($products, 'self::filterByCategory');
    }

    return $products;
  }

  private static function filterByCategory($product) {
    return in_array(self::$arrayFilterCategory, $product->get('categories'));
  }

  public static function create($data) {
    $slug = CatalogBackEnd::strtoslug($data['title']);
    $filename = isset($slug) ? CATALOGDATAPATH . 'products/' . $slug . '.xml': null;

    if (!file_exists($filename) && !empty($filename)) {
      $xml = new SimpleXMLExtended('<product/>');
      self::modifyXml($xml, $data);
      $succ = (bool) $xml->saveXML($filename);
      self::deleteI18nSearchIndex();

      return $succ;
    } else {
      return false;
    }
  }

  public static function edit($id, $data) {
    $filename = CATALOGDATAPATH . 'products/' . $id . '.xml';

    if (file_exists($filename)) {
      $xml = new SimpleXMLExtended('<product/>');
      self::modifyXml($xml, $data);
      $succ = (bool) $xml->saveXML($filename);
      self::deleteI18nSearchIndex();

      return $succ;
    } else {
      return false;
    }
  }

  public static function deleteI18nSearchIndex() {
    return (function_exists('delete_i18n_search_index')) ? delete_i18n_search_index() : false;
  }
  
  public static function delete($id) {
    if (file_exist($file = CATALOGDATAPATH . 'products/' . $id . '.xml')) {
      self::deleteI18nSearchIndex();
      return unlink($file);
    } else {
      return false;
    }
  }

  private static function modifyXml($xml, $data) {
    $data['categories'] = isset($data['categories']) ? implode("\n", $data['categories']) : null;
    foreach ($data as $field => $value) {
      self::setXmlDataField($xml, $field, $value);
    }
  }

  public static function setXmlDataField($xml, $field, $value) {
    $xml->{$field} = null;
    $xml->{$field}->addCData($value);
    return $xml;
  }

  public static function getProduct($id) {
    $filename = ($id == null) ? null : CATALOGDATAPATH . 'products/' . $id . '.xml';

    return new CatalogProduct($filename);
  }

  public function get($field) {
    return isset($this->data[$field]) ? $this->data[$field] : null;
  }

  public static function getI18nSearchResultItem($id, $language, $creDate, $pubDate, $score) {
    require_once(CATALOGPLUGINPATH . 'searchresult.class.php');
    return new I18nSearchCatalogResultItem($id, $language, $creDate, $pubDate, $score);
  }
}

?>