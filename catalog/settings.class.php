<?php

class CatalogSettings {
  private static $settings = array();
  private static $themeFields = array(
    'header',
    'indexheader',
    'indexcategories',
    'indexfooter',
    'category',
    'product',
    'searchresult',
    'footer',
    'css'
  );

  // == GET SETTINGS ==
  private static function getSettingsImplementation($setting) {
    if (!isset(self::$settings[$setting])) {  
      $tmp = array();
      $filename = CATALOGDATAPATH . 'settings/' . $setting . '.xml';

      $xml = new SimpleXMLExtended($filename, 0, true);

      foreach ($xml as $field => $value) {
        $tmp[$field] = (string) $value;
      }

      self::$settings[$setting] = $tmp;
    }
    return self::$settings[$setting];
  }

  public static function getMainSettings() {
    self::getSettingsImplementation('main');

    if (!is_array(self::$settings['main']['languages'])) {
      self::$settings['main']['languages'] = explode("\n", self::$settings['main']['languages']);
    }

    return self::$settings['main'];
  }

  public static function getThemeSettings() {
    if (!isset(self::$settings['theme'])) {  
      $tmp = array();
      $filename = CATALOGDATAPATH . 'settings/theme.xml';

      // check current theme
      $xml = new SimpleXMLExtended($filename, 0, true);

      foreach ($xml as $field => $value) {
        $tmp[$field] = (string) $value;
      }

      // load current theme data
      $xml = new SimpleXMLExtended(CATALOGDATAPATH . 'theme/' . $tmp['current'] . '.xml', 0, true);

      foreach ($xml as $field => $value) {
        $tmp[$field] = (string) $value;
      }

      self::$settings['theme'] = $tmp;

      foreach (self::$themeFields as $field) {
        self::$settings['theme'][$field] = isset(self::$settings['theme'][$field]) ? self::$settings['theme'][$field] : '';
      }
    }
    return self::$settings['theme'];
  }

  public static function getFieldsSettings() {
    if (!isset(self::$settings['fields'])) {  
      $tmp = array();
      $filename = CATALOGDATAPATH . 'settings/fields.xml';

      $xml = new SimpleXMLExtended($filename, 0, true);

      foreach ($xml->field as $i => $field) {
        $name = (string) $field->name;
        foreach ($field as $prop => $val) {
          $tmp[$name][$prop] = (string) $val;
        }
      }

      self::$settings['fields'] = $tmp;
    }
    return self::$settings['fields'];
  }

  public static function getCartSettings() {
    return self::getSettingsImplementation('cart');
  }

  // == EDIT SETTINGS ==
  public static function editSettingsMain(array $data) {
    $filename = CATALOGDATAPATH . '/settings/main.xml';
    $xml = new SimpleXMLExtended('<settings/>');

    // checkboxes
    $data['wysiwyg']        = isset($data['wysiwyg']) ? 'y' : 'n';
    $data['slugged']        = isset($data['slugged']) ? 'y' : 'n';
    $data['internalsearch'] = isset($data['internalsearch']) ? 'y' : 'n';
    $data['i18nsearch']     = isset($data['i18nsearch']) ? 'y' : 'n';
    
    foreach ($data as $field => $value) {
      self::addCData($xml, $field, $value);
    }

    return (bool) $xml->saveXML($filename);
  }

  public static function editSettingsTheme(array $data) {
    $filename = CATALOGDATAPATH . '/theme/' . $data['current'] . '.xml';
    $xml = new SimpleXMLExtended('<theme/>');

    foreach (self::$themeFields as $field) {
      $content = isset($data[$field]) ? $data[$field] : '';
      self::addCData($xml, $field, $data[$field]);
    }

    return (bool) $xml->saveXML($filename);
  }

  public static function editSettingsFields(array $data) {
    $filename = CATALOGDATAPATH . '/settings/fields.xml';
    $xml = new SimpleXMLExtended('<fields/>');
    $xml->field = null;

    foreach ($data['name'] as $i => $field) {
      $xml->field[$i] = null;
      self::addCData($xml->field[$i], 'name', $field);
      self::addCData($xml->field[$i], 'label', $data['label'][$i]);
      self::addCData($xml->field[$i], 'type', $data['type'][$i]);
      self::addCData($xml->field[$i], 'default', $data['default'][$i]);
      self::addCData($xml->field[$i], 'index', $data['index'][$i]);
    }

    return (bool) $xml->saveXML($filename);
  }

  private static function addCData($node, $name, $data) {
    $node->{$name} = null;
    $node->{$name}->addCData($data);
    return $node;
  }

  public static function editCartSettings($data) {
  }
}

?>