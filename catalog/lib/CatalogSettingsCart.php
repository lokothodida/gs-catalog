<?php

/**
 * Cart settings for the catalog
 */
class CatalogSettingsCart {
  /** properties */
  private $cart;

  /** methods */
  // Constructor
  public function __construct($params) {
    $this->loadFile($params['file']);
  }

  // Get a particular field
  public function get($field) {
    $field = strtolower($field);

    return isset($this->cart[$field]) ? $this->formatValue($this->cart[$field]) : null; 
  }

  // Format the field
  private function formatValue($value) {
    if ($value == 'y') {
      $value = true;
    } elseif ($value == 'n') {
      $value = false;
    }

    return $value;
  }

  // Load a cart
  private function loadFile($file) {
    $xml = simplexml_load_file($file);

    $this->cart['labelname'] = (string) $xml->columns->labels->name;
    $this->cart['labelprice'] = (string) $xml->columns->labels->price;
    $this->cart['labelquantity'] = (string) $xml->columns->labels->quantity;
    $this->cart['labeltotal'] = (string) $xml->columns->labels->total;

    $this->cart['cartstyle'] = (string) $xml->style;

    $this->cart['currency'] = (string) $xml->currency;
    $this->cart['language'] = (string) $xml->language;

    $this->cart['checkouttype'] = (string) $xml->checkout->type;
    $this->cart['checkoutemail'] = (string) $xml->checkout->email;

    $this->cart['shippingflatrate'] = (string) $xml->shipping->flatrate;
    $this->cart['shippingquantityrate'] = (string) $xml->shipping->quantityrate;
    $this->cart['shippingtotalrate'] = (string) $xml->shipping->totalrate;

    $this->cart['taxrate'] = (string) $xml->tax->rate;
    $this->cart['taxshipping'] = (string) $xml->tax->shipping;

    $this->cart['css'] = (string) $xml->css;
    $this->cart['carttemplate'] = (string) $xml->template;
  }
}

?>
