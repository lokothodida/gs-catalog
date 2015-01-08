<?php

class CatalogCategory {
  // == PROPERTIES ==
  private $data = array();

  // == METHODS ==
  public function __construct($filename) {
    if (file_exists($filename)) {
      $this->getData($filename);
    } elseif ($filename != null) {
      throw new Exception('CATEGORYDOESNTEXIST');
    }
  }

  private function getData($filename) {
    $xml = new SimpleXMLExtended($filename, 0, true);

    $this->data['title'] = $this->getXmlDataField($xml, 'title');
    $this->data['description'] = $this->getXmlDataField($xml, 'description');
    $this->data['order'] = $this->getXmlDataField($xml, 'order');
    $this->data['parent'] = $this->getXmlDataField($xml, 'parent');
    $this->data['slug'] = basename($filename, '.xml');
  }

  private function getXmlDataField($xml, $field) {
    return !empty($xml->{$field}) ? (string) $xml->{$field} : null;
  }

  public static function getCategory($id) {
    $filename = ($id == null) ? null : CATALOGDATAPATH . 'categories/' . $id . '.xml';

    return new CatalogCategory($filename);
  }

  public static function getCategories($params = array()) {
    $categories = array();

    foreach (glob(CATALOGDATAPATH . 'categories/*.xml') as $file) {
      $categories[] = new CatalogCategory($file);
    }

    // order
    if (!empty($params['order'])) {
      if ($params['order'] == 'custom') {
        usort($categories, 'self::sortCustom');
      } elseif ($params['order'] == 'desc') {
        $categories = array_reverse($categories);
      }
    }

    if (!empty($params['key'])) {
      if ($params['key'] == 'slug') {
        foreach ($categories as $k => $cat) {
          $categories[$cat->get('slug')] = $cat;
          unset($categories[$k]);
        }
      }
    }

    return $categories;
  }

  private static function sortCustom($a, $b) {
    return (int) $a->get('order') - (int) $b->get('order');
  }

  public static function create(array $data) {
    $slug = CatalogBackEnd::strtoslug($data['title']);
    $filename = isset($slug) ? CATALOGDATAPATH . 'categories/' . $slug . '.xml': null;

    if (!file_exists($filename) && !empty($filename)) {
      $xml = new SimpleXMLExtended('<category/>');
      self::setXmlDataField($xml, 'title', $data['title']);
      self::setXmlDataField($xml, 'description', $data['description']);
      self::setXmlDataField($xml, 'order', 0);
      self::setXmlDataField($xml, 'parent', $data['parent']);
      self::setXmlDataField($xml, 'slug', $slug);

      return (bool) $xml->saveXML($filename);
    } else {
      return false;
    }
  }

  public static function edit($id, $data) {
    $filename = CATALOGDATAPATH . 'categories/' . $id . '.xml';

    if (file_exists($filename)) {
      $xml = new SimpleXMLExtended('<category/>');
      $data['slug'] = $id;
      self::modifyXml($xml, $data);

      return (bool) $xml->saveXML($filename);
    } else {
      return false;
    }
  }

  private static function modifyXml($xml, $data) {
    self::setXmlDataField($xml, 'title', $data['title']);
    self::setXmlDataField($xml, 'description', $data['description']);
    self::setXmlDataField($xml, 'order', 0);
    self::setXmlDataField($xml, 'parent', $data['parent']);
    self::setXmlDataField($xml, 'slug', $data['slug']);
  }

  public static function delete($id) {
    $filename = ($id == null) ? null : CATALOGDATAPATH . 'categories/' . $id . '.xml';
    
    if (file_exists($filename)) {
      return unlink($filename);
    } else {
      return false;
    }
  }

  public static function setXmlDataField($xml, $field, $value) {
    $xml->{$field} = null;
    $xml->{$field}->addCData($value);
    return $xml;
  }

  public function get($field) {
    return isset($this->data[$field]) ? $this->data[$field] : null;
  }
}

?>