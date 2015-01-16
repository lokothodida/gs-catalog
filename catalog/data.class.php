<?php

abstract class CatalogData {
  // Class for common functions to CatalogCategory and CatalogProduct
  // == PROPERTIES ==
  protected $data = array();
  protected static $noExist = 'NOEXIST';
  protected static $classname = __CLASS__,
                   $folder = '',
                   $type = '',
                   $index;

  // == STATIC (SINGLETON) METHODS ==
  public static function getItem($id, $lang = false) {
    if (is_numeric($id)) {
      $id = static::hasSlug($id);
    }

    $filename     = ($id == null) ? null : CATALOGDATAPATH . static::$folder . '/' . $id . '.xml';
    $filenamelang = ($id == null) ? null : CATALOGDATAPATH . static::$folder . '/' . $id . '_' . $lang . '.xml';
    $file         = file_exists($filenamelang) ? $filenamelang : $filename;

    return new CatalogProduct($file);
  }

  public static function getItems(array $params = array()) {
    $items = array();
    $files = glob(CATALOGDATAPATH . static::$folder . '/*.xml');

    foreach ($files as $file) {
      $tmp = explode('_', basename($file, '.xml'));
      if (!isset($tmp[1])) {
        $class = static::$classname;
        $items[] = new $class($file);
      }
    }

    return $items;
  }

  public static function getItemsPaginated($items, $paginateParams) {
    require_once(CATALOGPLUGINPATH . 'paginate.class.php');
    $ap = new ArrayPaginate($items);
    return $ap->paginate($paginateParams);
  }

  public static function exists($slug) {
    $slug = is_numeric($slug) ? static::hasSlug($slug) : $slug;

    return file_exists(CATALOGDATAPATH . static::$folder . '/' . $slug . '.xml');
  }

  public static function create($data) {
    $folder = CATALOGDATAPATH . static::$folder . '/';

    if (!file_exists($folder)) {
      mkdir($folder, 0755, true);
    }

    $slug = CatalogBackEnd::strtoslug($data['title']);

    $filename = (isset($slug) ? $folder . $slug . (!empty($data['language']) ? '_' . $data['language'] : null) . '.xml': null);

    if (!file_exists($filename) && !empty($filename)) {
      $xml = new SimpleXMLExtended('<' . static::$type . '/>');
      static::modifyXml($xml, $data);
      $succ = (bool) $xml->saveXML($filename);
      static::updateItemIndex($slug . (!empty($data['language']) ? '_' . $data['language'] : null));
      static::deleteI18nSearchIndex();

      return $succ;
    } else {
      return false;
    }
  }

  public static function createItemIndex() {
    $currentindex = static::getItemIndex();
    $items = static::getItems();

    foreach ($items as $product) {
      $slug = $product->get('slug');
      $inIndex = array_search($slug, $currentindex);
      $matches = preg_grep('/^[0-9]+ ' . $slug . '+/i', $currentindex);

      if (!$matches) {
        $i = 0;
        if ($currentindex) {
          $tmp = explode(' ', $currentindex[count($currentindex) - 1]);
          $i = $tmp[0] + 1;
        }
        $currentindex[] = $i . ' ' . $slug; 
      }
    }

    return static::saveItemIndex($currentindex);
  }

  protected static function saveItemIndex(array $index) {
    $filename = CATALOGDATAPATH . static::$type . '_index.txt';
    $contents = implode("\n", $index);

    return (bool) file_put_contents($filename, $contents);
  }

  protected static function updateItemIndex($slug) {
    $filename = CATALOGDATAPATH . static::$type . '_index.txt';
    $index = file_get_contents($filename);

    $lines = explode("\n", $index);
    $lines = array_filter($lines);
    $lastline = end($lines);

    $tmp = explode(" ", $lastline);

    $index .= "\n" . ((isset($tmp[0]) && is_numeric($tmp[0])) ? $tmp[0] + 1 : 0) . ' ' . $slug . "\n";

    return (bool) file_put_contents($filename, $index);
  }

  public static function getItemIndex($refresh = false) {
    if ($refresh || !isset(static::$index)) {
      $filename = CATALOGDATAPATH . static::$type . '_index.txt';
      $index = file_get_contents($filename);
      $items = array();

      $lines = explode("\n", $index);
      $lines = array_filter($lines);

      foreach ($lines as $line) {
        $tmp = explode(" ", $line);
        $items[(int) $tmp[0]] = $tmp[0] . ' ' . trim($tmp[1]);
      }

      static::$index = $items;
    }
    
    return static::$index;
  }

  public static function isInIndex($slug) {
    $key = false;

    foreach (static::getItemIndex() as $index) {
      $tmp = explode(' ', $index);
      if (isset($tmp[1]) && trim($tmp[1]) == $slug) {
        $key = (int) $tmp[0];
      }
    }

    return $key;
  }

  public static function hasSlug($i) {
    $succ = false;

    foreach (static::getItemIndex() as $index) {
      $tmp = explode(' ', $index);

      if (isset($tmp[1]) && ((int) $tmp[0]) == $i) {
        $succ = trim($tmp[1]);

      }
    }

    return $succ;
  }

  public static function edit($id, $data) {
    $filename = CATALOGDATAPATH . static::$folder . '/' . $id . '.xml';

    if (file_exists($filename)) {
      $xml = new SimpleXMLExtended('<' . static::$type . '/>');
      static::modifyXml($xml, $data);
      $succ = (bool) $xml->saveXML($filename);
      static::deleteI18nSearchIndex();

      return $succ;
    } else {
      return false;
    }
  }

  public static function deleteI18nSearchIndex() {
    return (function_exists('delete_i18n_search_index')) ? delete_i18n_search_index() : false;
  }
  
  public static function delete($id) {
    if (file_exists($file = CATALOGDATAPATH . static::$folder . '/' . $id . '.xml')) {
      static::deleteI18nSearchIndex();
      return unlink($file);
    } else {
      return false;
    }
  }

  protected static function modifyXml($xml, $data) {
    foreach ($data as $field => $value) {
      static::setXmlDataField($xml, $field, $value);
    }
  }

  public static function setXmlDataField($xml, $field, $value) {
    $xml->{$field} = null;
    $xml->{$field}->addCData($value);
    return $xml;
  }

  // == DYNAMIC (OBJECT) METHODS ==
  public function __construct($filename) {
    if (file_exists($filename)) {
      $this->getData($filename);
    } elseif ($filename != null) {
      throw new Exception(static::$noExist);
    }
  }

  protected function getData($filename) {
    $xml = new SimpleXMLExtended($filename, 0, true);

    foreach ($xml as $field => $value) {
      $this->data[$field] = $this->getXmlDataField($xml, $field);
    }

    $this->data['slug'] = basename($filename, '.xml');
  }

  protected function getXmlDataField($xml, $field) {
    return (string) ($xml->{$field});
  }

  public function get($field) {
    $data = isset($this->data[$field]) ? $this->data[$field] : null;

    if ($field == 'slug') {
      $tmp = explode('_', $data);
      if (isset($tmp[1])) {
        $data = trim($tmp[1]);
      }
    }

    return $data;
  }

  public function getFields() {
    return array_keys($this->data);
  }

  protected function getId() {
    $slug = $this->data['slug'];
    $tmp = explode('_', $slug);

    return trim($tmp[0]);
  }

  public function getAvailableLanguages() {
    $id = $this->getId();
    $languages = array();
    $files = glob(CATALOGDATAPATH . static::$folder . '/' . $id . '*.xml');

    foreach ($files as $file) {
      $tmp = explode('_', basename($file, '.xml'));
      if (isset($tmp[1])) {
        $languages[] = trim($tmp[1]);
      }
    }

    return $languages;
  }
}

?>