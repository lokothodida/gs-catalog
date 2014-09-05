<?php

/**
 * Generic item class for catalog (extended by both categories and products)
 */
class CatalogItem {
  /** properties */
  protected $item = array(),
            $settings,
            $generalSettings;

  /** methods */
  // Constructor
  public function __construct(array $params) {
    $this->loadItem($params['file']);
    $this->settings = $params['settings'];
    $this->generalSettings = $this->settings->get('general');
  }

  // Load the product from the file
  private function loadItem($file) {
    if (file_exists($file)) {
      $xml = simplexml_load_file($file);

      // format the xml
      foreach ($xml as $field => $value) {
        // type casting
        if (count($array = (array) $value) > 0) {
          $value = $array;
        } else {
          $value = (string) $value;
        }

        $this->item[$field] = $value;
      }
    }
  }

  // Get a field
  public function getField($field) {
    return (isset($this->item[$field])) ? $this->item[$field] : null;
  }

  // Parse an id into its numeric id and slug
  protected function parseId($id) {
    $delim = '-';                      // delimiter
    $explode = explode($delim, $id);
    $numeric = reset($explode);
    array_shift($explode);
    $slug = implode($delim, $explode);

    return array(
      'numeric' => $numeric,
      'slug'    => $slug,
    );
  }
}

?>
